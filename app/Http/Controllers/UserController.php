<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $nameRules = [
        'required',
        'string',
        'max:255',
        'regex:/^[a-zA-ZàáâäãåąčďëèéêěǵḧîïíóôöőøñřśťúûüűýźžŁłŚśŹźŻżĄąĘęÓóŃńĆć\s]+$/u'
    ];

    protected $customMessages = [
        'name.regex' => 'Imię i nazwisko może zawierać tylko litery i spacje.',
        'student_name.regex' => 'Imię i nazwisko ucznia może zawierać tylko litery i spacje.',
        'parent_name.regex' => 'Imię i nazwisko rodzica może zawierać tylko litery i spacje.',
    ];

    
    public function storeTeacher(Request $request)
    {
        $validated = $request->validate([
            'name' => $this->nameRules,
            'email' => 'required|email|unique:users,email',
        ], $this->customMessages);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('nauczyciel123'),
            'role' => 'teacher',
        ]);

        return redirect()->back()->with('success', 'Nauczyciel dodany! Hasło startowe: nauczyciel123');
    }

    public function editTeacher($id)
    {
        $teacher = User::findOrFail($id);
        return view('admin.edit-teacher', compact('teacher'));
    }

    public function updateTeacher(Request $request, $id)
    {
        $teacher = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => $this->nameRules,
            'email' => 'required|email|unique:users,email,' . $id,
        ], $this->customMessages);

        $teacher->update($validated);
        return redirect()->route('panel.admin')->with('success', 'Dane nauczyciela zaktualizowane.');
    }

    public function destroyTeacher($id)
    {
        $teacher = User::findOrFail($id);

        DB::transaction(function () use ($id, $teacher) {
            Grade::where('teacher_id', $id)->delete();

            DB::table('teacher_assignments')->where('teacher_id', $id)->delete();

            $teacher->delete();
        });

        return redirect()->back()->with('success', 'Konto nauczyciela oraz wszystkie jego powiązania (oceny, klasy) zostały usunięte.');
    }


    public function storeStudent(Request $request)
    {
        $request->validate([
            'student_name' => $this->nameRules,
            'student_email' => 'required|email|unique:users,email',
            'parent_name' => $this->nameRules,
            'parent_email' => 'required|email',
            'class_group_id' => 'required|exists:class_groups,id'
        ], $this->customMessages);

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

        return redirect()->back()->with('success', 'Uczeń dodany! Hasło startowe: start123');
    }

    public function editStudent($id)
    {
        $student = User::with('parent')->findOrFail($id);
        $classes = \App\Models\ClassGroup::all();
        return view('admin.edit-student', compact('student', 'classes'));
    }

    public function updateStudent(Request $request, $id)
    {
        $student = User::findOrFail($id);
        
        $request->validate([
            'student_name' => $this->nameRules,
            'student_email' => 'required|email|unique:users,email,' . $student->id,
            'parent_name' => $this->nameRules,
            'parent_email' => 'required|email',
            'class_group_id' => 'required|exists:class_groups,id',
        ], $this->customMessages);

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

        return redirect()->route('panel.admin')->with('success', 'Dane ucznia i rodzica zaktualizowane.');
    }

    public function destroyStudent($id)
    {
        $student = User::findOrFail($id);

        DB::transaction(function () use ($id, $student) {
            Grade::where('student_id', $id)->delete();
            $student->delete();
        });

        return redirect()->back()->with('success', 'Uczeń oraz jego oceny zostały usunięte.');
    }
}
