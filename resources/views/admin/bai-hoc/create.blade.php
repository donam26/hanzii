@extends('layouts.dashboard')

@section('title', 'Thêm bài học mới')
@section('page-heading', 'Thêm bài học mới')

@php
    $active = 'khoa-hoc';
    $role = 'admin';
@endphp

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Heading -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Thêm bài học mới</h2>
        <div class="mt-4 md:mt-0">
            @if ($khoaHoc)
                <a href="{{ route('admin.khoa-hoc.show', $khoaHoc->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 active:bg-gray-700 disabled:opacity-25 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại khóa học
                </a>
            @else
                <a href="{{ route('admin.bai-hoc.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 active:bg-gray-700 disabled:opacity-25 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại
                </a>
            @endif
        </div>
    </div>

    <!-- Content -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Thông tin bài học</h3>
            @if ($khoaHoc)
                <p class="mt-1 text-sm text-gray-600">Thêm bài học cho khóa học: <strong>{{ $khoaHoc->ten }}</strong></p>
            @endif
        </div>
        <div class="p-6">
            <form action="{{ route('admin.bai-hoc.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="khoa_hoc_id" class="block text-sm font-medium text-gray-700 mb-1">Khóa học <span class="text-red-600">*</span></label>
                        <select id="khoa_hoc_id" name="khoa_hoc_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm @error('khoa_hoc_id') border-red-500 @enderror" required>
                            @if ($khoaHoc)
                                <option value="{{ $khoaHoc->id }}" selected>{{ $khoaHoc->ten }}</option>
                            @else
                                <option value="">-- Chọn khóa học --</option>
                                @foreach($khoaHocs as $k)
                                    <option value="{{ $k->id }}" {{ old('khoa_hoc_id') == $k->id ? 'selected' : '' }}>{{ $k->ten }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('khoa_hoc_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="trang_thai" class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-600">*</span></label>
                        <select id="trang_thai" name="trang_thai" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm @error('trang_thai') border-red-500 @enderror" required>
                            <option value="chua_xuat_ban" {{ old('trang_thai') == 'chua_xuat_ban' ? 'selected' : '' }}>Chưa xuất bản</option>
                            <option value="da_xuat_ban" {{ old('trang_thai') == 'da_xuat_ban' ? 'selected' : '' }}>Đã xuất bản</option>
                        </select>
                        @error('trang_thai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="tieu_de" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề bài học <span class="text-red-600">*</span></label>
                        <input type="text" id="tieu_de" name="tieu_de" value="{{ old('tieu_de') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm @error('tieu_de') border-red-500 @enderror" required>
                        @error('tieu_de')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="loai" class="block text-sm font-medium text-gray-700 mb-1">Loại bài học <span class="text-red-600">*</span></label>
                        <select id="loai" name="loai" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm @error('loai') border-red-500 @enderror" required>
                            <option value="van_ban" {{ old('loai') == 'van_ban' ? 'selected' : '' }}>Văn bản</option>
                            <option value="video" {{ old('loai') == 'video' ? 'selected' : '' }}>Video</option>
                            <option value="slide" {{ old('loai') == 'slide' ? 'selected' : '' }}>Slide</option>
                            <option value="bai_tap" {{ old('loai') == 'bai_tap' ? 'selected' : '' }}>Bài tập</option>
                        </select>
                        @error('loai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="so_thu_tu" class="block text-sm font-medium text-gray-700 mb-1">Số thứ tự <span class="text-red-600">*</span></label>
                        <input type="number" id="so_thu_tu" name="so_thu_tu" value="{{ old('so_thu_tu', 1) }}" min="1" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm @error('so_thu_tu') border-red-500 @enderror" required>
                        @error('so_thu_tu')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="thoi_luong" class="block text-sm font-medium text-gray-700 mb-1">Thời lượng (phút) <span class="text-red-600">*</span></label>
                        <input type="number" id="thoi_luong" name="thoi_luong" value="{{ old('thoi_luong', 30) }}" min="1" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm @error('thoi_luong') border-red-500 @enderror" required>
                        @error('thoi_luong')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div id="url_video_container" class="mb-6" style="display: none;">
                    <label for="url_video" class="block text-sm font-medium text-gray-700 mb-1">URL Video</label>
                    <input type="text" id="url_video" name="url_video" value="{{ old('url_video') }}" placeholder="Nhập đường dẫn video (YouTube, Vimeo...)" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm @error('url_video') border-red-500 @enderror">
                    @error('url_video')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="noi_dung" class="block text-sm font-medium text-gray-700 mb-1">Nội dung bài học <span class="text-red-600">*</span></label>
                    <textarea id="noi_dung" name="noi_dung" rows="10" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm @error('noi_dung') border-red-500 @enderror" required>{{ old('noi_dung') }}</textarea>
                    @error('noi_dung')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="tai_lieu" class="block text-sm font-medium text-gray-700 mb-1">Tài liệu đính kèm</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="tai_lieu[]" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                    <span>Tải lên tài liệu</span>
                                    <input id="tai_lieu[]" name="tai_lieu[]" type="file" class="sr-only" multiple>
                                </label>
                                <p class="pl-1">hoặc kéo thả tài liệu vào đây</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                Hỗ trợ PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, RAR, TXT (tối đa 10MB mỗi file)
                            </p>
                        </div>
                    </div>
                    <div id="selected-files" class="mt-2"></div>
                </div>
                
                <div class="flex justify-start space-x-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-700 disabled:opacity-25 transition">
                        <i class="fas fa-save mr-2"></i> Lưu bài học
                    </button>
                    @if ($khoaHoc)
                        <a href="{{ route('admin.khoa-hoc.show', $khoaHoc->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-times mr-2"></i> Hủy
                        </a>
                    @else
                        <a href="{{ route('admin.bai-hoc.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-times mr-2"></i> Hủy
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Hiển thị/ẩn trường URL Video dựa trên loại bài học
    const loaiSelect = document.getElementById('loai');
    const urlVideoContainer = document.getElementById('url_video_container');
    
    function toggleUrlVideoField() {
        if (loaiSelect.value === 'video') {
            urlVideoContainer.style.display = 'block';
        } else {
            urlVideoContainer.style.display = 'none';
        }
    }
    
    // Gọi khi trang tải và khi thay đổi
    document.addEventListener('DOMContentLoaded', toggleUrlVideoField);
    loaiSelect.addEventListener('change', toggleUrlVideoField);
    
    // Hiển thị danh sách file đã chọn
    document.querySelector('input[type="file"]').addEventListener('change', function(e) {
        const fileList = e.target.files;
        const selectedFilesDiv = document.getElementById('selected-files');
        selectedFilesDiv.innerHTML = '';
        
        if (fileList.length > 0) {
            const fileListElement = document.createElement('ul');
            fileListElement.className = 'mt-2 divide-y divide-gray-200';
            
            for (let i = 0; i < fileList.length; i++) {
                const file = fileList[i];
                const fileItem = document.createElement('li');
                fileItem.className = 'py-2 flex items-center text-sm';
                fileItem.innerHTML = `
                    <span class="flex-shrink-0 mr-2 text-gray-500">
                        <i class="far fa-file"></i>
                    </span>
                    <span class="truncate">${file.name}</span>
                    <span class="ml-2 text-gray-500">(${(file.size / 1024).toFixed(1)} KB)</span>
                `;
                fileListElement.appendChild(fileItem);
            }
            
            selectedFilesDiv.appendChild(fileListElement);
        }
    });
</script>
@endpush 