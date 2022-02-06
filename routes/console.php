<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('configure', function (){

    Artisan::call('migrate:fresh');

    Artisan::call('passport:install');

    Role::updateOrcreate([
        'name' => 'administrator'
    ]);

    Role::updateOrcreate([
        'name' => 'client'
    ]);

    $user = User::updateOrcreate([
        'email' => 'administrator@ams-core.test',
        'phone' => '2349035875967',
        ],
        [
        'name' => 'Administrator',
        'password' => Hash::make('password')
    ]);
    $user->profile()->create([
        'address' => 'Some Address'
    ]);
    $user->assignRole('administrator');

    $user = User::updateOrcreate([
        'email' => 'adamsohiani@gmail.com',
        'phone' => '2347069948122',
    ],
        [
            'name' => 'Adams Paul',
            'password' => Hash::make('password')
        ]);
    $user->assignRole('client');
    $user->profile()->create([
        'address' => 'Some Another Address'
    ]);
    $this->comment('Configuration completed!');
});
