<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'value' => 'required|integer|between:1,6',
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'comment' => 'nullable|string|max:100',
        ]);

        $validated['teacher_id'] = Auth::id();

        Grade::create($validated);
        return redirect()->back()->with('success', 'Ocena została wystawiona!');
    }

    public function edit($id)
    {
        $grade = Grade::with(['student', 'subject'])->findOrFail($id);
        return view('nauczyciel.edit-grade', compact('grade'));
    }

    public function update(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);

        $validated = $request->validate([
            'value' => 'required|integer|between:1,6',
            'comment' => 'nullable|string|max:100',
        ]);

        $grade->update($validated);

        return redirect()->route('panel.nauczyciel', [
            'class_id' => $grade->student->class_group_id,
            'subject_id' => $grade->subject_id
        ])->with('success', 'Ocena została pomyślnie zaktualizowana.');
    }

    public function destroy($id)
    {
        $grade = Grade::findOrFail($id);
        
        $classId = $grade->student->class_group_id;
        $subjectId = $grade->subject_id;

        $grade->delete();

        return redirect()->route('panel.nauczyciel', [
            'class_id' => $classId,
            'subject_id' => $subjectId
        ])->with('success', 'Ocena została trwale usunięta.');
    }
}
