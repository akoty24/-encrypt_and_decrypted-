<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeopleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Person::create([
            'name' => 'mohamed Saber',
            'age' => 23,
            'nationality_id' => 1,
            'birthdate' => '2001-06-10',

        ]);
    }
}
