@extends('layouts.dashboard')

@section('title', 'Chi tiết lương')
@section('page-heading', 'Chi tiết lương')

@php
    $active = 'lop-hoc';
    $role = 'giao_vien';
@endphp

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h6>Chi tiết lương</h6>
                        <a href="{{ route('giao-vien.luong.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Mã lớp:</strong> {{ $luong->lop_hoc->ma_lop }}</p>
                            <p><strong>Tên lớp:</strong> {{ $luong->lop_hoc->ten_lop }}</p>
                            <p><strong>Khóa học:</strong> {{ $luong->lop_hoc->khoa_hoc->ten_khoa_hoc }}</p>
                            <p><strong>Số tiền:</strong> {{ number_format($luong->so_tien, 0, ',', '.') }} VND</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tháng:</strong> {{ \Carbon\Carbon::parse($luong->thang)->format('m/Y') }}</p>
                            <p><strong>Ngày tạo:</strong> {{ $luong->created_at->format('d/m/Y H:i:s') }}</p>
                            <p><strong>Trạng thái:</strong> 
                                @if($luong->da_nhan)
                                    <span class="badge bg-gradient-success">Đã nhận</span>
                                @else
                                    <span class="badge bg-gradient-danger">Chưa nhận</span>
                                @endif
                            </p>
                            @if($luong->da_nhan)
                                <p><strong>Ngày nhận:</strong> {{ \Carbon\Carbon::parse($luong->ngay_nhan)->format('d/m/Y H:i:s') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Thông tin lớp học</h6>
                            <hr class="horizontal dark">
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tổng số học viên:</strong> {{ $luong->lop_hoc->hoc_viens->count() }}</p>
                            <p><strong>Thời gian bắt đầu:</strong> {{ $luong->lop_hoc->thoi_gian_bat_dau->format('d/m/Y') }}</p>
                            <p><strong>Thời gian kết thúc:</strong> {{ $luong->lop_hoc->thoi_gian_ket_thuc ? $luong->lop_hoc->thoi_gian_ket_thuc->format('d/m/Y') : 'Chưa kết thúc' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Giờ học:</strong> {{ $luong->lop_hoc->gio_hoc }}</p>
                            <p><strong>Ngày học:</strong> {{ $luong->lop_hoc->ngay_hoc }}</p>
                            <p><strong>Trạng thái lớp:</strong> 
                                @if($luong->lop_hoc->trang_thai == 1)
                                    <span class="badge bg-gradient-success">Đang hoạt động</span>
                                @elseif($luong->lop_hoc->trang_thai == 0)
                                    <span class="badge bg-gradient-danger">Đã kết thúc</span>
                                @else
                                    <span class="badge bg-gradient-warning">Chưa bắt đầu</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 