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
    $this->comment('Migration completed!');

    Artisan::call('passport:install');
    $this->comment('Passport installation completed!');

    Role::create([
        'name' => 'administrator'
    ]);

    Role::create([
        'name' => 'client'
    ]);
    $this->comment('Role creation completed!');

    $user = User::create([
        'email' => 'administrator@ams-core.test',
        'phone' => '2349035875967',
        'name' => 'Administrator',
        'password' => Hash::make('password')
    ]);
    $user->profile()->create([
        'address' => 'Some Address'
    ]);
    $user->assignRole('administrator');
    $this->comment('Admin creation completed!');

    $user = User::create([
        'email' => 'adamsohiani@gmail.com',
        'phone' => '2347069948122',
        'name' => 'Adams Paul',
        'password' => Hash::make('password')
        ]);
    $user->assignRole('client');
    $user->profile()->create([
        'address' => 'Some Another Address'
    ]);
    $this->comment('Client creation completed!');

    $this->comment('Configuration completed!');
});
