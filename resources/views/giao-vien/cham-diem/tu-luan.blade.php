@extends('layouts.dashboard')

@section('title', 'Chấm điểm bài tập tự luận')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Chấm điểm bài tập tự luận</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('giao-vien.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('giao-vien.cham-diem.index') }}">Chấm điểm</a></li>
        <li class="breadcrumb-item active">Chấm điểm bài tập tự luận</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-tasks me-1"></i>
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
                            <td>{{ $baiNop->hocVien->nguoiDung->ho_ten }}</td>
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
                            <td>{{ $baiNop->thoi_gian_nop->format('d/m/Y H:i') }}</td>
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
                                @elseif($baiNop->trang_thai == 'yeu_cau_nop_lai')
                                    <span class="badge bg-danger">Yêu cầu nộp lại</span>
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
                            <i class="fas fa-info-circle me-1"></i>
                            Hướng dẫn chấm điểm
                        </div>
                        <div class="card-body">
                            <ul>
                                <li>Xem kỹ nội dung bài làm của học viên</li>
                                <li>Đánh giá dựa trên tiêu chí của bài tập</li>
                                <li>Điểm số từ 0-10, có thể có thập phân (ví dụ: 8.5)</li>
                                <li>Nhận xét cụ thể để học viên hiểu được điểm mạnh, điểm yếu</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-file-alt me-1"></i>
                            Nội dung bài làm
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h5>Nội dung bài làm của học viên:</h5>
                                <div class="border p-3 bg-light">
                                    {!! nl2br(e($baiNop->noi_dung)) !!}
                                </div>
                            </div>

                            @if($baiNop->file_path)
                                <div class="mb-3">
                                    <h5>File đính kèm:</h5>
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $baiNop->file_name ?? 'File đính kèm' }}</span>
                                            <div>
                                                <a href="{{ asset('storage/' . $baiNop->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i> Xem
                                                </a>
                                                <a href="{{ asset('storage/' . $baiNop->file_path) }}" download class="btn btn-sm btn-success">
                                                    <i class="fas fa-download me-1"></i> Tải xuống
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('giao-vien.cham-diem.cham-tu-luan', $baiNop->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="nhan_xet" class="form-label fw-bold">Nhận xét:</label>
                            <textarea class="form-control" id="nhan_xet" name="nhan_xet" rows="5" required>{{ old('nhan_xet', $baiNop->nhan_xet) }}</textarea>
                            @error('nhan_xet')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="diem" class="form-label fw-bold">Điểm số (0-10):</label>
                            <input type="number" class="form-control" id="diem" name="diem" min="0" max="10" step="0.1" value="{{ old('diem', $baiNop->diem) }}" required>
                            @error('diem')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">Tiêu chí chấm điểm</div>
                            <div class="card-body">
                                <p><strong>Điểm tối đa:</strong> {{ $baiNop->baiTap->diem_toi_da ?? 10 }}</p>
                                <ul>
                                    <li>Hoàn thành yêu cầu: 5 điểm</li>
                                    <li>Độ chính xác: 3 điểm</li>
                                    <li>Sáng tạo, phát triển ý tưởng: 2 điểm</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('giao-vien.cham-diem.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-times me-1"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu điểm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Tự động cập nhật trạng thái đang chấm
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
        
        // Xác nhận trước khi rời trang nếu chưa lưu
        let formChanged = false;
        $('form input, form textarea').on('change', function() {
            formChanged = true;
        });
        
        $(window).on('beforeunload', function() {
            if (formChanged) {
                return "Bạn có thông tin chưa lưu. Bạn có chắc chắn muốn rời khỏi trang này?";
            }
        });
        
        $('form').on('submit', function() {
            formChanged = false;
        });
    });
</script>
@endsection 