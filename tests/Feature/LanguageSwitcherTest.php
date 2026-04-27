<?php

namespace Tests\Feature;

use Tests\TestCase;

class LanguageSwitcherTest extends TestCase
{
    public function test_default_locale_is_uzbek_and_entry_page_renders(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSeeText('Davlat platformasi')
            ->assertSeeText('Kabinetga kiring')
            ->assertSeeText('Kirish turini tanlang');

        $this->assertSame('uz', app()->getLocale());
    }

    public function test_switching_to_russian_persists_in_session_and_translates_entry_page(): void
    {
        // Click "RU" — language route stores the locale in session and
        // bounces back to the previous page.
        $this->get('/lang/ru')->assertRedirect();
        $this->assertSame('ru', session('locale'));

        // Reload the entry page → it must now render in Russian.
        $this->get('/')
            ->assertOk()
            ->assertSeeText('Безопасный вход')
            ->assertSeeText('Войдите в кабинет')
            ->assertSeeText('Государственная платформа')
            ->assertDontSeeText('Davlat platformasi');
    }

    public function test_switching_to_english_translates_entry_page(): void
    {
        $this->get('/lang/en')->assertRedirect();
        $this->assertSame('en', session('locale'));

        $this->get('/')
            ->assertOk()
            ->assertSeeText('Secure sign-in')
            ->assertSeeText('Sign in to your panel')
            ->assertSeeText('Government platform');
    }

    public function test_unknown_locale_is_ignored(): void
    {
        // Bogus locale codes must not poison the session — the active
        // locale stays on the previous (or default) value.
        $this->get('/lang/fr')->assertRedirect();

        $this->assertNotSame('fr', session('locale'));
    }

    public function test_locale_persists_to_role_login_screens(): void
    {
        $this->get('/lang/ru');
        $this->get('/login/admin')
            ->assertOk()
            ->assertSeeText('Администратор');

        $this->get('/lang/uz');
        $this->get('/login/admin')
            ->assertOk()
            ->assertSeeText('Administrator');
    }
}
