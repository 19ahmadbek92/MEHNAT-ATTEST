<x-app-layout>
    <x-slot name="header">📑 Davlat Ekspertizasiga Yuborish</x-slot>

    @if(session('success'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: var(--green-light); color: var(--green); border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; border: 1px solid rgba(26,107,60,0.2);">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="margin-bottom: 20px; padding: 14px 18px; background: var(--red-light); color: var(--red); border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; border: 1px solid rgba(192,57,43,0.2);">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <p style="color: var(--muted); font-size: 14px; margin: 0 0 24px; font-style: italic;">
        Laboratoriya tomonidan o'lchov natijalari kiritilgan arizalarni Davlat ekspertizasiga yuboring.
    </p>

    <div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 20px;" class="detail-grid-responsive">

        {{-- Yuborish formasi --}}
        <div class="att-card">
            <div class="att-card-header">
                <span class="att-card-title">✅ Tayyor protokollar</span>
            </div>
            <div class="att-card-body">
                @if($applications->isEmpty())
                    <div style="text-align: center; padding: 36px; color: var(--muted);">
                        <div style="font-size: 36px; margin-bottom: 12px;">⏳</div>
                        <div style="font-weight: 600; margin-bottom: 4px;">Tayyor protokollar yo'q</div>
                        <div style="font-size: 13px;">Laboratoriya o'lchovlarni yakunlagandan so'ng, arizalar bu yerda paydo bo'ladi.</div>
                    </div>
                @else
                    <form action="{{ route('employer.expertise.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="laboratory_id" value="{{ $applications->first()->protocol->laboratory_id ?? '' }}">
                        <table class="att-table">
                            <thead>
                                <tr>
                                    <th>✓</th>
                                    <th>Ish o'rni</th>
                                    <th style="text-align: center;">Sinf</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $app)
                                    <tr>
                                        <td><input type="checkbox" name="application_ids[]" value="{{ $app->id }}" checked style="width: 16px; height: 16px; cursor: pointer;"></td>
                                        <td style="font-weight: 600; color: var(--ink);">{{ $app->workplace_name }}</td>
                                        <td style="text-align: center;">
                                            <span class="status-badge" style="background: var(--teal-light); color: var(--teal);">Klass {{ $app->protocol->overall_class }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="margin-top: 20px; text-align: right;">
                            <button type="submit" class="btn-att btn-att-primary">
                                📤 Institut ekspertizasiga yuborish
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- Holat kuzatuv --}}
        <div class="att-card">
            <div class="att-card-header">
                <span class="att-card-title">🔄 Jarayon holati</span>
            </div>
            <div class="att-card-body">
                @if($expertiseApplications->isEmpty())
                    <div style="text-align: center; padding: 36px; color: var(--muted);">
                        <div style="font-size: 36px; margin-bottom: 12px;">📭</div>
                        <div style="font-size: 13px;">Hali ekspertizaga hujjat yuborilmagan.</div>
                    </div>
                @else
                    @foreach($expertiseApplications as $exp)
                        <div style="border: 1px solid var(--border); border-radius: 10px; padding: 16px; margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="font-weight: 700; color: var(--ink);">Ekspertiza #{{ $exp->id }}</span>
                                <span style="font-size: 11px; color: var(--muted);">{{ $exp->created_at->format('d.m.Y') }}</span>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 13px; color: var(--muted);">🏫 Institut (15 kun)</span>
                                    @if($exp->institute_status == 'pending')
                                        <span class="status-badge sb-submitted">Kutilmoqda</span>
                                    @elseif($exp->institute_status == 'approved')
                                        <span class="status-badge sb-finalized">✓ Ma'qullandi</span>
                                    @else
                                        <span class="status-badge sb-rejected">Qaytarildi</span>
                                    @endif
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 13px; color: var(--muted);">🏛 Vazirlik (10 kun)</span>
                                    @if($exp->ministry_status == 'pending')
                                        <span class="status-badge sb-submitted">Kutilmoqda</span>
                                    @elseif($exp->ministry_status == 'approved')
                                        <span class="status-badge sb-finalized">✓ Tasdiqlandi</span>
                                    @else
                                        <span class="status-badge sb-rejected">Qaytarildi</span>
                                    @endif
                                </div>
                                @if($exp->conclusion_number)
                                    <div style="margin-top: 4px; padding: 10px; background: var(--green-light); border-radius: 8px; border: 1px solid rgba(26,107,60,0.2);">
                                        <div style="font-size: 11px; color: var(--green); text-transform: uppercase; font-weight: 600; margin-bottom: 2px;">Xulosa Raqami</div>
                                        <div style="font-family: monospace; font-size: 15px; font-weight: 700; color: var(--green);">{{ $exp->conclusion_number }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>

    <style>
        @media (max-width: 768px) {
            .detail-grid-responsive { grid-template-columns: 1fr !important; }
        }
    </style>
</x-app-layout>
