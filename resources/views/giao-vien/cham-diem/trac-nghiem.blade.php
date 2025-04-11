@extends('layouts.dashboard')

@section('title', 'Chấm điểm bài tập trắc nghiệm')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Chấm điểm bài tập trắc nghiệm</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('giao-vien.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('giao-vien.cham-diem.index') }}">Chấm điểm</a></li>
        <li class="breadcrumb-item active">Chấm điểm bài tập trắc nghiệm</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-check-square me-1"></i>
                Thông tin bài nộp
            </div>
            <div>
                <a href="{{ route('giao-vien.cham-diem.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 150px">Học viên:</th>
                            <td>{{ $baiNop->hocVien->ho_ten }}</td>
                        </tr>
                        <tr>
                            <th>Lớp học:</th>
                            <td>{{ $baiNop->baiTap->baiHoc->lopHoc->ten }}</td>
                        </tr>
                        <tr>
                            <th>Bài tập:</th>
                            <td>{{ $baiNop->baiTap->tieu_de }}</td>
                        </tr>
                        <tr>
                            <th>Ngày nộp:</th>
                            <td>{{ $baiNop->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái:</th>
                            <td>
                                @if($baiNop->trang_thai == 'da_nop')
                                    <span class="badge bg-info">Đã nộp</span>
                                @elseif($baiNop->trang_thai == 'dang_cham')
                                    <span class="badge bg-warning">Đang chấm</span>
                                @elseif($baiNop->trang_thai == 'da_cham')
                                    <span class="badge bg-success">Đã chấm</span>
                                @else
                                    <span class="badge bg-secondary">{{ $baiNop->trang_thai }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-1"></i>
                            Thống kê
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex flex-column align-items-center">
                                        <h3 class="mb-0">{{ count($baiNop->baiTap->cauHois) }}</h3>
                                        <small>Tổng số câu</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column align-items-center">
                                        <h3 class="mb-0" id="so-cau-dung">0</h3>
                                        <small>Số câu đúng</small>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="text-center">
                                <h4 class="mb-0" id="diem-so">0</h4>
                                <small>Điểm số</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list me-1"></i>
                    Danh sách câu hỏi và câu trả lời
                </div>
                <div class="card-body">
                    @php $soCauDung = 0; @endphp
                    @foreach($baiNop->baiTap->cauHois as $index => $cauHoi)
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Câu {{ $index + 1 }}:</strong> {{ $cauHoi->noi_dung }}
                                </div>
                                <div>
                                    @php
                                        $cauTraLoi = isset($cauTraLois[$cauHoi->id]) ? $cauTraLois[$cauHoi->id] : null;
                                        $isDung = $cauTraLoi == $cauHoi->dap_an_dung;
                                        if ($isDung) $soCauDung++;
                                    @endphp
                                    
                                    @if($cauTraLoi !== null)
                                        @if($isDung)
                                            <span class="badge bg-success">Đúng</span>
                                        @else
                                            <span class="badge bg-danger">Sai</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Không trả lời</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach(['a', 'b', 'c', 'd'] as $option)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" 
                                                    disabled
                                                    {{ $cauTraLoi == $option ? 'checked' : '' }}
                                                    {{ $cauHoi->dap_an_dung == $option ? 'data-correct="true"' : '' }}>
                                                <label class="form-check-label {{ $cauHoi->dap_an_dung == $option ? 'fw-bold text-success' : '' }}">
                                                    {{ strtoupper($option) }}. {{ $cauHoi->{"dap_an_$option"} }}
                                                    @if($cauHoi->dap_an_dung == $option)
                                                        <i class="fas fa-check text-success ms-1"></i>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if($cauHoi->giai_thich)
                                    <div class="mt-3 p-3 bg-light border rounded">
                                        <strong>Giải thích:</strong> {{ $cauHoi->giai_thich }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <form action="{{ route('giao-vien.cham-diem.cham-trac-nghiem', $baiNop->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="nhan_xet" class="form-label fw-bold">Nhận xét (tùy chọn):</label>
                            <textarea class="form-control" id="nhan_xet" name="nhan_xet" rows="3">{{ old('nhan_xet', $baiNop->nhan_xet) }}</textarea>
                            @error('nhan_xet')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Thông tin điểm số:</strong> 
                            Học viên đã trả lời đúng <strong>{{ $soCauDung }}</strong> trong tổng số <strong>{{ count($baiNop->baiTap->cauHois) }}</strong> câu hỏi.
                            Điểm số sẽ được tự động tính theo tỷ lệ: <strong>{{ number_format($soCauDung / max(1, count($baiNop->baiTap->cauHois)) * 10, 1) }}</strong>.
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('giao-vien.cham-diem.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-times me-1"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Xác nhận chấm điểm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Hiển thị số câu đúng và điểm số
    $(document).ready(function() {
        const tongSoCau = {{ count($baiNop->baiTap->cauHois) }};
        const soCauDung = {{ $soCauDung }};
        const diemSo = tongSoCau > 0 ? (soCauDung / tongSoCau * 10).toFixed(1) : 0;
        
        $('#so-cau-dung').text(soCauDung);
        $('#diem-so').text(diemSo);
        
        // Cập nhật trạng thái đang chấm
        $.ajax({
            url: "{{ route('giao-vien.cham-diem.cap-nhat-trang-thai', $baiNop->id) }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                trang_thai: "dang_cham"
            },
            success: function(response) {
                console.log("Đã cập nhật trạng thái thành đang chấm");
            }
        });
    });
</script>
@endsection 