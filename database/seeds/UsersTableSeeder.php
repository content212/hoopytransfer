<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\User::class, 3)->create()
            ->each(function ($user) {
                $user->role()->save(factory(\App\Models\Role::class)->make());
            });
    }
}
