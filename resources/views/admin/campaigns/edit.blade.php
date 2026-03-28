<x-app-layout>
    <x-slot name="header">✏️ Kampaniyani tahrirlash</x-slot>

    <div style="max-width: 680px;">
        <div class="att-card">
            <div style="background: var(--ink); padding: 20px 24px; border-radius: 12px 12px 0 0;">
                <h3 style="font-family: 'DM Serif Display', serif; font-size: 20px; color: white; margin: 0 0 4px;">{{ $campaign->title }}</h3>
                <p style="color: rgba(255,255,255,0.4); font-size: 13px; margin: 0;">Kampaniya ma'lumotlarini tahrirlang</p>
            </div>
            <div class="att-card-body">
                <form method="POST" action="{{ route('admin.campaigns.update', $campaign) }}">
                    @csrf
                    @method('PUT')

                    <div class="att-field" style="margin-bottom: 18px;">
                        <label>Kampaniya nomi *</label>
                        <input type="text" name="title" value="{{ old('title', $campaign->title) }}" required>
                        @error('title') <p style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>

                    <div class="att-field" style="margin-bottom: 18px;">
                        <label>Izoh</label>
                        <textarea name="description" rows="3">{{ old('description', $campaign->description) }}</textarea>
                        @error('description') <p style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px;">
                        <div class="att-field">
                            <label>Boshlanish sanasi *</label>
                            <input type="date" name="start_date" value="{{ old('start_date', $campaign->start_date) }}" required>
                            @error('start_date') <p style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>
                        <div class="att-field">
                            <label>Tugash sanasi *</label>
                            <input type="date" name="end_date" value="{{ old('end_date', $campaign->end_date) }}" required>
                            @error('end_date') <p style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="att-field" style="margin-bottom: 24px;">
                        <label>Holati</label>
                        <select name="status">
                            <option value="draft" @selected(old('status', $campaign->status) === 'draft')>📝 Draft</option>
                            <option value="open" @selected(old('status', $campaign->status) === 'open')>✅ Ochiq</option>
                            <option value="closed" @selected(old('status', $campaign->status) === 'closed')>🔒 Yopilgan</option>
                        </select>
                        @error('status') <p style="color: var(--red); font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>

                    <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid var(--border);">
                        <a href="{{ route('admin.campaigns.index') }}" class="btn-att btn-att-secondary">← Bekor qilish</a>
                        <button type="submit" class="btn-att btn-att-primary">✓ Yangilash</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
