<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the active locale.
     *
     * The new locale is stored in the session (consumed by SetLocale
     * middleware) and we always redirect back to the page the user came
     * from. If the request has no Referer (e.g. when the link was opened
     * directly), we fall back to the platform entry page so the user is
     * never stranded on a blank URL.
     */
    public function switchLang(string $lang): RedirectResponse
    {
        if (in_array($lang, ['uz', 'ru', 'en'], true)) {
            Session::put('locale', $lang);
            App::setLocale($lang);
        }

        return redirect()->to(url()->previous() ?: route('home'));
    }
}
