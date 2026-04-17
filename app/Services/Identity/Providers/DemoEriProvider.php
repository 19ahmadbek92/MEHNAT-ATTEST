<?php

namespace App\Services\Identity\Providers;

use App\Models\User;
use App\Services\Identity\Contracts\EriProviderContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DemoEriProvider implements EriProviderContract
{
    public function resolveUser(Request $request): User
    {
        $eriData = [
            'tin' => (string) config('demo.eri.tin', '123456789'),
            'name' => (string) config('demo.eri.name', 'ERI Company'),
            'email' => (string) config('demo.eri.email', 'company@eri.uz'),
        ];

        $user = User::firstOrCreate(
            ['tin' => $eriData['tin']],
            [
                'name' => $eriData['name'],
                'email' => $eriData['email'],
                'password' => bcrypt(Str::random(40)),
                'role' => 'employer',
                'person_type' => 'yuridik',
                'is_verified' => true,
            ]
        );

        $user->forceFill([
            'name' => $eriData['name'],
            'email' => $eriData['email'],
        ])->save();

        return $user;
    }
}
