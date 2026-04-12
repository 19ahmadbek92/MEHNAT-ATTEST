<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class SelectTypeController extends Controller
{
    public function __invoke(): View
    {
        return view('auth.select-type');
    }
}
