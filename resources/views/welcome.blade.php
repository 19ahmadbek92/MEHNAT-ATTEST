<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yagona Attestatsiya Axborot Tizimi</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        .hero-pattern {
            background-color: #ffffff;
            background-image: 
                radial-gradient(at 40% 20%, hsla(228,100%,74%,1) 0px, transparent 50%),
                radial-gradient(at 80% 0%, hsla(189,100%,56%,1) 0px, transparent 50%),
                radial-gradient(at 0% 50%, hsla(228,100%,74%,1) 0px, transparent 50%),
                radial-gradient(at 80% 50%, hsla(228,100%,74%,1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(22,100%,77%,1) 0px, transparent 50%),
                radial-gradient(at 80% 100%, hsla(228,100%,74%,1) 0px, transparent 50%),
                radial-gradient(at 0% 0%, hsla(228,100%,74%,1) 0px, transparent 50%);
            opacity: 0.1;
            position: absolute;
            inset: 0;
            z-index: -1;
        }
        .blob {
            position: absolute;
            filter: blur(80px);
            z-index: -2;
            opacity: 0.5;
            animation: move 10s infinite alternate linear;
        }
        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(20px, -20px) scale(1.1); }
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900 relative min-h-screen selection:bg-indigo-500 selection:text-white flex flex-col overflow-x-hidden">
    
    <!-- Background Animated Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-[-1]">
        <div class="blob bg-blue-400 w-96 h-96 rounded-full top-[-10%] left-[-10%] mix-blend-multiply transition-all duration-1000"></div>
        <div class="blob bg-indigo-300 w-[30rem] h-[30rem] rounded-full top-[20%] right-[-10%] mix-blend-multiply transition-all duration-1000" style="animation-delay: 2s;"></div>
        <div class="blob bg-purple-300 w-80 h-80 rounded-full bottom-[-10%] left-[20%] mix-blend-multiply transition-all duration-1000" style="animation-delay: 4s;"></div>
        <div class="hero-pattern"></div>
    </div>

    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-50 glass-nav transition-all duration-300 shadow-sm border-b border-indigo-100/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3 group cursor-pointer">
                    <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-500/40 group-hover:rotate-6 transition-transform">
                        E
                    </div>
                    <span class="font-extrabold text-xl tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-900 to-indigo-800">
                        Attestatsiya
                    </span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#about" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors">Tizim haqida</a>
                    <a href="#features" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors">Afzalliklar</a>
                    <a href="#workflow" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors">Qanday ishlaydi?</a>
                    <a href="#contact" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors">Bog'lanish</a>
                </div>

                <!-- Right Side (Lang & Login) -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 border-r border-gray-300 pr-4 mr-2 hidden sm:flex">
                        <a href="{{ route('lang.switch', 'uz') }}" class="text-xs font-bold {{ app()->getLocale() == 'uz' ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">UZ</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('lang.switch', 'ru') }}" class="text-xs font-bold {{ app()->getLocale() == 'ru' ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">RU</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('lang.switch', 'en') }}" class="text-xs font-bold {{ app()->getLocale() == 'en' ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-600' }}">EN</a>
                    </div>
                    
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-full shadow-lg hover:shadow-indigo-500/30 transform hover:-translate-y-0.5 transition-all text-sm flex items-center gap-2">
                            Kabinetga o'tish
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('auth.select-type') ?? '/login/select-type' }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-full shadow-lg hover:shadow-indigo-500/30 transform hover:-translate-y-0.5 transition-all outline-none text-sm border border-indigo-500">
                            Saytga kirish (OneID)
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow pt-28">
        <!-- Hero Section -->
        <section id="about" class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-32 text-center lg:text-left lg:flex items-center justify-between z-10 min-h-[85vh]">
            <div class="lg:w-1/2 space-y-8 animate-fade-in-up">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/70 backdrop-blur border border-indigo-100 text-indigo-700 text-xs font-bold tracking-wide uppercase mb-2 shadow-sm">
                    <span class="flex h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    Raqamlashtirilgan Davlat Portali
                </div>
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-gray-900 leading-[1.1] tracking-tight">
                    Ish o'rinlarini yagona <br class="hidden lg:block"/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 drop-shadow-sm">elektron attestatsiya</span>
                    tizimi
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-medium">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-6">
                    <a href="{{ route('auth.select-type') ?? '/login/select-type' }}" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-2xl shadow-xl shadow-indigo-500/30 hover:scale-105 hover:-translate-y-1 transition-all text-center flex items-center justify-center gap-2 text-lg">
                        Tizimga Kiriish
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="#how-it-works" class="px-8 py-4 bg-white text-gray-800 font-bold rounded-2xl shadow-sm border border-gray-200 hover:bg-gray-50 hover:shadow-md transition-all text-center flex justify-center items-center gap-2 text-lg">
                        <svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                        Yo'riqnoma
                    </a>
                </div>
            </div>
            
            <div class="hidden lg:block lg:w-5/12 relative mt-16 lg:mt-0">
                <!-- Floating Illustration/Mockup representation -->
                <div class="relative w-full aspect-square max-w-lg mx-auto transform translate-x-12">
                    <div class="absolute inset-0 bg-gradient-to-tr from-blue-300/40 to-purple-300/40 rounded-[3rem] blur-2xl opacity-80 mix-blend-multiply"></div>
                    
                    <!-- App Card UI Mock -->
                    <div class="relative glass-card rounded-[2.5rem] p-8 border border-white/80 transform rotate-[4deg] hover:rotate-0 transition-all duration-700 h-[28rem] flex flex-col justify-between overflow-hidden shadow-2xl z-20 hover:-translate-y-4">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="w-24 h-6 bg-indigo-50 rounded-full animate-pulse border border-indigo-100"></div>
                        </div>
                        
                        <div class="space-y-4 flex-grow relative z-10">
                            <!-- Graph mockup -->
                            <div class="w-full h-32 bg-gradient-to-tr from-blue-50 to-indigo-100 rounded-2xl border border-white p-4 flex items-end gap-3 justify-center">
                                <div class="w-1/6 bg-indigo-200 rounded-t-md h-[40%]"></div>
                                <div class="w-1/6 bg-indigo-300 rounded-t-md h-[70%]"></div>
                                <div class="w-1/6 bg-blue-400 rounded-t-md h-[90%]"></div>
                                <div class="w-1/6 bg-indigo-500 rounded-t-md h-[100%] shadow-lg shadow-indigo-500/40"></div>
                                <div class="w-1/6 bg-indigo-200 rounded-t-md h-[60%]"></div>
                            </div>
                            <!-- List item -->
                            <div class="w-full bg-white/80 rounded-xl p-3 flex items-center gap-4 shadow-sm border border-white">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500">🧪</div>
                                <div class="flex-grow">
                                    <div class="w-1/2 h-3 bg-gray-200 rounded mb-2"></div>
                                    <div class="w-1/3 h-2 bg-gray-100 rounded"></div>
                                </div>
                                <div class="w-16 h-6 bg-green-100 rounded-full"></div>
                            </div>
                            <!-- List item -->
                            <div class="w-full bg-white/80 rounded-xl p-3 flex items-center gap-4 shadow-sm border border-white">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">🏭</div>
                                <div class="flex-grow">
                                    <div class="w-2/3 h-3 bg-gray-200 rounded mb-2"></div>
                                    <div class="w-1/4 h-2 bg-gray-100 rounded"></div>
                                </div>
                                <div class="w-16 h-6 bg-yellow-100 rounded-full"></div>
                            </div>
                        </div>
                        
                        <!-- Glow effect inside -->
                        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-purple-400 rounded-full mix-blend-screen filter blur-[40px] opacity-40"></div>
                    </div>
                    
                    <!-- Floating Stat Badge -->
                    <div class="absolute -left-12 bottom-16 glass-card rounded-2xl p-5 shadow-2xl shadow-indigo-500/20 transform -rotate-3 hover:translate-y-2 hover:rotate-0 transition-all duration-500 border border-white flex gap-4 items-center z-30">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-inner">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-gray-800 tracking-tight">15 k+</p>
                            <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider mt-0.5">Xulosalar berildi</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24 relative z-10 w-full bg-white/50 backdrop-blur-md border-y border-white flex flex-col items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-20">
                    <h2 class="text-indigo-600 font-bold tracking-wide uppercase text-sm mb-2">Tizim Afzalliklari</h2>
                    <h3 class="text-3xl md:text-4xl font-black text-gray-900 mb-6 leading-tight">Nima uchun Yagona Elektron Attestatsiya?</h3>
                    <p class="text-lg text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white rounded-[2rem] p-8 hover:-translate-y-3 transition-transform duration-300 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-8 opacity-0 group-hover:opacity-10 transform translate-x-10 -translate-y-10 transition-all duration-500">
                            <svg class="w-32 h-32 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13h-13L12 6.5z"/></svg>
                        </div>
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-600 rounded-2xl flex items-center justify-center mb-8 shadow-inner border border-white">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h4 class="text-2xl font-bold text-gray-900 mb-4 tracking-tight">Tezkor Ma'lumot</h4>
                        <p class="text-gray-600 text-base leading-relaxed">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-gradient-to-b from-indigo-600 to-purple-700 rounded-[2rem] p-8 hover:-translate-y-3 transition-transform duration-300 shadow-xl shadow-indigo-500/30 border border-indigo-500 relative overflow-hidden group text-white">
                        <div class="absolute top-0 right-0 p-8 opacity-10 transform translate-x-10 -translate-y-10 transition-all duration-500">
                            <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                        </div>
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-md text-white rounded-2xl flex items-center justify-center mb-8 shadow-inner border border-white/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h4 class="text-2xl font-bold text-white mb-4 tracking-tight">Mutlaq Shaffoflik</h4>
                        <p class="text-indigo-100 text-base leading-relaxed">Lorem ipsum consectetur adipisicing elit. Nemo vero rem sit distinctio necessitatibus officia ut quasi in vulputate reprehenderit fugiat.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white rounded-[2rem] p-8 hover:-translate-y-3 transition-transform duration-300 shadow-xl shadow-gray-200/50 border border-gray-100 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-8 opacity-0 group-hover:opacity-10 transform translate-x-10 -translate-y-10 transition-all duration-500">
                            <svg class="w-32 h-32 text-purple-600" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/x></svg>
                        </div>
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-pink-100 text-purple-600 rounded-2xl flex items-center justify-center mb-8 shadow-inner border border-white">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h4 class="text-2xl font-bold text-gray-900 mb-4 tracking-tight">Elektron Hujjat</h4>
                        <p class="text-gray-600 text-base leading-relaxed">Lorem amet sit consectetur adipisicing elit. Nostrum magnam tempora dolor dolores impedit sit amet minim veniam ex ea commodo.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Workflow / Qanday ishlaydi Section -->
        <section id="workflow" class="py-32 relative z-10 w-full overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-24">
                    <h2 class="text-3xl lg:text-5xl font-black text-gray-900 tracking-tight">Jarayon Qanday Kechadi?</h2>
                </div>
                
                <div class="relative">
                    <!-- Magic gradient line for desktop -->
                    <div class="hidden md:block absolute top-[40px] left-[10%] w-[80%] h-2 bg-gray-100 rounded-full">
                        <div class="h-full w-full bg-gradient-to-r from-blue-400 via-indigo-500 to-green-400 rounded-full animate-pulse opacity-50"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 relative z-10">
                        <div class="text-center group">
                            <div class="w-20 h-20 mx-auto bg-white rounded-3xl flex items-center justify-center text-3xl font-black text-blue-600 shadow-xl shadow-blue-500/20 mb-8 border border-white group-hover:scale-110 group-hover:-rotate-6 transition-all duration-300">
                                1
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-3">Tashkilot profili</h4>
                            <p class="text-base text-gray-500 leading-relaxed px-4">Lorem ipsum dolor sit amet adis tempor incididunt labore.</p>
                        </div>

                        <div class="text-center group">
                            <div class="w-20 h-20 mx-auto bg-white rounded-3xl flex items-center justify-center text-3xl font-black text-indigo-600 shadow-xl shadow-indigo-500/20 mb-8 border border-white group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                                2
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-3">Ish o'rinlarini yuborish</h4>
                            <p class="text-base text-gray-500 leading-relaxed px-4">Ipsum lorem adipiscing elit sed do eiusmod incididunt dolor.</p>
                        </div>

                        <div class="text-center group">
                            <div class="w-20 h-20 mx-auto bg-white rounded-3xl flex items-center justify-center text-3xl font-black text-purple-600 shadow-xl shadow-purple-500/20 mb-8 border border-white group-hover:scale-110 group-hover:-rotate-6 transition-all duration-300">
                                3
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-3">Laboratoriya o'lchovi</h4>
                            <p class="text-base text-gray-500 leading-relaxed px-4">Dolor sit amet magna aliqua ut enim occaecat cupidatat.</p>
                        </div>

                        <div class="text-center group">
                            <div class="w-20 h-20 mx-auto bg-white rounded-3xl flex items-center justify-center text-3xl font-black text-green-600 shadow-xl shadow-green-500/20 mb-8 border border-white group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 bg-gradient-to-br from-green-50 to-emerald-100 border-green-200">
                                ✓
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-3">Elektron Xulosa</h4>
                            <p class="text-base text-gray-500 leading-relaxed px-4">Ut labore et dolore adisiping minima veniam quis nostrud.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer id="contact" class="bg-[#0f172a] text-gray-300 py-20 relative z-10 overflow-hidden transform-gpu border-t-8 border-indigo-600">
        <!-- Abstract footer decoration -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600 rounded-full mix-blend-multiply filter blur-[100px] opacity-20 transform translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-[100px] opacity-10 transform -translate-x-1/2 translate-y-1/2"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 border-b border-gray-800 pb-16 mb-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-8 cursor-pointer hover:opacity-80 transition-opacity">
                        <div class="w-12 h-12 bg-gradient-to-tr from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center text-white font-black text-2xl shadow-lg shadow-blue-500/20">E</div>
                        <span class="font-extrabold tracking-tight text-3xl text-white">Attestatsiya</span>
                    </div>
                    <p class="text-base text-gray-400 max-w-sm mb-6 leading-relaxed">
                        Mehnat va bandlik portali. <br>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    </p>
                    <div class="flex gap-4">
                        <!-- Social Icons Placeholder -->
                        <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-indigo-600 transition-colors cursor-pointer text-white">in</div>
                        <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-500 transition-colors cursor-pointer text-white">tw</div>
                        <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition-colors cursor-pointer text-white">fb</div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-white font-bold text-lg mb-6 tracking-wide">Tezkor Havolalar</h4>
                    <ul class="space-y-4 text-base">
                        <li><a href="#" class="text-gray-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Davlat Xaridlar Portali</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">My.gov.uz ga o'tish</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Ochiq Ma'lumotlar</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Texnik Qoidalar</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold text-lg mb-6 tracking-wide">Bog'lanish</h4>
                    <ul class="space-y-5 text-base text-gray-400">
                        <li class="flex items-start gap-4">
                            <svg class="w-6 h-6 text-indigo-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="leading-relaxed">Lorem Ipsum ko'chasi, 12-uy, Toshkent shahri, 100000, O'zbekiston</span>
                        </li>
                        <li class="flex items-center gap-4">
                            <svg class="w-6 h-6 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>+998 (71) 000-00-00</span>
                        </li>
                        <li class="flex items-center gap-4">
                            <svg class="w-6 h-6 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>info@loremipsum.uz</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-500 pt-4">
                <p>&copy; 2026 E-Attestatsiya Platformasi. Barcha huquqlar himoyalangan.</p>
                <div class="flex gap-6 mt-6 md:mt-0 font-medium">
                    <a href="#" class="hover:text-indigo-400 transition-colors">Maxfiylik Siyosati</a>
                    <a href="#" class="hover:text-indigo-400 transition-colors">Ommaviy Oferta</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
