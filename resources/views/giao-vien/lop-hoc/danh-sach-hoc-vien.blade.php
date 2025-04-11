@extends('layouts.dashboard')

@section('title', 'Danh sách học viên lớp ' . $lopHoc->ten)
@section('page-heading', 'Danh sách học viên lớp ' . $lopHoc->ten)

@php
    $active = 'lop-hoc';
    $role = 'giao_vien';
@endphp

@section('content')
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Danh sách học viên lớp {{ $lopHoc->ten }}</h2>
                <p class="mt-1 text-sm text-gray-600">Khóa học: {{ $lopHoc->khoaHoc->ten }}</p>
            </div>
            <div class="mt-4 md:mt-0 flex">
                <a href="{{ route('giao-vien.lop-hoc.show', $lopHoc->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 active:bg-gray-700 disabled:opacity-25 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại
                </a>
                <a href="{{ route('giao-vien.lop-hoc.add-student-form', $lopHoc->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-700 disabled:opacity-25 transition ml-2">
                    <i class="fas fa-user-plus mr-2"></i> Thêm học viên
                </a>
            </div>
        </div>
    </div>
    
    <!-- Thông tin lớp học -->
    <div class="bg-white shadow rounded-lg mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Thông tin lớp học</h3>
                <p class="mt-1 text-sm text-gray-900">Mã lớp: {{ $lopHoc->ma_lop }}</p>
                <p class="mt-1 text-sm text-gray-900">Hình thức: {{ $lopHoc->hinh_thuc_hoc == 'online' ? 'Trực tuyến' : 'Tại trung tâm' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Thông tin giảng dạy</h3>
                <p class="mt-1 text-sm text-gray-900">Giáo viên: {{ $lopHoc->giaoVien->nguoiDung->ho . ' ' . $lopHoc->giaoVien->nguoiDung->ten }}</p>
                <p class="mt-1 text-sm text-gray-900">Trợ giảng: {{ $lopHoc->troGiang->nguoiDung->ho . ' ' . $lopHoc->troGiang->nguoiDung->ten }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Thời gian học</h3>
                <p class="mt-1 text-sm text-gray-900">Bắt đầu: {{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }}</p>
                <p class="mt-1 text-sm text-gray-900">Kết thúc: {{ \Carbon\Carbon::parse($lopHoc->ngay_ket_thuc)->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>
    
    <!-- Thống kê và tính năng -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white shadow rounded-lg p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-md bg-red-100 flex items-center justify-center text-red-600">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Tổng số học viên</h3>
                    <p class="text-xl font-semibold text-gray-800">{{ $tongSo }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-md bg-green-100 flex items-center justify-center text-green-600">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Đã xác nhận</h3>
                    <p class="text-xl font-semibold text-gray-800">{{ $daXacNhan }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-md bg-yellow-100 flex items-center justify-center text-yellow-600">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Chờ xác nhận</h3>
                        <p class="text-xl font-semibold text-gray-800">{{ $chuaXacNhan }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-md bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-ban"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Đã hủy</h3>
                    <p class="text-xl font-semibold text-gray-800">{{ $daHuy }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Danh sách học viên -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="hocVienTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            STT
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thông tin học viên
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Thông tin liên hệ
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ngày đăng ký
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Trạng thái
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hành động
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($tongSo > 0)
                        @php $index = 0; @endphp
                        @foreach($dangKyHocs as $trangThai => $dkGroup)
                            @foreach($dkGroup as $dangKy)
                                @php $index++; @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-700">
                                                {{ strtoupper(substr($dangKy->hocVien->nguoiDung->ten, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $dangKy->hocVien->nguoiDung->ho . ' ' . $dangKy->hocVien->nguoiDung->ten }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-envelope text-gray-400 mr-1"></i> {{ $dangKy->hocVien->nguoiDung->email }}
                                        </div>
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-phone text-gray-400 mr-1"></i> {{ $dangKy->hocVien->nguoiDung->so_dien_thoai }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($dangKy->ngay_dang_ky)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = '';
                                            $statusText = '';
                                            
                                            switch($trangThai) {
                                                case 'cho_xac_nhan':
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    $statusText = 'Chờ xác nhận';
                                                    break;
                                                case 'da_duyet':
                                                case 'da_xac_nhan':
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                    $statusText = 'Đã xác nhận';
                                                    break;
                                                case 'da_huy':
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                    $statusText = 'Đã hủy';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    $statusText = ucfirst(str_replace('_', ' ', $trangThai));
                                            }
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-2 justify-end">
                                            <a href="#" class="text-blue-600 hover:text-blue-900" title="Chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if ($dangKy->trang_thai === 'cho_xac_nhan')
                                                <td class="d-flex">
                                                    <form action="{{ route('giao-vien.lop-hoc.xac-nhan-hoc-vien', [$lopHoc->id, $dangKy->id]) }}" method="POST" class="me-2">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-check"></i> Xác nhận
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modal-tu-choi-{{ $dangKy->id }}">
                                                        <i class="fas fa-times"></i> Từ chối
                                                    </button>
                                                    
                                                    <!-- Modal từ chối -->
                                                    <div class="modal fade" id="modal-tu-choi-{{ $dangKy->id }}" tabindex="-1" aria-labelledby="modalTuChoiLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="modalTuChoiLabel">Từ chối học viên: {{ $dangKy->hocVien->nguoiDung->ho_ten }}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <form action="{{ route('giao-vien.lop-hoc.tu-choi-hoc-vien', [$lopHoc->id, $dangKy->id]) }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label for="ly-do-{{ $dangKy->id }}" class="form-label">Lý do từ chối (tùy chọn)</label>
                                                                            <textarea class="form-control" id="ly-do-{{ $dangKy->id }}" name="ly_do" rows="3" placeholder="Nhập lý do từ chối..."></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                                        <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif
                                            
                                            <form action="{{ route('giao-vien.lop-hoc.remove-student', ['id' => $lopHoc->id, 'hocVienId' => $dangKy->hocVien->id]) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa học viên này khỏi lớp học?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Xóa">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Chưa có học viên nào đăng ký lớp học này.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function printTable() {
        let printContents = document.getElementById('hocVienTable').outerHTML;
        let originalContents = document.body.innerHTML;
        
        document.body.innerHTML = `
            <html>
                <head>
                    <title>Danh sách học viên - {{ $lopHoc->ten }}</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        table { border-collapse: collapse; width: 100%; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        h2 { text-align: center; }
                    </style>
                </head>
                <body>
                    <h2>Danh sách học viên lớp {{ $lopHoc->ten }}</h2>
                    <p>Khóa học: {{ $lopHoc->khoaHoc->ten }}</p>
                    <p>Ngày in: ${new Date().toLocaleDateString('vi-VN')}</p>
                    ${printContents}
                </body>
            </html>
        `;
        
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
@endsection 