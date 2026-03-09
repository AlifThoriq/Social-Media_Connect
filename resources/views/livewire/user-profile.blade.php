<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Cache;

new #[Layout('layouts.app')] class extends Component
{
    public User $user;
    public $posts = [];

    public bool $isFollowing = false;
    public int $followersCount = 0;
    public int $followingCount = 0;

    public function mount($username): void
    {
        $this->user = User::where('username', $username)->firstOrFail();

        // ✅ HANYA GUNAKAN KODE OPTIMASI INI (KODE LAMA DIHAPUS)
        $this->posts = $this->user->posts()->with('user')->latest()->limit(10)->get();

        $this->followersCount = Cache::remember(
            "followers_{$this->user->id}",
            60,
            fn() => $this->user->followers()->count()
        );

        $this->followingCount = Cache::remember(
            "following_{$this->user->id}",
            60,
            fn() => $this->user->following()->count()
        );

        if (Auth::check()) {
            $this->isFollowing = $this->user
                ->followers()
                ->where('follower_id', Auth::id())
                ->exists();
        }
    }

    public function toggleFollow()
    {
        if (!Auth::check() || Auth::id() === $this->user->id) return;

        $follow = Follow::where('follower_id', Auth::id())->where('following_id', $this->user->id)->first();

        if ($follow) {
            $follow->delete();
            $this->isFollowing = false;
            $this->followersCount--; // ✅ Langsung kurangi di memori
        } else {
            Follow::create(['follower_id' => Auth::id(), 'following_id' => $this->user->id]);
            $this->isFollowing = true;
            $this->followersCount++; // ✅ Langsung tambah di memori
        }

        // ✅ Paksa perbarui cache, TIDAK PERLU query ulang ke database!
        Cache::put("followers_{$this->user->id}", $this->followersCount, 60);
    }

    // Variabel untuk Modal Edit
    public string $editName = '';
    public string $editUsername = '';
    public string $editBio = '';

    // Isi variabel saat modal dibuka
    public function loadEditData(): void
    {
        $this->editName = $this->user->name;
        $this->editUsername = $this->user->username;
        $this->editBio = $this->user->bio ?? '';
    }

    public function updateProfile(): void
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editUsername' => 'required|string|max:255|unique:users,username,'.$this->user->id,
            'editBio' => 'nullable|string|max:160',
        ]);

        $this->user->update([
            'name' => $this->editName,
            'username' => $this->editUsername,
            'bio' => $this->editBio,
        ]);

        session()->flash('success', 'Profil berhasil diperbarui!');
        $this->dispatch('profile-updated'); // Tutup modal
    }

    public function saveCroppedAvatar($base64Image)
    {
        $image_parts = explode(";base64,", $base64Image);
        $image_base64 = base64_decode($image_parts[1]);
        
        $path = 'avatars/' . uniqid('avatar_') . '.jpg';

        Storage::disk('s3')->put($path, $image_base64);

        $baseUrl = str_replace('/storage/v1/s3', '', env('AWS_ENDPOINT'));
        $publicUrl = $baseUrl . '/storage/v1/object/public/' . env('AWS_BUCKET') . '/' . $path;

        $this->user->update(['avatar_url' => $publicUrl]);

        session()->flash('success', 'Avatar berhasil diperbarui!');
    }
};
?>

