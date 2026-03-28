<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $workplace->name }} - Xulosa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; background: white !important; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
        }
        body { font-family: 'Times New Roman', Times, serif; }
    </style>
</head>
<body class="bg-gray-100 py-8 text-black">
    <div class="max-w-4xl mx-auto bg-white p-12 shadow-2xl relative">
        <div class="no-print absolute top-4 right-4 space-x-2">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white font-sans font-semibold rounded shadow hover:bg-blue-700">Chop etish / PDF saqlash</button>
            <button onclick="window.close()" class="px-4 py-2 bg-gray-500 text-white font-sans font-semibold rounded shadow hover:bg-gray-600">Yopish</button>
        </div>

        <div class="text-center mt-8 mb-12">
            <h1 class="text-2xl font-bold uppercase mb-2">O'zbekiston Respublikasi Kambag'allikni qisqartirish va Mehnat munosabatlari vazirligi</h1>
            <h2 class="text-xl font-bold mt-6 underline">Ish o'rinlarini mehnat sharoitlari va asbob-uskunalarning jarohatlash xavfliligi yuzasidan attestatsiyadan o'tkazish xulosasi</h2>
        </div>

        <div class="mb-8 space-y-3 pb-6 border-b-2 border-dashed border-gray-300">
            <p><span class="font-bold">Korxona (Tashkilot) nomi:</span> {{ $workplace->organization->name }} (STIR: {{ $workplace->organization->stir_inn }})</p>
            <p><span class="font-bold">Kasb yoki lavozim:</span> {{ $workplace->name }} (OKZ: {{ $workplace->code ?? 'Kiritilmagan' }})</p>
            <p><span class="font-bold">Sex / Bo'lim:</span> {{ $workplace->department ?? 'Tarkibiy bo\'lim' }}</p>
            <p><span class="font-bold">Xodimlar soni:</span> {{ $workplace->employees_count }} kishi</p>
            <p><span class="font-bold">Xulosa berilgan sana:</span> {{ now()->format('d.m.Y') }}</p>
        </div>

        <h3 class="text-lg font-bold mb-4">Mehnat sharoitlari va xavf omillarini o'lchash natijalari:</h3>
        
        <table class="w-full border-collapse border border-gray-400 mb-8 text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="border border-gray-400 px-4 py-2 w-10 text-center">T/r</th>
                    <th class="border border-gray-400 px-4 py-2">Xavf omili (SanQvaM 0069-24)</th>
                    <th class="border border-gray-400 px-4 py-2 text-center">Fakt o'lchov</th>
                    <th class="border border-gray-400 px-4 py-2 text-center">Me'yor</th>
                    <th class="border border-gray-400 px-4 py-2 text-center">Xavf Klassi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workplace->measurements as $index => $measure)
                <tr>
                    <td class="border border-gray-400 px-4 py-2 text-center">{{ $index + 1 }}</td>
                    <td class="border border-gray-400 px-4 py-2">{{ $measure->factor_name }}</td>
                    <td class="border border-gray-400 px-4 py-2 text-center font-semibold">{{ $measure->measured_value ?? '-' }}</td>
                    <td class="border border-gray-400 px-4 py-2 text-center text-gray-700">{{ $measure->norm_value ?? '-' }}</td>
                    <td class="border border-gray-400 px-4 py-2 text-center font-bold">{{ $measure->danger_class }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="border border-gray-400 px-4 py-4 text-center italic text-gray-500">O'lchov natijalari kiritilmagan</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-12 pt-8 border-t border-gray-800">
            <p class="font-bold mb-4">Umumiy Yakuniy Xulosa:</p>
            <p class="mb-4 text-justify">O'lchov natijalariga ko'ra, "<strong>{{ $workplace->organization->name }}</strong>" tashkilotidagi "<strong>{{ $workplace->name }}</strong>" ish o'rni attestatsiyadan o'tdi deb hisoblansin. Mehnat sharoitlari va uskunalarning jarohatlash xavfliligi yuzasidan kiritilgan ko'rsatkichlar me'yoriy huquqiy hujjatlarga asosan (VMQ 263-son, SanQvaM 0069-24) baholandi.</p>
        </div>

        <div class="mt-16 flex justify-between">
            <div class="text-center">
                <p class="mb-8">Laboratoriya (Attestatsiya) mutaxassisi</p>
                <div class="h-0 border-b border-black w-48 mx-auto mb-2"></div>
                <p class="text-xs">(Imzo)</p>
            </div>
            
            <div class="text-center">
                <p class="mb-8">Tashkilot rahbari (Yoki vakolatli shaxs)</p>
                <div class="h-0 border-b border-black w-48 mx-auto mb-2"></div>
                <p class="text-xs">(Imzo)</p>
            </div>
        </div>

        <div class="mt-8 text-center text-xs text-gray-500">
            * Ushbu xulosa elektron tizim orqali generatsiya qilingan (E-Attestatsiya)
        </div>
    </div>
</body>
</html>
