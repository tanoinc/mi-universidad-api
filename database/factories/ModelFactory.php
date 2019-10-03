<?php

/*
  |--------------------------------------------------------------------------
  | Model Factories
  |--------------------------------------------------------------------------
  |
  | Here you may define all of your model factories. Model factories give
  | you a convenient way to create models for testing and seeding your
  | database. Just tell the factory how a default model should look.
  |
 */

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'username' => $faker->unique()->userName,
        'password' => '$2y$10$A1jA.MSvW4U4oQCFrLb/YeI83vz5eIVb8BZoj/G/HeRdKdWsXVL0q',
        'name' => $faker->name,
        'surname' => $faker->lastName,
        'email' => $faker->email,
        'hash_id' => $faker->md5,
        'created_at' => $faker->dateTimeThisYear,
        'updated_at' => $faker->dateTimeThisYear,
        'deleted_at' => null,
        'origin' => 'mobile',
        'recover_password_value' => null,
        'recover_password_count' => 0,
        'confirmed' => true,
        'last_password_recovery' => null,
        'attempts_login' => 0,
    ];
});

$factory->define(App\Attendance::class, function (Faker\Generator $faker) {
    $start_date = $faker->dateTimeThisMonth();
    $end_date = clone $start_date;
    $end_date->add(new DateInterval("PT3H"));

    return [
        'name' => $faker->sentence,
        'description' => $faker->paragraph,
        'location' => $faker->city,
        'start_time' => $start_date,
        'end_time' => $end_date,
        'send_notification' => false,
        'application_id' => 1,
        'global' => false,
        'context_id' => null,
        'created_at' => $faker->dateTimeThisYear,
        'updated_at' => $faker->dateTimeThisYear,
    ];
});

$factory->define(App\Application::class, function (Faker\Generator $faker) {
    return [
        'api_key' => md5($faker->unique()->uuid),
        'name' => $faker->unique()->uuid,
        'description' => $faker->sentence,
        'api_secret' => md5($faker->password()),
        'privilege_version' => 1,
        'auth_required' => false,
        'auth_callback_url' => null,
        'created_at' => $faker->dateTimeThisYear,
        'updated_at' => $faker->dateTimeThisYear,
    ];
});
