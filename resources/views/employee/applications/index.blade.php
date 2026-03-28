<x-app-layout>
    <x-slot name="header">🏭 Ish o'rinlari arizalarim</x-slot>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <p style="color: var(--muted); font-size: 14px; margin: 0;">Siz tomoningdan attestatsiyaga taqdim etilgan ish o'rinlari ro'yxati.</p>
        <a href="{{ route('employee.applications.create') }}" class="btn-att btn-att-primary">+ Yangi ariza berish</a>
    </div>

    {{-- Desktop Table --}}
    <div class="att-card desktop-table">
        <table class="att-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ish o'rni va bo'lim</th>
                    <th>Kampaniya</th>
                    <th>Status</th>
                    <th>Ball / Klass</th>
                    <th>Sana</th>
                    <th>Harakatlar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td style="font-weight: 600; color: var(--muted);">#{{ $app->id }}</td>
                        <td>
                            <div style="font-weight: 600; color: var(--ink);">{{ $app->workplace_name ?? $app->position }}</div>
                            <div style="font-size: 12px; color: var(--muted);">{{ $app->department ?? '—' }}</div>
                        </td>
                        <td style="color: #555;">{{ $app->campaign?->title }}</td>
                        <td>
                            <span class="status-badge
                                @switch($app->status)
                                    @case('submitted') sb-submitted @break
                                    @case('hr_approved') sb-approved @break
                                    @case('hr_rejected') sb-rejected @break
                                    @case('finalized') sb-finalized @break
                                @endswitch">
                                @switch($app->status)
                                    @case('submitted') 🆕 Yuborilgan @break
                                    @case('hr_approved') ⏳ Tekshiruvda @break
                                    @case('hr_rejected') ❌ Rad etilgan @break
                                    @case('finalized') ✅ Yakunlangan @break
                                    @default {{ $app->status }}
                                @endswitch
                            </span>
                        </td>
                        <td>
                            @if($app->status === 'finalized')
                                <div style="font-weight: 700; color: var(--teal);">{{ $app->getWorkplaceClassLabel() }}</div>
                                <div style="font-size: 11px; color: var(--muted);">Ball: {{ $app->final_score ?? '—' }}</div>
                            @else
                                <span style="color: var(--muted); font-style: italic; font-size: 13px;">Kutilmoqda...</span>
                            @endif
                        </td>
                        <td style="font-size: 13px; color: var(--muted);">{{ $app->created_at->format('d.m.Y') }}</td>
                        <td style="display: flex; gap: 8px; flex-direction: column;">
                            @if($app->status === 'submitted')
                                <form method="POST" action="{{ route('employee.applications.ai_process', $app) }}">
                                    @csrf
                                    <button type="submit" class="btn-att" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; width: 100%; border: none; font-size: 11px; padding: 6px 14px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.4);">
                                        ✨ AI Avto-tasdiqlash
                                    </button>
                                </form>
                            @endif
                            <!-- <a href="#" class="btn-att btn-att-secondary" style="font-size: 11px; padding: 6px 14px; text-align: center;">Ko'rish</a> -->
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 48px 20px; color: var(--muted); font-style: italic;">
                            <div style="font-size: 36px; margin-bottom: 12px;">📭</div>
                            Sizda hali arizalar yo'q. Arizani yuborish uchun yuqoridagi tugmani bosing.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="mobile-card-list">
        @forelse($applications as $app)
            <div class="mobile-app-card {{ $app->status === 'finalized' ? 'border-green' : ($app->status === 'hr_rejected' ? 'border-red' : ($app->status === 'hr_approved' ? 'border-gold' : '')) }}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <div>
                        <div style="font-weight: 600; font-size: 15px; color: var(--ink);">{{ $app->workplace_name ?? $app->position }}</div>
                        <div style="font-size: 12px; color: var(--muted);">{{ $app->department ?? '—' }}</div>
                    </div>
                    <span class="status-badge
                        @switch($app->status)
                            @case('submitted') sb-submitted @break
                            @case('hr_approved') sb-approved @break
                            @case('hr_rejected') sb-rejected @break
                            @case('finalized') sb-finalized @break
                        @endswitch">
                        @switch($app->status)
                            @case('submitted') 🆕 Yangi @break
                            @case('hr_approved') ⏳ Tekshiruvda @break
                            @case('hr_rejected') ❌ Rad @break
                            @case('finalized') ✅ Tayyor @break
                        @endswitch
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--muted);">
                    <span>{{ $app->campaign?->title }}</span>
                    <span>{{ $app->created_at->format('d.m.Y') }}</span>
                </div>
                @if($app->status === 'finalized')
                    <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid var(--cream); font-weight: 700; color: var(--teal); font-size: 13px;">
                        {{ $app->getWorkplaceClassLabel() }} · Ball: {{ $app->final_score ?? '—' }}
                    </div>
                @endif
                @if($app->status === 'submitted')
                    <div style="margin-top: 12px;">
                        <form method="POST" action="{{ route('employee.applications.ai_process', $app) }}">
                            @csrf
                            <button type="submit" class="btn-att" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; width: 100%; border: none; font-size: 12px; padding: 8px; text-transform: uppercase;">
                                ✨ AI Avto-tasdiqlash
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div style="text-align: center; padding: 48px 20px; color: var(--muted);">
                <div style="font-size: 36px; margin-bottom: 12px;">📭</div>
                <p>Sizda hali arizalar yo'q.</p>
            </div>
        @endforelse
    </div>

    @if($applications->hasPages())
        <div style="margin-top: 16px;">
            {{ $applications->links() }}
        </div>
    @endif
</x-app-layout>
