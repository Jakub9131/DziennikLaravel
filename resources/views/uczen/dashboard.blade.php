@extends('layouts.dziennik')

@section('title', 'Panel Ucznia')

@section('content')
<div class="admin-panel">
    <div class="panel-box" style="max-width: 1000px;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-button">Wyloguj</button>
        </form>

        <h1>Panel Ucznia</h1>
        <p style="text-align: center; margin-bottom: 20px;">Witaj, <strong>{{ $student->name }}</strong></p>

        {{-- PODSUMOWANIE --}}
        <div class="card" style="display: flex; justify-content: space-around; align-items: center; margin-bottom: 30px; border-left: 6px solid #4a90e2;">
            <div>
                <small style="color: #666; display: block;">Twoja Klasa</small>
                <strong style="font-size: 1.2em;">{{ $student->classGroup->name ?? 'Brak' }}</strong>
            </div>
            <div style="text-align: center;">
                <small style="color: #666; display: block;">Średnia ogólna</small>
                <strong style="font-size: 1.5em; color: #4a90e2;">{{ $average }}</strong>
            </div>
        </div>

        {{-- TABELA OCEN --}}
        <div class="card" style="margin-bottom: 30px;">
            <h3>Twoje Oceny</h3>
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Przedmiot</th>
                        <th>Oceny</th>
                        <th style="text-align: center;">Średnia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects_with_grades as $subjectId => $grades)
                        <tr>
                            <td><strong>{{ $grades->first()->subject->name }}</strong></td>
                            <td>
                                @foreach($grades as $grade)
                                    <span class="grade-badge" 
                                          title="Nauczyciel: {{ $grade->teacher->name ?? 'Brak' }} | Data: {{ $grade->created_at->format('d.m.Y') }} | Komentarz: {{ $grade->comment ?? '-' }}">
                                        {{ $grade->value }}
                                    </span>
                                @endforeach
                            </td>
                            <td style="text-align: center; font-weight: bold;">
                                {{ round($grades->avg('value'), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #999; padding: 20px;">Nie masz jeszcze żadnych ocen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- KONTAKT DO NAUCZYCIELI --}}
        <div class="card" style="margin-bottom: 30px;">
            <h3>📧 Twoi Nauczyciele</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                @forelse($teachers_contacts as $teacher)
                    <div style="background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <strong>{{ $teacher->name }}</strong><br>
                        <a href="mailto:{{ $teacher->email }}" style="font-size: 0.9em;">{{ $teacher->email }}</a>
                    </div>
                @empty
                    <p style="color: #999;">Brak danych kontaktowych.</p>
                @endforelse
            </div>
        </div>

        {{-- SEKCJA ZMIANY HASŁA --}}
        <div class="card" style="border-top: 4px solid #ecc94b;">
            <h3>🔐 Bezpieczeństwo konta</h3>
            <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Zalecamy regularną zmianę hasła dla zachowania bezpieczeństwa Twoich danych.</p>
            
            @if(session('success'))
                <div style="background: #c6f6d5; color: #22543d; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px;">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('user.password.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; align-items: flex-end;">
                    <div>
                        <label style="font-size: 13px;">Obecne hasło</label>
                        <input type="password" name="current_password" required style="margin-bottom: 0;">
                    </div>
                    <div>
                        <label style="font-size: 13px;">Nowe hasło (min. 8 znaków)</label>
                        <input type="password" name="new_password" required style="margin-bottom: 0;">
                    </div>
                    <div>
                        <button type="submit" style="width: 100%; background-color: #2d3748; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer;">
                            Zaktualizuj hasło
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
