@extends('layouts.admin')

@section('title', 'Chi tiết lương')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết lương</h1>
        <a href="{{ route('admin.luong.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Trạng thái thanh toán lương</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($luong->trang_thai == 'cho_thanh_toan')
                                    <span class="badge badge-warning">Chờ thanh toán</span>
                                @elseif($luong->trang_thai == 'da_thanh_toan')
                                    <span class="badge badge-success">Đã thanh toán</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng tiền lương</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($luong->tong_luong, 0, ',', '.') }} VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        @if($luong->giaoVien)
                            Thông tin giáo viên
                        @else
                            Thông tin trợ giảng
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if($luong->giaoVien)
                        <div class="text-center mb-4">
                            <img class="img-profile rounded-circle" width="120" height="120" 
                                src="{{ $luong->giaoVien->nguoiDung->avatar ? asset('storage/' . $luong->giaoVien->nguoiDung->avatar) : asset('img/undraw_profile.svg') }}">
                            <h4 class="mt-3">{{ $luong->giaoVien->nguoiDung->ho . ' ' . $luong->giaoVien->nguoiDung->ten }}</h4>
                            <p class="text-muted">Giáo viên</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $luong->giaoVien->nguoiDung->email }}</td>
                                </tr>
                                <tr>
                                    <th>Số điện thoại</th>
                                    <td>{{ $luong->giaoVien->nguoiDung->so_dien_thoai }}</td>
                                </tr>
                                <tr>
                                    <th>Bằng cấp</th>
                                    <td>{{ $luong->giaoVien->bang_cap }}</td>
                                </tr>
                                <tr>
                                    <th>Chuyên môn</th>
                                    <td>{{ $luong->giaoVien->chuyen_mon }}</td>
                                </tr>
                                <tr>
                                    <th>Kinh nghiệm</th>
                                    <td>{{ $luong->giaoVien->so_nam_kinh_nghiem }} năm</td>
                                </tr>
                            </table>
                        </div>
                    @else
                        <div class="text-center mb-4">
                            <img class="img-profile rounded-circle" width="120" height="120" 
                                src="{{ $luong->troGiang->nguoiDung->avatar ? asset('storage/' . $luong->troGiang->nguoiDung->avatar) : asset('img/undraw_profile.svg') }}">
                            <h4 class="mt-3">{{ $luong->troGiang->nguoiDung->ho . ' ' . $luong->troGiang->nguoiDung->ten }}</h4>
                            <p class="text-muted">Trợ giảng</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $luong->troGiang->nguoiDung->email }}</td>
                                </tr>
                                <tr>
                                    <th>Số điện thoại</th>
                                    <td>{{ $luong->troGiang->nguoiDung->so_dien_thoai }}</td>
                                </tr>
                                <tr>
                                    <th>Chuyên môn</th>
                                    <td>{{ $luong->troGiang->chuyen_mon }}</td>
                                </tr>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin lớp học</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Tên lớp</th>
                                <td>{{ $luong->lopHoc->ten }}</td>
                            </tr>
                            <tr>
                                <th>Mã lớp</th>
                                <td>{{ $luong->lopHoc->ma_lop }}</td>
                            </tr>
                            <tr>
                                <th>Khóa học</th>
                                <td>{{ $luong->lopHoc->khoaHoc->ten }}</td>
                            </tr>
                            <tr>
                                <th>Thời gian bắt đầu</th>
                                <td>{{ $luong->lopHoc->thoi_gian_bat_dau ? $luong->lopHoc->thoi_gian_bat_dau->format('d/m/Y') : 'Chưa có' }}</td>
                            </tr>
                            <tr>
                                <th>Thời gian kết thúc</th>
                                <td>{{ $luong->lopHoc->thoi_gian_ket_thuc ? $luong->lopHoc->thoi_gian_ket_thuc->format('d/m/Y') : 'Chưa có' }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái lớp</th>
                                <td>
                                    @if($luong->lopHoc->trang_thai == 'sap_dien_ra')
                                        <span class="badge badge-warning">Sắp diễn ra</span>
                                    @elseif($luong->lopHoc->trang_thai == 'dang_dien_ra')
                                        <span class="badge badge-primary">Đang diễn ra</span>
                                    @elseif($luong->lopHoc->trang_thai == 'da_hoan_thanh')
                                        <span class="badge badge-success">Đã hoàn thành</span>
                                    @elseif($luong->lopHoc->trang_thai == 'da_huy')
                                        <span class="badge badge-danger">Đã hủy</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin tiền lương</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 300px;">Vai trò</th>
                                <td>{{ ucfirst($luong->vaiTro->ten) }}</td>
                            </tr>
                            <tr>
                                <th>Hệ số lương</th>
                                <td>{{ $luong->vaiTro->he_so_luong }}%</td>
                            </tr>
                            <tr>
                                <th>Tổng học phí thu được từ lớp</th>
                                <td>{{ number_format($luong->tong_hoc_phi_thu_duoc, 0, ',', '.') }} VNĐ</td>
                            </tr>
                            <tr>
                                <th>Tổng lương</th>
                                <td>{{ number_format($luong->tong_luong, 0, ',', '.') }} VNĐ</td>
                            </tr>
                            <tr>
                                <th>Trạng thái</th>
                                <td>
                                    @if($luong->trang_thai == 'cho_thanh_toan')
                                        <span class="badge badge-warning">Chờ thanh toán</span>
                                    @elseif($luong->trang_thai == 'da_thanh_toan')
                                        <span class="badge badge-success">Đã thanh toán</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày thanh toán</th>
                                <td>{{ $luong->ngay_thanh_toan ? $luong->ngay_thanh_toan->format('d/m/Y H:i:s') : 'Chưa thanh toán' }}</td>
                            </tr>
                            <tr>
                                <th>Ngày tạo</th>
                                <td>{{ $luong->tao_luc->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách học viên đã thanh toán</h6>
                </div>
                <div class="card-body">
                    @if($hocViens->isEmpty())
                        <div class="text-center">
                            <p>Chưa có học viên nào thanh toán cho lớp học này.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Học phí</th>
                                        <th>Ngày thanh toán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hocViens as $dangKy)
                                    <tr>
                                        <td>{{ $dangKy->hocVien->id }}</td>
                                        <td>{{ $dangKy->hocVien->nguoiDung->ho . ' ' . $dangKy->hocVien->nguoiDung->ten }}</td>
                                        <td>{{ $dangKy->hocVien->nguoiDung->email }}</td>
                                        <td>{{ $dangKy->hocVien->nguoiDung->so_dien_thoai }}</td>
                                        <td>{{ number_format($dangKy->hoc_phi, 0, ',', '.') }} VNĐ</td>
                                        <td>
                                            @if($dangKy->thanhToan->first())
                                                {{ $dangKy->thanhToan->first()->ngay_thanh_toan ? $dangKy->thanhToan->first()->ngay_thanh_toan->format('d/m/Y') : 'Chưa có' }}
                                            @else
                                                Chưa có
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($luong->trang_thai == 'cho_thanh_toan')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cập nhật trạng thái thanh toán</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.luong.update', $luong->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="trang_thai">Trạng thái</label>
                            <select class="form-control @error('trang_thai') is-invalid @enderror" id="trang_thai" name="trang_thai" required>
                                <option value="cho_thanh_toan" {{ $luong->trang_thai == 'cho_thanh_toan' ? 'selected' : '' }}>Chờ thanh toán</option>
                                <option value="da_thanh_toan">Đã thanh toán</option>
                            </select>
                            @error('trang_thai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group" id="ngay_thanh_toan_group">
                            <label for="ngay_thanh_toan">Ngày thanh toán</label>
                            <input type="datetime-local" class="form-control @error('ngay_thanh_toan') is-invalid @enderror" id="ngay_thanh_toan" name="ngay_thanh_toan" value="{{ old('ngay_thanh_toan', $luong->ngay_thanh_toan ? $luong->ngay_thanh_toan->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                            @error('ngay_thanh_toan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Ẩn/hiện trường ngày thanh toán
        function toggleNgayThanhToan() {
            if ($('#trang_thai').val() === 'da_thanh_toan') {
                $('#ngay_thanh_toan_group').show();
            } else {
                $('#ngay_thanh_toan_group').hide();
            }
        }
        
        // Gọi hàm khi trang tải
        toggleNgayThanhToan();
        
        // Gọi hàm khi thay đổi trạng thái
        $('#trang_thai').change(function() {
            toggleNgayThanhToan();
        });
    });
</script>
@endsection 