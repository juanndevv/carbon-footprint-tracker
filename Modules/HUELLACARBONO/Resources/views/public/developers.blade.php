@extends('huellacarbono::layouts.master')

@section('content')
<!-- Banner título (igual que página principal: imagen de fondo + overlay) -->
<div class="relative overflow-hidden h-[320px] min-h-[320px]">
    <div class="absolute inset-0 z-0">
        <img src="https://images.pexels.com/photos/577585/pexels-photo-577585.jpeg?auto=compress&cs=tinysrgb&w=1600" alt="" class="w-full h-full object-cover" loading="lazy">
        <div class="absolute inset-0 bg-gradient-to-r from-green-900/80 to-emerald-900/80"></div>
    </div>
    <div class="absolute inset-0 z-10 flex items-center justify-center max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 left-0 right-0">
        <div class="text-center text-white">
            <i class="fas fa-code text-7xl mb-6 drop-shadow-lg"></i>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4 drop-shadow-lg">Desarrolladores y Herramientas</h1>
            <p class="text-xl opacity-90 drop-shadow-md">Equipo y tecnologías del proyecto Huella de Carbono</p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Cards de Juan Manuel y Evelin (primero, lado a lado) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-12">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border-2 border-green-100">
            <div class="p-10 text-center">
                <img src="{{ asset('images/huellacarbono-desarrolladores/juan-manuel.jpg') }}" 
                     alt="Juan Manuel Mosquera Vargas" 
                     class="w-56 h-56 rounded-full mx-auto object-cover border-4 border-green-100 shadow-lg"
                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Juan+Manuel+Mosquera+Vargas&size=280&background=10b981&color=fff&bold=true';">
                <h3 class="mt-6 text-2xl font-bold text-gray-900">Juan Manuel Mosquera Vargas</h3>
                <p class="mt-3 text-base font-medium text-green-700">Tgo. Análisis y desarrollo de software</p>
                <div class="mt-6 flex justify-center gap-5">
                    <a href="#" class="text-gray-400 hover:text-green-600 transition" title="LinkedIn"><i class="fab fa-linkedin text-3xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-gray-800 transition" title="GitHub"><i class="fab fa-github text-3xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-green-500 transition" title="WhatsApp"><i class="fab fa-whatsapp text-3xl"></i></a>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border-2 border-green-100">
            <div class="p-10 text-center">
                <img src="{{ asset('images/huellacarbono-desarrolladores/evelin-yorely.jpg') }}" 
                     alt="Evelin Yorely Burgos Mahecha" 
                     class="w-56 h-56 rounded-full mx-auto object-cover border-4 border-green-100 shadow-lg"
                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=Evelin+Yorely+Burgos+Mahecha&size=280&background=059669&color=fff&bold=true';">
                <h3 class="mt-6 text-2xl font-bold text-gray-900">Evelin Yorely Burgos Mahecha</h3>
                <p class="mt-3 text-base font-medium text-green-700">Tgo. Análisis y desarrollo de software</p>
                <div class="mt-6 flex justify-center gap-5">
                    <a href="#" class="text-gray-400 hover:text-green-600 transition" title="LinkedIn"><i class="fab fa-linkedin text-3xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-gray-800 transition" title="GitHub"><i class="fab fa-github text-3xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-green-500 transition" title="WhatsApp"><i class="fab fa-whatsapp text-3xl"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Herramientas (abajo, ancho completo) -->
    <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-green-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-wrench text-green-600 mr-3"></i>
                    Herramientas
                </h2>
                <p class="text-gray-600 mb-8">Lenguajes y tecnologías utilizados en el desarrollo de este proyecto.</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <div class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-green-50 border border-gray-100 hover:border-green-200 transition">
                        <i class="fab fa-laravel text-4xl text-amber-600 mb-2"></i>
                        <span class="text-sm font-semibold text-gray-800">Laravel</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-green-50 border border-gray-100 hover:border-green-200 transition">
                        <i class="fab fa-php text-4xl text-teal-600 mb-2"></i>
                        <span class="text-sm font-semibold text-gray-800">PHP</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-green-50 border border-gray-100 hover:border-green-200 transition">
                        <i class="fab fa-github text-4xl text-gray-800 mb-2"></i>
                        <span class="text-sm font-semibold text-gray-800">GitHub</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-green-50 border border-gray-100 hover:border-green-200 transition">
                        <i class="fab fa-js-square text-4xl text-yellow-500 mb-2"></i>
                        <span class="text-sm font-semibold text-gray-800">JavaScript</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-green-50 border border-gray-100 hover:border-green-200 transition">
                        <i class="fab fa-font-awesome text-4xl text-blue-600 mb-2"></i>
                        <span class="text-sm font-semibold text-gray-800">Font Awesome</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-green-50 border border-gray-100 hover:border-green-200 transition">
                        <i class="fab fa-bootstrap text-4xl text-emerald-600 mb-2"></i>
                        <span class="text-sm font-semibold text-gray-800">Bootstrap</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-green-50 border border-gray-100 hover:border-green-200 transition">
                        <i class="fab fa-css3-alt text-4xl text-blue-500 mb-2"></i>
                        <span class="text-sm font-semibold text-gray-800">CSS3</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-green-50 border border-gray-100 hover:border-green-200 transition">
                        <i class="fas fa-database text-4xl text-blue-700 mb-2"></i>
                        <span class="text-sm font-semibold text-gray-800">MySQL</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-green-50 border border-gray-100 hover:border-green-200 transition">
                        <i class="fas fa-code text-4xl text-green-600 mb-2"></i>
                        <span class="text-sm font-semibold text-gray-800">jQuery</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-green-50 border border-gray-100 hover:border-green-200 transition">
                        <i class="fas fa-palette text-4xl text-cyan-500 mb-2"></i>
                        <span class="text-sm font-semibold text-gray-800">Tailwind CSS</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-green-50 border border-gray-100 hover:border-green-200 transition">
                        <i class="fas fa-font text-4xl text-gray-600 mb-2"></i>
                        <span class="text-sm font-semibold text-gray-800">Google Fonts</span>
                    </div>
                    <div class="flex flex-col items-center p-4 rounded-xl bg-gray-50 hover:bg-green-50 border border-gray-100 hover:border-green-200 transition">
                        <i class="fas fa-desktop text-4xl text-blue-600 mb-2"></i>
                        <span class="text-sm font-semibold text-gray-800">VS Code</span>
                    </div>
                </div>
            </div>
</div>
@endsection
