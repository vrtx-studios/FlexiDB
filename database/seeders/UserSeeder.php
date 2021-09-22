<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sPassword = Hash::make( 'Test123' );

        DB::table( 'users' )->insert( [
            'name' => 'rickard',
            'email' => 'rickard@ahlstedt.xyz',
            'password' => $sPassword
        ] );
    }
}
