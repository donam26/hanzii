@extends('layouts.dashboard')

@section('title', 'Chi tiết thông báo: ' . $thongBao->tieu_de)
@section('page-heading', 'Chi tiết thông báo')

@php
    $active = 'thong_bao';
    $role = 'giao_vien';
@endphp

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('giao-vien.thong-bao.index') }}" class="text-blue-600 hover:text-blue-800 mr-2">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $thongBao->tieu_de }}</h1>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('giao-vien.thong-bao.edit', $thongBao->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-edit mr-2"></i> Chỉnh sửa
                </a>
                <form action="{{ route('giao-vien.thong-bao.destroy', $thongBao->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thông báo này?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-trash-alt mr-2"></i> Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Thông tin thông báo
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Chi tiết và nội dung thông báo
                    </p>
                </div>
                <div class="text-sm text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $thongBao->lopHoc->ten ?? 'Không xác định' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Tiêu đề
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $thongBao->tieu_de }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Lớp học
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $thongBao->lopHoc->ten ?? 'Không xác định' }} 
                        @if($thongBao->lopHoc)
                        <span class="text-gray-500">(Mã: {{ $thongBao->lopHoc->ma }})</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Ngày tạo
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $thongBao->created_at->format('d/m/Y H:i') }}
                    </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Ngày cập nhật
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $thongBao->updated_at->format('d/m/Y H:i') }}
                    </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        Người tạo
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ $thongBao->giaoVien->ho_ten ?? 'Không xác định' }}
                    </dd>
                </div>
                
                @if($thongBao->file_path)
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                        File đính kèm
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center">
                            <i class="fas fa-file-alt text-gray-500 mr-2"></i>
                            <span>{{ $thongBao->ten_file ?? basename($thongBao->file_path) }}</span>
                            <a href="{{ route('giao-vien.thong-bao.download', $thongBao->id) }}" class="ml-3 text-blue-600 hover:text-blue-800">
                                <i class="fas fa-download"></i> Tải xuống
                            </a>
                        </div>
                    </dd>
                </div>
                @endif
                
                <div class="bg-white px-4 py-5 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500 mb-4">
                        Nội dung thông báo
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 border-t pt-4">
                        <div class="prose prose-sm max-w-none">
                            {!! $thongBao->noi_dung !!}
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Danh sách sinh viên đã đọc -->
    <div class="mt-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Danh sách học viên đã đọc ({{ count($thongBao->danhSachDaDoc) }}/{{ $tongSoHocVien ?? 0 }})</h2>
        
        @if($tongSoHocVien == 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6 text-center text-gray-500">
                Lớp học này chưa có học viên nào
            </div>
        @elseif(count($thongBao->danhSachDaDoc) == 0)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6 text-center text-gray-500">
                Chưa có học viên nào đọc thông báo này
            </div>
        @else
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                STT
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Học viên
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày đọc
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($thongBao->danhSachDaDoc as $index => $daDoc)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($daDoc->hocVien->avatar)
                                                <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $daDoc->hocVien->avatar) }}" alt="{{ $daDoc->hocVien->ho_ten }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-gray-500 font-medium">{{ substr($daDoc->hocVien->ho_ten, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $daDoc->hocVien->ho_ten }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $daDoc->hocVien->ma_hoc_vien }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $daDoc->hocVien->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($daDoc->ngay_doc)->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection 