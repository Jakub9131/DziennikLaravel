<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseMigrator extends Seeder
{
    public function run(): void
    {
        // 1. Import Nauczycieli
        $nauczyciele = DB::table('nauczyciele')->get();
        foreach ($nauczyciele as $n) {
            DB::table('users')->updateOrInsert(
                ['email' => $n->email],
                [
                    'name' => $n->imie . ' ' . $n->nazwisko,
                    'password' => Hash::make($n->haslo), // Szyfrujemy stare hasło '1234'
                    'role' => 'nauczyciel',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 2. Import Uczniów
        $uczniowie = DB::table('uczniowie')->get();
        foreach ($uczniowie as $u) {
            DB::table('users')->updateOrInsert(
                ['email' => $u->email],
                [
                    'name' => $u->imie . ' ' . $u->nazwisko,
                    'password' => Hash::make($u->haslo),
                    'role' => 'uczen',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        
        $this->command->info('Dostosowano hasła i zaimportowano użytkowników!');
    }
}
