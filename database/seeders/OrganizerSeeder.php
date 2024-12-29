<?php

namespace Database\Seeders;

use App\Models\Organizer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizer = Organizer::all();
        // print($organizer->pluck('user_id'));
        $users = User::where('role', 'organizer')
                    ->whereNotIn('id', $organizer->pluck('user_id'))
                    ->get();
        // print($users);

        foreach ($users as $key => $user) {
            Organizer::factory()
                ->userId($user->id)
                ->create();
        }
    }
}
