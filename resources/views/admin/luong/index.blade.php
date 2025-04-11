@extends('layouts.dashboard')

@section('title', 'Quản lý lương')

@section('page-heading', 'Quản lý lương nhân viên')

@php
    $active = 'luong';
    $role = 'admin';
@endphp

@section('content')
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6 bg-white border-b border-gray-200 flex justify-between">
            <h3 class="text-lg font-medium text-gray-900">Tính lương tháng</h3>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.luong.calculate') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label for="thang" class="block text-sm font-medium text-gray-700 mb-1">Tháng</label>
                    <select id="thang" name="thang" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ now()->month == $i ? 'selected' : '' }}>Tháng {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="nam" class="block text-sm font-medium text-gray-700 mb-1">Năm</label>
                    <select id="nam" name="nam" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @php
                            $currentYear = now()->year;
                        @endphp
                        @for ($i = $currentYear - 2; $i <= $currentYear; $i++)
                            <option value="{{ $i }}" {{ $currentYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-calculator mr-2"></i> Tính lương
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Danh sách lương nhân viên</h3>
        </div>

        <div class="p-6">
            <div class="mb-4 flex justify-between">
                <div>
                    <h4 class="text-md font-medium text-gray-800">Tháng: {{ $thang ?? now()->month }}/{{ $nam ?? now()->year }}</h4>
                </div>
                <div>
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-file-excel mr-2"></i> Xuất Excel
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhân viên</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lương cơ bản</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số giờ dạy</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phụ cấp</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thưởng</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khấu trừ</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng lương</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($bangLuongs ?? [] as $bangLuong)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-800 font-medium">{{ substr($bangLuong->nhanVien->nguoiDung->ho, 0, 1) }}{{ substr($bangLuong->nhanVien->nguoiDung->ten, 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $bangLuong->nhanVien->nguoiDung->ho }} {{ $bangLuong->nhanVien->nguoiDung->ten }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $bangLuong->nhanVien->nguoiDung->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($bangLuong->luong_co_ban) }}đ</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $bangLuong->so_gio_day }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($bangLuong->phu_cap) }}đ</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($bangLuong->thuong) }}đ</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ number_format($bangLuong->khau_tru) }}đ</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($bangLuong->tong_luong) }}đ</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($bangLuong->trang_thai == 'da_thanh_toan')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Đã thanh toán
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Chưa thanh toán
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button type="button" class="text-indigo-600 hover:text-indigo-900" onclick="openEditModal('{{ $bangLuong->id }}')">
                                        <i class="fas fa-edit"></i> Chỉnh sửa
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                    Không có dữ liệu lương. Vui lòng tính lương cho tháng hiện tại.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($bangLuongs) && $bangLuongs->hasPages())
                <div class="mt-4">
                    {{ $bangLuongs->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal chỉnh sửa lương -->
    <div id="editModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="bg-white rounded-lg p-8 max-w-md w-full z-10">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Chỉnh sửa thông tin lương</h3>
            
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="phu_cap" class="block text-gray-700 text-sm font-bold mb-2">Phụ cấp</label>
                    <input type="number" id="phu_cap" name="phu_cap" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <div class="mb-4">
                    <label for="thuong" class="block text-gray-700 text-sm font-bold mb-2">Thưởng</label>
                    <input type="number" id="thuong" name="thuong" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <div class="mb-4">
                    <label for="khau_tru" class="block text-gray-700 text-sm font-bold mb-2">Khấu trừ</label>
                    <input type="number" id="khau_tru" name="khau_tru" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>
                
                <div class="mb-4">
                    <label for="ghi_chu" class="block text-gray-700 text-sm font-bold mb-2">Ghi chú</label>
                    <textarea id="ghi_chu" name="ghi_chu" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="trang_thai" value="da_thanh_toan" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-600">Đánh dấu đã thanh toán</span>
                    </label>
                </div>
                
                <div class="flex justify-end mt-6">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 mr-2">
                        Hủy
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function openEditModal(id) {
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editForm').action = `{{ url('admin/luong') }}/${id}`;
        
        // Thêm code để fetch dữ liệu của bảng lương theo id và điền vào form
    }
    
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endsection 