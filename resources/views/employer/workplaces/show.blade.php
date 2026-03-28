<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('employer.workplaces.index') }}" class="p-2 bg-white rounded-full hover:bg-gray-100 transition-colors shadow-sm border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight drop-shadow-sm">{{ $workplace->name }}</h2>
            </div>
            
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold 
                @if($workplace->status == 'pending') bg-yellow-100 text-yellow-800 border-yellow-200
                @elseif($workplace->status == 'attested') bg-green-100 text-green-800 border-green-200
                @else bg-blue-100 text-blue-800 border-blue-200 @endif border shadow-sm">
                {{ $workplace->status == 'pending' ? 'Kutayotgan (Attestatsiyalanmagan)' : ucfirst($workplace->status) }}
            </span>

            @if($workplace->status == 'attested')
            <a href="{{ route('employer.workplaces.print', $workplace) }}" target="_blank" class="px-4 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-full shadow hover:shadow-lg transition-transform hover:-translate-y-0.5 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Xulosa PDF
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Ma'lumot Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Chap tomon (Asosiy ma'lumotlar) -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white/80 backdrop-blur-xl border border-white rounded-3xl shadow-xl p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-3 bg-blue-100 text-blue-600 rounded-2xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Tafsilotlar</h3>
                        </div>

                        <ul class="space-y-4 text-sm text-gray-600">
                            <li class="flex justify-between border-b border-gray-100 pb-2">
                                <span class="font-medium">Bo'lim (Sex):</span>
                                <span class="font-semibold text-gray-900">{{ $workplace->department ?? 'Noma\'lum' }}</span>
                            </li>
                            <li class="flex justify-between border-b border-gray-100 pb-2">
                                <span class="font-medium">OKZ Kodi:</span>
                                <span class="font-semibold text-gray-900">{{ $workplace->code ?? 'Kiritilmagan' }}</span>
                            </li>
                            <li class="flex justify-between border-b border-gray-100 pb-2">
                                <span class="font-medium">Xodimlar soni:</span>
                                <span class="font-semibold px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-lg">{{ $workplace->employees_count }} ta</span>
                            </li>
                            <li class="flex flex-col gap-1 pt-2">
                                <span class="font-medium">Qo'shimcha tavsif:</span>
                                <p class="bg-gray-50 p-3 rounded-xl border border-gray-100 text-gray-700 text-xs leading-relaxed">
                                    {{ $workplace->description ?? 'Tavsif kiritilmagan.' }}
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- O'ng tomon (O'lchov natijalari va Xavf omillari) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white/80 backdrop-blur-xl border border-white rounded-3xl shadow-xl overflow-hidden">
                        
                        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                            <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-500 to-orange-500 flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Xavf Omillari va O'lchovlar
                            </h3>
                            <!-- Agar hali o'lchanmagan bo'lsa, xabar chiqariladi yomon rangda. Bu qism Laboratoriya tomonidan to'ldiriladi. -->
                        </div>

                        <div class="p-6">
                            @if($workplace->measurements->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($workplace->measurements as $measure)
                                    <div class="border border-gray-200 rounded-2xl p-4 bg-white hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ $measure->factor_name }}</div>
                                        <div class="flex gap-2 items-baseline mb-3">
                                            <span class="text-2xl font-black text-gray-800">{{ $measure->measured_value ?? '-' }}</span>
                                            <span class="text-xs text-gray-500">me'yor: {{ $measure->norm_value ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-100">
                                            <span class="text-xs text-gray-500">Klass:</span>
                                            <span class="px-2 py-1 bg-red-100 text-red-700 font-bold text-xs rounded-md shadow-sm border border-red-200">{{ $measure->danger_class }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-10 bg-gray-50/50 rounded-2xl border border-dashed border-gray-300 flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="text-gray-500 font-medium">Hali laboratoriya o'lchovlari kiritilmagan</p>
                                    <span class="text-xs text-gray-400 mt-1">Attestatsiya tashkiloti (Laboratoriya) bu oynani to'ldiradi.</span>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
