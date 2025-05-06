<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Hanzii - Trung tâm đào tạo tiếng Trung chất lượng cao</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Roboto', sans-serif;
            }
            .hero-pattern {
                background-color: #f9fafb;
                background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23e53e3e' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            }
        </style>
    </head>
    <body class="antialiased">
        <!-- Thông báo thành công -->
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 fixed top-20 right-4 z-50 shadow-lg rounded" id="success-alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="w-6 h-6 mr-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <p>{{ session('success') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none" onclick="document.getElementById('success-alert').style.display='none'">
                            <span class="sr-only">Đóng</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            setTimeout(function() {
                var alert = document.getElementById('success-alert');
                if (alert) {
                    alert.style.display = 'none';
                }
            }, 5000);
        </script>
        @endif

        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-red-600">
                        <i class="fas fa-book-open mr-2"></i>Hanzii
                    </h1>
                    <span class="ml-2 text-sm text-gray-600">Trung tâm tiếng Trung</span>
                </div>
                <nav class="hidden md:flex space-x-8">
                    <a href="#" class="text-gray-800 hover:text-red-600">Trang chủ</a>
                    <a href="#khoa-hoc" class="text-gray-800 hover:text-red-600">Khóa học</a>
                    <a href="#gioi-thieu" class="text-gray-800 hover:text-red-600">Giới thiệu</a>
                    <a href="#lien-he" class="text-gray-800 hover:text-red-600">Liên hệ</a>
                </nav>
                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('hoc-vien.dashboard') }}" class="px-4 py-2 border border-red-600 text-red-600 rounded hover:bg-red-600 hover:text-white transition duration-300">
                                Vào học
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-800 hover:text-red-600">Đăng nhập</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-300">Đăng ký</a>
                            @endif
                        @endauth
                    @endif
                </div>
                <button class="md:hidden focus:outline-none">
                    <i class="fas fa-bars text-gray-800"></i>
                </button>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero-pattern py-20">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="md:w-1/2 mb-10 md:mb-0">
                        <h2 class="text-4xl font-bold text-gray-800 mb-4">Học tiếng Trung hiệu quả cùng Hanzii</h2>
                        <p class="text-lg text-gray-600 mb-8">Khám phá cách học tiếng Trung hiện đại, tương tác và hiệu quả với đội ngũ giáo viên chuyên nghiệp và phương pháp đã được chứng minh.</p>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="#khoa-hoc" class="px-6 py-3 bg-red-600 text-white rounded-lg text-center hover:bg-red-700 transition duration-300">
                                Khám phá khóa học
                            </a>
                            <a href="#lien-he" class="px-6 py-3 border border-red-600 text-red-600 rounded-lg text-center hover:bg-red-600 hover:text-white transition duration-300">
                                Tư vấn miễn phí
                            </a>
                        </div>
                    </div>
                    <div class="md:w-1/2">
                    <img src="{{ asset('storage/background.jpg') }}" alt="Lớp học tiếng Trung" class="rounded-lg shadow-xl">
                    </div>
                </div>
            </div>
        </section>

        <!-- Giới thiệu trung tâm -->
        <section id="gioi-thieu" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Về Hanzii</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Trung tâm tiếng Trung Hanzii cung cấp chương trình đào tạo chất lượng cao với phương pháp giảng dạy hiện đại và hiệu quả.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-gray-50 p-8 rounded-lg shadow-md">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-user-graduate text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Giáo viên chất lượng</h3>
                        <p class="text-gray-600">Đội ngũ giáo viên có kinh nghiệm, nhiệt tình, đa phần đã học tập và sinh sống tại Trung Quốc.</p>
                    </div>
                    
                    <div class="bg-gray-50 p-8 rounded-lg shadow-md">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-book text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Giáo trình chính thống</h3>
                        <p class="text-gray-600">Giáo trình được biên soạn kỹ lưỡng, chuẩn HSK và phù hợp với người Việt Nam học tiếng Trung.</p>
                    </div>
                    
                    <div class="bg-gray-50 p-8 rounded-lg shadow-md">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-chart-line text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Theo dõi tiến độ</h3>
                        <p class="text-gray-600">Hệ thống theo dõi tiến độ học tập giúp học viên và phụ huynh nắm rõ kết quả và quá trình học tập.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Khóa học -->
        <section id="khoa-hoc" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Khóa học nổi bật</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Chúng tôi cung cấp các khóa học từ cơ bản đến nâng cao, phù hợp với mọi lứa tuổi và mục tiêu.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="https://source.unsplash.com/random/600x400/?chinese,language" alt="HSK 1-2" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">Tiếng Trung cơ bản (HSK 1-2)</h3>
                            <div class="flex items-center mb-3">
                                <i class="fas fa-clock text-gray-500 mr-2"></i>
                                <span class="text-gray-600">3 tháng - 36 buổi</span>
                            </div>
                            <p class="text-gray-600 mb-4">Khóa học dành cho người mới bắt đầu, giúp xây dựng nền tảng vững chắc với 300 từ vựng cơ bản và giao tiếp đơn giản.</p>
                            <div class="flex justify-between items-center">
                                <span class="text-red-600 font-bold">2.500.000đ</span>
                                <a href="#" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-300">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="https://source.unsplash.com/random/600x400/?chinese,study" alt="HSK 3-4" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">Tiếng Trung trung cấp (HSK 3-4)</h3>
                            <div class="flex items-center mb-3">
                                <i class="fas fa-clock text-gray-500 mr-2"></i>
                                <span class="text-gray-600">6 tháng - 72 buổi</span>
                            </div>
                            <p class="text-gray-600 mb-4">Nâng cao kỹ năng giao tiếp, phù hợp cho người đã có nền tảng cơ bản, mục tiêu 1200 từ vựng và giao tiếp lưu loát.</p>
                            <div class="flex justify-between items-center">
                                <span class="text-red-600 font-bold">4.500.000đ</span>
                                <a href="#" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-300">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="https://source.unsplash.com/random/600x400/?chinese,business" alt="HSK 5-6" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">Tiếng Trung cao cấp (HSK 5-6)</h3>
                            <div class="flex items-center mb-3">
                                <i class="fas fa-clock text-gray-500 mr-2"></i>
                                <span class="text-gray-600">8 tháng - 96 buổi</span>
                            </div>
                            <p class="text-gray-600 mb-4">Dành cho người muốn thành thạo tiếng Trung ở mức độ cao cấp, đạt trình độ gần người bản xứ với 5000+ từ vựng.</p>
                            <div class="flex justify-between items-center">
                                <span class="text-red-600 font-bold">6.500.000đ</span>
                                <a href="#" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-300">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-10">
                    <a href="{{ route('all-courses') }}" class="px-6 py-3 border border-red-600 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition duration-300">
                        Xem tất cả khóa học
                    </a>
                </div>
            </div>
        </section>

       
        <!-- Liên hệ -->
        <section id="lien-he" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Liên hệ với chúng tôi</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Bạn có thắc mắc? Hãy liên hệ với chúng tôi để được tư vấn miễn phí về các khóa học.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-gray-50 p-6 rounded-lg shadow-md text-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-map-marker-alt text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Địa chỉ</h3>
                        <p class="text-gray-600">123 Đường Láng, Đống Đa, Hà Nội</p>
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-lg shadow-md text-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-phone-alt text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Điện thoại</h3>
                        <p class="text-gray-600">+84 123 456 789</p>
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-lg shadow-md text-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-envelope text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Email</h3>
                        <p class="text-gray-600">info@hanzii.vn</p>
                    </div>
                </div>
                
                <div class="mt-12 bg-gray-50 p-8 rounded-lg shadow-md">
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    
                    <form action="{{ route('lien-he.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="ho_ten" class="block text-gray-700 mb-2">Họ và tên</label>
                                <input type="text" id="ho_ten" name="ho_ten" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 @error('ho_ten') border-red-500 @enderror" value="{{ old('ho_ten') }}">
                                @error('ho_ten')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 @error('email') border-red-500 @enderror" value="{{ old('email') }}">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-6">
                            <label for="chu_de" class="block text-gray-700 mb-2">Chủ đề</label>
                            <input type="text" id="chu_de" name="chu_de" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 @error('chu_de') border-red-500 @enderror" value="{{ old('chu_de') }}">
                            @error('chu_de')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="noi_dung" class="block text-gray-700 mb-2">Tin nhắn</label>
                            <textarea id="noi_dung" name="noi_dung" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 @error('noi_dung') border-red-500 @enderror">{{ old('noi_dung') }}</textarea>
                            @error('noi_dung')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded hover:bg-red-700 transition duration-300">
                            Gửi tin nhắn
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-12">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h3 class="text-xl font-bold mb-4">Hanzii</h3>
                        <p class="text-gray-400 mb-4">Trung tâm đào tạo tiếng Trung hàng đầu với phương pháp giảng dạy hiện đại và hiệu quả.</p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-youtube"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Khóa học</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">HSK 1-2 (Sơ cấp)</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">HSK 3-4 (Trung cấp)</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">HSK 5-6 (Cao cấp)</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Tiếng Trung giao tiếp</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Tiếng Trung thương mại</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Hỗ trợ</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">Hướng dẫn đăng ký</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Câu hỏi thường gặp</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Điều khoản sử dụng</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Chính sách bảo mật</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Liên hệ</h3>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <i class="fas fa-map-marker-alt mt-1 mr-3 text-red-500"></i>
                                <span class="text-gray-400">123 Đường Láng, Đống Đa, Hà Nội</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-phone-alt mt-1 mr-3 text-red-500"></i>
                                <span class="text-gray-400">+84 123 456 789</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-envelope mt-1 mr-3 text-red-500"></i>
                                <span class="text-gray-400">info@hanzii.vn</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} Hanzii. Tất cả các quyền được bảo lưu.</p>
                </div>
            </div>
        </footer>

        <script>
            // Xử lý menu mobile
            document.querySelector('button.md\\:hidden').addEventListener('click', function() {
                const nav = document.querySelector('nav');
                nav.classList.toggle('hidden');
            });
        </script>
    </body>
</html>
