<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Run seeders in proper order due to foreign key constraints
        $this->call('FakultasSeeder');
        $this->call('DivisiSeeder');
        $this->call('ProdiSeeder');
        $this->call('UserSeeder');
    }
}