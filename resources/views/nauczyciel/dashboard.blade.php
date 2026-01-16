@extends('layouts.dziennik')

@section('title', 'Panel Nauczyciela')

@section('content')
<div class="teacher-panel">
    <div class="panel-box" style="max-width: 1200px;">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-button">Wyloguj</button>
        </form>

        <h1>Panel Nauczyciela</h1>
        <p style="text-align: center; margin-bottom: 20px;">Zalogowany jako: <strong>{{ Auth::user()->name }}</strong></p>

        @if(session('success'))
            <div style="background: #c6f6d5; color: #22543d; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center;">
                {{ session('success') }}
            </div>
        @endif

        <div style="margin-bottom: 30px;">
            <h3>Twoje klasy i przedmioty:</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
                @foreach($assignments as $assign)
                    @php
                        // Sprawdzamy, czy ten konkretny kafelek jest obecnie wybrany
                        $isActive = (request('class_id') == $assign->class_id && request('subject_id') == $assign->subject_id);
                    @endphp
                    
                    <a href="{{ route('panel.nauczyciel', ['class_id' => $assign->class_id, 'subject_id' => $assign->subject_id]) }}" 
                       style="text-decoration: none; padding: 15px; border-radius: 8px; 
                              border: 2px solid {{ $isActive ? '#4CAF50' : '#eee' }}; 
                              background: {{ $isActive ? '#f0fff4' : '#fff' }}; 
                              color: #333; min-width: 150px; text-align: center; transition: all 0.2s;">
                        <strong>{{ $assign->class_name }}</strong><br>
                        <small style="color: {{ $isActive ? '#2d6a4f' : '#666' }};">{{ $assign->subject_name }}</small>
                    </a>
                @endforeach
            </div>
        </div>

        @if($selectedStudents)
            <div class="card" style="border-top: 5px solid #4CAF50;">
                <h2 style="margin-bottom: 20px;">{{ $currentClass->name }} - {{ $currentSubject->name }}</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Uczeń</th>
                            <th>Oceny (kliknij, aby edytować)</th>
                            <th style="width: 280px;">Wystaw nową</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedStudents as $student)
                        <tr>
                            <td><strong>{{ $student->name }}</strong></td>
                            <td>
                                @foreach($student->grades as $grade)
                                    <a href="{{ route('nauczyciel.grades.edit', $grade->id) }}" 
                                       class="grade-badge" 
                                       title="Nauczyciel: {{ $grade->teacher->name ?? 'Brak' }} | Kliknij, aby edytować/usunąć: {{ $grade->comment }}"
                                       style="text-decoration: none; cursor: pointer;">
                                         {{ $grade->value }}
                                    </a>
                                @endforeach
                            </td>
                            <td>
                                <form action="{{ route('nauczyciel.grades.store') }}" method="POST" style="display: flex; gap: 5px;">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <input type="hidden" name="subject_id" value="{{ $currentSubject->id }}">
                                    <select name="value" required style="width: 60px; margin-bottom: 0;">
                                        @foreach([1,2,3,4,5,6] as $v) <option value="{{$v}}">{{$v}}</option> @endforeach
                                    </select>
                                    <input type="text" name="comment" placeholder="Opis" style="width: 100px; margin-bottom: 0;">
                                    <button type="submit" style="padding: 5px 12px;">+</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card" style="text-align: center; border: 2px dashed #ccc;">
                <p>Wybierz klasę z powyższej listy, aby otworzyć dziennik.</p>
            </div>
        @endif

        {{-- Sekcja zmiany hasła dla nauczyciela --}}
        <div class="card" style="margin-top: 40px; border-top: 4px solid #ecc94b;">
            <h3>🔐 Bezpieczeństwo konta</h3>
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
                            Zmień hasło
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
