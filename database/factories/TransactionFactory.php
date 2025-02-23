<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 5000),
            'transaction_type' => $this->faker->randomElement(['income', 'expense']),
            'description' => $this->faker->sentence,
            'transaction_date' => $this->faker->date(),
        ];
    }
}
