@extends('layouts.dziennik')

@section('title', 'Panel Administratora')

@section('content')
<div class="admin-panel">
    <div class="admin-container">
       <div style="display: flex; justify-content: flex-end; position: absolute; top: 20px; right: 20px;">
             <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-button">Wyloguj</button>
            </form>
        </div>

        <h1>Panel Administratora</h1>

        {{-- Komunikaty --}}
        @if(session('success'))
            <div style="background: #c6f6d5; color: #22543d; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; border: 1px solid #38a169;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #fff5f5; color: #c53030; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; border: 1px solid #feb2b2;">
                <strong>⚠️ Błąd:</strong> {{ session('error') }}
            </div>
        @endif

        {{-- SEKCJA 1: KLASY I PRZEDMIOTY --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div class="card">
                <h3>Klasy ({{ count($classes) }})</h3>
                <form action="{{ route('admin.classes.store') }}" method="POST" style="display: flex; gap: 10px; margin-bottom: 15px;"> 
                    @csrf
                    <input type="text" name="name" placeholder="np. 1A" required style="margin-bottom: 0;">
                    <button type="submit">Dodaj</button>
                </form>
                
                <div class="scrollable-list" style="max-height: 250px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px; padding: 5px;">
                    @forelse($classes as $class)
                        <div class="list-item" style="display: flex; justify-content: space-between; padding: 8px; border-bottom: 1px solid #f9f9f9;">
                            <span><strong>{{ $class->name }}</strong></span>
                            <form action="{{ route('admin.classes.destroy', $class->id) }}" method="POST" onsubmit="return confirm('Usunąć tę klasę?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="color: #e53e3e; border:none; background:none; cursor:pointer;">&times;</button>
                            </form>
                        </div>
                    @empty
                        <p style="text-align: center; color: #999;">Brak klas</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <h3>Przedmioty ({{ count($subjects) }})</h3>
                <form action="{{ route('admin.subjects.store') }}" method="POST" style="display: flex; gap: 10px; margin-bottom: 15px;"> 
                    @csrf
                    <input type="text" name="name" placeholder="np. Biologia" required style="margin-bottom: 0;">
                    <button type="submit" style="background-color: #3490dc;">Dodaj</button>
                </form>

                <div class="scrollable-list" style="max-height: 250px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px; padding: 5px;">
                    @forelse($subjects as $subject)
                        <div class="list-item" style="display: flex; justify-content: space-between; padding: 8px; border-bottom: 1px solid #f9f9f9;">
                            <span>{{ $subject->name }}</span>
                            <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('Usunąć przedmiot?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="color: #e53e3e; border:none; background:none; cursor:pointer;">&times;</button>
                            </form>
                        </div>
                    @empty
                        <p style="text-align: center; color: #999;">Brak przedmiotów</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- SEKCJA 2: NAUCZYCIELE --}}
        <div class="card" style="margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin:0;">Zarządzanie Nauczycielami ({{ count($teachers) }})</h2>
                <input type="text" id="teacherSearch" onkeyup="filterTable('teacherSearch', 'teachersTable', 0)" placeholder="Szukaj nauczyciela..." style="width: 250px; margin-bottom: 0; padding: 8px;">
            </div>
            
            <div style="max-height: 400px; overflow-y: auto; border: 1px solid #edf2f7; border-radius: 8px;">
                <table class="table-custom" id="teachersTable" style="margin-bottom: 0;">
                    <thead style="position: sticky; top: 0; background: #f8fafc; z-index: 10;">
                        <tr>
                            <th>Nauczyciel</th>
                            <th>Email</th>
                            <th style="text-align: center;">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachers as $teacher)
                        <tr>
                            <td><strong>{{ $teacher->name }}</strong></td>
                            <td>{{ $teacher->email }}</td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 5px; justify-content: center;">
                                    <a href="{{ route('admin.users.editTeacher', $teacher->id) }}" class="btn-primary" style="text-decoration:none; font-size:12px; background-color: #ecc94b; color: #744210; padding: 5px 10px; border-radius: 6px;">Edytuj</a>
                                    <form action="{{ route('admin.users.destroyTeacher', $teacher->id) }}" method="POST" onsubmit="return confirm('Usunąć?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="background-color: #e53e3e; color: white; border: none; padding: 5px 10px; border-radius: 6px; cursor: pointer;">Usuń</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SEKCJA 3: UCZNIOWIE I RODZICE --}}
        <div class="card" style="margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin:0;">Uczniowie i Rodzice ({{ count($students) }})</h2>
                <input type="text" id="studentSearch" onkeyup="filterTable('studentSearch', 'studentsTable', 0)" placeholder="Szukaj ucznia..." style="width: 250px; margin-bottom: 0; padding: 8px;">
            </div>

            <div style="max-height: 500px; overflow-y: auto; border: 1px solid #edf2f7; border-radius: 8px;">
                <table class="table-custom" id="studentsTable" style="margin-bottom: 0;">
                    <thead style="position: sticky; top: 0; background: #f8fafc; z-index: 10;">
                        <tr>
                            <th>Uczeń</th>
                            <th>Klasa</th>
                            <th>Rodzic</th>
                            <th style="text-align: center;">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td><strong>{{ $student->name }}</strong></td>
                            <td><span style="background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-size: 13px;">{{ $student->classGroup->name ?? 'Brak' }}</span></td>
                            <td>{{ $student->parent->name ?? 'Brak' }}</td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 5px; justify-content: center;">
                                    <a href="{{ route('admin.users.editStudent', $student->id) }}" class="btn-primary" style="text-decoration:none; font-size:12px; background-color: #ecc94b; color: #744210; padding: 5px 10px; border-radius: 6px;">Edytuj</a>
                                    <form action="{{ route('admin.users.destroyStudent', $student->id) }}" method="POST" onsubmit="return confirm('Usunąć?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="background-color: #e53e3e; color: white; border: none; padding: 5px 10px; border-radius: 6px; cursor: pointer;">Usuń</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SEKCJA FORMULARZY DODAWANIA --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div class="card">
                <h3>Nowy Nauczyciel</h3>
                <form action="{{ route('admin.users.storeTeacher') }}" method="POST">
                    @csrf
                    <input type="text" name="name" required placeholder="Imię i Nazwisko">
                    <input type="email" name="email" required placeholder="Email">
                    <button type="submit" style="width: 100%;">Stwórz konto nauczyciela</button>
                </form>
            </div>

            <div class="card" style="background-color: #f0f4f8;">
                <h3>Przypisz Nauczyciela</h3>
                <form action="{{ route('admin.assignments.store') }}" method="POST">
                    @csrf
                    <select name="teacher_id" required>
                        <option value="">Wybierz nauczyciela...</option>
                        @foreach($teachers as $teacher) <option value="{{ $teacher->id }}">{{ $teacher->name }}</option> @endforeach
                    </select>
                    <select name="subject_id" required>
                        <option value="">Wybierz przedmiot...</option>
                        @foreach($subjects as $subject) <option value="{{ $subject->id }}">{{ $subject->name }}</option> @endforeach
                    </select>
                    <select name="class_group_id" required>
                        <option value="">Wybierz klasę...</option>
                        @foreach($classes as $class) <option value="{{ $class->id }}">{{ $class->name }}</option> @endforeach
                    </select>
                    <button type="submit" style="width: 100%; background-color: #3490dc;">Zapisz przypisanie</button>
                </form>
            </div>
        </div>

        <div class="card">
            <h3>Dodaj nową parę Uczeń-Rodzic</h3>
            <form action="{{ route('admin.users.storeStudent') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <input type="text" name="student_name" placeholder="Imię ucznia" required>
                        <input type="email" name="student_email" placeholder="Email ucznia" required>
                        <select name="class_group_id" required>
                            <option value="">Klasa ucznia...</option>
                            @foreach($classes as $class) <option value="{{ $class->id }}">{{ $class->name }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="text" name="parent_name" placeholder="Imię rodzica" required>
                        <input type="email" name="parent_email" placeholder="Email rodzica" required>
                        <button type="submit" style="width: 100%; height: 45px; margin-top: 10px;">Dodaj parę</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- SEKCJA 4: ZMIANA HASŁA --}}
        <div class="card" style="margin-top: 30px; border-top: 4px solid #ecc94b;">
            <h3>🔐 Bezpieczeństwo konta Administratora</h3>
            <form action="{{ route('user.password.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; align-items: flex-end;">
                    <div><label>Obecne hasło</label><input type="password" name="current_password" required></div>
                    <div><label>Nowe hasło</label><input type="password" name="new_password" required></div>
                    <div><button type="submit" style="width: 100%; background-color: #2d3748; color: white; border: none; padding: 10px; border-radius: 6px;">Aktualizuj hasło</button></div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function filterTable(inputId, tableId, colIndex) {
    let input = document.getElementById(inputId);
    let filter = input.value.toUpperCase();
    let table = document.getElementById(tableId);
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName("td")[colIndex];
        if (td) {
            let txtValue = td.textContent || td.innerText;
            tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}
</script>
@endsection
