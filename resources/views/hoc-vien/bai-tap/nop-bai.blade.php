@extends('layouts.dashboard')

@section('title', 'Nộp bài tập')
@section('page-heading', 'Nộp bài tập')

@php
    $active = 'lop-hoc';
    $role = 'hoc_vien';
@endphp

@section('content')
<div class="container px-4 mx-auto">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('hoc-vien.bai-hoc.show', ['lopHocId' => $lopHoc->id, 'baiHocId' => $baiHocId]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Quay lại bài học
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">{{ $baiTap->tieu_de }}</h3>
        </div>
        <div class="p-6">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-md">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-2"><span class="font-medium">Lớp học:</span> {{ $lopHoc->ten }}</p>
                        <p class="text-sm text-gray-600 mb-2"><span class="font-medium">Bài học:</span> {{ $baiTap->baiHoc->tieu_de }}</p>
                        <p class="text-sm text-gray-600 mb-2">
                            <span class="font-medium">Loại bài tập:</span> 
                            @if ($baiTap->loai == 'tu_luan')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Tự luận</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">File</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-2"><span class="font-medium">Điểm tối đa:</span> {{ $baiTap->diem_toi_da }}</p>
                        <p class="text-sm text-gray-600 mb-2">
                            <span class="font-medium">Hạn nộp:</span> 
                            {{ $baiTap->han_nop ? $baiTap->han_nop->format('d/m/Y H:i') : 'Không có hạn nộp' }}
                        </p>
                    </div>
                </div>
            </div>

            @if($baiTap->mo_ta)
                <div class="mb-6">
                    <h4 class="text-base font-medium text-gray-900 mb-2">Mô tả bài tập:</h4>
                    <div class="prose max-w-none bg-gray-50 p-4 rounded-md">
                        {!! $baiTap->mo_ta !!}
                    </div>
                </div>
            @endif

            @if($baiTap->file_dinh_kem)
                <div class="mb-6">
                    <h4 class="text-base font-medium text-gray-900 mb-2">File đính kèm:</h4>
                    <a href="{{ asset('storage/' . $baiTap->file_dinh_kem) }}" target="_blank" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Tải xuống {{ $baiTap->ten_file ?? 'file đính kèm' }}
                    </a>
                </div>
            @endif

            <!-- Form nộp bài -->
            <div class="mt-8">
                <h4 class="text-base font-medium text-gray-900 mb-4">Nộp bài làm của bạn</h4>

                <form action="{{ route('hoc-vien.bai-hoc.nop-bai-tap', ['lopHocId' => $lopHoc->id, 'baiHocId' => $baiHocId, 'baiTapId' => $baiTap->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    @if($baiTap->loai == 'tu_luan')
                        <div class="mb-4">
                            <label for="noi_dung" class="block text-sm font-medium text-gray-700 mb-1">Nội dung bài làm:</label>
                            <textarea id="noi_dung" name="noi_dung" rows="8" 
                                class="shadow-sm focus:ring-red-500 focus:border-red-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md @error('noi_dung') border-red-500 @enderror"
                                placeholder="Nhập nội dung bài làm của bạn">{{ old('noi_dung', $baiTapDaNop->noi_dung ?? '') }}</textarea>
                            @error('noi_dung')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="mb-6">
                        <label for="file_dinh_kem" class="block text-sm font-medium text-gray-700 mb-1">Tải lên file bài làm (nếu có):</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="file_dinh_kem" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                        <span>Tải lên file</span>
                                        <input id="file_dinh_kem" name="file_dinh_kem" type="file" class="sr-only">
                                    </label>
                                    <p class="pl-1">hoặc kéo thả vào đây</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PDF, DOC, DOCX, XLSX, PPT, JPG, PNG (tối đa 10MB)
                                </p>
                                @if(isset($baiTapDaNop) && $baiTapDaNop->file_dinh_kem)
                                    <p class="text-xs font-medium text-green-600 mt-2">
                                        Đã tải lên: <a href="{{ asset('storage/' . $baiTapDaNop->file_dinh_kem) }}" target="_blank" class="underline">Xem file đã nộp</a>
                                    </p>
                                @endif
                            </div>
                        </div>
                        @error('file_dinh_kem')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-center mt-8">
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Nộp bài
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Cập nhật tên file khi chọn
    document.getElementById('file_dinh_kem').addEventListener('change', function(e) {
        var fileName = '';
        if (this.files && this.files.length > 0) {
            fileName = this.files[0].name;
            document.querySelector('.text-xs.text-gray-500').textContent = fileName;
        }
    });
    
    // Cảnh báo khi học viên cố gắng rời khỏi trang
    window.addEventListener('beforeunload', function (e) {
        // Hủy sự kiện cho các trình duyệt khác nhau
        e.preventDefault();
        e.returnValue = '';
        
        // Hiển thị thông báo
        return 'Bạn đang làm bài tập, nếu rời khỏi trang này bạn sẽ mất dữ liệu đã làm. Bạn có chắc muốn rời đi?';
    });
    
    // Khi submit form thì không hiển thị cảnh báo nữa
    document.querySelector('form').addEventListener('submit', function() {
        window.removeEventListener('beforeunload', function() {});
    });
</script>
@endpush 