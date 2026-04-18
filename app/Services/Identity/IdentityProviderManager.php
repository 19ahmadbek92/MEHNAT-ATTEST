<?php

namespace App\Services\Identity;

use App\Services\Identity\Contracts\EriProviderContract;
use App\Services\Identity\Contracts\OneIdProviderContract;
use App\Services\Identity\Providers\DemoEriProvider;
use App\Services\Identity\Providers\DemoOneIdProvider;
use App\Services\Identity\Providers\RealEriProvider;
use App\Services\Identity\Providers\RealOneIdProvider;

class IdentityProviderManager
{
    public function oneId(): OneIdProviderContract
    {
        if ((bool) config('demo.sso', false) || app()->isLocal()) {
            return new DemoOneIdProvider;
        }

        return new RealOneIdProvider;
    }

    public function eri(): EriProviderContract
    {
        if ((bool) config('demo.sso', false) || app()->isLocal()) {
            return new DemoEriProvider;
        }

        return new RealEriProvider;
    }
}
