<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(
    \App\User::class,
    function (Faker $faker) {

        $userName = $faker->userName;
        $uniqueSuffix = $faker->unique()->word;
        $teacher = false;
        $domain = 'elev.ga.ntig.se';

        if (rand(0, 1) % 2) {
            $domain = 'ga.ntig.se';
            $teacher = true;
        }

        $uniqueFakeEmail = "$userName.$uniqueSuffix@$domain";

        $pos = strpos($uniqueFakeEmail, 'elev');

        return [
            'name' => $faker->name,
            'email' => $uniqueFakeEmail,
            'email_verified_at' => now(),
            'teacher' => $pos === false ? true : false,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
);
