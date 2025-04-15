@extends('layouts.dashboard')

@section('title', 'Chỉnh sửa thông báo')
@section('page-heading', 'Chỉnh sửa thông báo')

@php
    $active = 'thong_bao';
    $role = 'giao_vien';
@endphp

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tinymce@5/skins/content/default/content.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('giao-vien.thong-bao.index') }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách thông báo
                </a>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form action="{{ route('giao-vien.thong-bao.update', $thongBao->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div>
                                <label for="lop_hoc_id" class="block text-sm font-medium text-gray-700 mb-1">Lớp học</label>
                                <select id="lop_hoc_id" name="lop_hoc_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    @foreach($lopHocs as $lopHoc)
                                        <option value="{{ $lopHoc->id }}" @if($lopHoc->id == $thongBao->lop_hoc_id) selected @endif>
                                            {{ $lopHoc->ten }} ({{ $lopHoc->ma }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('lop_hoc_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="tieu_de" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề thông báo</label>
                                <input type="text" name="tieu_de" id="tieu_de" value="{{ old('tieu_de', $thongBao->tieu_de) }}" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                @error('tieu_de')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="noi_dung" class="block text-sm font-medium text-gray-700 mb-1">Nội dung thông báo</label>
                                <textarea id="noi_dung" name="noi_dung" rows="8" class="tinymce shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('noi_dung', $thongBao->noi_dung) }}</textarea>
                                @error('noi_dung')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">File đính kèm hiện tại</label>
                                @if($thongBao->file_path)
                                    <div class="flex items-center mt-2 bg-gray-50 p-2 rounded">
                                        <i class="fas fa-file text-gray-500 mr-2"></i>
                                        <span class="text-sm text-gray-700">{{ $thongBao->ten_file ?? basename($thongBao->file_path) }}</span>
                                        <a href="{{ route('giao-vien.thong-bao.download', $thongBao->id) }}" class="ml-3 text-blue-600 hover:text-blue-800 text-sm">
                                            <i class="fas fa-download"></i> Tải xuống
                                        </a>
                                    </div>
                                    <div class="mt-2">
                                        <div class="flex items-center">
                                            <input id="remove_file" name="remove_file" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="remove_file" class="ml-2 block text-sm text-gray-700">Xóa file này</label>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500 italic">Không có file đính kèm</div>
                                @endif
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Thay đổi file đính kèm (nếu cần)</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Tải lên một file</span>
                                                <input id="file" name="file" type="file" class="sr-only">
                                            </label>
                                            <p class="pl-1">hoặc kéo thả vào đây</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            DOC, DOCX, PDF, XLS, XLSX tối đa 10MB
                                        </p>
                                    </div>
                                </div>
                                <div id="file-name" class="mt-2 text-sm text-gray-500 hidden">
                                    File đã chọn: <span class="font-medium"></span>
                                </div>
                                @error('file')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="pt-5">
                                <div class="flex justify-end">
                                    <a href="{{ route('giao-vien.thong-bao.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Hủy bỏ
                                    </a>
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Cập nhật thông báo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // TinyMCE
        tinymce.init({
            selector: 'textarea.tinymce',
            height: 300,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }'
        });
        
        // File upload
        const fileInput = document.getElementById('file');
        const fileNameDiv = document.getElementById('file-name');
        const fileNameSpan = fileNameDiv.querySelector('span');
        
        fileInput.addEventListener('change', function(e) {
            if (fileInput.files.length > 0) {
                fileNameSpan.textContent = fileInput.files[0].name;
                fileNameDiv.classList.remove('hidden');
            } else {
                fileNameDiv.classList.add('hidden');
            }
        });
    });
</script>
@endpush 