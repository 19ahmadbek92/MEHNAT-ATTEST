<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OneIDController extends Controller
{
    /**
     * OneID xizmatiga yo'naltirish (demo varianti).
     *
     * Real integratsiyada bu yerda OneID SSO URL'iga redirect qilasiz.
     */
    public function redirect()
    {
        // Demo: to'g'ridan-to'g'ri callback route'iga yuboramiz.
        return redirect()->route('auth.oneid.callback');
    }

    /**
     * OneID callback (demo).
     *
     * Real holatda bu metod OneID'dan kelgan ma'lumotlarni qabul qiladi,
     * PINFL bo'yicha foydalanuvchini topadi yoki yaratadi va tizimga kiritadi.
     */
    public function callback()
    {
        // Demo ma'lumot (real hayotda OneID javobidan olinadi)
        $oneidData = [
            'name' => 'OneID User',
            'pinfl' => '12345678901234',
        ];

        $user = User::updateOrCreate(
            ['pinfl' => $oneidData['pinfl']], // PINFL bo‘yicha qidiradi
            [
                'name' => $oneidData['name'],
                'email' => $oneidData['pinfl'] . '@oneid.uz',
                'password' => bcrypt('password'),
                'role' => 'employer',
                'person_type' => 'jismoniy',
                'is_verified' => true,
            ]
        );

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}