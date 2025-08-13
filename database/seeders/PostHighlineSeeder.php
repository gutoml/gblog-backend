<?php

namespace Database\Seeders;

use App\Models\PostHighline;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostHighlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PostHighline::factory()->count(7)->create();
    }
}
