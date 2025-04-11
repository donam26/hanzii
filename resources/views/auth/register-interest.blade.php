@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Đăng ký quan tâm</h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Vui lòng cung cấp thông tin để chúng tôi có thể tư vấn khóa học phù hợp cho bạn
            </p>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('register.interest') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="ten" class="block text-sm font-medium text-gray-700">Họ và tên</label>
                    <input id="ten" name="ten" type="text" autocomplete="name" required
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Họ và tên" value="{{ old('ten') }}">
                    @error('ten')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Email" value="{{ old('email') }}">
                    @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="so_dien_thoai" class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                    <input id="so_dien_thoai" name="so_dien_thoai" type="text" autocomplete="tel" required
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Số điện thoại" value="{{ old('so_dien_thoai') }}">
                    @error('so_dien_thoai')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="khoa_hoc_id" class="block text-sm font-medium text-gray-700">Khóa học quan tâm</label>
                    <select id="khoa_hoc_id" name="khoa_hoc_id" required
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Chọn khóa học</option>
                        @foreach($khoaHocs as $khoaHoc)
                        <option value="{{ $khoaHoc->id }}" {{ old('khoa_hoc_id') == $khoaHoc->id ? 'selected' : '' }}>
                            {{ $khoaHoc->ten }} - {{ number_format($khoaHoc->hoc_phi, 0, ',', '.') }} VNĐ
                        </option>
                        @endforeach
                    </select>
                    @error('khoa_hoc_id')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="hinh_thuc_hoc" class="block text-sm font-medium text-gray-700">Hình thức học</label>
                        <select id="hinh_thuc_hoc" name="hinh_thuc_hoc"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="online" {{ old('hinh_thuc_hoc') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="offline" {{ old('hinh_thuc_hoc') == 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="hybrid" {{ old('hinh_thuc_hoc') == 'hybrid' ? 'selected' : '' }}>Kết hợp (Hybrid)</option>
                        </select>
                        @error('hinh_thuc_hoc')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="lich_hoc_mong_muon" class="block text-sm font-medium text-gray-700">Lịch học mong muốn</label>
                        <select id="lich_hoc_mong_muon" name="lich_hoc_mong_muon"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="sang" {{ old('lich_hoc_mong_muon') == 'sang' ? 'selected' : '' }}>Buổi sáng</option>
                            <option value="chieu" {{ old('lich_hoc_mong_muon') == 'chieu' ? 'selected' : '' }}>Buổi chiều</option>
                            <option value="toi" {{ old('lich_hoc_mong_muon') == 'toi' ? 'selected' : '' }}>Buổi tối</option>
                            <option value="cuoi_tuan" {{ old('lich_hoc_mong_muon') == 'cuoi_tuan' ? 'selected' : '' }}>Cuối tuần</option>
                        </select>
                        @error('lich_hoc_mong_muon')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="dia_chi" class="block text-sm font-medium text-gray-700">Địa chỉ</label>
                    <textarea id="dia_chi" name="dia_chi"
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Địa chỉ">{{ old('dia_chi') }}</textarea>
                    @error('dia_chi')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="ly_do" class="block text-sm font-medium text-gray-700">Lý do bạn muốn học tiếng Nhật</label>
                    <textarea id="ly_do" name="ly_do"
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Lý do bạn muốn học tiếng Nhật">{{ old('ly_do') }}</textarea>
                    @error('ly_do')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Đăng ký ngay
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 