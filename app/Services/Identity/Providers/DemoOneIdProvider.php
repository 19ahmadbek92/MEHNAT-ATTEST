<?php

namespace App\Services\Identity\Providers;

use App\Models\User;
use App\Services\Identity\Contracts\OneIdProviderContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DemoOneIdProvider implements OneIdProviderContract
{
    public function redirect(Request $request): RedirectResponse
    {
        return redirect()->route('auth.oneid.callback');
    }

    public function resolveUser(Request $request): User
    {
        $oneidData = [
            'name' => (string) config('demo.oneid.name', 'OneID User'),
            'pinfl' => (string) config('demo.oneid.pinfl', '12345678901234'),
        ];

        $user = User::firstOrCreate(
            ['pinfl' => $oneidData['pinfl']],
            [
                'name' => $oneidData['name'],
                'email' => $oneidData['pinfl'].'@oneid.uz',
                'password' => bcrypt(Str::random(40)),
                'role' => 'employer',
                'person_type' => 'jismoniy',
                'is_verified' => true,
            ]
        );

        if ($user->name !== $oneidData['name']) {
            $user->forceFill(['name' => $oneidData['name']])->save();
        }

        return $user;
    }
}
