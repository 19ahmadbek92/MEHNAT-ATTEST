<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
            {{ __('messages.workplaces') }}
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
                <div class="p-8 pb-4 flex justify-between items-center border-b border-gray-100/50">
                    <h3 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                        Ro'yxatga olingan ish o'rinlari
                    </h3>
                    <a href="{{ route('employer.workplaces.create') }}" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-2xl shadow-lg hover:shadow-indigo-500/30 transform hover:-translate-y-1 transition-all duration-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Yangi qo'shish
                    </a>
                </div>

                <div class="p-8">
                    @if($workplaces->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 rounded-lg">
                                <tr>
                                    <th scope="col" class="px-6 py-4 rounded-l-xl font-bold">Nomi/Kasb</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Bo'lim (Sex)</th>
                                    <th scope="col" class="px-6 py-4 font-bold">OKZ Kodi</th>
                                    <th scope="col" class="px-6 py-4 font-bold">Holati</th>
                                    <th scope="col" class="px-6 py-4 rounded-r-xl font-bold">Amal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workplaces as $wp)
                                <tr class="border-b border-gray-50 hover:bg-white/40 transition-colors">
                                    <td class="px-6 py-5 font-semibold text-gray-800">{{ $wp->name }}</td>
                                    <td class="px-6 py-5">{{ $wp->department ?? '-' }}</td>
                                    <td class="px-6 py-5">{{ $wp->code ?? '-' }}</td>
                                    <td class="px-6 py-5">
                                        @if($wp->status == 'pending')
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full border border-yellow-200">Kutayotgan</span>
                                        @elseif($wp->status == 'attested')
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full border border-green-200">Attestatsiyadan o'tgan</span>
                                        @else
                                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full border border-blue-200">{{ ucfirst($wp->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        <a href="{{ route('employer.workplaces.show', $wp) }}" class="text-indigo-600 hover:text-indigo-900 font-medium hover:underline bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">
                                            Batafsil &rarr;
                                        </a>
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
                    <div class="text-center py-12 flex flex-col items-center justify-center">
                        <div class="bg-indigo-50 outline-none ring-4 ring-indigo-50/50 rounded-full p-4 mb-4">
                            <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h4 class="text-xl font-medium text-gray-500 mb-2">Hozircha ish o'rinlari qo'shilmagan</h4>
                        <p class="text-gray-400 max-w-sm mx-auto">Tashkilotingiz tarkibidagi barcha attestatsiya qilinishi kerak bo'lgan ish o'rinlarini bu yerga qo'shing.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
