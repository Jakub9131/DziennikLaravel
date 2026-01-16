@extends('layouts.dziennik')

@section('title', 'Panel Rodzica')

@section('content')
<div class="admin-panel"> 
    <div class="panel-box">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-button">Wyloguj</button>
        </form>

        <h1>Panel Rodzica</h1>
        <p style="text-align: center;">Witaj, <strong>{{ Auth::user()->name }}</strong></p>

        @if($child)
            {{-- KARTA UCZNIA I OCENY --}}
            <div class="card" style="border-left: 6px solid var(--primary-color); margin-bottom: 30px; padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                    <h2 style="margin: 0; text-align: left;">Uczeń: {{ $child->name }}</h2>
                    <span style="background: var(--bg-light); padding: 8px 15px; border-radius: 8px; font-weight: bold; color: var(--text-dark);">
                        Klasa: {{ $child->classGroup->name ?? '-' }} | Średnia: {{ $child->average }}
                    </span>
                </div>

                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Przedmiot</th>
                            <th>Oceny (najedź, by zobaczyć szczegóły)</th>
                            <th style="text-align: center;">Średnia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($child->subjects_with_grades as $subjectId => $grades)
                            <tr>
                                <td><strong>{{ $grades->first()->subject->name }}</strong></td>
                                <td>
                                    @foreach($grades as $grade)
                                        {{-- Dodano informację o nauczycielu w atrybucie title --}}
                                        <span class="grade-badge" 
                                              title="Nauczyciel: {{ $grade->teacher->name ?? 'Brak danych' }} | Data: {{ $grade->created_at->format('d.m.Y') }} | Komentarz: {{ $grade->comment ?? '-' }}">
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
                                <td colspan="3" style="text-align: center; color: #999;">Brak wystawionych ocen.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- DANE KONTAKTOWE DO NAUCZYCIELI --}}
            <div class="card" style="margin-bottom: 30px;">
                <h3>📧 Kontakt z nauczycielami</h3>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Nauczyciel</th>
                            <th>Adres E-mail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($child->teachers_contacts as $teacher)
                            <tr>
                                <td><strong>{{ $teacher->name }}</strong></td>
                                <td><a href="mailto:{{ $teacher->email }}">{{ $teacher->email }}</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: #999;">Brak przypisanych nauczycieli.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ZMIANA HASŁA --}}
            <div class="card" style="border-top: 4px solid #ecc94b;">
                <h3>🔐 Zmień swoje hasło</h3>
                <form action="{{ route('user.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; align-items: flex-end;">
                        <div>
                            <label style="font-size: 13px;">Obecne hasło</label>
                            <input type="password" name="current_password" required style="margin-bottom: 0;">
                        </div>
                        <div>
                            <label style="font-size: 13px;">Nowe hasło</label>
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
        @else
            <div class="card" style="text-align: center; padding: 40px;">
                <p style="color: #e53e3e; font-weight: bold;">Błąd powiązania konta</p>
                <p>Nie znaleziono ucznia przypisanego do Twojego konta rodzica.</p>
            </div>
        @endif
    </div>
</div>
@endsection
