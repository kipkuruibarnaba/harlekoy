<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Faker\Factory as Faker;

class UpdateUserDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-random-data {userId}';
    // protected $description = 'Update user firstname, lastname, and timezone to random values';
    protected $description = 'Update user name and timezone to random values';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $user = User::find($userId);

        if (!$user) {
            $this->error('User not found!');
            return;
        }

        $faker = Faker::create();

        $user->firstname = $faker->Name;
        // $user->lastname = $faker->lastName;
        $user->timezone = $faker->timezone;

        $user->save();

        $this->info('User data updated successfully!');
    }   
}




