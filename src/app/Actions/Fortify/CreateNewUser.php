<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Http\Requests\RegisterRequest;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        $request = app(RegisterRequest::class);
        $request->merge($input);
        $request->validate($request->rules());

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
