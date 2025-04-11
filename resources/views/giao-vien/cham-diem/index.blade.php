@extends('layouts.dashboard')

@section('title', 'Chấm điểm bài tập')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Chấm điểm bài tập</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('giao-vien.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Chấm điểm bài tập</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="small text-white-50">Chờ chấm</div>
                            <div class="display-6">{{ $thongKe['cho_cham'] }}</div>
                        </div>
                        <i class="fas fa-clipboard-list fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('giao-vien.cham-diem.index', ['trang_thai' => 'da_nop']) }}">Xem chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="small text-white-50">Đang chấm</div>
                            <div class="display-6">{{ $thongKe['dang_cham'] }}</div>
                        </div>
                        <i class="fas fa-pen-fancy fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('giao-vien.cham-diem.index', ['trang_thai' => 'dang_cham']) }}">Xem chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="small text-white-50">Đã chấm</div>
                            <div class="display-6">{{ $thongKe['da_cham'] }}</div>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('giao-vien.cham-diem.index', ['trang_thai' => 'da_cham']) }}">Xem chi tiết</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="small text-white-50">Tổng số bài</div>
                            <div class="display-6">{{ $thongKe['cho_cham'] + $thongKe['dang_cham'] + $thongKe['da_cham'] }}</div>
                        </div>
                        <i class="fas fa-tasks fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('giao-vien.cham-diem.index') }}">Xem tất cả</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Danh sách bài tập cần chấm điểm
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="{{ route('giao-vien.cham-diem.index') }}" method="GET" class="d-flex">
                        <select name="lop_hoc_id" class="form-select me-2">
                            <option value="">-- Tất cả lớp học --</option>
                            @foreach($lopHocs as $lopHoc)
                                <option value="{{ $lopHoc->id }}" {{ $lopHocId == $lopHoc->id ? 'selected' : '' }}>
                                    {{ $lopHoc->ten }} ({{ $lopHoc->ma_lop }})
                                </option>
                            @endforeach
                        </select>
                        <select name="trang_thai" class="form-select me-2">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="da_nop" {{ $trangThai == 'da_nop' ? 'selected' : '' }}>Chờ chấm</option>
                            <option value="dang_cham" {{ $trangThai == 'dang_cham' ? 'selected' : '' }}>Đang chấm</option>
                            <option value="da_cham" {{ $trangThai == 'da_cham' ? 'selected' : '' }}>Đã chấm</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Lọc</button>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px">STT</th>
                            <th>Học viên</th>
                            <th>Bài tập</th>
                            <th>Lớp học</th>
                            <th style="width: 150px">Ngày nộp</th>
                            <th style="width: 100px">Trạng thái</th>
                            <th style="width: 100px">Điểm</th>
                            <th style="width: 120px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($baiNops as $index => $baiNop)
                            <tr>
                                <td class="text-center">{{ $index + 1 + ($baiNops->currentPage() - 1) * $baiNops->perPage() }}</td>
                                <td>
                                    {{ $baiNop->hocVien->ho_ten ?? 'N/A' }}
                                </td>
                                <td>
                                    {{ $baiNop->baiTap->tieu_de ?? 'N/A' }}
                                    <div class="small text-muted">
                                        {{ $baiNop->baiTap->mo_ta_ngan ?? '' }}
                                    </div>
                                </td>
                                <td>
                                    {{ $baiNop->baiTap->baiHoc->lopHoc->ten ?? 'N/A' }}
                                    <div class="small text-muted">
                                        {{ $baiNop->baiTap->baiHoc->lopHoc->ma_lop ?? '' }}
                                    </div>
                                </td>
                                <td>{{ $baiNop->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($baiNop->trang_thai == 'da_nop')
                                        <span class="badge bg-info">Chờ chấm</span>
                                    @elseif($baiNop->trang_thai == 'dang_cham')
                                        <span class="badge bg-warning">Đang chấm</span>
                                    @elseif($baiNop->trang_thai == 'da_cham')
                                        <span class="badge bg-success">Đã chấm</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $baiNop->trang_thai }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($baiNop->diem !== null)
                                        <span class="fw-bold">{{ $baiNop->diem }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if($baiNop->baiTap->loai == 'trac_nghiem')
                                            <a href="{{ route('giao-vien.cham-diem.trac-nghiem', $baiNop->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-check-square"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('giao-vien.cham-diem.tu-luan', $baiNop->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-3">
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-1"></i> Không có bài tập nào cần chấm điểm.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $baiNops->appends(['lop_hoc_id' => $lopHocId, 'trang_thai' => $trangThai])->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 