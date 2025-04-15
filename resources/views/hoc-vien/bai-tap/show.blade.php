@extends('layouts.dashboard')

@section('title', 'Chi tiết bài tập')
@section('page-heading', 'Chi tiết bài tập')

@php
    $active = 'lop-hoc';
    $role = 'hoc_vien';
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ $baiTap->tieu_de }}
                    </h3>
                    <div class="card-tools">
                        @if ($baiTap->han_nop && now()->gt($baiTap->han_nop))
                            <span class="badge badge-danger">Đã hết hạn</span>
                        @elseif ($baiTapDaNop)
                            <span class="badge badge-success">Đã nộp</span>
                        @else
                            <span class="badge badge-warning">Chưa nộp</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Lớp học:</label>
                                <p>{{ $lopHoc->ten }}</p>
                            </div>
                            <div class="form-group">
                                <label>Bài học:</label>
                                <p>{{ $baiTap->baiHoc->tieu_de }}</p>
                            </div>
                            <div class="form-group">
                                <label>Loại bài tập:</label>
                                <p>
                                    @if ($baiTap->loai == 'trac_nghiem')
                                        <span class="badge badge-primary">Trắc nghiệm</span>
                                    @elseif ($baiTap->loai == 'tu_luan')
                                        <span class="badge badge-info">Tự luận</span>
                                    @else
                                        <span class="badge badge-secondary">File</span>
                                    @endif
                                </p>
                            </div>
                            <div class="form-group">
                                <label>Điểm tối đa:</label>
                                <p>{{ $baiTap->diem_toi_da }}</p>
                            </div>
                            <div class="form-group">
                                <label>Hạn nộp:</label>
                                <p>{{ $baiTap->han_nop ? $baiTap->han_nop->format('d/m/Y H:i') : 'Không có hạn nộp' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            @if ($baiTapDaNop)
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Thông tin nộp bài</h5>
                                    <p><strong>Ngày nộp:</strong> {{ $baiTapDaNop->ngay_nop->format('d/m/Y H:i') }}</p>
                                    <p><strong>Trạng thái:</strong> 
                                        @if ($baiTapDaNop->trang_thai == 'da_nop')
                                            <span class="badge badge-warning">Đã nộp - Chờ chấm</span>
                                        @else
                                            <span class="badge badge-success">Đã chấm</span>
                                        @endif
                                    </p>
                                    @if ($baiTapDaNop->diem !== null)
                                        <p><strong>Điểm:</strong> {{ $baiTapDaNop->diem }}</p>
                                    @endif
                                    @if ($baiTapDaNop->phan_hoi)
                                        <p><strong>Phản hồi:</strong> {{ $baiTapDaNop->phan_hoi }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mô tả bài tập:</label>
                        <div class="p-3 bg-light">
                            {!! $baiTap->mo_ta !!}
                        </div>
                    </div>

                    @if ($baiTap->file_dinh_kem)
                        <div class="form-group">
                            <label>File đính kèm:</label>
                            <p>
                                <a href="{{ asset('storage/' . $baiTap->file_dinh_kem) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-download"></i> Tải xuống {{ $baiTap->ten_file }}
                                </a>
                            </p>
                        </div>
                    @endif

                    @if ($baiTapDaNop)
                        <div class="mt-4">
                            <h5>Bài làm của bạn</h5>
                            @if ($baiTap->loai == 'trac_nghiem')
                                <a href="{{ route('hoc-vien.bai-tap.ket-qua', $baiTapDaNop->id) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Xem kết quả
                                </a>
                            @elseif ($baiTap->loai == 'tu_luan' && $baiTapDaNop->noi_dung)
                                <div class="card">
                                    <div class="card-body">
                                        {!! $baiTapDaNop->noi_dung !!}
                                    </div>
                                </div>
                            @elseif ($baiTap->loai == 'file' && $baiTapDaNop->file_path)
                                <p>
                                    <a href="{{ asset('storage/' . $baiTapDaNop->file_path) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-download"></i> Tải xuống {{ $baiTapDaNop->ten_file }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    @else
                        @if ($baiTap->han_nop && now()->gt($baiTap->han_nop))
                            <div class="alert alert-danger">
                                <i class="icon fas fa-ban"></i> Bài tập đã hết hạn nộp!
                            </div>
                        @else
                            <div class="mt-4">
                                @if ($baiTap->loai == 'trac_nghiem')
                                    <a href="{{ route('hoc-vien.bai-tap.lam-bai-trac-nghiem', $baiTap->id) }}" class="btn btn-primary">
                                        <i class="fas fa-pen"></i> Làm bài trắc nghiệm
                                    </a>
                                @else
                                    <a href="{{ route('hoc-vien.bai-tap.form-nop-bai', $baiTap->id) }}" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> Nộp bài
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 