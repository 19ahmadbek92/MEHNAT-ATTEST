<?php

namespace App\Services\Identity\Contracts;

use App\Models\User;
use Illuminate\Http\Request;

interface EriProviderContract
{
    public function resolveUser(Request $request): User;
}
