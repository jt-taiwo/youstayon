<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Notification\Models\Notification;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'type' => 'reminder',
            'title' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'read_at' => null,
        ];
    }

    public function read(): self
    {
        return $this->state(fn () => [
            'read_at' => now(),
        ]);
    }

    public function unread(): self
    {
        return $this->state(fn () => [
            'read_at' => null,
        ]);
    }
}
