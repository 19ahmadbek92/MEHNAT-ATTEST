<?php

namespace App\Services\Identity\Contracts;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

interface OneIdProviderContract
{
    public function redirect(Request $request): RedirectResponse;

    public function resolveUser(Request $request): User;
}
