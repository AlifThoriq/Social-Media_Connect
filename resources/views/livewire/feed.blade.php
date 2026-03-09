<?php
use Livewire\Volt\Component;
use App\Models\Post;
use Livewire\Attributes\On;

new class extends Component {
    public int $perPage = 5; // Batas awal 5 postingan

    #[On('post-created')]
    #[On('post-deleted')] // Otomatis refresh kalau ada yang dihapus
    public function render(): mixed 
    {
        return view('livewire.feed', [
            'posts' => Post::with('user')->latest()->take($this->perPage)->get()
        ]);
    }

    // Fungsi ini dipanggil otomatis oleh AlpineJS saat scroll mentok bawah
    public function loadMore(): void
    {
        $this->perPage += 5;
    }
}; ?>
<div class="space-y-6">
    @forelse($posts as $post)
        <livewire:post-item :post="$post" :key="'post-'.$post->id" />
    @empty
        <div class="text-center text-gray-500 py-10">Belum ada postingan.</div>
    @endforelse

    @if($posts->count() >= $this->perPage)
        <div x-intersect="$wire.loadMore()" class="py-6 flex justify-center items-center gap-2 text-blue-500">
            <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span class="text-sm font-bold">Memuat lebih banyak...</span>
        </div>
    @endif
</div>