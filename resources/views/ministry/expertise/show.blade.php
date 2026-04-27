<x-app-layout>
    <x-slot name="header">Ekspertiza #{{ $expertise->id }}</x-slot>

    <x-page-header
        title="Yakuniy davlat ekspertizasi"
        subtitle="Institut ma‘qullagan arizalar bo‘yicha vazirlik xulosasi."
        :crumbs="[
            ['label' => 'Boshqaruv paneli', 'url' => route('dashboard')],
            ['label' => 'Davlat ekspertizasi', 'url' => route('ministry.expertise.index')],
            ['label' => 'Ekspertiza #'.$expertise->id],
        ]"
    >
        <x-slot name="actions">
            <x-att-button :href="route('ministry.expertise.index')" variant="secondary" size="sm">Ortga</x-att-button>
        </x-slot>
    </x-page-header>

    @php
        $appsArray = is_array($expertise->application_ids)
            ? $expertise->application_ids
            : json_decode($expertise->application_ids, true);
        $applications = \App\Models\AttestationApplication::with('protocol')->whereIn('id', $appsArray ?? [])->get();
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="lg:col-span-2 space-y-4" style="display:flex;flex-direction:column;gap:18px;">

            {{-- Buyurtmachi va laboratoriya --}}
            <div class="att-card">
                <div class="att-card-header">
                    <span class="att-card-title">Buyurtmachi va laboratoriya</span>
                </div>
                <div class="att-card-body">
                    <table class="kv-list">
                        <tr class="kv-row"><td class="kv-key">Korxona</td><td class="kv-val">{{ optional($expertise->organization)->name }}</td></tr>
                        <tr class="kv-row"><td class="kv-key">Laboratoriya</td><td class="kv-val">{{ optional($expertise->laboratory)->name }}</td></tr>
                        <tr class="kv-row">
                            <td class="kv-key">Institut xulosasi</td>
                            <td class="kv-val">
                                <x-att-badge status="finalized" label="Ma‘qullandi" />
                                <span class="text-muted" style="margin-left:8px;font-size:12px;">{{ optional($expertise->institute_reviewed_at)->format('d.m.Y H:i') }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Ish o'rinlari natijasi --}}
            <div class="att-card">
                <div class="att-card-header">
                    <span class="att-card-title">Ish o‘rinlari natijalari ({{ $applications->count() }})</span>
                </div>
                <div class="att-card-body" style="padding:0;">
                    @if($applications->isEmpty())
                        <div style="padding:24px;">
                            <x-empty-state icon="▤" title="Ish o‘rinlari topilmadi" />
                        </div>
                    @else
                        <table class="att-table">
                            <thead>
                                <tr>
                                    <th>Ish o‘rni</th>
                                    <th style="text-align:center;">Sinf</th>
                                    <th style="text-align:center;">Imtiyoz</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $app)
                                    <tr>
                                        <td style="font-weight:600;">{{ $app->workplace_name }}</td>
                                        <td style="text-align:center;">
                                            <span class="status-badge sb-info">Klass {{ optional($app->protocol)->overall_class ?? '—' }}</span>
                                        </td>
                                        <td style="text-align:center;">
                                            @if(optional($app->protocol)->requires_benefits)
                                                <x-att-badge status="approved" label="Belgilangan" />
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- Yakuniy qaror paneli --}}
        <div>
            <div class="att-card att-card-accent-red">
                <div class="att-card-header">
                    <span class="att-card-title" style="color:var(--red);">Yakuniy xulosa</span>
                </div>
                <div class="att-card-body">
                    @if($expertise->ministry_status === 'pending')
                        <form method="POST" action="{{ route('ministry.expertise.process', $expertise) }}" x-data="{ action: '' }">
                            @csrf
                            <div class="att-field" style="margin-bottom:14px;">
                                <label>Amalni tanlang <span class="req">*</span></label>
                                <select name="action" x-model="action" required>
                                    <option value="">— Tanlang —</option>
                                    <option value="approve">Tasdiqlash (QR-kodli xulosa)</option>
                                    <option value="return">Rad etish / Qaytarish</option>
                                </select>
                            </div>

                            <div class="att-field" x-show="action === 'return'" x-cloak style="margin-bottom:14px;">
                                <label>Rad etish asosi <span class="req">*</span></label>
                                <textarea name="comment" rows="4" placeholder="Qaysi me‘yoriy talablar bajarilmaganligini yozing..." x-bind:required="action === 'return'"></textarea>
                            </div>

                            <x-att-button type="submit" variant="primary" style="width:100%;">Tasdiqlash</x-att-button>
                        </form>
                    @else
                        <div style="text-align:center;">
                            <div style="font-family:var(--font-display);font-size:22px;color:{{ $expertise->ministry_status === 'approved' ? 'var(--green)' : 'var(--red)' }};margin-bottom:4px;">
                                {{ $expertise->ministry_status === 'approved' ? 'Tasdiqlandi' : 'Rad etildi' }}
                            </div>
                            <div class="text-muted" style="font-size:12px;">{{ optional($expertise->ministry_reviewed_at)->format('d.m.Y H:i') }}</div>

                            @if($expertise->conclusion_number)
                                <div class="conclusion-stamp">
                                    <div class="cs-label">Elektron xulosa raqami</div>
                                    <div class="cs-number">{{ $expertise->conclusion_number }}</div>
                                    <div class="cs-hint">QR-kod orqali tekshirish mumkin</div>
                                </div>
                            @endif

                            @if($expertise->ministry_comment)
                                <div style="margin-top:14px;padding:12px 14px;background:var(--red-light);border:1px solid rgba(184,50,50,.18);border-radius:var(--r);font-size:13px;text-align:left;color:var(--red);">
                                    {{ $expertise->ministry_comment }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('head')
        <style>[x-cloak] { display: none !important; }</style>
    @endpush
</x-app-layout>
