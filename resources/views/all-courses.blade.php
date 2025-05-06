@extends('layouts.app')

@section('title', 'Danh sách tất cả khóa học - Hanzii')

@section('content')
<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Danh sách tất cả khóa học</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Chúng tôi cung cấp các khóa học tiếng Trung đa dạng từ cơ bản đến nâng cao, phù hợp với mọi đối tượng học viên.</p>
        </div>

        <!-- Bộ lọc tìm kiếm -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form action="{{ route('all-courses') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="tu_khoa" class="block text-gray-700 mb-1">Từ khóa</label>
                    <input type="text" name="tu_khoa" id="tu_khoa" value="{{ request('tu_khoa') }}" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500" placeholder="Tìm kiếm khóa học...">
                </div>

                <div>
                    <label for="sap_xep" class="block text-gray-700 mb-1">Sắp xếp</label>
                    <select name="sap_xep" id="sap_xep" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500">
                        <option value="moi_nhat" {{ request('sap_xep') == 'moi_nhat' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="gia_tang" {{ request('sap_xep') == 'gia_tang' ? 'selected' : '' }}>Giá tăng dần</option>
                        <option value="gia_giam" {{ request('sap_xep') == 'gia_giam' ? 'selected' : '' }}>Giá giảm dần</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-300">
                        <i class="fas fa-search mr-2"></i>Tìm kiếm
                    </button>
                </div>
            </form>
        </div>

        <!-- Danh sách khóa học -->
        @if($khoaHocs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-10">
                @foreach($khoaHocs as $khoaHoc)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @if($khoaHoc->hinh_anh)
                        <img src="{{ asset('storage/' . $khoaHoc->hinh_anh) }}" alt="{{ $khoaHoc->ten }}" class="w-full h-48 object-cover">
                    @else
                        <img src="https://source.unsplash.com/random/600x400/?chinese,{{ $loop->index }}" alt="{{ $khoaHoc->ten }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">{{ $khoaHoc->ten }}</h3>
                        <div class="flex items-center mb-3">
                            <i class="fas fa-clock text-gray-500 mr-2"></i>
                            <span class="text-gray-600">{{ $khoaHoc->thoi_gian_hoan_thanh }}</span>
                        </div>
                        <p class="text-gray-600 mb-4">{{ Str::limit($khoaHoc->mo_ta, 100) }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-red-600 font-bold">{{ number_format($khoaHoc->hoc_phi, 0, ',', '.') }}đ</span>
                            <a href="{{ route('course.show', $khoaHoc->id) }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-300">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Phân trang -->
            <div class="mt-8">
                {{ $khoaHocs->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-10">
                <p class="text-gray-600 mb-4">Không tìm thấy khóa học nào phù hợp với điều kiện tìm kiếm.</p>
                <a href="{{ route('all-courses') }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-300">Xem tất cả khóa học</a>
            </div>
        @endif
    </div>
</div>

<!-- Đăng ký tư vấn -->
<div class="bg-red-600 py-16">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-8">Đăng ký nhận tư vấn miễn phí</h2>
        <p class="text-white mb-8 max-w-2xl mx-auto">Để lại thông tin của bạn, chúng tôi sẽ liên hệ và tư vấn khóa học phù hợp nhất với nhu cầu của bạn.</p>
        <a href="#lien-he" class="px-8 py-3 bg-white text-red-600 font-bold rounded-lg hover:bg-gray-100 transition duration-300 inline-block">Đăng ký ngay</a>
    </div>
</div>
@endsection 