<?php
use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Follow;
use Livewire\Attributes\On;
new class extends Component {
    public $suggestedUsers = [];

    public function mount()
    {
        $this->loadSuggestions();
    }
    #[On('follow-updated')]
    public function loadSuggestions()
    {
        // 1. Ambil ID kita sendiri dan ID orang-orang yang sudah kita follow
        $followingIds = Follow::where('follower_id', auth()->id())
                              ->pluck('following_id')
                              ->toArray();
                              
        // 2. Tambahkan ID kita sendiri ke daftar pengecualian (agar kita tidak mem-follow diri sendiri)
        $followingIds[] = auth()->id();

        // 3. Cari 3 User acak yang ID-nya TIDAK ADA di dalam daftar pengecualian tadi
        $this->suggestedUsers = User::whereNotIn('id', $followingIds)
                                    ->inRandomOrder()
                                    ->take(3)
                                    ->get();
    }

    // Fungsi saat tombol follow diklik dari widget
    public function followUser($userId)
    {

    Follow::firstOrCreate([
            'follower_id' => auth()->id(),
            'following_id' => $userId,
        ]);

        // 👈 Sinyal Radio: Beritahu komponen lain (seperti Explore) bahwa kita baru saja follow orang
        $this->dispatch('follow-updated');

        // Refresh daftar rekomendasi secara instan setelah follow berhasil
        $this->loadSuggestions();
    }
}; ?>

<div class="bg-white dark:bg-[#1e1e1e] border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm transition-colors duration-300">
    <h3 class="font-bold text-gray-900 dark:text-white mb-4">Suggested for you</h3>
    
    <div class="space-y-4">
        @forelse($suggestedUsers as $user)
            <div class="flex items-center justify-between">
                
                <a href="{{ route('user.profile', ['username' => $user->username]) }}" wire:navigate class="flex items-center gap-3 group">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                    @else
                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-brand dark:text-blue-300 font-bold text-sm uppercase">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    
                    <div>
                        <h4 class="font-bold text-sm text-gray-900 dark:text-white group-hover:text-blue-500 transition line-clamp-1">{{ $user->name }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">{{ '@'.$user->username }}</p>
                    </div>
                </a>

                <button wire:click="followUser({{ $user->id }})" 
                        wire:loading.attr="disabled"
                        class="text-blue-600 hover:text-white hover:bg-blue-600 text-sm font-bold px-4 py-1.5 rounded-full border border-blue-600 transition-all duration-200 disabled:opacity-50">
                    Follow
                </button>
                
            </div>
        @empty
            <div class="text-center py-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Kamu sudah mengikuti semua orang!</p>
            </div>
        @endforelse
    </div>
</div>