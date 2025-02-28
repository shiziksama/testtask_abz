<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 45; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => '+380' . $faker->numerify('#########'),
                'position_id' => $faker->numberBetween(1, 10),
                'photo' => $faker->image('public/images', 70, 70, 'people', false), // Generates a random image
                // add other fields as necessary
            ]);
        }
    }
}
