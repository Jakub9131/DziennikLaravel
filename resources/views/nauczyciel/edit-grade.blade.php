@extends('layouts.dziennik')

@section('title', 'Edycja Oceny')

@section('content')
<div class="teacher-panel">
    <div class="panel-box" style="max-width: 500px;">
        <div style="display: flex; justify-content: flex-start; margin-bottom: 20px;">
            <a href="{{ route('panel.nauczyciel', ['class_id' => $grade->student->class_group_id, 'subject_id' => $grade->subject_id]) }}" 
               style="text-decoration: none; color: var(--text-muted); font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Powrót do dziennika
            </a>
        </div>

        <h1>Edytuj Ocenę</h1>
        
        <div style="text-align: center; margin-bottom: 20px;">
            <p>Uczeń: <strong>{{ $grade->student->name }}</strong></p>
            <p>Przedmiot: <strong>{{ $grade->subject->name }}</strong></p>
        </div>

        @if($errors->any())
            <div style="background: #fed7d7; color: #c53030; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <form action="{{ route('nauczyciel.grades.update', $grade->id) }}" method="POST">
                @csrf
                @method('PUT')

                <label>Ocena</label>
                <select name="value" required>
                    @foreach([1, 2, 3, 4, 5, 6] as $v)
                        <option value="{{ $v }}" {{ $grade->value == $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>

                <label>Komentarz / Opis</label>
                <input type="text" name="comment" value="{{ old('comment', $grade->comment) }}" placeholder="np. Sprawdzian: Funkcje">

                <div style="display: grid; grid-template-columns: 1fr; gap: 10px; margin-top: 20px;">
                    <button type="submit">Zapisz zmiany</button>
                </div>
            </form>

            <form action="{{ route('nauczyciel.grades.destroy', $grade->id) }}" method="POST" 
                  onsubmit="return confirm('Czy na pewno chcesz trwale usunąć tę ocenę?')" style="margin-top: 10px;">
                @csrf
                @method('DELETE')
                <button type="submit" style="background-color: var(--danger); width: 100%;">Usuń ocenę</button>
            </form>
        </div>
    </div>
</div>
@endsection
