<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Companies\Company::class, function (Faker $faker) {

    return [
        'company_name' => $faker->company,
        'address' => $faker->address,
        'contact_name' => $faker->name,
        'contact_email' => $faker->email,
        'phone' => $faker->phoneNumber,
    ];
});