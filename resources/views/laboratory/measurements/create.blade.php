<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('laboratory.workplaces.index') }}" class="p-2 bg-white rounded-full hover:bg-gray-100 transition-colors shadow-sm">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-2xl text-gray-900 leading-tight">O'lchov Kiritish: {{ $workplace->name }}</h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/70 backdrop-blur-xl border border-white rounded-3xl shadow-xl overflow-hidden p-8">
                
                <h3 class="text-xl font-bold mb-6 text-gray-800">
                    SanQvaM 0069-24 bo'yicha Faktorlar
                </h3>

                <form action="{{ route('laboratory.measurements.store', $workplace) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-6">
                        @foreach($factors as $index => $factor)
                        <div class="border border-gray-200 rounded-2xl p-6 bg-gray-50/50 hover:bg-white transition-colors duration-200">
                            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="bg-indigo-100 text-indigo-700 w-6 h-6 rounded-full flex items-center justify-center text-xs">{{ $loop->iteration }}</span>
                                {{ $factor }}
                            </h4>
                            <input type="hidden" name="measurements[{{ $index }}][factor_name]" value="{{ $factor }}">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">O'lchangan Miqdor</label>
                                    <input type="text" name="measurements[{{ $index }}][measured_value]" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masalan: 85 dB">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Ruxsat Etilgan Me'yor</label>
                                    <input type="text" name="measurements[{{ $index }}][norm_value]" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masalan: 80 dB">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Xavf Klassi</label>
                                    <select name="measurements[{{ $index }}][danger_class]" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="Optimal">Optimal (Kiritilmaydi)</option>
                                        <option value="1">1 - Optimal</option>
                                        <option value="2">2 - Ruxsat etilgan</option>
                                        <option value="3.1">3.1 - Zararli (1-daraja)</option>
                                        <option value="3.2">3.2 - Zararli (2-daraja)</option>
                                        <option value="3.3">3.3 - Zararli (3-daraja)</option>
                                        <option value="3.4">3.4 - Zararli (4-daraja)</option>
                                        <option value="4">4 - Xavfli</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-indigo-500/30 transform hover:-translate-y-1 transition-all">
                            Yakunlash va Saqlash
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
