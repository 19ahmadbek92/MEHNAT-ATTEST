<x-app-layout>
    <x-slot name="header">👤 Profil sozlamalari</x-slot>

    <div style="max-width: 680px;">
        {{-- Profil ma'lumotlari --}}
        <div class="att-card" style="margin-bottom: 24px;">
            <div class="att-card-header">
                <div class="att-card-title">Shaxsiy ma'lumotlar</div>
            </div>
            <div class="att-card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Parol o'zgartirish --}}
        <div class="att-card" style="margin-bottom: 24px;">
            <div class="att-card-header">
                <div class="att-card-title">🔒 Parolni o'zgartirish</div>
            </div>
            <div class="att-card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Hisobni o'chirish --}}
        <div class="att-card" style="border-left: 4px solid var(--red);">
            <div class="att-card-header">
                <div class="att-card-title" style="color: var(--red);">⚠️ Hisobni o'chirish</div>
            </div>
            <div class="att-card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
