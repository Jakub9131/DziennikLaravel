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
    protected $nameRegex = 'regex:/^[a-zA-ZàáâäãåąčďëèéêěǵḧîïíóôöőøñřśťúûüűýźžŁłŚśŹźŻżĄąĘęÓóŃńĆć\s]+$/u';

    public function index() {
        $role = Auth::user()->role;
        return match($role) {
            'admin' => redirect()->route('panel.admin'),
            'teacher' => redirect()->route('panel.nauczyciel'),
            'parent' => redirect()->route('panel.rodzic'),
            default => redirect()->route('panel.uczen'),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARDY
    |--------------------------------------------------------------------------
    */

    public function admin() {
        return view('admin.dashboard', [
            'classes' => ClassGroup::all(),
            'subjects' => Subject::all(),
            'teachers' => User::where('role', 'teacher')->get(),
            'students' => User::where('role', 'student')->with(['parent', 'classGroup'])->get()
        ]);
    }

    public function nauczyciel(Request $request) {
        $teacherId = Auth::id();
        $assignments = DB::table('teacher_assignments')
            ->where('teacher_id', $teacherId)
            ->join('class_groups', 'teacher_assignments.class_group_id', '=', 'class_groups.id')
            ->join('subjects', 'teacher_assignments.subject_id', '=', 'subjects.id')
            ->select(
                'teacher_assignments.*', 
                'class_groups.name as class_name', 
                'subjects.name as subject_name',
                'class_groups.id as class_id',
                'subjects.id as subject_id'
            )
            ->get();

        $selectedStudents = null;
        $currentClass = null;
        $currentSubject = null;

        if ($request->has('class_id') && $request->has('subject_id')) {
            $currentClass = ClassGroup::find($request->class_id);
            $currentSubject = Subject::find($request->subject_id);

            if ($currentClass && $currentSubject) {
                $selectedStudents = User::where('role', 'student')
                    ->where('class_group_id', $request->class_id)
                    ->with(['grades' => function($query) use ($request) {
                        $query->where('subject_id', $request->subject_id);
                    }])
                    ->get();
            }
        }
        return view('nauczyciel.dashboard', compact('assignments', 'selectedStudents', 'currentClass', 'currentSubject'));
    }

    public function uczen() {
        $user = Auth::user();
        $student = User::where('id', $user->id)
            ->with(['classGroup', 'grades.subject', 'grades.teacher'])
            ->first();

        
        $subjects_with_grades = $student->grades->groupBy('subject_id');
        $average = $student->grades->count() > 0 ? round($student->grades->avg('value'), 2) : 0;
        
        
        $student->subjects_with_grades = $subjects_with_grades;
        $student->average = $average;

        $teachers_contacts = User::where('role', 'teacher')
            ->whereIn('id', function($query) use ($student) {
                $query->select('teacher_id')
                    ->from('teacher_assignments')
                    ->where('class_group_id', $student->class_group_id);
            })
            ->get();

        return view('uczen.dashboard', compact('student', 'teachers_contacts', 'subjects_with_grades', 'average'));
    }

public function rodzic() {
    $parent = Auth::user();
    
    
    $child = User::where('parent_id', $parent->id)
        ->with(['classGroup', 'grades.subject', 'grades.teacher'])
        ->first();

    if (!$child) {
        return view('rodzic.dashboard', [
            'child' => null, 
            'teachers_contacts' => collect()
        ]);
    }

    
    $child->subjects_with_grades = $child->grades->groupBy('subject_id');
    
    $child->average = $child->grades->count() > 0 
        ? round($child->grades->avg('value'), 2) 
        : 0;

    
    $child->teachers_contacts = User::where('role', 'teacher')
        ->whereIn('id', function($query) use ($child) {
            $query->select('teacher_id')
                ->from('teacher_assignments')
                ->where('class_group_id', $child->class_group_id);
        })
        ->get();

    
    $teachers_contacts = $child->teachers_contacts;
    $subjects_with_grades = $child->subjects_with_grades;
    $average = $child->average;

    return view('rodzic.dashboard', compact('child', 'teachers_contacts', 'subjects_with_grades', 'average'));
}

    /*
    |--------------------------------------------------------------------------
    | LOGIKA ADMINISTRACYJNA
    |--------------------------------------------------------------------------
    */

    public function storeClass(Request $request) {
        $request->validate(['name' => ['required', 'unique:class_groups', 'max:2', 'regex:/^[1-9][A-Z]$/']]);
        ClassGroup::create(['name' => $request->name]);
        return back()->with('success', 'Klasa dodana!');
    }

    public function destroyClass(ClassGroup $class) {
        if (User::where('class_group_id', $class->id)->exists()) return back()->with('error', 'Klasa ma uczniów.');
        $class->delete();
        return back()->with('success', 'Usunięto.');
    }

    public function storeSubject(Request $request) {
        $request->validate(['name' => ['required', 'unique:subjects', 'min:3', 'string', $this->nameRegex]]);
        Subject::create(['name' => $request->name]);
        return back()->with('success', 'Dodano przedmiot.');
    }

    public function destroySubject(Subject $subject) {
        if (Grade::where('subject_id', $subject->id)->exists()) return back()->with('error', 'Są oceny.');
        $subject->delete();
        return back()->with('success', 'Usunięto.');
    }

    public function storeAssignment(Request $request) {
        $exists = DB::table('teacher_assignments')
            ->where(['teacher_id' => $request->teacher_id, 'subject_id' => $request->subject_id, 'class_group_id' => $request->class_group_id])
            ->exists();
        if ($exists) return back()->with('error', 'Już istnieje!');
        DB::table('teacher_assignments')->insert([
            'teacher_id' => $request->teacher_id, 'subject_id' => $request->subject_id, 'class_group_id' => $request->class_group_id,
            'created_at' => now(), 'updated_at' => now()
        ]);
        return back()->with('success', 'Przypisano!');
    }
}
