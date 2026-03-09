<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $avatars = [
            'https://nchwdpqtozikfdocjcae.supabase.co/storage/v1/object/public/sosmed-storage/avatars/1.png',
            'https://nchwdpqtozikfdocjcae.supabase.co/storage/v1/object/public/sosmed-storage/avatars/2.png',
            'https://nchwdpqtozikfdocjcae.supabase.co/storage/v1/object/public/sosmed-storage/avatars/3.png',
            'https://nchwdpqtozikfdocjcae.supabase.co/storage/v1/object/public/sosmed-storage/avatars/4.png',
            'https://nchwdpqtozikfdocjcae.supabase.co/storage/v1/object/public/sosmed-storage/avatars/5.png',
            'https://nchwdpqtozikfdocjcae.supabase.co/storage/v1/object/public/sosmed-storage/avatars/6.png',
            'https://nchwdpqtozikfdocjcae.supabase.co/storage/v1/object/public/sosmed-storage/avatars/7.png',
            'https://nchwdpqtozikfdocjcae.supabase.co/storage/v1/object/public/sosmed-storage/avatars/8.png',
            'https://nchwdpqtozikfdocjcae.supabase.co/storage/v1/object/public/sosmed-storage/avatars/9.png',
            'https://nchwdpqtozikfdocjcae.supabase.co/storage/v1/object/public/sosmed-storage/avatars/10.png',
        ];

        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(), // Buat username unik
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            // Default password untuk semua akun bot adalah 'password'
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            
            // 2. Pilih salah satu avatar secara acak!
            'avatar_url' => fake()->randomElement($avatars),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
