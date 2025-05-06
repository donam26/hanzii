@extends('layouts.app')

@section('title', 'Liên hệ - Hanzii')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-red-600">
                            <i class="fas fa-home mr-2"></i>Trang chủ
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-500">Liên hệ</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Liên hệ với chúng tôi</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Bạn có thắc mắc? Hãy liên hệ với chúng tôi để được tư vấn miễn phí về các khóa học.</p>
        </div>
        
        <!-- Thông tin liên hệ -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-red-600"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">Địa chỉ</h3>
                <p class="text-gray-600">123 Đường Láng, Đống Đa, Hà Nội</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-phone-alt text-red-600"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">Điện thoại</h3>
                <p class="text-gray-600">+84 123 456 789</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-red-600"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2">Email</h3>
                <p class="text-gray-600">info@hanzii.vn</p>
            </div>
        </div>

        <!-- Form liên hệ -->
        <div class="bg-white p-8 rounded-lg shadow-md mb-12">
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
                        <input type="text" id="ho_ten" name="ho_ten" value="{{ old('ho_ten') }}" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 @error('ho_ten') border-red-500 @enderror">
                        @error('ho_ten')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mb-6">
                    <label for="chu_de" class="block text-gray-700 mb-2">Chủ đề</label>
                    <input type="text" id="chu_de" name="chu_de" value="{{ old('chu_de') }}" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 @error('chu_de') border-red-500 @enderror">
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

        <!-- Bản đồ -->
        <div class="rounded-lg overflow-hidden shadow-md">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.096947032564!2d105.80254491493297!3d21.028362485998228!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab424a50fff9%3A0xbe3a5fc10cb25e24!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBLaG9hIGjhu41jIFThu7Egbmhpw6puIC0gxJDhuqFpIGjhu41jIFF14buRYyBnaWEgSMOgIE7hu5lp!5e0!3m2!1svi!2s!4v1620458383284!5m2!1svi!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</div>
@endsection 