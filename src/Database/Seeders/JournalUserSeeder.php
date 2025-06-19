<?php

namespace Layman\LaravelJournal\Database\Seeders;

use Illuminate\Database\Seeder;
use Layman\LaravelJournal\Models\JournalUser;

class JournalUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $config = config('journal.users');

        $users = array_map(function ($user) {
            $user['password'] = bcrypt($user['password']);
            return $user;
        }, $config);

        foreach ($users as $user) {
            JournalUser::query()->create($user);
        }
    }
}
