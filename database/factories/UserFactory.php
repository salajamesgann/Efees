<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\ParentContact;
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
        $role = Role::whereIn('role_name', ['admin', 'staff', 'parent'])->inRandomOrder()->first();
        
        $roleable_type = match ($role->role_name) {
            'admin' => Admin::class,
            'staff' => Staff::class,
            'parent' => ParentContact::class,
            default => null,
        };

        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role_id' => $role->role_id,
            'roleable_type' => $roleable_type,
            'roleable_id' => substr(str_replace('-', '', \Illuminate\Support\Str::uuid()->toString()), 0, 20),
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
