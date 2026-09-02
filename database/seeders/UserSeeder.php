<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [             
            [                 
                'name' => 'Pasyha zulfahmi rochmat',                 
                'email' => 'admin@gmail.com',                 
                'password' => Hash::make('password123'),             
                'role' => 'admin',                 
                'no_hp' => '081234567890',                 
                'alamat' => 'Bandung, West Java',             
            ],             
            [                 
                'name' => 'daus dos santos',                 
                'email' => 'petugas@gmail.com',                 
                'password' => Hash::make('password123'),                 
                'role' => 'petugas',                 
                'no_hp' => '082345678901',                                     
                'alamat' => 'Baleendah, Bandung',             
            ],             
            [                 
                'name' => 'dadang el kembar',                 
                'email' => 'dadang@gmail.com',                 
                'password' => Hash::make('password123'),                 
                'role' => 'peminjam',                                        
                'no_hp' => '083456789012',                                     
                'alamat' => 'Ciparay, Bandung',             
            ],             
            [                 
                'name' => 'barman ferdinan agus',                 
                'email' => 'barman@gmail.com',                 
                'password' => Hash::make('password123'),                 
                'role' => 'peminjam',                 
                'no_hp' => '084567890123',                 
                'alamat' => 'Dayeuhkolot, Bandung',                             
            ],             
            [ 
                'name' => 'eko bin epul bin nasir',                 
                'email' => 'eko@gmail.com',                 
                'password' => Hash::make('password123'),                 
                'role' => 'peminjam',                 
                'no_hp' => '085678901234',                 
                'alamat' => 'Banjaran, Bandung',             
            ],    
        ];          
        
        foreach ($users as $user) {             
            User::create($user);         
        }     
    } 
}
