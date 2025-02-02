<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Alat;
use App\Models\Role;
use App\Models\User;
use App\Models\Jenis;
use App\Models\Barang;
use App\Models\Satuan;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Role::create([
            'role'      => 'superadmin',
            'deskripsi' => 'Superadmin memiliki kendali penuh pada aplikasi termasuk manajemen User'
        ]);

        Role::create([
            'role'      => 'kepala gudang',
            'deskripsi' => 'Kepala gudang memilki akses untuk verifikasi dan laporan'
        ]);

        Role::create([
            'role'      => 'admin gudang',
            'deskripsi' => 'Admin gudang memilki akses untuk mengelola barang masuk, barang keluar dan laporannya'
        ]);

        Role::create([
            'role'      => 'staff warehouse',
            'deskripsi' => 'Staff warehouse memilki akses untuk mengelola data item'
        ]);

        User::create([
            'name'      => 'Super Admin',
            'email'     => 'superadmin@gmail.com',
            'password'  => bcrypt('1234'),
            'role_id'   => 1
        ]);

        User::create([
            'name'      => 'Kepala Gudang',
            'email'     => 'kepalagudang@gmail.com',
            'password'  => bcrypt('1234'),
            'role_id'   => 2
        ]);

        User::create([
            'name'      => 'Admin Gudang',
            'email'     => 'admin@gmail.com',
            'password'  => bcrypt('1234'),
            'role_id'   => 3
        ]);

        User::create([
            'name'      => 'Staff Warehouse',
            'email'     => 'staffwarehouse@gmail.com',
            'password'  => bcrypt('1234'),
            'role_id'   => 4
        ]);

        Jenis::create([
            'jenis_barang'  => 'Reagen Dingin',
            'user_id'       => 1
        ]);

        Jenis::create([
            'jenis_barang'  => 'Reagen Kering',
            'user_id'       => 1
        ]);

        Satuan::create([
            'satuan'        => 'mL',
            'user_id'       => 1
        ]);
        Satuan::create([
            'satuan'        => 'pcs',
            'user_id'       => 1
        ]);

        Supplier::create([
            'supplier'      => 'PT Saba Indomedika',
            'alamat'        => 'Surabaya, Jawa Timur',
            'user_id'       => 1
        ]);

        Alat::create([
            'alat'      => 'Analyzer',
            'user_id'   => 1
        ]);
    
    }
}
