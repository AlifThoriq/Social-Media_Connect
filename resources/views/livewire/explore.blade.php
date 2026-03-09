<?php
use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Post;
use App\Models\Follow;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url; // Untuk menyimpan tab di URL

new #[Layout('layouts.app')] class extends Component {
    #[Url]
    public string $search = '';
    
    #[Url]
    public string $tab = 'posts'; // Default tab adalah Posts ala IG
    
    public function setTab($tabName)
    {
        $this->tab = $tabName;
    }

    public function toggleFollow($userId)
    {
        $follow = Follow::where('follower_id', auth()->id())->where('following_id', $userId)->first();
        if ($follow) {
            $follow->delete();
        } else {
            Follow::firstOrCreate(['follower_id' => auth()->id(), 'following_id' => $userId]);
        }
        $this->dispatch('follow-updated');
    }

    public function with(): array
    {
        $followingIds = Follow::where('follower_id', auth()->id())->pluck('following_id')->toArray();

        $users = collect();
        $posts = collect();

        // Gunakan 'like' karena Anda menggunakan MySQL lokal. (Jika balik ke Postgres, ganti jadi 'ilike')
        if ($this->tab === 'users') {
            $users = User::where('id', '!=', auth()->id())
                ->when($this->search, function ($query) {
                    $query->where(function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('username', 'like', '%' . $this->search . '%');
                    });
                })
                ->latest()
                ->paginate(20);
        } else {
            $posts = Post::with('user')
                ->when($this->search, function ($query) {
                    $query->where('content', 'like', '%' . $this->search . '%');
                })
                ->whereNotNull('image_url') // Hanya tampilkan postingan bergambar di explore
                ->inRandomOrder() // Acak agar explore selalu fresh
                ->paginate(20);
        }

        return [
            'users' => $users,
            'posts' => $posts,
            'followingIds' => $followingIds
        ];
    }
}; ?>

<div class="w-full pb-10">
    <div class="mb-6 px-2 sticky top-0 bg-gray-50/90 dark:bg-[#121212]/90 backdrop-blur-md z-20 py-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input wire:model.live.debounce.500ms="search" type="text" placeholder="Cari orang atau postingan..." 
                   class="w-full pl-11 pr-4 py-3.5 bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-gray-800 rounded-full text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm transition">
        </div>

        <div class="flex gap-2 mt-4 border-b border-gray-200 dark:border-gray-800 pb-2 overflow-x-auto">
            <button wire:click="setTab('posts')" class="px-5 py-2 rounded-full font-bold text-sm transition whitespace-nowrap {{ $tab === 'posts' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Postingan Top
                </div>
            </button>
            <button wire:click="setTab('users')" class="px-5 py-2 rounded-full font-bold text-sm transition whitespace-nowrap {{ $tab === 'users' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Akun
                </div>
            </button>
        </div>
    </div>

    <div wire:loading class="w-full text-center py-4 text-blue-500 font-medium">
        <svg class="animate-spin h-6 w-6 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
    </div>

    <div wire:loading.remove>
        @if($tab === 'users')
            <div class="grid grid-cols-1 gap-4 px-2">
                @forelse($users as $user)
                    @php $isFollowing = in_array($user->id, $followingIds); @endphp
                    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm flex items-center justify-between transition-colors">
                        <a href="{{ route('user.profile', ['username' => $user->username]) }}" wire:navigate class="flex items-center gap-4 group">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" class="w-14 h-14 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                            @else
                                <div class="w-14 h-14 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-brand font-bold text-xl uppercase">{{ substr($user->name, 0, 1) }}</div>
                            @endif
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white group-hover:text-blue-500 transition line-clamp-1">{{ $user->name }}</h4>
                                <p class="text-sm text-gray-500 line-clamp-1">{{ '@'.$user->username }}</p>
                            </div>
                        </a>
                        <button wire:click="toggleFollow({{ $user->id }})" class="shrink-0 font-bold py-2 px-5 rounded-full transition shadow-sm border {{ $isFollowing ? 'border-gray-300 bg-transparent text-gray-900 hover:text-red-500' : 'bg-gray-900 text-white' }}">
                            {{ $isFollowing ? 'Following' : 'Follow' }}
                        </button>
                    </div>
                @empty
                    <div class="col-span-1 text-center py-10 text-gray-500">Tidak ada akun yang cocok.</div>
                @endforelse
                <div class="mt-4">{{ $users->links() }}</div>
            </div>
        @endif

        @if($tab === 'posts')
            <div class="columns-2 md:columns-3 gap-4 px-2 space-y-4">
                @forelse($posts as $p)
                    <div class="relative group rounded-2xl overflow-hidden cursor-pointer shadow-sm break-inside-avoid">
                        <img src="{{ $p->image_url }}" alt="Explore Image" class="w-full h-auto object-cover transform group-hover:scale-105 transition duration-500">
                        
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                            <div class="flex items-center gap-2 mb-2">
                                @if($p->user->avatar_url)
                                    <img src="{{ $p->user->avatar_url }}" class="w-6 h-6 rounded-full border border-white">
                                @else
                                    <div class="w-6 h-6 rounded-full bg-white text-black flex items-center justify-center text-[10px] font-bold">{{ substr($p->user->name, 0, 1) }}</div>
                                @endif
                                <span class="text-white text-xs font-bold truncate">{{ $p->user->name }}</span>
                            </div>
                            <p class="text-white text-xs line-clamp-2">{{ $p->content }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-10 text-gray-500">Tidak ada postingan yang cocok.</div>
                @endforelse
            </div>
            <div class="mt-6 px-2">{{ $posts->links() }}</div>
        @endif
    </div>
</div>