<div class="w-full"
     x-data="{
        showCropModal: false,
        localImageUrl: '',
        cropperInstance: null,
        isUploading: false,
        
        // Fungsi saat file dipilih (SANGAT INSTAN, TANPA INTERNET!)
        fileChosen(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Buat URL lokal di browser
            this.localImageUrl = URL.createObjectURL(file);
            this.showCropModal = true;
            
            // Reset input file agar bisa pilih file yang sama berulang kali
            event.target.value = '';

            // Init Cropper
            setTimeout(() => {
                const imgElement = document.getElementById('image-to-crop');
                if (this.cropperInstance) this.cropperInstance.destroy();
                this.cropperInstance = new Cropper(imgElement, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 0.9,
                    guides: true,
                    center: true,
                    dragMode: 'move',
                });
            }, 100);
        },
        
        // Fungsi tutup modal
        closeModal() {
            this.showCropModal = false;
            if (this.cropperInstance) {
                this.cropperInstance.destroy();
                this.cropperInstance = null;
            }
            this.localImageUrl = '';
            this.isUploading = false;
        },
        
        // Fungsi eksekusi potong dan simpan
        cropAndSave() {
            if (!this.cropperInstance) return;
            
            // Nyalakan status loading di tombol
            this.isUploading = true;
            
            // Ambil gambar ukuran 400x400
            let canvas = this.cropperInstance.getCroppedCanvas({ width: 400, height: 400 });
            let base64Image = canvas.toDataURL('image/jpeg', 0.9);
            
            // Tembak ke PHP dan tunggu sampai selesai
            $wire.saveCroppedAvatar(base64Image).then(() => {
                this.closeModal(); // Tutup otomatis jika sukses
            });
        }
     }">

  @@if(session()->has('success'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3000)"
         x-transition.duration.500ms
         class="bg-green-500 text-white p-3 rounded-xl mb-4 font-bold shadow-md flex items-center gap-2">
        
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        
        {{ session('success') }}
    </div>
  @endif

    <div class="relative bg-white dark:bg-[#1e1e1e] border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden mb-8 shadow-sm transition-colors duration-300">
        <div class="h-48 bg-gradient-to-r from-blue-600 to-indigo-800"></div>

        <div class="px-6 pb-6 relative">
            <div class="flex justify-between items-end -mt-16 mb-4">
                
                <div class="relative group z-10">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" class="w-32 h-32 rounded-full border-4 border-white dark:border-[#1e1e1e] object-cover bg-white">
                    @else
                        <div class="w-32 h-32 rounded-full border-4 border-white dark:border-[#1e1e1e] bg-blue-100 dark:bg-blue-900 flex items-center justify-center font-bold text-4xl uppercase text-brand dark:text-blue-300">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif

                    @if(Auth::id() === $user->id)
                        <label class="absolute inset-0 flex items-center justify-center bg-black/60 text-white rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition">
                            <input type="file" @change="fileChosen" class="hidden" accept="image/*">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </label>
                    @endif
                </div>

                <div>
                    @if(Auth::id() !== $user->id)
                        <button wire:click="toggleFollow" class="font-bold py-2 px-6 rounded-full transition shadow-md {{ $isFollowing ? 'bg-gray-200 text-gray-900 dark:bg-gray-700 dark:text-white' : 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' }}">
                            {{ $isFollowing ? 'Following' : 'Follow' }}
                        </button>
                    @else
                        <button @click="$dispatch('open-edit-modal'); $wire.loadEditData()" class="border border-gray-300 dark:border-gray-600 font-bold py-2 px-6 rounded-full dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition">
    Edit Profile
</button>
                    @endif
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                <p class="text-gray-500 dark:text-gray-400">{{ '@'.$user->username }}</p>
                <p class="mt-4 text-gray-800 dark:text-gray-200">{{ $user->bio ?? 'Halo! Saya sedang menggunakan Sosmed Masterpiece.' }}</p>
                <div class="flex gap-6 mt-5 text-sm">
                    <p><span class="font-bold text-gray-900 dark:text-white text-base">{{ $followingCount }}</span> <span class="text-gray-500">Following</span></p>
                    <p><span class="font-bold text-gray-900 dark:text-white text-base">{{ $followersCount }}</span> <span class="text-gray-500">Followers</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        @forelse($posts as $post)
            <livewire:post-item :post="$post" :key="$post->id" />
        @empty
            <div class="text-center py-10 bg-white dark:bg-[#1e1e1e] rounded-2xl border border-gray-100 dark:border-gray-800 text-gray-500">
                Belum ada postingan.
            </div>
        @endforelse
    </div>

    <div x-show="showCropModal" style="display: none;" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 backdrop-blur-sm p-4">
        <div @click.outside="closeModal()" class="bg-white dark:bg-[#1e1e1e] p-6 rounded-2xl w-full max-w-[500px] shadow-2xl border border-gray-200 dark:border-gray-800">
            
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Sesuaikan Avatar</h2>

            <div wire:ignore class="w-full max-h-[60vh] overflow-hidden bg-gray-100 dark:bg-[#121212] rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                <img id="image-to-crop" :src="localImageUrl" class="block max-w-full max-h-[50vh]">
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-5 mt-5">
                <button @click="closeModal()"
                        :disabled="isUploading"
                        class="px-5 py-2.5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl font-bold transition disabled:opacity-50">
                    Batal
                </button>

                <button @click="cropAndSave()"
                        :disabled="isUploading"
                        class="px-5 py-2.5 bg-[#2864f0] hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-md disabled:opacity-50 flex items-center gap-2">
                    
                    <span x-show="!isUploading">Simpan</span>
                    
                    <div x-show="isUploading" style="display: none;" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Menyimpan...</span>
                    </div>

               </button>
            </div>
        </div>
    </div>
    
    <div x-data="{ showEditModal: false }" 
         @open-edit-modal.window="showEditModal = true" 
         @profile-updated.window="showEditModal = false"
         x-show="showEditModal" style="display: none;" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 backdrop-blur-sm p-4">
        
        <div @click.outside="showEditModal = false" class="bg-white dark:bg-[#1e1e1e] p-6 rounded-2xl w-full max-w-md shadow-2xl border border-gray-200 dark:border-gray-800">
            <h2 class="text-xl font-bold mb-5 text-gray-900 dark:text-white">Edit Profile</h2>

            <form wire:submit="updateProfile" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                    <input wire:model="editName" type="text" class="w-full bg-gray-50 dark:bg-[#121212] border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    @error('editName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Username</label>
                    <input wire:model="editUsername" type="text" class="w-full bg-gray-50 dark:bg-[#121212] border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    @error('editUsername') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Bio (Opsional)</label>
                    <textarea wire:model="editBio" rows="3" class="w-full bg-gray-50 dark:bg-[#121212] border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"></textarea>
                    @error('editBio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl font-bold transition">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 bg-[#2864f0] hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-md flex items-center gap-2">
                        <span wire:loading.remove wire:target="updateProfile">Simpan</span>
                        <span wire:loading wire:target="updateProfile">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>