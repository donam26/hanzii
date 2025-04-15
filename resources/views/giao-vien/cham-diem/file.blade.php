@extends('layouts.dashboard')

@section('title', 'Chấm điểm bài tập file')
@section('page-heading', 'Chấm điểm bài tập file')

@php
    $active = 'cham_diem';
    $role = 'giao_vien';
    
    function formatFileSize($size) {
        if (!$size) return '0 B';
        if ($size < 1024) {
            return $size . ' B';
        } elseif ($size < 1024*1024) {
            return round($size/1024, 2) . ' KB';
        } elseif ($size < 1024*1024*1024) {
            return round($size/(1024*1024), 2) . ' MB';
        } else {
            return round($size/(1024*1024*1024), 2) . ' GB';
        }
    }
@endphp

@section('content')
    <div class="mb-6">
        <div class="flex items-center">
            <a href="{{ route('giao-vien.cham-diem.index') }}" class="mr-4 text-blue-600 hover:text-blue-800">
                <svg class="h-5 w-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Thông tin bài tập -->
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Thông tin bài tập</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Học viên</p>
                    <p class="text-base font-semibold">
                        {{ $baiNop->hocVien->nguoiDung->ho_ten ?? ($baiNop->hocVien->nguoiDung->ho . ' ' . $baiNop->hocVien->nguoiDung->ten) }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Bài tập</p>
                    <p class="text-base font-semibold">{{ $baiNop->baiTap->tieu_de }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Bài học</p>
                    <p class="text-base">{{ $baiNop->baiTap->baiHoc->tieu_de }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Lớp học</p>
                    <p class="text-base">
                        @if(isset($baiNop->baiTap->baiHoc->baiHocLops) && count($baiNop->baiTap->baiHoc->baiHocLops) > 0)
                            {{ $baiNop->baiTap->baiHoc->baiHocLops[0]->lopHoc->ten ?? 'Không có lớp' }}
                        @else
                            Không có lớp
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Ngày nộp</p>
                    <p class="text-base">{{ \Carbon\Carbon::parse($baiNop->ngay_nop)->format('d/m/Y H:i:s') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Trạng thái</p>
                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($baiNop->trang_thai == 'da_nop') bg-yellow-100 text-yellow-800
                        @elseif($baiNop->trang_thai == 'dang_cham') bg-blue-100 text-blue-800
                        @elseif($baiNop->trang_thai == 'da_cham') bg-green-100 text-green-800
                        @elseif($baiNop->trang_thai == 'yeu_cau_nop_lai') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        @if($baiNop->trang_thai == 'da_nop') Đã nộp
                        @elseif($baiNop->trang_thai == 'dang_cham') Đang chấm
                        @elseif($baiNop->trang_thai == 'da_cham') Đã chấm
                        @elseif($baiNop->trang_thai == 'yeu_cau_nop_lai') Yêu cầu nộp lại
                        @else {{ $baiNop->trang_thai }} @endif
                    </p>
                </div>
            </div>
            
            <!-- File đính kèm -->
            @if($baiNop->file_path)
            <div class="mt-6">
                <h4 class="text-md font-medium text-gray-700 mb-3">File đính kèm</h4>
                <div class="bg-gray-50 rounded-lg p-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="mr-3 flex-shrink-0">
                            @php
                                $extension = pathinfo($baiNop->ten_file, PATHINFO_EXTENSION);
                            @endphp
                            
                            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']))
                                <svg class="h-10 w-10 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif(in_array(strtolower($extension), ['doc', 'docx']))
                                <svg class="h-10 w-10 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif(in_array(strtolower($extension), ['xls', 'xlsx']))
                                <svg class="h-10 w-10 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif(in_array(strtolower($extension), ['pdf']))
                                <svg class="h-10 w-10 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="h-10 w-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $baiNop->ten_file }}</p>
                            <p class="text-xs text-gray-500">{{ formatFileSize($baiNop->kich_thuoc ?? 0) }}</p>
                        </div>
                    </div>
                    <a href="{{ route('giao-vien.cham-diem.download', $baiNop->id) }}" class="px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
                        Tải xuống
                    </a>
                </div>
                
                <!-- Hiển thị xem trước nếu là ảnh -->
                @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']))
                <div class="mt-4">
                    <h4 class="text-md font-medium text-gray-700 mb-3">Xem trước</h4>
                    <div class="border border-gray-200 rounded-lg p-2">
                        <img src="{{ asset('storage/' . $baiNop->file_path) }}" alt="Preview" class="max-w-full h-auto rounded">
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Form chấm điểm -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Chấm điểm bài tập</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('giao-vien.cham-diem.cham', $baiNop->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="diem" class="block text-sm font-medium text-gray-700 mb-1">Điểm số (0-10)</label>
                    <input type="number" name="diem" id="diem" min="0" max="10" step="0.1" value="{{ old('diem', $baiNop->diem) }}" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    @error('diem')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="phan_hoi" class="block text-sm font-medium text-gray-700 mb-1">Phản hồi</label>
                    <textarea name="phan_hoi" id="phan_hoi" rows="4" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('phan_hoi', $baiNop->phan_hoi) }}</textarea>
                    @error('phan_hoi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('giao-vien.cham-diem.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Hủy
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Lưu điểm
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection 