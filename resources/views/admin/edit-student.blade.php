@extends('layouts.dziennik')

@section('title', 'Edycja Ucznia i Rodzica')

@section('content')
<div class="admin-container" style="padding: 20px; font-family: sans-serif; max-width: 800px; margin: auto;">
    <h1 style="border-bottom: 2px solid #3490dc; padding-bottom: 10px;">Edycja kont</h1>

    <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <form action="{{ route('admin.users.updateStudent', $student->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                <div>
                    <h4 style="color: #3490dc;">Dane Ucznia</h4>
                    <label>Imię i Nazwisko:</label>
                    <input type="text" name="student_name" value="{{ $student->name }}" required style="width:100%; margin-bottom:10px; padding:8px;">
                    
                    <label>Email:</label>
                    <input type="email" name="student_email" value="{{ $student->email }}" required style="width:100%; margin-bottom:10px; padding:8px;">
                    
                    <label>Klasa:</label>
                    <select name="class_group_id" required style="width:100%; padding:8px;">
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $student->class_group_id == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <h4 style="color: #38a169;">Dane Rodzica</h4>
                    <label>Imię i Nazwisko:</label>
                    <input type="text" name="parent_name" value="{{ $student->parent->name ?? '' }}" required style="width:100%; margin-bottom:10px; padding:8px;">
                    
                    <label>Email:</label>
                    <input type="email" name="parent_email" value="{{ $student->parent->email ?? '' }}" required style="width:100%; margin-bottom:10px; padding:8px;">
                    
                    <p style="font-size: 12px; color: #666; margin-top: 20px;">Uwaga: Zmiana e-maila wpłynie na dane logowania użytkowników.</p>
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <button type="submit" style="background: #3490dc; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                    Zapisz zmiany
                </button>
                <a href="{{ route('panel.admin') }}" style="background: #e2e8f0; color: #2d3748; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
                    Anuluj
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
