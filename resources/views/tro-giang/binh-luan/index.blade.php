@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Quản lý bình luận</h1>
    </div>

    <!-- Filter lớp học -->
    <div class="mb-6 bg-white rounded-lg shadow p-4">
        <form action="{{ route('tro-giang.binh-luan.index') }}" method="GET" class="flex items-center space-x-4">
            <div class="w-1/3">
                <label for="lop_hoc_id" class="block text-sm font-medium text-gray-700 mb-1">Lọc theo lớp học</label>
                <select id="lop_hoc_id" name="lop_hoc_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
                    <option value="">Tất cả lớp học</option>
                    @foreach($lopHocs as $lopHoc)
                        <option value="{{ $lopHoc->id }}" {{ $lopHocId == $lopHoc->id ? 'selected' : '' }}>
                            {{ $lopHoc->ten }} ({{ $lopHoc->khoaHoc->ten }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="pt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- Danh sách bình luận -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($binhLuans->isEmpty())
            <div class="px-4 py-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Không có bình luận nào</h3>
                <p class="mt-1 text-sm text-gray-500">Hiện tại chưa có bình luận nào trong các lớp học của bạn.</p>
            </div>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach($binhLuans as $binhLuan)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                            <span class="text-red-700 font-medium">
                                                {{ strtoupper(substr($binhLuan->nguoiDung->ho, 0, 1) . substr($binhLuan->nguoiDung->ten, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $binhLuan->nguoiDung->ho . ' ' . $binhLuan->nguoiDung->ten }}
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if(optional($binhLuan->nguoiDung->vaiTros->first())->ten == 'giao_vien')
                                                    bg-blue-100 text-blue-800
                                                @elseif(optional($binhLuan->nguoiDung->vaiTros->first())->ten == 'tro_giang')
                                                    bg-green-100 text-green-800
                                                @else
                                                    bg-gray-100 text-gray-800
                                                @endif
                                            ">
                                                @if(optional($binhLuan->nguoiDung->vaiTros->first())->ten == 'giao_vien')
                                                    Giáo viên
                                                @elseif(optional($binhLuan->nguoiDung->vaiTros->first())->ten == 'tro_giang')
                                                    Trợ giảng
                                                @else
                                                    Học viên
                                                @endif
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Lớp: <a href="{{ route('tro-giang.lop-hoc.show', $binhLuan->lopHoc->id) }}" class="font-medium text-red-600 hover:text-red-500">
                                                {{ $binhLuan->lopHoc->ten }}
                                            </a>
                                            - Bài học: 
                                            <a href="{{ route('tro-giang.bai-hoc.show', [$binhLuan->lopHoc->id, $binhLuan->baiHoc->id]) }}" class="font-medium text-red-600 hover:text-red-500">
                                                {{ $binhLuan->baiHoc->tieu_de }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($binhLuan->tao_luc)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="mt-2 text-sm text-gray-700">
                                <p>{{ $binhLuan->noi_dung }}</p>
                            </div>
                            @if($binhLuan->nguoi_dung_id == session('nguoi_dung_id'))
                                <div class="mt-2 flex justify-end">
                                    <form action="{{ route('tro-giang.binh-luan.destroy', $binhLuan->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-900 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                {{ $binhLuans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 