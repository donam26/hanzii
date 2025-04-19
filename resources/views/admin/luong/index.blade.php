@extends('layouts.dashboard')

@section('title', 'Quản lý lương')

@section('page-heading', 'Quản lý lương')

@php
    $active = 'luong';
    $role = 'admin';
@endphp

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex flex-col md:flex-row justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">Danh sách lương cần thanh toán</h2>
                        </div>
                        <div class="mt-3 md:mt-0 flex">
                            <div class="relative">
                                <form action="{{ route('admin.luong.index') }}" method="GET">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm..." class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 sm:text-sm border-gray-300 rounded-md">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <button type="submit" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="ml-2">
                                <select name="status" id="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" onchange="window.location.href='{{ route('admin.luong.index') }}?status='+this.value">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="chua_thanh_toan" {{ request('status') == 'chua_thanh_toan' ? 'selected' : '' }}>Chưa thanh toán</option>
                                    <option value="da_thanh_toan" {{ request('status') == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Khóa học
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Lớp học
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Giáo viên/Trợ giảng
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vai trò
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Số tiền
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Trạng thái
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Thao tác
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($luongs as $luong)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $luong->lopHoc->khoaHoc->ten }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $luong->lopHoc->ten }} ({{ $luong->lopHoc->ma }})
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $luong->nguoiDung->ho_ten }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $luong->vai_tro == 'giao_vien' ? 'Giáo viên' : 'Trợ giảng' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ number_format($luong->so_tien, 0, ',', '.') }} đ
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $luong->trang_thai == 'da_thanh_toan' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $luong->trang_thai == 'da_thanh_toan' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.luong.show', $luong->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($luong->trang_thai != 'da_thanh_toan')
                                                <a href="{{ route('admin.luong.edit', $luong->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.luong.thanh-toan', $luong->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Bạn có chắc chắn muốn đánh dấu lương này là đã thanh toán?')">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Không có dữ liệu lương
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $luongs->links() }}
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Thống kê lương</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-blue-800">Tổng lương đã thanh toán</h3>
                            <p class="mt-2 text-2xl font-semibold text-blue-900">
                                {{ number_format($tongLuongDaThanhToan, 0, ',', '.') }} đ
                            </p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-yellow-800">Tổng lương chưa thanh toán</h3>
                            <p class="mt-2 text-2xl font-semibold text-yellow-900">
                                {{ number_format($tongLuongChuaThanhToan, 0, ',', '.') }} đ
                            </p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-green-800">Tổng lương tháng này</h3>
                            <p class="mt-2 text-2xl font-semibold text-green-900">
                                {{ number_format($tongLuongThangNay, 0, ',', '.') }} đ
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 