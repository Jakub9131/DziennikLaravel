<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\ClassGroup;
use App\Models\Subject;
use App\Models\User;
use App\Models\Grade;

class DashboardController extends Controller
{
   
    public function index() {
        $role = Auth::user()->role;
        return match($role) {
            'admin' => redirect()->route('panel.admin'),
            'teacher' => redirect()->route('panel.nauczyciel'),
            'parent' => redirect()->route('panel.rodzic'),
            default => redirect()->route('panel.uczen'),
        };
    }

    /**
     * PANEL ADMINISTRATORA
     */
    public function admin() {
        return view('admin.dashboard', [
            'classes' => ClassGroup::all(),
            'subjects' => Subject::all(),
            'teachers' => User::where('role', 'teacher')->get(),
            'students' => User::where('role', 'student')->with(['parent', 'classGroup'])->get()
        ]);
    }

    // --- ZARZĄDZANIE KLASAMI ---
    public function storeClass(Request $request) {
        ClassGroup::create($request->validate(['name' => 'required|unique:class_groups']));
        return back()->with('success', 'Klasa dodana!');
    }

   
    public function destroyClass(ClassGroup $class) {
        $hasStudents = User::where('class_group_id', $class->id)->exists();

        if ($hasStudents) {
            return back()->with('error', 'Nie można usunąć klasy, do której przypisani są uczniowie. Najpierw przenieś uczniów.');
        }

        $class->delete();
        return back()->with('success', 'Klasa usunięta!');
    }

    // --- ZARZĄDZANIE PRZEDMIOTAMI ---
    public function storeSubject(Request $request) {
        Subject::create($request->validate(['name' => 'required|unique:subjects']));
        return back()->with('success', 'Przedmiot dodany!');
    }

    
    public function destroySubject(Subject $subject) {
        $hasGrades = Grade::where('subject_id', $subject->id)->exists();

        if ($hasGrades) {
            return back()->with('error', 'Nie można usunąć przedmiotu, z którego wystawiono już oceny.');
        }

        $subject->delete();
        return back()->with('success', 'Przedmiot usunięty!');
    }

