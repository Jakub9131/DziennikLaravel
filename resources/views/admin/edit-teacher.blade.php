@extends('layouts.dziennik')

@section('title', 'Edycja Nauczyciela')

@section('content')
<div class="admin-panel">
    <div class="panel-box" style="max-width: 600px;">
        <h1>Edytuj Nauczyciela</h1>

        <form action="{{ route('admin.users.updateTeacher', $teacher->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <label>Imię i Nazwisko</label>
                <input type="text" name="name" value="{{ $teacher->name }}" required>

                <label>Adres Email</label>
                <input type="email" name="email" value="{{ $teacher->email }}" required>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" style="flex: 1;">Zapisz zmiany</button>
                    <a href="{{ route('panel.admin') }}" class="btn-primary" 
                       style="background-color: #718096; text-decoration: none; text-align: center; flex: 1;">
                       Anuluj
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
