<x-app-layout>
    <x-slot name="header">Audit jurnali</x-slot>

    <x-page-header
        title="Audit jurnali"
        subtitle="Tizim ichidagi barcha amallar va o‘zgartirishlar tarixi."
        :crumbs="[
            ['label' => 'Boshqaruv paneli', 'url' => route('dashboard')],
            ['label' => 'Admin paneli', 'url' => route('admin.dashboard')],
            ['label' => 'Audit jurnali'],
        ]"
    />

    {{-- ─── Filters ─── --}}
    <div class="att-card" style="margin-bottom:18px;">
        <div class="att-card-body">
            <form method="GET" action="{{ route('admin.audit.index') }}">
                <div class="att-form-grid">
                    <div class="att-field">
                        <label>Amal</label>
                        <select name="action">
                            <option value="">Hammasi</option>
                            @foreach($availableActions as $a)
                                <option value="{{ $a }}" @selected($filters['action'] === $a)>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="att-field">
                        <label>Foydalanuvchi</label>
                        <select name="user_id">
                            <option value="">Hammasi</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected((string) $filters['user_id'] === (string) $u->id)>{{ $u->name }} <small>({{ $u->role }})</small></option>
                            @endforeach
                        </select>
                    </div>
                    <div class="att-field">
                        <label>Boshlanish sanasi</label>
                        <input type="date" name="from" value="{{ $filters['from'] }}">
                    </div>
                    <div class="att-field">
                        <label>Tugash sanasi</label>
                        <input type="date" name="to" value="{{ $filters['to'] }}">
                    </div>
                </div>
                <div style="margin-top:14px;display:flex;gap:8px;">
                    <x-att-button type="submit" variant="primary" size="sm">Filtrlash</x-att-button>
                    <x-att-button :href="route('admin.audit.index')" variant="ghost" size="sm">Tozalash</x-att-button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── Results ─── --}}
    <div class="att-card">
        <div class="att-card-header">
            <span class="att-card-title">Yozuvlar ({{ $logs->total() }})</span>
        </div>
        <div class="att-card-body" style="padding:0;">
            @if($logs->isEmpty())
                <div style="padding:30px;">
                    <x-empty-state
                        icon="▤"
                        title="Yozuvlar topilmadi"
                        description="Belgilangan filtr bo‘yicha audit yozuvlari mavjud emas."
                    />
                </div>
            @else
                <div class="desktop-table">
                    <table class="att-table">
                        <thead>
                            <tr>
                                <th>Sana</th>
                                <th>Foydalanuvchi</th>
                                <th>Amal</th>
                                <th>Obyekt</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td class="text-muted" style="white-space:nowrap;font-variant-numeric:tabular-nums;">
                                        {{ optional($log->created_at)->format('d.m.Y H:i:s') }}
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <div style="font-weight:600;color:var(--ink);">{{ $log->user->name }}</div>
                                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.4px;">{{ $log->user->role }}</div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge sb-info">{{ $log->action }}</span>
                                    </td>
                                    <td>
                                        @if($log->subject_type)
                                            <span style="font-family:ui-monospace,monospace;font-size:12px;color:var(--ink);">{{ class_basename($log->subject_type) }}#{{ $log->subject_id }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted" style="font-family:ui-monospace,monospace;font-size:11.5px;">{{ $log->ip_address ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mobile-card-list" style="padding:14px;">
                    @foreach($logs as $log)
                        <div class="mobile-app-card">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
                                <span class="status-badge sb-info">{{ $log->action }}</span>
                                <span class="text-muted" style="font-size:11px;">{{ optional($log->created_at)->format('d.m H:i') }}</span>
                            </div>
                            <div style="margin-top:6px;font-size:13px;color:var(--ink);">{{ $log->user?->name ?? 'Tizim' }}</div>
                            @if($log->subject_type)
                                <div class="text-muted" style="font-size:11.5px;font-family:ui-monospace,monospace;margin-top:3px;">{{ class_basename($log->subject_type) }}#{{ $log->subject_id }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if($logs->hasPages())
        <div style="margin-top:18px;">{{ $logs->withQueryString()->links() }}</div>
    @endif
</x-app-layout>
