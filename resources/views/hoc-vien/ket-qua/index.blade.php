@extends('layouts.dashboard')

@section('title', 'Kết quả học tập')
@section('page-heading', 'Kết quả học tập')

@php
    $active = 'ket-qua';
    $role = 'hoc_vien';
@endphp

@section('content')
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Kết quả học tập của {{ $hocVien->ho_ten }}</h2>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="bg-white shadow rounded-lg mb-6 p-4">
        <form action="{{ route('hoc-vien.ket-qua.index') }}" method="GET" class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
            <div class="flex-1">
                <label for="lop_hoc_id" class="block text-sm font-medium text-gray-700 mb-1">Lớp học</label>
                <select id="lop_hoc_id" name="lop_hoc_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Tất cả lớp học</option>
                    @foreach($lopHocs as $lop)
                        <option value="{{ $lop->id }}" {{ request('lop_hoc_id') == $lop->id ? 'selected' : '' }}>
                            {{ $lop->ten }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md">
                    Lọc kết quả
                </button>
            </div>
        </form>
    </div>

    <!-- Tổng quan kết quả -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Tổng quan kết quả học tập</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <div class="text-4xl font-bold text-gray-700 mb-2">{{ number_format($diemTrungBinh, 1) }}</div>
                    <div class="text-sm text-gray-600">Điểm trung bình</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <div class="text-4xl font-bold text-gray-700 mb-2">{{ $nopBaiTaps->total() }}</div>
                    <div class="text-sm text-gray-600">Tổng số bài đã làm</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <div class="text-4xl font-bold text-gray-700 mb-2">0</div>
                    <div class="text-sm text-gray-600">Tổng số bài chưa làm</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách kết quả -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Chi tiết kết quả học tập</h3>
            <div>
                <select id="sortOption" onchange="sortTable()" class="border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm">
                    <option value="newest">Mới nhất</option>
                    <option value="oldest">Cũ nhất</option>
                    <option value="highest">Điểm cao nhất</option>
                    <option value="lowest">Điểm thấp nhất</option>
                </select>
            </div>
        </div>
        <div class="overflow-hidden">
            @if($nopBaiTaps->isNotEmpty())
                <table id="resultsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tên bài
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lớp học
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Loại bài
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày nộp
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Điểm
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($nopBaiTaps as $baiLam)
                            <tr data-date="{{ $baiLam->tao_luc }}" data-score="{{ $baiLam->diem ?? 0 }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $baiLam->baiTap->ten ?? 'Không có tên' }}</div>
                                    <div class="text-xs text-gray-500">{{ $baiLam->baiTap->baiHoc->ten ?? 'Không có bài học' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($baiLam->baiTap && $baiLam->baiTap->baiHoc && $baiLam->baiTap->baiHoc->baiHocLops)
                                            @foreach($baiLam->baiTap->baiHoc->baiHocLops as $baiHocLop)
                                                {{ $baiHocLop->lopHoc->ten ?? 'Không xác định' }}
                                                @if(!$loop->last), @endif
                                            @endforeach
                                        @else
                                            Không xác định
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        @if($baiLam->baiTap && $baiLam->baiTap->loai)
                                            {{ ucfirst($baiLam->baiTap->loai) }}
                                        @else
                                            Không xác định
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($baiLam->tao_luc)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(isset($baiLam->diem))
                                        <span class="text-sm font-medium {{ $baiLam->diem >= 8 ? 'text-green-600' : 
                                                                     ($baiLam->diem >= 6.5 ? 'text-blue-600' : 
                                                                      ($baiLam->diem >= 5 ? 'text-yellow-600' : 'text-red-600')) }}">
                                            {{ number_format($baiLam->diem, 1) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">Chưa chấm</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('hoc-vien.ket-qua.show', $baiLam->id) }}" class="text-blue-600 hover:text-blue-900">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="px-6 py-4">
                    {{ $nopBaiTaps->links() }}
                </div>
            @else
                <div class="p-6 text-center text-gray-500">
                    <p>Bạn chưa có kết quả nào</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function sortTable() {
        const table = document.getElementById('resultsTable');
        if (!table) return;
        
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        const sortOption = document.getElementById('sortOption').value;
        
        rows.sort((a, b) => {
            if (sortOption === 'newest') {
                const dateA = new Date(a.dataset.date);
                const dateB = new Date(b.dataset.date);
                return dateB - dateA;
            } else if (sortOption === 'oldest') {
                const dateA = new Date(a.dataset.date);
                const dateB = new Date(b.dataset.date);
                return dateA - dateB;
            } else if (sortOption === 'highest') {
                return parseFloat(b.dataset.score) - parseFloat(a.dataset.score);
            } else if (sortOption === 'lowest') {
                return parseFloat(a.dataset.score) - parseFloat(b.dataset.score);
            }
            return 0;
        });
        
        const tbody = table.querySelector('tbody');
        rows.forEach(row => tbody.appendChild(row));
    }
</script>
@endsection 