<x-guest-layout>
    <div class="space-y-4">
        <div class="text-center">
            <h1 class="text-lg font-semibold text-gray-900">Ro'yhatdan o'tish</h1>
            <p class="text-sm text-gray-600 mt-1">
                Ro'yhatdan o'tish faqat OneID yoki ERI (E-IMZO) orqali amalga oshiriladi.
            </p>
        </div>

        @php($demoSso = app()->isLocal() || config('demo.sso'))

        @if($demoSso)
            <div class="space-y-3">
                <a href="{{ route('auth.oneid.redirect') }}"
                   class="w-full block text-center bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    OneID (demo) orqali ro'yhatdan o'tish
                </a>

                <a href="{{ route('auth.eri.login') }}"
                   class="w-full block text-center bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                    ERI (demo) orqali ro'yhatdan o'tish
                </a>
            </div>
        @else
            <p class="text-sm text-gray-600 text-center">
                OneID / ERI demo rejimi productionda o‘chirilgan. Ro‘yxatdan o‘tish uchun tizim administratoriga murojaat qiling yoki
                <a class="text-blue-600 underline" href="{{ route('login.email') }}">email orqali kirish</a>.
            </p>
        @endif

        <div class="text-center">
            <a class="underline text-xs text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                Akkountingiz bormi? Tizimga kirish
            </a>
        </div>
    </div>
</x-guest-layout>

