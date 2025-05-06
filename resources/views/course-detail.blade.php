@extends('layouts.app')

@section('title', "{$khoaHoc->ten} - Hanzii")

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <!-- Điều hướng -->
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-red-600">
                            <i class="fas fa-home mr-2"></i>Trang chủ
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="{{ route('all-courses') }}" class="text-gray-600 hover:text-red-600">Khóa học</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-500">{{ $khoaHoc->ten }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Thông tin khóa học -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-10">
            <div class="md:flex">
                <div class="md:w-1/2">
                    @if($khoaHoc->hinh_anh)
                        <img src="{{ asset('storage/' . $khoaHoc->hinh_anh) }}" alt="{{ $khoaHoc->ten }}" class="w-full h-96 object-cover">
                    @else
                        <img src="https://source.unsplash.com/random/800x600/?chinese,language" alt="{{ $khoaHoc->ten }}" class="w-full h-96 object-cover">
                    @endif
                </div>
                <div class="md:w-1/2 p-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $khoaHoc->ten }}</h1>
                    
                    <div class="flex items-center mb-4">
                        <div class="bg-red-100 text-red-800 rounded-full px-3 py-1 text-sm mr-3">
                            <i class="fas fa-graduation-cap mr-1"></i> Khóa học tiếng Trung
                        </div>
                        <div class="text-gray-600">
                            <i class="fas fa-users mr-1"></i> {{ rand(20, 100) }} học viên đã đăng ký
                        </div>
                    </div>
                    
                    <div class="flex items-center mb-6">
                        <div class="text-yellow-500 flex mr-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <span class="text-gray-600">({{ rand(10, 50) }} đánh giá)</span>
                    </div>
                    
                    <div class="mb-6">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-clock text-gray-600 mr-3 w-5 text-center"></i>
                            <span>{{ $khoaHoc->thoi_gian_hoan_thanh }}</span>
                        </div>
                        <div class="flex items-center mb-2">
                            <i class="fas fa-book text-gray-600 mr-3 w-5 text-center"></i>
                            <span>{{ $khoaHoc->tong_so_bai }} bài học</span>
                        </div>
                        <div class="flex items-center mb-2">
                            <i class="fas fa-certificate text-gray-600 mr-3 w-5 text-center"></i>
                            <span>Cấp chứng chỉ sau khi hoàn thành</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center mb-8">
                        <div class="text-3xl font-bold text-red-600 mr-4">{{ number_format($khoaHoc->hoc_phi, 0, ',', '.') }}đ</div>
                    </div>
                    
                    <div class="flex flex-col space-y-3 md:space-y-0 md:flex-row md:space-x-4">
                        @if(Auth::check())
                            <a href="{{ route('hoc-vien.khoa-hoc.show', $khoaHoc->id) }}" class="px-6 py-3 bg-red-600 text-white rounded text-center hover:bg-red-700 transition duration-300">
                                <i class="fas fa-info-circle mr-2"></i>Xem chi tiết đầy đủ
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-3 bg-red-600 text-white rounded text-center hover:bg-red-700 transition duration-300">
                                <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập để đăng ký
                            </a>
                        @endif
                        <a href="#lop-hoc" class="px-6 py-3 border border-red-600 text-red-600 rounded text-center hover:bg-red-50 transition duration-300">
                            <i class="fas fa-list-ul mr-2"></i>Xem lớp học đang mở
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mô tả khóa học -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Mô tả khóa học</h2>
            <div class="prose prose-red max-w-none">
                {!! $khoaHoc->mo_ta ?? 'Chưa có mô tả chi tiết cho khóa học này.' !!}
            </div>
        </div>

        <!-- Lớp học đang mở -->
        <div id="lop-hoc" class="bg-white rounded-lg shadow-md p-8 mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Lớp học đang mở</h2>
            
            @if($lopHocMo->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Tên lớp</th>
                                <th class="py-3 px-6 text-left">Hình thức học</th>
                                <th class="py-3 px-6 text-left">Lịch học</th>
                                <th class="py-3 px-6 text-left">Ngày khai giảng</th>
                                <th class="py-3 px-6 text-left">Giáo viên</th>
                                <th class="py-3 px-6 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm">
                            @foreach($lopHocMo as $lop)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left">{{ $lop->ten }}</td>
                                    <td class="py-3 px-6 text-left">
                                        @if($lop->hinh_thuc_hoc == 'online')
                                            <span class="bg-blue-100 text-blue-800 rounded-full px-3 py-1 text-xs">Trực tuyến</span>
                                        @else
                                            <span class="bg-green-100 text-green-800 rounded-full px-3 py-1 text-xs">Tại trung tâm</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-left">{{ $lop->lich_hoc }}</td>
                                    <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($lop->ngay_bat_dau)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-6 text-left">
                                        @if($lop->giaoVien && $lop->giaoVien->nguoiDung)
                                            {{ $lop->giaoVien->nguoiDung->ho }} {{ $lop->giaoVien->nguoiDung->ten }}
                                        @else
                                            Chưa phân công
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        @if(Auth::check())
                                            <a href="{{ route('hoc-vien.lop-hoc.show', $lop->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                                                Xem chi tiết
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                                                Đăng nhập để đăng ký
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Hiện chưa có lớp nào đang mở đăng ký cho khóa học này. Vui lòng liên hệ với chúng tôi để biết thêm chi tiết hoặc đăng ký nhận thông báo khi có lớp mới.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Khóa học liên quan -->
        @if($khoaHocLienQuan->count() > 0)
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Khóa học liên quan</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($khoaHocLienQuan as $khoaHocLQ)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            @if($khoaHocLQ->hinh_anh)
                                <img src="{{ asset('storage/' . $khoaHocLQ->hinh_anh) }}" alt="{{ $khoaHocLQ->ten }}" class="w-full h-40 object-cover">
                            @else
                                <img src="https://source.unsplash.com/random/600x400/?chinese,{{ $loop->index }}" alt="{{ $khoaHocLQ->ten }}" class="w-full h-40 object-cover">
                            @endif
                            <div class="p-4">
                                <h3 class="text-lg font-semibold mb-2">{{ $khoaHocLQ->ten }}</h3>
                                <div class="flex justify-between items-center">
                                    <span class="text-red-600 font-bold">{{ number_format($khoaHocLQ->hoc_phi, 0, ',', '.') }}đ</span>
                                    <a href="{{ route('course.show', $khoaHocLQ->id) }}" class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700 transition duration-300">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Đăng ký tư vấn -->
<div class="bg-red-600 py-16">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-8">Đăng ký nhận tư vấn miễn phí</h2>
        <p class="text-white mb-8 max-w-2xl mx-auto">Để lại thông tin của bạn, chúng tôi sẽ liên hệ và tư vấn khóa học phù hợp nhất với nhu cầu của bạn.</p>
        <a href="{{ route('welcome') }}#lien-he" class="px-8 py-3 bg-white text-red-600 font-bold rounded-lg hover:bg-gray-100 transition duration-300 inline-block">Đăng ký ngay</a>
    </div>
</div>
@endsection 