<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EriController extends Controller
{
    public function login()
    {
        // Demo ERI login (real hayotda E-IMZO orqali imzolash va ma'lumotlarni olish kerak bo'ladi)
        $user = User::updateOrCreate(
            ['tin' => '123456789'],
            [
                'name' => 'ERI Company',
                'email' => 'company@eri.uz',
                'password' => bcrypt('password'),
                'role' => 'employer',
                'person_type' => 'yuridik',
                'is_verified' => true,
            ]
        );

        Auth::login($user);

        return redirect('/dashboard');
    }
}
