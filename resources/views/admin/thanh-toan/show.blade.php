@extends('layouts.admin')

@section('title', 'Chi tiết thanh toán')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Chi tiết thanh toán #{{ $thanhToan->id }}</h6>
            <div class="dropdown no-arrow">
                <a href="{{ route('admin.thanh-toan.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-arrow-left fa-sm"></i> Quay lại
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Trạng thái thanh toán</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        @if($thanhToan->trang_thai == 'cho_xac_nhan')
                                            <span class="badge badge-warning">Chờ xác nhận</span>
                                        @elseif($thanhToan->trang_thai == 'da_thanh_toan')
                                            <span class="badge badge-success">Đã thanh toán</span>
                                        @elseif($thanhToan->trang_thai == 'da_huy')
                                            <span class="badge badge-danger">Đã hủy</span>
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
                                        Số tiền</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($thanhToan->so_tien, 0, ',', '.') }} VNĐ</div>
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
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin thanh toán</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Phương thức thanh toán</th>
                                    <td>
                                        @if($thanhToan->phuong_thuc_thanh_toan == 'chuyen_khoan')
                                            <span class="badge badge-info">Chuyển khoản ngân hàng</span>
                                        @elseif($thanhToan->phuong_thuc_thanh_toan == 'vi_dien_tu')
                                            <span class="badge badge-info">Ví điện tử (MoMo, ZaloPay)</span>
                                        @elseif($thanhToan->phuong_thuc_thanh_toan == 'tien_mat')
                                            <span class="badge badge-info">Tiền mặt tại trung tâm</span>
                                        @elseif($thanhToan->phuong_thuc_thanh_toan == 'vnpay')
                                            <span class="badge badge-info">Thanh toán qua VNPay</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Mã giao dịch</th>
                                    <td>{{ $thanhToan->ma_giao_dich ?? 'Không có' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày thanh toán</th>
                                    <td>{{ $thanhToan->ngay_thanh_toan ? $thanhToan->ngay_thanh_toan->format('d/m/Y H:i:s') : 'Chưa thanh toán' }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày tạo</th>
                                    <td>{{ $thanhToan->tao_luc->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Ngày cập nhật</th>
                                    <td>{{ $thanhToan->cap_nhat_luc->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Ghi chú</th>
                                    <td>{{ $thanhToan->ghi_chu ?? 'Không có' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin học viên</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img class="img-profile rounded-circle" width="100" height="100" 
                                    src="{{ $thanhToan->dangKyHoc->hocVien->nguoiDung->avatar ? asset('storage/' . $thanhToan->dangKyHoc->hocVien->nguoiDung->avatar) : asset('img/undraw_profile.svg') }}">
                            </div>
                            <h5 class="text-center font-weight-bold">{{ $thanhToan->dangKyHoc->hocVien->nguoiDung->ho . ' ' . $thanhToan->dangKyHoc->hocVien->nguoiDung->ten }}</h5>
                            <p class="text-center text-muted">{{ $thanhToan->dangKyHoc->hocVien->nguoiDung->email }}</p>
                            <p class="text-center text-muted">{{ $thanhToan->dangKyHoc->hocVien->nguoiDung->so_dien_thoai }}</p>
                            <hr>
                            <a href="{{ route('admin.hoc-vien.show', $thanhToan->dangKyHoc->hocVien->id) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-user fa-sm"></i> Xem thông tin học viên
                            </a>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin lớp học</h6>
                        </div>
                        <div class="card-body">
                            <h5 class="font-weight-bold">{{ $thanhToan->dangKyHoc->lopHoc->ten }}</h5>
                            <p class="text-muted">Khóa học: {{ $thanhToan->dangKyHoc->lopHoc->khoaHoc->ten }}</p>
                            <p class="text-muted">Mã lớp: {{ $thanhToan->dangKyHoc->lopHoc->ma_lop }}</p>
                            <p class="text-muted">Giáo viên: {{ $thanhToan->dangKyHoc->lopHoc->giaoVien->nguoiDung->ho . ' ' . $thanhToan->dangKyHoc->lopHoc->giaoVien->nguoiDung->ten }}</p>
                            <p class="text-muted">Trợ giảng: {{ $thanhToan->dangKyHoc->lopHoc->troGiang->nguoiDung->ho . ' ' . $thanhToan->dangKyHoc->lopHoc->troGiang->nguoiDung->ten }}</p>
                            <hr>
                            <a href="{{ route('admin.lop-hoc.show', $thanhToan->dangKyHoc->lopHoc->id) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-chalkboard-teacher fa-sm"></i> Xem thông tin lớp học
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if($thanhToan->trang_thai == 'cho_xac_nhan')
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thao tác</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="{{ route('admin.thanh-toan.confirm', $thanhToan->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fas fa-check fa-sm"></i> Xác nhận thanh toán
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form action="{{ route('admin.thanh-toan.cancel', $thanhToan->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-block">
                                            <i class="fas fa-times fa-sm"></i> Hủy thanh toán
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 