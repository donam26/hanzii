@extends('layouts.dashboard')

@section('title', 'Chỉnh sửa bài tập')
@section('page-heading', 'Chỉnh sửa bài tập')

@php
    $active = 'bai-tap';
    $role = 'giao_vien';
@endphp

@section('content')
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('giao-vien.bai-tap.show', $baiTap->id) }}" class="text-red-600 hover:text-red-800 mr-2">
                <i class="fas fa-arrow-left"></i> Quay lại chi tiết bài tập
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p class="font-bold">Lỗi!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-edit mr-2"></i> Chỉnh sửa bài tập
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Thông tin bài tập cho bài học "{{ $baiTap->baiHoc->tieu_de }}"
                </p>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Lớp học: {{ $lopHoc->ten }} ({{ $lopHoc->ma_lop }})
                </p>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <form action="{{ route('giao-vien.bai-tap.update', $baiTap->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="bai_hoc_id" value="{{ $baiTap->bai_hoc_id }}">
                
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="tieu_de" class="block text-sm font-medium text-gray-700">Tiêu đề bài tập <span class="text-red-600">*</span></label>
                            <input type="text" name="tieu_de" id="tieu_de" value="{{ old('tieu_de', $baiTap->tieu_de) }}" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                            @error('tieu_de')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="loai" class="block text-sm font-medium text-gray-700">Loại bài tập <span class="text-red-600">*</span></label>
                            <select name="loai" id="loai" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                <option value="tu_luan" {{ old('loai', $baiTap->loai) == 'tu_luan' ? 'selected' : '' }}>Tự luận</option>
                                <option value="file" {{ old('loai', $baiTap->loai) == 'file' ? 'selected' : '' }}>File</option>
                            </select>
                            @error('loai')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="diem_toi_da" class="block text-sm font-medium text-gray-700">Điểm tối đa <span class="text-red-600">*</span></label>
                            <input type="number" name="diem_toi_da" id="diem_toi_da" value="{{ old('diem_toi_da', $baiTap->diem_toi_da) }}" min="1" max="100" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                            @error('diem_toi_da')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="han_nop" class="block text-sm font-medium text-gray-700">Hạn nộp <span class="text-red-600">*</span></label>
                            <input type="datetime-local" name="han_nop" id="han_nop" value="{{ old('han_nop', \Carbon\Carbon::parse($baiTap->han_nop)->format('Y-m-d\TH:i')) }}" class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                            @error('han_nop')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700">File đính kèm (tối đa 10MB)</label>
                            <input type="file" name="file" id="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                            @if($baiTap->file_dinh_kem)
                                <div class="mt-2 flex items-center">
                                    <span class="text-sm text-gray-500 mr-2">File hiện tại:</span>
                                    <a href="{{ asset('storage/' . $baiTap->file_dinh_kem) }}" target="_blank" class="text-sm text-red-600 hover:text-red-800">
                                        <i class="fas fa-file-download mr-1"></i> {{ $baiTap->ten_file ?: 'Tải xuống file đính kèm' }}
                                    </a>
                                </div>
                                <div class="mt-1">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="xoa_file" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600">Xóa file hiện tại</span>
                                    </label>
                                </div>
                            @endif
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    
                    </div>
                </div>

                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-save mr-2"></i> Cập nhật bài tập
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
