<?php

namespace App\Services\Identity\Providers;

use App\Models\User;
use App\Services\Identity\Contracts\EriProviderContract;
use Illuminate\Http\Request;
use RuntimeException;

class RealEriProvider implements EriProviderContract
{
    public function resolveUser(Request $request): User
    {
        throw new RuntimeException('Real E-IMZO verification flow is not implemented yet.');
    }
}
