<header class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('welcome') }}" class="text-2xl font-bold text-red-600">
                    {{ config('app.name', 'Trung tâm Tiếng Trung') }}
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('welcome') }}" class="text-gray-700 hover:text-red-600 py-2">Trang chủ</a>
                <a href="{{ route('all-courses') }}" class="text-gray-700 hover:text-red-600 py-2">Khóa học</a>
                <a href="{{ route('lien-he') }}" class="text-gray-700 hover:text-red-600 py-2">Liên hệ</a>
                
                @auth
                    @if(auth()->user()->loai_tai_khoan == 'hoc_vien')
                        <a href="{{ route('hoc-vien.dashboard') }}" class="text-gray-700 hover:text-red-600 py-2">Dashboard</a>
                        <a href="{{ route('hoc-vien.khoa-hoc.index') }}" class="text-gray-700 hover:text-red-600 py-2">Khóa học</a>
                        <a href="{{ route('hoc-vien.lop-hoc.index') }}" class="text-gray-700 hover:text-red-600 py-2">Lớp học</a>
                        <a href="{{ route('hoc-vien.ket-qua.index') }}" class="text-gray-700 hover:text-red-600 py-2">Kết quả học tập</a>
                    @elseif(auth()->user()->loai_tai_khoan == 'giao_vien')
                        @if(auth()->user()->vaiTros->contains('ten', 'quan_tri'))
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-red-600 py-2">Dashboard</a>
                            <a href="{{ route('admin.khoa-hoc.index') }}" class="text-gray-700 hover:text-red-600 py-2">Quản lý khóa học</a>
                            <a href="{{ route('admin.lop-hoc.index') }}" class="text-gray-700 hover:text-red-600 py-2">Quản lý lớp học</a>
                            <a href="{{ route('admin.nguoi-dung.index') }}" class="text-gray-700 hover:text-red-600 py-2">Quản lý người dùng</a>
                        @elseif(auth()->user()->vaiTros->contains('ten', 'giao_vien'))
                            <a href="{{ route('giao-vien.dashboard') }}" class="text-gray-700 hover:text-red-600 py-2">Dashboard</a>
                            <a href="{{ route('giao-vien.lop-hoc.index') }}" class="text-gray-700 hover:text-red-600 py-2">Lớp giảng dạy</a>
                            <a href="{{ route('giao-vien.hoc-vien.index') }}" class="text-gray-700 hover:text-red-600 py-2">Học viên</a>
                        @elseif(auth()->user()->vaiTros->contains('ten', 'tro_giang'))
                            <a href="{{ route('tro-giang.dashboard') }}" class="text-gray-700 hover:text-red-600 py-2">Dashboard</a>
                            <a href="{{ route('tro-giang.lop-hoc.index') }}" class="text-gray-700 hover:text-red-600 py-2">Lớp học</a>
                        @endif
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-red-600 py-2">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="text-gray-700 hover:text-red-600 py-2">Đăng ký</a>
                @endauth
            </nav>

            <!-- User Menu -->
            @auth
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                        <span class="hidden md:inline-block">{{ auth()->user()->ho }} {{ auth()->user()->ten }}</span>
                        @if(session('anh_dai_dien'))
                            <img src="{{ session('anh_dai_dien') }}" alt="Avatar" class="h-8 w-8 rounded-full object-cover">
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                        @if(auth()->user()->loai_tai_khoan == 'hoc_vien')
                            <a href="{{ route('hoc-vien.profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Thông tin cá nhân</a>
                        @elseif(auth()->user()->loai_tai_khoan == 'nhan_vien')
                            <a href="{{ route('nhan-vien.profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Thông tin cá nhân</a>
                        @elseif(auth()->user()->loai_tai_khoan == 'giao_vien')
                            <a href="{{ route('giao-vien.profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Thông tin cá nhân</a>
                        @elseif(auth()->user()->vaiTros && auth()->user()->vaiTros->contains('ten', 'tro_giang'))
                            <a href="{{ route('tro-giang.profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Thông tin cá nhân</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            @endauth

            <!-- Mobile Menu Button -->
            <div class="md:hidden" x-data="{ open: false }">
                <button @click="open = !open" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Mobile Menu -->
                <div x-show="open" @click.away="open = false" class="absolute top-20 right-0 left-0 bg-white shadow-md z-50">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <a href="{{ route('welcome') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Trang chủ</a>
                        <a href="{{ route('all-courses') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Khóa học</a>
                        <a href="{{ route('lien-he') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Liên hệ</a>
                        
                        @auth
                            @if(auth()->user()->loai_tai_khoan == 'hoc_vien')
                                <a href="{{ route('hoc-vien.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Dashboard</a>
                                <a href="{{ route('hoc-vien.khoa-hoc.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Khóa học</a>
                                <a href="{{ route('hoc-vien.lop-hoc.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Lớp học</a>
                                <a href="{{ route('hoc-vien.ket-qua.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Kết quả học tập</a>
                                <a href="{{ route('hoc-vien.profile.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Thông tin cá nhân</a>
                            @elseif(auth()->user()->loai_tai_khoan == 'nhan_vien')
                                @if(auth()->user()->vaiTros->contains('ten', 'quan_tri'))
                                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Dashboard</a>
                                    <a href="{{ route('admin.khoa-hoc.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Quản lý khóa học</a>
                                    <a href="{{ route('admin.lop-hoc.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Quản lý lớp học</a>
                                    <a href="{{ route('admin.nguoi-dung.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Quản lý người dùng</a>
                                @elseif(auth()->user()->vaiTros->contains('ten', 'giao_vien'))
                                    <a href="{{ route('giao-vien.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Dashboard</a>
                                    <a href="{{ route('giao-vien.lop-hoc.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Lớp giảng dạy</a>
                                    <a href="{{ route('giao-vien.hoc-vien.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Học viên</a>
                                @elseif(auth()->user()->vaiTros->contains('ten', 'tro_giang'))
                                    <a href="{{ route('tro-giang.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Dashboard</a>
                                    <a href="{{ route('tro-giang.lop-hoc.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Lớp học</a>
                                @endif
                                <a href="{{ route('nhan-vien.profile.show') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Thông tin cá nhân</a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Đăng xuất</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Đăng nhập</a>
                            <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gray-50">Đăng ký</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</header> 