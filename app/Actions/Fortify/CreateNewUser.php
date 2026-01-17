<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => [
                'required', 
                'string', 
                'max:255', 
                'regex:/^[a-zA-ZàáâäãåąčďëèéêěǵḧîïíóôöőøñřśťúûüűýźžŁłŚśŹźŻżĄąĘęÓóŃńĆć\s]+$/u'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
            ],
            'password' => $this->passwordRules(),
            'role' => ['sometimes', 'string', Rule::in(['admin', 'teacher', 'student', 'parent'])],
            'class_group_id' => ['sometimes', 'nullable', 'exists:class_groups,id'],
        ], [
            'name.regex' => 'Imię i nazwisko nie może zawierać cyfr ani znaków specjalnych.',
            'email.unique' => 'Ten adres e-mail jest już zajęty.',
            'password.min' => 'Hasło musi mieć co najmniej 8 znaków.',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => $input['role'] ?? 'student',
            'class_group_id' => $input['class_group_id'] ?? null,
        ]);
    }
}