    // --- ZARZĄDZANIE NAUCZYCIELAMI ---
    public function storeTeacher(Request $request) {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('nauczyciel123'),
            'role' => 'teacher',
        ]);
        return back()->with('success', 'Konto nauczyciela utworzone!');
    }

    public function editTeacher($id) {
        $teacher = User::findOrFail($id);
        return view('admin.edit-teacher', compact('teacher'));
    }

    public function updateTeacher(Request $request, $id) {
        $teacher = User::findOrFail($id);
        $request->validate(['email' => 'required|email|unique:users,email,' . $teacher->id]);
        $teacher->update(['name' => $request->name, 'email' => $request->email]);
        return redirect()->route('panel.admin')->with('success', 'Dane nauczyciela zaktualizowane!');
    }

    public function destroyTeacher($id) {
        $teacher = User::findOrFail($id);
        DB::table('teacher_assignments')->where('teacher_id', $id)->delete();
        $teacher->delete();
        return back()->with('success', 'Nauczyciel i jego przypisania usunięte!');
    }

    // --- ZARZĄDZANIE UCZNIAMI I RODZICAMI ---
    public function storeStudent(Request $request) {
        $parent = User::where('email', $request->parent_email)->first();

        if (!$parent) {
            $parent = User::create([
                'name' => $request->parent_name,
                'email' => $request->parent_email,
                'password' => Hash::make('start123'),
                'role' => 'parent',
            ]);
        }

        User::create([
            'name' => $request->student_name,
            'email' => $request->student_email,
            'password' => Hash::make('start123'),
            'role' => 'student',
            'class_group_id' => $request->class_group_id,
            'parent_id' => $parent->id,
        ]);

        return redirect()->back()->with('success', 'Uczeń dodany i powiązany z rodzicem.');
    }

    public function editStudent($id) {
        $student = User::with('parent')->findOrFail($id);
        $classes = ClassGroup::all();
        return view('admin.edit-student', compact('student', 'classes'));
    }

    public function updateStudent(Request $request, $id) {
        $student = User::findOrFail($id);
        
        $request->validate([
            'student_email' => 'required|email|unique:users,email,' . $student->id,
        ]);

        $student->update([
            'name' => $request->student_name,
            'email' => $request->student_email,
            'class_group_id' => $request->class_group_id
        ]);

        if ($student->parent_id) {
            $parent = User::findOrFail($student->parent_id);
            $parent->update([
                'name' => $request->parent_name,
                'email' => $request->parent_email,
            ]);
        }

        return redirect()->route('panel.admin')->with('success', 'Dane uaktualnione pomyślnie!');
    }

    public function destroyStudent($id) {
        $student = User::findOrFail($id);
        $student->delete();
        return back()->with('success', 'Uczeń usunięty!');
    }

    public function storeAssignment(Request $request) {
        DB::table('teacher_assignments')->insert([
            'teacher_id' => $request->teacher_id,
            'subject_id' => $request->subject_id,
            'class_group_id' => $request->class_group_id,
            'created_at' => now(), 
            'updated_at' => now()
        ]);
        return back()->with('success', 'Zajęcia przypisane!');
    }

    /**
     * PANEL NAUCZYCIELA
     */
    public function nauczyciel(Request $request) 
    {
        $teacherId = Auth::id();
        $assignments = DB::table('teacher_assignments')
            ->join('subjects', 'teacher_assignments.subject_id', '=', 'subjects.id')
            ->join('class_groups', 'teacher_assignments.class_group_id', '=', 'class_groups.id')
            ->where('teacher_id', $teacherId)
            ->select('subjects.name as subject_name', 'class_groups.name as class_name', 'subjects.id as subject_id', 'class_groups.id as class_id')
            ->get();

        $selectedStudents = null;
        $currentSubject = null;
        $currentClass = null;

        if ($request->has('class_id') && $request->has('subject_id')) {
            $currentClass = ClassGroup::find($request->class_id);
            $currentSubject = Subject::find($request->subject_id);
            $selectedStudents = User::where('class_group_id', $request->class_id)
                ->where('role', 'student')
                ->with(['grades' => function($query) use ($request) {
                    $query->where('subject_id', $request->subject_id);
                }])->get();
        }
        return view('nauczyciel.dashboard', compact('assignments', 'selectedStudents', 'currentClass', 'currentSubject'));
    }

    // --- ZARZĄDZANIE OCENAMI ---
    public function storeGrade(Request $request) {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'value' => 'required|integer|min:1|max:6',
            'comment' => 'nullable|string|max:100'
        ]);

        Grade::create([
            'student_id' => $request->student_id,
            'teacher_id' => Auth::id(),
            'subject_id' => $request->subject_id,
            'value' => $request->value,
            'comment' => $request->comment,
        ]);
        return back()->with('success', 'Ocena została wystawiona!');
    }

    public function editGrade($id) {
        $grade = Grade::with(['student', 'subject'])->findOrFail($id);
        if ($grade->teacher_id !== Auth::id()) {
            return abort(403, 'Brak uprawnień do edycji tej oceny.');
        }
        return view('nauczyciel.edit-grade', compact('grade'));
    }

    public function updateGrade(Request $request, $id) {
        $grade = Grade::findOrFail($id);
        if ($grade->teacher_id !== Auth::id()) return abort(403);

        $request->validate([
            'value' => 'required|integer|min:1|max:6',
            'comment' => 'nullable|string|max:100'
        ]);

        $grade->update(['value' => $request->value, 'comment' => $request->comment]);
        return redirect()->route('panel.nauczyciel', ['class_id' => $grade->student->class_group_id, 'subject_id' => $grade->subject_id])
                         ->with('success', 'Ocena zaktualizowana!');
    }

    public function destroyGrade($id) {
        $grade = Grade::findOrFail($id);
        if ($grade->teacher_id !== Auth::id()) return abort(403);
        $grade->delete();
        return back()->with('success', 'Ocena została usunięta!');
    }

    /**
     * PANEL RODZICA
     */
    public function rodzic() {
        $child = User::where('parent_id', Auth::id())
            ->where('role', 'student')
            ->with(['classGroup', 'grades.subject', 'grades.teacher'])
            ->first();

        if ($child) {
            $child->subjects_with_grades = $child->grades->groupBy('subject_id');
            $child->average = $child->grades->count() > 0 ? round($child->grades->avg('value'), 2) : 0;
            $child->teachers_contacts = $child->grades->map(fn($g) => $g->teacher)->filter()->unique('id');
        }

        return view('rodzic.dashboard', compact('child'));
    }

    /**
     * PANEL UCZNIA
     */
    public function uczen() {
        $student = User::where('id', Auth::id())
            ->with(['classGroup', 'grades.subject', 'grades.teacher'])
            ->first();

        $subjects_with_grades = $student->grades->groupBy('subject_id');
        $average = $student->grades->count() > 0 ? round($student->grades->avg('value'), 2) : 0;
        $teachers_contacts = $student->grades->map(fn($g) => $g->teacher)->filter()->unique('id');

        return view('uczen.dashboard', compact('student', 'subjects_with_grades', 'average', 'teachers_contacts'));
    }
}
