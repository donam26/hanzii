@extends('layouts.dashboard')

@section('title', 'Chi tiết lớp học')
@section('page-heading', $lopHoc->ten)

@php
    $active = 'lop-hoc';
    $role = 'giao_vien';
@endphp

@section('content')
    <!-- Thông tin lớp học -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('giao-vien.lop-hoc.index') }}" class="text-red-600 hover:text-red-800 mr-2">
                <i class="fas fa-arrow-left"></i> Danh sách lớp
            </a>
        </div>
        
        <div class="bg-white shadow overflow-hidden rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Thông tin lớp học
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        {{ $lopHoc->khoaHoc->ten }}
                    </p>
                </div>
                <div>
                    @php
                        $statusClass = '';
                        $statusText = '';
                        
                        if ($lopHoc->ngay_bat_dau > now()) {
                            $statusClass = 'bg-yellow-100 text-yellow-800';
                            $statusText = 'Sắp diễn ra';
                        } elseif ($lopHoc->ngay_ket_thuc > now()) {
                            $statusClass = 'bg-green-100 text-green-800';
                            $statusText = 'Đang diễn ra';
                        } else {
                            $statusClass = 'bg-gray-100 text-gray-800';
                            $statusText = 'Đã kết thúc';
                        }
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                        {{ $statusText }}
                    </span>
                </div>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Ngày bắt đầu
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Ngày kết thúc
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ \Carbon\Carbon::parse($lopHoc->ngay_ket_thuc)->format('d/m/Y') }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Số buổi học
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $lopHoc->so_buoi }} buổi
                        </dd>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Hình thức học
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $lopHoc->hinh_thuc == 'online' ? 'Trực tuyến' : 'Tại trung tâm' }}
                            @if($lopHoc->hinh_thuc == 'online' && $lopHoc->link_meeting)
                                <a href="{{ $lopHoc->link_meeting }}" target="_blank" class="ml-2 text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-video"></i> Link học trực tuyến
                                </a>
                            @endif
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Lịch học
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $lopHoc->lich_hoc }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Tiến độ giảng dạy
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-red-600 h-2.5 rounded-full" style="width: {{ $lopHoc->tienDo() }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 mt-1 inline-block">{{ $lopHoc->tienDo() }}% hoàn thành</span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Công cụ quản lý lớp học -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="h-12 w-12 rounded-md bg-red-100 flex items-center justify-center">
                    <i class="fas fa-users text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Học viên</h3>
                    <p class="text-sm text-gray-500">Quản lý danh sách học viên</p>
                </div>
            </div>
            <div class="mt-6">
                <a href="{{ route('giao-vien.lop-hoc.danh-sach-hoc-vien', $lopHoc->id) }}" class="w-full block bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md text-center">
                    Xem danh sách
                </a>
            </div>
        </div>


        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="h-12 w-12 rounded-md bg-green-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Tiến độ & Kết quả</h3>
                    <p class="text-sm text-gray-500">Xem tiến độ và kết quả học tập</p>
                </div>
            </div>
            <div class="mt-6">
                <a href="{{ route('giao-vien.lop-hoc.ket-qua', $lopHoc->id) }}" class="w-full block bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md text-center">
                    Xem kết quả
                </a>
            </div>
        </div>
    </div>

    <!-- Danh sách bài học -->
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Danh sách bài học</h3>
            <a href="{{ route('giao-vien.bai-hoc.index', ['lop_hoc_id' => $lopHoc->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                Xem tất cả <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </div>
        <div class="px-6 py-4">
            @forelse($baiHocs as $baiHoc)
                <div class="border-b border-gray-200 py-4 last:border-b-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            @if($baiHoc->da_hoan_thanh)
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-check text-green-600"></i>
                                </div>
                            @else
                                <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-book text-gray-600"></i>
                                </div>
                            @endif
                            <div>
                                <h4 class="text-base font-medium text-gray-900">{{ $baiHoc->ten }}</h4>
                                <p class="text-sm text-gray-500">
                                    <span class="mr-3"><i class="far fa-clock mr-1"></i> {{ $baiHoc->thoi_luong }} phút</span>
                                    <span><i class="far fa-calendar-alt mr-1"></i> {{ $baiHoc->ngay_day ? \Carbon\Carbon::parse($baiHoc->ngay_day)->format('d/m/Y') : 'Chưa có' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if($baiHoc->ngay_day && $baiHoc->ngay_day <= now())
                                <a href="{{ route('giao-vien.bai-hoc.show', $baiHoc->id) }}" class="inline-flex items-center py-1.5 px-3 rounded-md bg-blue-100 text-blue-700 text-sm">
                                    <i class="fas fa-eye mr-1"></i> Xem
                                </a>
                                <a href="{{ route('giao-vien.bai-tap.index', ['bai_hoc_id' => $baiHoc->id]) }}" class="inline-flex items-center py-1.5 px-3 rounded-md bg-purple-100 text-purple-700 text-sm">
                                    <i class="fas fa-tasks mr-1"></i> Bài tập
                                </a>
                            @else
                                <a href="{{ route('giao-vien.bai-hoc.show', $baiHoc->id) }}" class="inline-flex items-center py-1.5 px-3 rounded-md bg-gray-100 text-gray-700 text-sm">
                                    <i class="fas fa-eye mr-1"></i> Xem
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <p class="text-gray-500">Chưa có bài học nào trong lớp này</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function openRejectModal(id) {
            document.getElementById('rejectForm').action = '{{ route('giao-vien.yeu-cau-tham-gia.tu-choi', '') }}/' + id;
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
@endsection 