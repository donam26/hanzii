@extends('layouts.dashboard')

@section('title', 'Trang chủ học viên')
@section('page-heading', 'Trang chủ học viên')

@php
    $active = 'dashboard';
    $role = 'hoc_vien';
@endphp

@section('content')
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Xin chào, {{ $hocVien->nguoiDung->ho }} {{ $hocVien->nguoiDung->ten }}!</h2>
            <div class="mt-2 md:mt-0">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    Học viên
                </span>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-card>
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Lớp học đang tham gia</div>
                    <div class="text-xl font-semibold">{{ $totalLopHoc }}</div>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Bài tập đã hoàn thành</div>
                    <div class="text-xl font-semibold">{{ $completedTasks }}</div>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Bài tập đang chờ</div>
                    <div class="text-xl font-semibold">{{ $pendingTasks }}</div>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Điểm trung bình</div>
                    <div class="text-xl font-semibold">{{ number_format($averageScore, 1) }}</div>
                </div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lớp học sắp tới -->
        <div class="lg:col-span-2">
            <x-card title="Lớp học sắp diễn ra">
                @if(count($upcomingClasses) > 0)
                    <div class="space-y-4">
                        @foreach($upcomingClasses as $class)
                            <div class="border rounded-lg overflow-hidden">
                                <div class="flex flex-col md:flex-row">
                                    <div class="bg-red-600 text-white p-4 flex flex-col items-center justify-center md:w-1/4">
                                        <div class="text-2xl font-bold">{{ \Carbon\Carbon::parse($class->ngay_hoc)->format('d') }}</div>
                                        <div>{{ \Carbon\Carbon::parse($class->ngay_hoc)->format('M Y') }}</div>
                                        <div class="mt-1 text-sm">{{ \Carbon\Carbon::parse($class->gio_bat_dau)->format('H:i') }} - {{ \Carbon\Carbon::parse($class->gio_ket_thuc)->format('H:i') }}</div>
                                    </div>
                                    <div class="p-4 md:w-3/4">
                                        <h3 class="font-semibold text-lg">{{ $class->lopHoc->ten }}</h3>
                                        <p class="text-gray-600 text-sm mb-2">{{ $class->baiHoc->ten }}</p>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            @if($class->lopHoc->hinh_thuc_hoc == 'online')
                                                <span>Học online</span>
                                                @if($class->link_hoc)
                                                    <a href="{{ $class->link_hoc }}" target="_blank" class="ml-2 text-red-600 hover:text-red-800">Tham gia</a>
                                                @endif
                                            @else
                                                <span>Học tại trung tâm</span>
                                            @endif
                                        </div>
                                        <div class="mt-3 text-right">
                                            <a href="{{ route('hoc-vien.lop-hoc.show', $class->lopHoc->id) }}" class="text-sm text-red-600 hover:text-red-800">Xem chi tiết lớp học</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-600">Không có lớp học nào sắp diễn ra</p>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Bài tập cần làm -->
        <div>
            <x-card title="Bài tập cần làm">
                @if(count($pendingAssignments) > 0)
                    <div class="space-y-3">
                        @foreach($pendingAssignments as $assignment)
                            <div class="border rounded-lg p-3 hover:bg-gray-50 transition">
                                <h4 class="font-medium">{{ $assignment->baiTap->ten }}</h4>
                                <div class="text-sm text-gray-600 mb-2">{{ $assignment->baiTap->baiHoc->ten }}</div>
                                
                                <div class="flex justify-between items-center text-sm">
                                    <div class="flex items-center text-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Hạn nộp: {{ \Carbon\Carbon::parse($assignment->han_nop)->format('d/m/Y') }}</span>
                                    </div>
                                    
                                    <a href="{{ route('hoc-vien.bai-tap.show', $assignment->id) }}" class="text-blue-600 hover:text-blue-800">Làm bài</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <p class="text-gray-600">Không có bài tập nào cần làm</p>
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    <!-- Tiến độ học tập -->
    <div class="mt-6">
        <x-card title="Tiến độ học tập">
            @if(count($learningProgresses) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($learningProgresses as $progress)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-semibold">{{ $progress->lopHoc->ten }}</h3>
                                    <p class="text-sm text-gray-600">{{ $progress->lopHoc->khoa_hoc->ten }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-blue-600">{{ $progress->phan_tram_hoan_thanh }}%</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-red-600 h-2.5 rounded-full" style="width: {{ $progress->phan_tram_hoan_thanh }}%"></div>
                            </div>
                            <div class="mt-3 text-sm text-gray-600">
                                <span>{{ $progress->so_bai_da_hoc }}/{{ $progress->tong_so_bai }} bài học</span>
                            </div>
                            <div class="mt-2 text-right">
                                <a href="{{ route('hoc-vien.lop-hoc.progress', $progress->lopHoc->id) }}" class="text-sm text-red-600 hover:text-red-800">Xem chi tiết</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <p class="text-gray-600">Chưa có dữ liệu tiến độ học tập</p>
                </div>
            @endif
        </x-card>
    </div>
@endsection 