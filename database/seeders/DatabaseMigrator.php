<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseMigrator extends Seeder
{
    public function run(): void
    {
        
        $nauczyciele = DB::table('nauczyciele')->get();
        foreach ($nauczyciele as $n) {
            DB::table('users')->updateOrInsert(
                ['email' => $n->email],
                [
                    'name' => $n->imie . ' ' . $n->nazwisko,
                    'password' => Hash::make($n->haslo),
                    'role' => 'nauczyciel',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        
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
