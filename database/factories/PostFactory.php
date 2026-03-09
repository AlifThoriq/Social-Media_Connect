<?php
namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class PostFactory extends Factory
{
    public function definition(): array
    {
        // Peluang 50% postingan memiliki gambar
        $hasImage = fake()->boolean(50);
        
        // Gunakan ID acak dari 1 sampai 1000 agar Picsum memberikan gambar yang berbeda-beda
        $randomImageId = fake()->unique()->numberBetween(1, 1000);

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? 1,
            
            // Bikin teks lebih nyata ala sosmed
            'content' => fake()->realText(200) . "\n\n" . fake()->randomElement(['#vibes #chill', '#codinglife #tech', '#nature #peace', '#sosmedmasterpiece']),
            
            // URL gambar yang dijamin unik dan jalan
            'image_url' => $hasImage ? "https://picsum.photos/id/{$randomImageId}/800/600" : null,
            
            'created_at' => fake()->dateTimeBetween('-2 weeks', 'now'),
        ];
    }
}