@extends('layouts.dashboard')

@section('title', 'Quản lý lương')
@section('page-heading', 'Quản lý lương')

@php
    $active = 'luong';
    $role = 'admin';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Hiển thị thông báo lỗi validation -->
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-medium">Lỗi!</strong>
        <ul class="mt-1 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Hiển thị thông báo thành công -->
    @if (session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-medium">Thành công!</strong>
        <span class="block sm:inline ml-1">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Hiển thị thông báo lỗi -->
    @if (session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-medium">Lỗi!</strong>
        <span class="block sm:inline ml-1">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Thống kê tổng quan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md border border-blue-200 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-500 text-white rounded-lg">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-800">
                        {{ number_format(
                            (method_exists($luongGiaoViens, 'total') ? $luongGiaoViens->total() : count($luongGiaoViens)) +
                            (method_exists($luongTroGiangs, 'total') ? $luongTroGiangs->total() : count($luongTroGiangs))
                        ) }}
                    </h2>
                    <p class="text-sm text-gray-600">Tổng lương cần trả</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md border border-green-200 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-500 text-white rounded-lg">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ number_format($luongGiaoViens->where('trang_thai', 'da_thanh_toan')->count() + $luongTroGiangs->where('trang_thai', 'da_thanh_toan')->count()) }}</h2>
                    <p class="text-sm text-gray-600">Đã thanh toán</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow-md border border-red-200 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-red-500 text-white rounded-lg">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ number_format($luongGiaoViens->where('trang_thai', 'chua_thanh_toan')->count() + $luongTroGiangs->where('trang_thai', 'chua_thanh_toan')->count()) }}</h2>
                    <p class="text-sm text-gray-600">Chưa thanh toán</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg shadow-md border border-indigo-200 p-4">
            <div class="flex items-center">
                <div class="p-3 bg-indigo-500 text-white rounded-lg">
                    <i class="fas fa-calculator text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ number_format(now()->month) }}/{{ number_format(now()->year) }}</h2>
                    <p class="text-sm text-gray-600">Tháng hiện tại</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lọc dữ liệu -->
    <div class="bg-white p-4 rounded-lg shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="thang" class="block text-sm font-medium text-gray-700">Chọn tháng:</label>
                <input type="month" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50" id="thang" name="thang" value="{{ $thang ?? now()->format('Y-m') }}">
            </div>
            <div>
                <label for="loc-trang-thai" class="block text-sm font-medium text-gray-700">Trạng thái:</label>
                <select class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50" id="loc-trang-thai">
                    <option value="all" {{ !isset($trangThai) ? 'selected' : '' }}>Tất cả</option>
                    <option value="da_thanh_toan" {{ (isset($trangThai) && $trangThai == 'da_thanh_toan') ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="chua_thanh_toan" {{ (isset($trangThai) && $trangThai == 'chua_thanh_toan') ? 'selected' : '' }}>Chưa thanh toán</option>
                </select>
            </div>
            <div>
                <label for="lop_hoc_id" class="block text-sm font-medium text-gray-700">Chọn lớp học:</label>
                <select name="lop_hoc_id" id="lop_hoc_id" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50">
                    <option value="">-- Chọn lớp học --</option>
                    @foreach($lopHocs as $lopHoc)
                        <option value="{{ $lopHoc->id }}">
                            {{ $lopHoc->ma_lop }} - {{ $lopHoc->ten }}
                            @if($lopHoc->trang_thai == 'dang_dien_ra')
                                (Đang diễn ra)
                            @elseif($lopHoc->trang_thai == 'da_ket_thuc')
                                (Đã kết thúc)
                            @elseif($lopHoc->trang_thai == 'sap_khai_giang')
                                (Sắp khai giảng)
                            @else
                                ({{ $lopHoc->trang_thai }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end">
                <form action="{{ route('admin.luong.calculate') }}" method="POST" id="calculateForm">
                    @csrf
                    <input type="hidden" name="lop_hoc_id" id="lop_hoc_id_hidden">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition h-10">
                        <i class="fas fa-calculator mr-2"></i> Tính lương
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Thông tin quy trình tính lương -->
    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-4">
        <h3 class="text-lg font-medium text-blue-800 mb-2">
            <i class="fas fa-info-circle mr-2"></i>Quy trình tính lương
        </h3>
        <div class="text-sm text-blue-700 space-y-2">
            <p><span class="font-semibold">Bước 1:</span> Chọn lớp học đã kết thúc từ dropdown bên trên.</p>
            <p><span class="font-semibold">Bước 2:</span> Nhấn nút "Tính lương" để hệ thống tự động tính lương cho giáo viên (40%) và trợ giảng (15%) của lớp học đó.</p>
            <p><span class="font-semibold">Bước 3:</span> Sau khi tính lương, bản ghi lương sẽ được tạo với trạng thái "Chưa thanh toán".</p>
            <p><span class="font-semibold">Bước 4:</span> Khi đã thanh toán cho giáo viên/trợ giảng, nhấn biểu tượng <i class="fas fa-check-circle text-green-600"></i> để cập nhật trạng thái.</p>
            <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                <p class="text-yellow-700"><i class="fas fa-exclamation-triangle mr-1"></i> Lưu ý: Chỉ tính lương cho lớp học một lần. Hệ thống sẽ không cho phép tính lương lại cho lớp học đã được tính.</p>
            </div>
        </div>
    </div>

    <!-- Lương giáo viên -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Quản lý lương giáo viên</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giáo viên</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lớp học</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tháng</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(count($luongGiaoViens) > 0)
                    @foreach($luongGiaoViens as $luong)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        @if($luong->giaoVien && $luong->giaoVien->nguoiDung)
                                            {{ $luong->giaoVien->nguoiDung->ho }} {{ $luong->giaoVien->nguoiDung->ten }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $luong->giaoVien && $luong->giaoVien->nguoiDung ? $luong->giaoVien->nguoiDung->email : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $luong->lopHoc ? $luong->lopHoc->ten : 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $luong->lopHoc ? $luong->lopHoc->ma_lop : 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($luong->so_tien, 0, ',', '.') }} VND</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $luong->thang ? \Carbon\Carbon::parse($luong->thang)->format('m/Y') : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(isset($luong->trang_thai) && $luong->trang_thai == 'da_thanh_toan')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Đã thanh toán
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Chưa thanh toán
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.luong.show-giao-vien', $luong->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(!isset($luong->trang_thai) || $luong->trang_thai != 'da_thanh_toan')
                            <a href="#" onclick="event.preventDefault(); document.getElementById('confirm-payment-{{ $luong->id }}').submit();" class="text-green-600 hover:text-green-900 mr-3">
                                <i class="fas fa-check-circle"></i>
                            </a>
                            <form id="confirm-payment-{{ $luong->id }}" action="{{ route('admin.luong.thanh-toan-giao-vien', $luong->id) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Không có dữ liệu lương giáo viên
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <!-- Phân trang -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            @if(method_exists($luongGiaoViens, 'links'))
                {{ $luongGiaoViens->links() }}
            @endif
        </div>
    </div>

    <!-- Lương trợ giảng -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Quản lý lương trợ giảng</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trợ giảng</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lớp học</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tháng</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(count($luongTroGiangs) > 0)
                    @foreach($luongTroGiangs as $luong)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        @if($luong->troGiang && $luong->troGiang->nguoiDung)
                                            {{ $luong->troGiang->nguoiDung->ho }} {{ $luong->troGiang->nguoiDung->ten }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $luong->troGiang && $luong->troGiang->nguoiDung ? $luong->troGiang->nguoiDung->email : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $luong->lopHoc ? $luong->lopHoc->ten : 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $luong->lopHoc ? $luong->lopHoc->ma_lop : 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($luong->so_tien, 0, ',', '.') }} VND</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $luong->thang ? \Carbon\Carbon::parse($luong->thang)->format('m/Y') : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(isset($luong->trang_thai) && $luong->trang_thai == 'da_thanh_toan')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Đã thanh toán
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Chưa thanh toán
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.luong.show-tro-giang', $luong->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(!isset($luong->trang_thai) || $luong->trang_thai != 'da_thanh_toan')
                            <a href="#" onclick="event.preventDefault(); document.getElementById('confirm-payment-tg-{{ $luong->id }}').submit();" class="text-green-600 hover:text-green-900 mr-3">
                                <i class="fas fa-check-circle"></i>
                            </a>
                            <form id="confirm-payment-tg-{{ $luong->id }}" action="{{ route('admin.luong.thanh-toan-tro-giang', $luong->id) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Không có dữ liệu lương trợ giảng
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <!-- Phân trang -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            @if(method_exists($luongTroGiangs, 'links'))
                {{ $luongTroGiangs->links() }}
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const thangInput = document.getElementById('thang');
        const trangThaiSelect = document.getElementById('loc-trang-thai');
        const lopHocSelect = document.getElementById('lop_hoc_id');
        const hiddenLopHocInput = document.getElementById('lop_hoc_id_hidden');
        const calculateForm = document.getElementById('calculateForm');
        
        function locDuLieu() {
            const thang = thangInput.value;
            const trangThai = trangThaiSelect.value;
            
            let url = '{{ route("admin.luong.index") }}?';
            
            if (thang) {
                url += `thang=${thang}`;
            }
            
            if (trangThai !== 'all') {
                url += `${url.endsWith('?') ? '' : '&'}trang_thai=${trangThai}`;
            }
            
            window.location.href = url;
        }
        
        thangInput.addEventListener('change', locDuLieu);
        trangThaiSelect.addEventListener('change', locDuLieu);

        // Đồng bộ giá trị dropdown vào hidden input khi submit form
        calculateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            hiddenLopHocInput.value = lopHocSelect.value;
            if (!hiddenLopHocInput.value) {
                alert('Vui lòng chọn lớp học trước khi tính lương');
                return;
            }
            calculateForm.submit();
        });
    });
</script>
@endsection 