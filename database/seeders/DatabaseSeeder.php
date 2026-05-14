<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Demo judge
        User::create([
            'name'     => 'Judge One',
            'email'    => 'judge@judge.com',
            'password' => Hash::make('password'),
            'role'     => 'judge',
        ]);

        // Demo team
        $teamUser = User::create([
            'name'     => 'Team Alpha',
            'email'    => 'alpha@team.com',
            'password' => Hash::make('password'),
            'role'     => 'team',
        ]);
        Team::create(['name' => 'Team Alpha', 'user_id' => $teamUser->id]);

        $this->command->info('✓ Admin:  admin@admin.com  / password');
        $this->command->info('✓ Judge:  judge@judge.com  / password');
        $this->command->info('✓ Team:   alpha@team.com   / password');
    }
}
