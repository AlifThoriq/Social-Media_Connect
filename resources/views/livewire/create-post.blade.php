<?php
use Livewire\Volt\Component;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;

new class extends Component {
    
    #[Validate('required|string|max:1000')]
    public string $content = '';

    // Menerima data gambar dari Javascript
    public function storePost($imageBase64 = null): void
    {
        $this->validate();

        $publicUrl = null;

        // Jika ada gambar, proses tembak ke S3 secara instan
        if ($imageBase64) {
            $image_parts = explode(";base64,", $imageBase64);
            $image_base64 = base64_decode($image_parts[1]);
            
            // Format jpg untuk efisiensi
            $path = 'posts/' . uniqid('post_') . '.jpg';
            
            Storage::disk('s3')->put($path, $image_base64);

            $baseUrl = str_replace('/storage/v1/s3', '', env('AWS_ENDPOINT'));
            $publicUrl = $baseUrl . '/storage/v1/object/public/' . env('AWS_BUCKET') . '/' . $path;
        }

        Post::create([
            'user_id' => auth()->id(),
            'content' => $this->content,
            'image_url' => $publicUrl, // Asumsi Anda sudah punya kolom image_url di tabel posts
        ]);

        $this->content = '';
        $this->dispatch('post-created');
    }
}; ?>

<div class="bg-white dark:bg-[#1e1e1e] border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm mb-6 transition-colors duration-300"
     x-data="{
        postContent: @entangle('content'),
        imagePreview: null,
        isPosting: false,
        
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Inisialisasi proses kompresi gambar di browser
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (e) => {
                const img = new Image();
                img.src = e.target.result;

                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    // Set batas maksimal ukuran (mirip standar Instagram)
                    const MAX_WIDTH = 1080;
                    const MAX_HEIGHT = 1080;
                    let width = img.width;
                    let height = img.height;

                    // Hitung rasio aspek untuk resize
                    if (width > height) {
                        if (width > MAX_WIDTH) {
                            height *= MAX_WIDTH / width;
                            width = MAX_WIDTH;
                        }
                    } else {
                        if (height > MAX_HEIGHT) {
                            width *= MAX_HEIGHT / height;
                            height = MAX_HEIGHT;
                        }
                    }

                    // Terapkan dimensi baru ke canvas
                    canvas.width = width;
                    canvas.height = height;

                    // Gambar ulang foto yang sudah di-resize ke dalam canvas
                    ctx.drawImage(img, 0, 0, width, height);

                    // Konversi kembali canvas ke Base64 dengan kualitas 0.8 (80%) 
                    // Ini akan secara signifikan mengurangi ukuran file!
                    const compressedBase64 = canvas.toDataURL('image/jpeg', 0.8);

                    // Tampilkan hasilnya di preview
                    this.imagePreview = compressedBase64;
                };
            };
            event.target.value = ''; // Kosongkan input agar bisa mengunggah file yang sama
        },
        
        submitPost() {
            // Jangan jalankan jika teks dan gambar kosong
            if (this.postContent.trim() === '' && !this.imagePreview) return;
            
            this.isPosting = true;
            
            // Kirim gambar yang sudah dikompresi ke Livewire
            $wire.storePost(this.imagePreview).then(() => {
                this.imagePreview = null;
                this.isPosting = false;
            }).catch(error => {
                console.error('Error posting:', error);
                this.isPosting = false;
                alert('Terjadi kesalahan saat memposting. Coba unggah gambar yang berbeda.');
            });
        }
     }">
     
    <div class="flex gap-4">
        
        @if(auth()->user()->avatar_url)
            <img src="{{ auth()->user()->avatar_url }}" class="w-12 h-12 shrink-0 rounded-full object-cover">
        @else
            <div class="w-12 h-12 shrink-0 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-brand dark:text-blue-300 font-bold text-lg uppercase">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
        @endif

        <div class="flex-1">
            <textarea
                x-model="postContent"
                rows="3"
                placeholder="What's on your mind, {{ explode(' ', auth()->user()->name)[0] }}?"
                class="w-full bg-transparent border-none focus:ring-0 resize-none text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 text-lg px-0 transition-colors"
                :disabled="isPosting"
            ></textarea>

            <div x-show="imagePreview" style="display: none;" class="relative mt-3 mb-2 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                <img :src="imagePreview" class="w-full h-auto max-h-96 object-cover">
                <button @click="imagePreview = null" type="button" class="absolute top-3 right-3 p-1.5 bg-black/60 text-white rounded-full hover:bg-black/80 transition backdrop-blur-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="h-px bg-gray-100 dark:bg-gray-800 my-3"></div>

            <div class="flex items-center justify-between">
                
                <div class="flex gap-2">
                    <label class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-full transition cursor-pointer" :class="{ 'opacity-50 pointer-events-none': isPosting }" title="Add Image">
                        <input type="file" @change="handleImageUpload" class="hidden" accept="image/*">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </label>
                </div>

                <button @click="submitPost()"
                        :disabled="isPosting || (postContent.trim() === '' && !imagePreview)"
                        class="bg-[#2864f0] hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full transition duration-200 shadow-md flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!isPosting">Post</span>
                    <div x-show="isPosting" style="display: none;" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Posting...</span>
                    </div>
                </button>
                
            </div>
        </div>
    </div>
</div>