<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
            Attestatsiya O'lchovlari (Laboratoriya)
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            @endif

            <div class="bg-white/60 backdrop-blur-xl border border-white rounded-3xl shadow-xl overflow-hidden">
                <div class="p-8 pb-4 border-b border-gray-100/50">
                    <h3 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                        O'lchov o'tkazish kerak bo'lgan ish o'rinlari
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Sizning laboratoriyangiz nomiga rasmiylashtirilgan yoki umumiy bazadagi kutilayotgan ish o'rinlari ro'yxati.</p>
                </div>

                <div class="p-8">
                    @if($workplaces->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 rounded-lg">
                                <tr>
                                    <th class="px-6 py-4 rounded-l-xl font-bold">Korxona</th>
                                    <th class="px-6 py-4 font-bold">Kasb/Lavozimi</th>
                                    <th class="px-6 py-4 font-bold">Sex moduli</th>
                                    <th class="px-6 py-4 font-bold">Holat</th>
                                    <th class="px-6 py-4 rounded-r-xl font-bold">Amal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workplaces as $wp)
                                <tr class="border-b border-gray-50 hover:bg-white/40 transition-colors">
                                    <td class="px-6 py-5 font-semibold text-gray-800">{{ $wp->organization->name ?? 'Noma\'lum' }}</td>
                                    <td class="px-6 py-5 font-medium">{{ $wp->name }}</td>
                                    <td class="px-6 py-5">{{ $wp->department ?? '-' }}</td>
                                    <td class="px-6 py-5">
                                        @if($wp->status == 'pending')
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full border border-yellow-200">Kutayotgan</span>
                                        @elseif($wp->status == 'attested')
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full border border-green-200">Attestatsiyalangan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        @if($wp->status == 'pending')
                                            <a href="{{ route('laboratory.measurements.create', $wp) }}" class="inline-flex max-w-fit px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs font-bold rounded-xl hover:shadow-lg transition-transform hover:-translate-y-1">
                                                O'lchov kiritish
                                            </a>
                                        @else
                                            <span class="text-gray-400 font-medium text-xs">Yakunlangan</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $workplaces->links() }}
                    </div>
                    @else
                    <div class="text-center py-12">
                        <p class="text-gray-500">Hozircha o'lchov uchun kutilayotgan ish o'rinlari mavjud emas.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
