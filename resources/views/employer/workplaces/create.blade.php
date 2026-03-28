<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('employer.workplaces.index') }}" class="p-2 bg-white rounded-full hover:bg-gray-100 transition-colors shadow-sm">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Yangi Ish O'rni qo'shish</h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-indigo-50 via-white to-purple-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/70 backdrop-blur-xl border border-white rounded-3xl shadow-xl overflow-hidden p-8">
                
                <form action="{{ route('employer.workplaces.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nomi yoki Kasb <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full bg-white/50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-gray-400" placeholder="Masalan: Elektromontyor">
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">OKZ Kodi (ixtiyoriy)</label>
                            <input type="text" name="code" class="w-full bg-white/50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-gray-400" placeholder="1234">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Bo'lim yoki Sex</label>
                            <input type="text" name="department" class="w-full bg-white/50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-gray-400" placeholder="Asosiy ishlab chiqarish sexi">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ushbu turdagi xodimlar soni <span class="text-red-500">*</span></label>
                            <input type="number" name="employees_count" min="1" value="1" required class="w-full bg-white/50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all">
                            @error('employees_count') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Qo'shimcha tavsif</label>
                        <textarea name="description" rows="3" class="w-full bg-white/50 border border-gray-200 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder-gray-400" placeholder="Ish joyining batafsil tarxini va xususiyatlarini yozishingiz mumkin..."></textarea>
                    </div>

                    <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-100">
                        <a href="{{ route('employer.workplaces.index') }}" class="px-6 py-3 font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Bekor qilish</a>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-indigo-500/30 transform hover:-translate-y-[2px] transition-all">
                            Saqlash
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
