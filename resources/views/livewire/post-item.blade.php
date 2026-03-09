<?php
use Livewire\Volt\Component;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use Livewire\Attributes\Validate;

new class extends Component {
    public Post $post;
    public int $likesCount = 0;
    public bool $isLiked = false;
    public int $commentsCount = 0;
    
    // Variabel penampung komentar (Lazy Loading)
    public $loadedComments = [];
    public bool $hasLoadedComments = false;
    
    #[Validate('required|string|max:500')]
    public string $newComment = '';

    public function mount(Post $post): void
    {
        $this->post = $post;
        // Hanya menghitung jumlah, BUKAN mengambil semua data. Ini jauhhh lebih ringan!
        $this->likesCount = $post->likes()->count();
        $this->commentsCount = $post->comments()->count();
        $this->isLiked = $post->likes()->where('user_id', auth()->id())->exists();
    }

public function toggleLike(): void
    {
        if ($this->isLiked) {
            Like::where('post_id', $this->post->id)->where('user_id', auth()->id())->delete();
            $this->likesCount--;
            $this->isLiked = false;
        } else {
            // ✅ OPTIMASI: Gunakan firstOrCreate untuk menghindari spam Like error 500
            Like::firstOrCreate([
                'post_id' => $this->post->id,
                'user_id' => auth()->id()
            ]);
            $this->likesCount++;
            $this->isLiked = true;
        }
    }

    // Fungsi ini HANYA dipanggil saat tombol komentar di-klik
    public function loadCommentsData(): void
    {
        if (!$this->hasLoadedComments) {
            $this->loadedComments = $this->post->comments()->with('user')->latest()->get();
            $this->hasLoadedComments = true;
        }
    }

    public function addComment(): void
    {
        $this->validate();

        $newCommentData = Comment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'content' => $this->newComment
        ]);

        // Muat relasi user untuk komentar baru ini
        $newCommentData->load('user'); 
        
        // Langsung suntikkan ke array layar tanpa query ulang ke Supabase! (Super Cepat)
        $this->loadedComments->prepend($newCommentData);
        $this->commentsCount++;

        $this->newComment = ''; 
    }

    public function deletePost(): void
    {
        if (auth()->id() === $this->post->user_id) {
            $this->post->delete();
            $this->dispatch('post-deleted'); // Beritahu Feed untuk menghilangkannya
        }
    }

    public function deleteComment($commentId): void
    {
        $comment = Comment::find($commentId);
        if ($comment && auth()->id() === $comment->user_id) {
            $comment->delete();
            $this->commentsCount--;
            // Hapus dari tampilan array secara instan
            $this->loadedComments = $this->loadedComments->reject(fn($c) => $c->id === $commentId);
        }
    }
}; ?>

