@extends('layouts.hoc-vien')

@section('title', 'Chi tiết thanh toán')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết thanh toán</h1>
        <a href="{{ route('hoc-vien.thanh-toan.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
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
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin thanh toán</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <tr>
                                <th style="width: 200px;">Phương thức thanh toán</th>
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
                                <th>Ghi chú</th>
                                <td>{{ $thanhToan->ghi_chu ?? 'Không có' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin lớp học</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <tr>
                                <th style="width: 200px;">Lớp học</th>
                                <td>{{ $thanhToan->dangKyHoc->lopHoc->ten }}</td>
                            </tr>
                            <tr>
                                <th>Khóa học</th>
                                <td>{{ $thanhToan->dangKyHoc->lopHoc->khoaHoc->ten }}</td>
                            </tr>
                            <tr>
                                <th>Mã lớp</th>
                                <td>{{ $thanhToan->dangKyHoc->lopHoc->ma_lop }}</td>
                            </tr>
                            <tr>
                                <th>Giáo viên</th>
                                <td>{{ $thanhToan->dangKyHoc->lopHoc->giaoVien->nguoiDung->ho . ' ' . $thanhToan->dangKyHoc->lopHoc->giaoVien->nguoiDung->ten }}</td>
                            </tr>
                            <tr>
                                <th>Học phí</th>
                                <td>{{ number_format($thanhToan->dangKyHoc->hoc_phi, 0, ',', '.') }} VNĐ</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($thanhToan->trang_thai == 'cho_xac_nhan')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thao tác</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('hoc-vien.thanh-toan.cancel', $thanhToan->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy thanh toán này?')">
                            <i class="fas fa-times fa-sm"></i> Hủy thanh toán
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($thanhToan->trang_thai == 'da_thanh_toan')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin xác nhận</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h5 class="alert-heading">Thanh toán đã được xác nhận!</h5>
                        <p>Thanh toán của bạn đã được xác nhận vào ngày {{ $thanhToan->ngay_thanh_toan->format('d/m/Y H:i:s') }}.</p>
                        <hr>
                        <p class="mb-0">Bạn đã hoàn thành thanh toán học phí cho lớp học {{ $thanhToan->dangKyHoc->lopHoc->ten }}. Bạn có thể truy cập vào lớp học để bắt đầu học tập.</p>
                    </div>
                    <a href="{{ route('hoc-vien.lop-hoc.show', $thanhToan->dangKyHoc->lopHoc->id) }}" class="btn btn-primary">
                        <i class="fas fa-chalkboard-teacher fa-sm"></i> Đi đến lớp học
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($thanhToan->trang_thai == 'da_huy')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin hủy</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h5 class="alert-heading">Thanh toán đã bị hủy!</h5>
                        <p>Thanh toán này đã bị hủy.</p>
                        <hr>
                        <p class="mb-0">Nếu bạn vẫn muốn tham gia lớp học, vui lòng tạo một thanh toán mới hoặc liên hệ với trung tâm để được hỗ trợ.</p>
                    </div>
                    <a href="{{ route('hoc-vien.thanh-toan.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus fa-sm"></i> Tạo thanh toán mới
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 