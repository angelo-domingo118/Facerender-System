<div class="min-h-screen flex flex-col sm:justify-center items-center relative py-12 sm:py-0">
    <!-- Animated Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-[#2C3E50] to-[#34495E] overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-[#3498DB]/30 blur-3xl"></div>
            <div class="absolute top-[20%] -right-[5%] w-[35%] h-[50%] rounded-full bg-[#E74C3C]/20 blur-3xl"></div>
            <div class="absolute -bottom-[10%] left-[20%] w-[50%] h-[30%] rounded-full bg-[#3498DB]/25 blur-3xl"></div>
        </div>
    </div>

    <div class="mb-6 relative z-10 transform transition-all duration-500 hover:scale-105">
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md relative z-10">
        <div class="bg-white rounded-lg shadow-[0_15px_35px_rgba(0,0,0,0.2)] border border-white/10 p-8 transform transition-all duration-500 hover:shadow-[0_20px_35px_rgba(0,0,0,0.3)]">
            {{ $slot }}
        </div>
    </div>

    <!-- Wave Shape Bottom -->
    <div class="absolute bottom-0 left-0 right-0 h-16 bg-white/10 backdrop-blur-sm -z-10">
        <svg class="absolute bottom-0 left-0 right-0 h-16 w-full" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#ffffff" fill-opacity="0.05" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,208C1248,192,1344,192,1392,192L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</div>
