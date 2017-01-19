<?php

$factory('Difftechnology\Notifynder\Models\NotificationCategory', [

    'name' => $faker->name,
    'text' => 'test notification',
]);

$factory('Difftechnology\Tests\Models\User', [

    'name' => $faker->name,
    'surname' => $faker->lastName,
]);

$factory('Difftechnology\Notifynder\Models\Notification', [

    'from_id' => 'factory:Difftechnology\Tests\Models\User',
    'from_type' => 'Difftechnology\Tests\Models\User',
    'to_id' => 'factory:Difftechnology\Tests\Models\User',
    'to_type' => 'Difftechnology\Tests\Models\User',
    'category_id' => 'factory:Difftechnology\Notifynder\Models\NotificationCategory',
    'url' => $faker->url,
    'extra' => json_encode(['exta.name' => $faker->name]),
    'read' => 0,
    'expire_time' => null,
    'created_at' => $faker->dateTime,
    'updated_at' => $faker->dateTime,
]);

$factory('Difftechnology\Notifynder\Models\NotificationGroup', [
    'name' => $faker->name,
]);
