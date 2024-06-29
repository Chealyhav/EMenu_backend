<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
             // Create users
       $user1 = User::create(['name' => 'user1', 'email' => 'user1@gmail.com', 'password' => bcrypt('password')]);
    //    $user2 = User::create(['name' => 'user2', 'email' => 'user2@gmail.com', 'password' => bcrypt('password')]);
        // Create roles
        $adminRole = Role::create(['id'=>1,'name' => 'Admin']);
        // $editorRole = Role::create(['name' => 'Editor']);



        // Attach roles to users
        $user1->roles()->attach([$adminRole->id, $editorRole->id]);
        // $user2->roles()->attach($editorRole->id);
    }
}
