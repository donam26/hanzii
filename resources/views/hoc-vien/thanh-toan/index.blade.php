@extends('layouts.hoc-vien')

@section('title', 'Danh sách thanh toán học phí')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thanh toán học phí</h1>
        <a href="{{ route('hoc-vien.thanh-toan.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Thanh toán mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách thanh toán của bạn</h6>
                </div>
                <div class="card-body">
                    @if($thanhToans->isEmpty())
                        <div class="text-center">
                            <p>Bạn chưa có khoản thanh toán nào.</p>
                            <a href="{{ route('hoc-vien.thanh-toan.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus fa-sm"></i> Tạo thanh toán mới
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Lớp học</th>
                                        <th>Khóa học</th>
                                        <th>Số tiền</th>
                                        <th>Phương thức</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Tác vụ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($thanhToans as $thanhToan)
                                    <tr>
                                        <td>{{ $thanhToan->id }}</td>
                                        <td>{{ $thanhToan->dangKyHoc->lopHoc->ten }}</td>
                                        <td>{{ $thanhToan->dangKyHoc->lopHoc->khoaHoc->ten }}</td>
                                        <td>{{ number_format($thanhToan->so_tien, 0, ',', '.') }} VNĐ</td>
                                        <td>
                                            @if($thanhToan->phuong_thuc_thanh_toan == 'chuyen_khoan')
                                                <span class="badge badge-info">Chuyển khoản</span>
                                            @elseif($thanhToan->phuong_thuc_thanh_toan == 'vi_dien_tu')
                                                <span class="badge badge-info">Ví điện tử</span>
                                            @elseif($thanhToan->phuong_thuc_thanh_toan == 'tien_mat')
                                                <span class="badge badge-info">Tiền mặt</span>
                                            @elseif($thanhToan->phuong_thuc_thanh_toan == 'vnpay')
                                                <span class="badge badge-info">VNPay</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($thanhToan->trang_thai == 'cho_xac_nhan')
                                                <span class="badge badge-warning">Chờ xác nhận</span>
                                            @elseif($thanhToan->trang_thai == 'da_thanh_toan')
                                                <span class="badge badge-success">Đã thanh toán</span>
                                            @elseif($thanhToan->trang_thai == 'da_huy')
                                                <span class="badge badge-danger">Đã hủy</span>
                                            @endif
                                        </td>
                                        <td>{{ $thanhToan->tao_luc->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('hoc-vien.thanh-toan.show', $thanhToan->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($thanhToan->trang_thai == 'cho_xac_nhan')
                                                <form action="{{ route('hoc-vien.thanh-toan.cancel', $thanhToan->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn hủy thanh toán này?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $thanhToans->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 