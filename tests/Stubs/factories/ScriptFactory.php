<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use VCComponent\Laravel\Script\Entities\Script;

$factory->define(Script::class, function (Faker $faker) {
    return [
        'title'     => $faker->name,
        'position'   => $faker->word,
        'status'    => rand(1,3),
        'content'   => $faker->paragraphs(rand(4, 7), true),
    ];
});

$factory->state(Script::class, 'head',function () {
    return [
        'position' => 'head',
    ];
});