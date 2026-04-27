<x-app-layout>
    <x-slot name="header">Mening arizalarim</x-slot>

    <x-page-header
        title="Ish o‘rinlari arizalarim"
        subtitle="Siz tomondan attestatsiyaga taqdim etilgan ish o‘rinlarining holati va natijalari."
        :crumbs="[
            ['label' => 'Bosh sahifa', 'url' => route('dashboard')],
            ['label' => 'Mening arizalarim'],
        ]"
    >
        <x-slot name="actions">
            <x-att-button :href="route('employee.applications.create')" variant="primary">
                + Yangi ariza
            </x-att-button>
        </x-slot>
    </x-page-header>

    @if ($applications->isEmpty())
        <x-empty-state
            icon="📭"
            title="Hali arizalar yo‘q"
            description="Tashkilotingizdagi ish o‘rinlarini attestatsiyaga taqdim etish uchun yuqoridagi “Yangi ariza” tugmasini bosing."
        >
            <x-slot name="action">
                <x-att-button :href="route('employee.applications.create')" variant="primary">Birinchi arizani yuborish</x-att-button>
            </x-slot>
        </x-empty-state>
    @else
        {{-- Desktop --}}
        <div class="att-card desktop-table" style="padding:0;">
            <table class="att-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ish o‘rni va bo‘lim</th>
                        <th>Kampaniya</th>
                        <th>Holat</th>
                        <th>Ball / klass</th>
                        <th>Sana</th>
                        <th style="text-align:center;">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applications as $app)
                        <tr>
                            <td style="font-weight:600;color:var(--muted);">#{{ $app->id }}</td>
                            <td>
                                <div style="font-weight:600;color:var(--ink);">{{ $app->workplace_name ?? $app->position }}</div>
                                <div style="font-size:12px;color:var(--muted);">{{ $app->department ?? '—' }}</div>
                            </td>
                            <td style="color:#555;">{{ $app->campaign?->title ?? '—' }}</td>
                            <td>
                                <x-att-badge :status="$app->status" />
                            </td>
                            <td>
                                @if ($app->status === 'finalized')
                                    <div style="font-weight:700;color:var(--teal);">{{ $app->getWorkplaceClassLabel() }}</div>
                                    <div style="font-size:11px;color:var(--muted);">Ball: {{ $app->final_score ?? '—' }}</div>
                                @else
                                    <span style="color:var(--muted);font-style:italic;font-size:13px;">Kutilmoqda…</span>
                                @endif
                            </td>
                            <td style="font-size:13px;color:var(--muted);">{{ $app->created_at->format('d.m.Y') }}</td>
                            <td style="text-align:center;">
                                @if ($app->status === 'submitted')
                                    <form method="POST" action="{{ route('employee.applications.ai_process', $app) }}" style="margin:0;">
                                        @csrf
                                        <x-att-button type="submit" variant="success" size="sm">AI avto-tasdiqlash</x-att-button>
                                    </form>
                                @else
                                    <span style="color:var(--muted);font-size:12px;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="mobile-card-list">
            @foreach ($applications as $app)
                <div class="mobile-app-card">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:10px;">
                        <div>
                            <div style="font-weight:600;color:var(--ink);">{{ $app->workplace_name ?? $app->position }}</div>
                            <div style="font-size:12px;color:var(--muted);">{{ $app->department ?? '—' }}</div>
                        </div>
                        <x-att-badge :status="$app->status" />
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--muted);">
                        <span>{{ $app->campaign?->title }}</span>
                        <span>{{ $app->created_at->format('d.m.Y') }}</span>
                    </div>
                    @if ($app->status === 'finalized')
                        <div style="margin-top:8px;padding-top:8px;border-top:1px solid var(--cream);font-weight:700;color:var(--teal);font-size:13px;">
                            {{ $app->getWorkplaceClassLabel() }} · Ball: {{ $app->final_score ?? '—' }}
                        </div>
                    @endif
                    @if ($app->status === 'submitted')
                        <form method="POST" action="{{ route('employee.applications.ai_process', $app) }}" style="margin-top:12px;">
                            @csrf
                            <x-att-button type="submit" variant="success" size="sm" class="w-full">AI avto-tasdiqlash</x-att-button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>

        {{ $applications->links() }}
    @endif
</x-app-layout>