<div x-data="{ showCommentModal: false }">
    
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm transition-colors duration-300">
        
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 shrink-0 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-brand dark:text-blue-300 font-bold uppercase">
                {{ substr($post->user->name, 0, 1) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $post->user->name }}</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">&middot; {{ $post->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ '@'.$post->user->username }}</p>
            </div>
        </div>

        <div class="mb-4">
            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $post->content }}</p>
        </div>
        @if($post->image_url)
            <div class="mb-4 rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800">
                <img src="{{ $post->image_url }}" alt="Post image" class="w-full h-auto max-h-[500px] object-cover">
            </div>
        @endif

        <div class="flex items-center gap-6 border-t border-gray-100 dark:border-gray-800 pt-3">
            
            <button wire:click="toggleLike" 
                    wire:loading.attr="disabled"
                    wire:target="toggleLike"
                    class="flex items-center gap-2 transition group {{ $isLiked ? 'text-red-500' : 'text-gray-500 dark:text-gray-400 hover:text-red-500' }} disabled:opacity-50 disabled:cursor-wait">
                <div class="p-2 rounded-full transition {{ $isLiked ? 'bg-red-50 dark:bg-red-500/10' : 'group-hover:bg-red-50 dark:group-hover:bg-red-500/10' }}">
                    <svg class="w-5 h-5 {{ $isLiked ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <span class="text-sm font-medium">{{ $likesCount }}</span>
            </button>

            <button @click="showCommentModal = true" 
                    wire:click="loadCommentsData"
                    wire:loading.attr="disabled"
                    wire:target="loadCommentsData"
                    class="flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-blue-500 transition group disabled:opacity-50">
                <div class="p-2 rounded-full group-hover:bg-blue-50 dark:group-hover:bg-blue-500/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <span class="text-sm font-medium">{{ $commentsCount }}</span>
            </button>

        </div>
    </div>

    <div x-show="showCommentModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
        
        <div @click="showCommentModal = false" 
             x-show="showCommentModal"
             x-transition.opacity.duration.300ms
             class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="showCommentModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-5xl h-[85vh] md:h-[75vh] bg-white dark:bg-[#121212] rounded-2xl shadow-2xl flex flex-col md:flex-row overflow-hidden z-10 m-4 border border-gray-200 dark:border-gray-800">
            
            <button @click="showCommentModal = false" class="absolute top-4 right-4 z-20 p-2 text-gray-500 bg-white/50 dark:bg-black/50 rounded-full hover:text-gray-800 dark:hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="w-full md:w-1/2 bg-gray-50 dark:bg-[#0a0a0a] p-8 flex flex-col justify-start border-b md:border-b-0 md:border-r border-gray-200 dark:border-gray-800 overflow-y-auto">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-brand dark:text-blue-300 font-bold uppercase text-xl">
                        {{ substr($post->user->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ $post->user->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ '@'.$post->user->username }} &middot; {{ $post->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                <p class="text-xl md:text-2xl text-gray-800 dark:text-gray-200 font-medium leading-relaxed whitespace-pre-line">{{ $post->content }}</p>
            </div>

            <div class="w-full md:w-1/2 flex flex-col h-full bg-white dark:bg-[#1e1e1e]">
                
            <div class="p-4 pr-12 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                <h4 class="font-bold text-gray-900 dark:text-white">Comments</h4>
                
                <div wire:loading wire:target="loadCommentsData" class="text-blue-500 text-sm flex items-center gap-1">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Memuat...</span>
                </div>
            </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-5">
                    @forelse($loadedComments as $comment)
                        <div class="flex gap-3">
                            <div class="w-8 h-8 shrink-0 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mt-1">
                                {{ substr($comment->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1 bg-gray-50 dark:bg-[#2a2a2a] rounded-2xl p-3">
                                <div class="flex items-center justify-between mb-1">
                                    <h5 class="font-bold text-sm text-gray-900 dark:text-white">{{ $comment->user->name }}</h5>
                                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans(null, true, true) }}</span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $comment->content }}</p>
                            </div>
                        </div>
                    @empty
                        <div wire:loading.remove wire:target="loadCommentsData" class="h-full flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <p class="text-sm">Belum ada komentar.</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-[#121212]/50">
                    <form wire:submit="addComment" class="flex gap-2">
                        <input wire:model="newComment" type="text" placeholder="Add a comment..." required class="flex-1 bg-white dark:bg-[#2a2a2a] border border-gray-200 dark:border-gray-700 rounded-full px-4 py-2 text-sm text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition disabled:opacity-50" wire:loading.attr="disabled" wire:target="addComment">
                        
                        <button type="submit" 
                                wire:loading.attr="disabled" 
                                wire:target="addComment"
                                class="bg-blue-600 hover:bg-blue-700 text-white rounded-full px-5 py-2 text-sm font-bold transition disabled:opacity-50 flex items-center gap-2">
                            <span wire:loading.remove wire:target="addComment">Post</span>
                            <span wire:loading wire:target="addComment">Posting...</span>
                        </button>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</div>