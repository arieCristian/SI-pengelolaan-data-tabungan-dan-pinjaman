<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Nasabah>
 */
class NasabahFactory extends Factory
{
    private static $id = 1;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'users_id' => self::$id++ ,
            'kolektor' => $this->faker->randomElement([24,25]),
            'alamat' => $this->faker->address(),
            'keanggotaan' => $this->faker->randomElement(['calon anggota', 'anggota', 'anggota alit']),
            'shu' =>  0
        ];
    }
}
