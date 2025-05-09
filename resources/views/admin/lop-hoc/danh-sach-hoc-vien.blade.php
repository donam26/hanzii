@extends('layouts.dashboard')

@section('title', 'Danh sách học viên lớp ' . $lopHoc->ten)
@section('page-heading', 'Danh sách học viên lớp ' . $lopHoc->ten)

@php
    $active = 'lop-hoc';
    $role = 'admin';
@endphp

@section('content')
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Danh sách học viên lớp {{ $lopHoc->ten }}</h2>
                <p class="mt-1 text-sm text-gray-600">Khóa học: {{ $lopHoc->khoaHoc->ten }}</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-2">
                <a href="{{ route('admin.lop-hoc.show', $lopHoc->id) }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 active:bg-gray-700 disabled:opacity-25 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại
                </a>
                <button type="button" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-700 disabled:opacity-25 transition" data-modal-target="modalThemHocVien" data-modal-toggle="modalThemHocVien">
                    <i class="fas fa-user-plus mr-2"></i> Thêm học viên
                </button>
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
                <p class="mt-1 text-sm text-gray-900">Học phí: {{ number_format($lopHoc->khoaHoc->hoc_phi, 0, ',', '.') }} đ</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Giáo viên</h3>
                <p class="mt-1 text-sm text-gray-900">{{ $lopHoc->giaoVien->nguoiDung->ho_ten ?? 'Chưa phân công' }}</p>
                <p class="mt-1 text-sm text-gray-900">Trợ giảng: {{ $lopHoc->troGiang->nguoiDung->ho_ten ?? 'Chưa phân công' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Thời gian học</h3>
                <p class="mt-1 text-sm text-gray-900">Bắt đầu: {{ \Carbon\Carbon::parse($lopHoc->ngay_bat_dau)->format('d/m/Y') }}</p>
                <p class="mt-1 text-sm text-gray-900">Kết thúc: {{ \Carbon\Carbon::parse($lopHoc->ngay_ket_thuc)->format('d/m/Y') }}</p>
                <p class="mt-1 text-sm text-gray-900">Lịch học: {{ $lopHoc->lich_hoc }}</p>
            </div>
        </div>
    </div>
    
    <!-- Thống kê nhanh -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $dangKyHocs->count() }}</h3>
                    <p class="text-sm text-gray-500">Tổng số học viên</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $confirmedStudents->count() }}</h3>
                    <p class="text-sm text-gray-500">Đã xác nhận</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $pendingStudents->count() }}</h3>
                    <p class="text-sm text-gray-500">Chờ xác nhận</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white shadow rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                    <i class="fas fa-chair"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ max(0, $lopHoc->so_luong_toi_da - $confirmedStudents->count()) }}</h3>
                    <p class="text-sm text-gray-500">Còn trống</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab Nav -->
    <div class="mb-4 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px" id="tabDanhSachHocVien" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-red-600 rounded-t-lg active" id="all-tab" data-tabs-target="#tab-all" type="button" role="tab" aria-controls="tab-all" aria-selected="true">
                    Tất cả ({{ $dangKyHocs->count() }})
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:border-gray-300" id="confirmed-tab" data-tabs-target="#tab-confirmed" type="button" role="tab" aria-controls="tab-confirmed" aria-selected="false">
                    Đã xác nhận ({{ $confirmedStudents->count() }})
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:border-gray-300" id="pending-tab" data-tabs-target="#tab-pending" type="button" role="tab" aria-controls="tab-pending" aria-selected="false">
                    Chờ xác nhận ({{ $pendingStudents->count() }})
                </button>
            </li>
        </ul>
    </div>
    
    <!-- Tab Content -->
    <div id="tabContentDanhSachHocVien">
        <!-- Tất cả học viên -->
        <div class="bg-white shadow rounded-lg overflow-hidden mb-6" id="tab-all" role="tabpanel" aria-labelledby="all-tab">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
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
                        @forelse($dangKyHocs as $index => $dangKy)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-700">
                                            {{ strtoupper(substr($dangKy->hocVien->nguoiDung->ho_ten ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $dangKy->hocVien->nguoiDung->ho_ten ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <i class="fas fa-envelope text-gray-400 mr-1"></i> {{ $dangKy->hocVien->nguoiDung->email ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-900">
                                        <i class="fas fa-phone text-gray-400 mr-1"></i> {{ $dangKy->hocVien->nguoiDung->so_dien_thoai ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($dangKy->hoc_phi, 0, ',', '.') }} đ
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($dangKy->ngay_dang_ky)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClass = '';
                                        $statusText = '';
                                        
                                        switch($dangKy->trang_thai) {
                                            case 'cho_xac_nhan':
                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                                $statusText = 'Chờ xác nhận';
                                                break;
                                            case 'da_thanh_toan':
                                                $statusClass = 'bg-blue-100 text-blue-800';
                                                $statusText = 'Đã thanh toán';
                                                break;
                                            case 'da_xac_nhan':
                                                $statusClass = 'bg-green-100 text-green-800';
                                                $statusText = 'Đã xác nhận';
                                                break;
                                        }
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2 justify-end">
                                        <a href="{{ route('admin.hoc-vien.show', $dangKy->hocVien->id) }}" class="text-blue-600 hover:text-blue-900" title="Chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($dangKy->trang_thai == 'cho_xac_nhan')
                                            <form action="{{ route('admin.dang-ky-hoc.xac-nhan', $dangKy->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="text-green-600 hover:text-green-900" title="Xác nhận">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.lop-hoc.remove-student', ['id' => $lopHoc->id, 'dangKyId' => $dangKy->id]) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa học viên này khỏi lớp học?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Xóa">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Chưa có học viên nào đăng ký lớp học này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal thêm học viên -->
    <div id="modalThemHocVien" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-5 border-b rounded-t">
                    <h3 class="text-xl font-medium text-gray-900">
                        Thêm học viên vào lớp
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="modalThemHocVien">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Đóng</span>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <form action="{{ route('admin.lop-hoc.add-student', $lopHoc->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="hoc_vien_id" class="block mb-2 text-sm font-medium text-gray-900">Chọn học viên <span class="text-red-500">*</span></label>
                            <select id="hoc_vien_id" name="hoc_vien_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required>
                                <option value="">Chọn học viên</option>
                                @foreach($availableStudents as $hocVien)
                                    <option value="{{ $hocVien->id }}">
                                        {{ $hocVien->nguoiDung->ho . ' ' . $hocVien->nguoiDung->ten }} - {{ $hocVien->nguoiDung->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="hoc_phi" class="block mb-2 text-sm font-medium text-gray-900">Học phí <span class="text-red-500">*</span></label>
                            <input type="number" disabled id="hoc_phi" name="hoc_phi" value="{{ $lopHoc->khoaHoc->hoc_phi }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required>
                            <p class="mt-1 text-xs text-gray-500">Học phí mặc định: {{ number_format($lopHoc->khoaHoc->hoc_phi, 0, ',', '.') }} đ</p>
                        </div>
                        <div class="mb-4">
                            <label for="phuong_thuc_thanh_toan" class="block mb-2 text-sm font-medium text-gray-900">Phương thức thanh toán <span class="text-red-500">*</span></label>
                            <select id="phuong_thuc_thanh_toan" name="phuong_thuc_thanh_toan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required>
                                <option value="chuyen_khoan">Chuyển khoản</option>
                                <option value="tien_mat">Tiền mặt</option>
                                <option value="vi_dien_tu">Ví điện tử</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="ghi_chu" class="block mb-2 text-sm font-medium text-gray-900">Ghi chú</label>
                            <textarea id="ghi_chu" name="ghi_chu" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" rows="3"></textarea>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 mr-2" data-modal-hide="modalThemHocVien">Hủy</button>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Thêm học viên</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flowbite@1.6.5/dist/flowbite.min.js"></script>
<script>
    // Tabs control
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('[data-tabs-target]');
        const tabContents = document.querySelectorAll('[role="tabpanel"]');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = document.querySelector(tab.dataset.tabsTarget);
                
                tabContents.forEach(tabContent => {
                    tabContent.classList.add('hidden');
                });
                
                tabs.forEach(t => {
                    t.classList.remove('active', 'border-red-600');
                    t.classList.add('border-transparent');
                    t.setAttribute('aria-selected', false);
                });
                
                target.classList.remove('hidden');
                tab.classList.add('active', 'border-red-600');
                tab.classList.remove('border-transparent');
                tab.setAttribute('aria-selected', true);
            });
        });
    });
</script>
@endpush 