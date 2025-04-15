@extends('layouts.hoc-vien')

@section('title', 'Tạo thanh toán mới')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tạo thanh toán mới</h1>
        <a href="{{ route('hoc-vien.thanh-toan.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin thanh toán</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('hoc-vien.thanh-toan.store') }}" method="POST">
                @csrf
                
                @if(isset($dangKyHoc))
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lop_hoc">Lớp học</label>
                                <input type="text" class="form-control" id="lop_hoc" value="{{ $dangKyHoc->lopHoc->ten }}" readonly>
                                <input type="hidden" name="dang_ky_id" value="{{ $dangKyHoc->id }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="khoa_hoc">Khóa học</label>
                                <input type="text" class="form-control" id="khoa_hoc" value="{{ $dangKyHoc->lopHoc->khoaHoc->ten }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="so_tien">Số tiền cần thanh toán</label>
                                <input type="text" class="form-control" id="so_tien" value="{{ number_format($dangKyHoc->hoc_phi, 0, ',', '.') }} VNĐ" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="trang_thai">Trạng thái đăng ký</label>
                                <input type="text" class="form-control" id="trang_thai" value="{{ ucfirst(str_replace('_', ' ', $dangKyHoc->trang_thai)) }}" readonly>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="form-group">
                        <label for="dang_ky_id">Chọn lớp học cần thanh toán</label>
                        <select class="form-control @error('dang_ky_id') is-invalid @enderror" id="dang_ky_id" name="dang_ky_id" required>
                            <option value="">-- Chọn lớp học --</option>
                            @foreach($chuaThanhToans as $dangKy)
                                <option value="{{ $dangKy->id }}" {{ old('dang_ky_id') == $dangKy->id ? 'selected' : '' }}>
                                    {{ $dangKy->lopHoc->ten }} ({{ $dangKy->lopHoc->khoaHoc->ten }}) - {{ number_format($dangKy->hoc_phi, 0, ',', '.') }} VNĐ
                                </option>
                            @endforeach
                        </select>
                        @error('dang_ky_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="form-group">
                    <label for="phuong_thuc_thanh_toan">Phương thức thanh toán</label>
                    <select class="form-control @error('phuong_thuc_thanh_toan') is-invalid @enderror" id="phuong_thuc_thanh_toan" name="phuong_thuc_thanh_toan" required>
                        <option value="">-- Chọn phương thức thanh toán --</option>
                        @foreach($phuongThucThanhToan as $key => $value)
                            <option value="{{ $key }}" {{ old('phuong_thuc_thanh_toan') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('phuong_thuc_thanh_toan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Chuyển khoản ngân hàng -->
                <div id="chuyen_khoan_info" class="payment-info" style="display: none;">
                    <div class="alert alert-info">
                        <h5 class="alert-heading">Thông tin chuyển khoản</h5>
                        <p>Vui lòng chuyển khoản theo thông tin sau:</p>
                        <hr>
                        <p class="mb-0">Ngân hàng: <strong>Vietcombank</strong></p>
                        <p class="mb-0">Số tài khoản: <strong>1234567890</strong></p>
                        <p class="mb-0">Chủ tài khoản: <strong>CÔNG TY TNHH TIẾNG TRUNG HANZII</strong></p>
                        <p class="mb-0">Nội dung chuyển khoản: <strong>{{ isset($dangKyHoc) ? 'HP_' . $dangKyHoc->lopHoc->ma_lop . '_' . auth()->user()->ho . ' ' . auth()->user()->ten : 'HP_<Mã lớp>_<Họ và tên>' }}</strong></p>
                    </div>
                    <div class="form-group">
                        <label for="ma_giao_dich">Mã giao dịch</label>
                        <input type="text" class="form-control @error('ma_giao_dich') is-invalid @enderror" id="ma_giao_dich" name="ma_giao_dich" value="{{ old('ma_giao_dich') }}" placeholder="Nhập mã giao dịch chuyển khoản">
                        @error('ma_giao_dich')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Ví điện tử -->
                <div id="vi_dien_tu_info" class="payment-info" style="display: none;">
                    <div class="alert alert-info">
                        <h5 class="alert-heading">Thông tin thanh toán qua ví điện tử</h5>
                        <p>Vui lòng thanh toán theo thông tin sau:</p>
                        <hr>
                        <p class="mb-0">MoMo: <strong>0987654321</strong></p>
                        <p class="mb-0">ZaloPay: <strong>0987654321</strong></p>
                        <p class="mb-0">Chủ tài khoản: <strong>CÔNG TY TNHH TIẾNG TRUNG HANZII</strong></p>
                        <p class="mb-0">Nội dung thanh toán: <strong>{{ isset($dangKyHoc) ? 'HP_' . $dangKyHoc->lopHoc->ma_lop . '_' . auth()->user()->ho . ' ' . auth()->user()->ten : 'HP_<Mã lớp>_<Họ và tên>' }}</strong></p>
                    </div>
                    <div class="form-group">
                        <label for="ma_giao_dich">Mã giao dịch</label>
                        <input type="text" class="form-control @error('ma_giao_dich') is-invalid @enderror" id="ma_giao_dich_vi" name="ma_giao_dich" value="{{ old('ma_giao_dich') }}" placeholder="Nhập mã giao dịch thanh toán">
                        @error('ma_giao_dich')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Tiền mặt -->
                <div id="tien_mat_info" class="payment-info" style="display: none;">
                    <div class="alert alert-info">
                        <h5 class="alert-heading">Thông tin thanh toán tiền mặt</h5>
                        <p>Vui lòng đến địa chỉ sau để thanh toán trực tiếp:</p>
                        <hr>
                        <p class="mb-0">Địa chỉ: <strong>123 Đường Nguyễn Văn Linh, Quận 7, TP. Hồ Chí Minh</strong></p>
                        <p class="mb-0">Thời gian làm việc: <strong>Thứ 2 - Thứ 6: 8:00 - 17:30, Thứ 7: 8:00 - 12:00</strong></p>
                        <p class="mb-0">Hotline: <strong>0987654321</strong></p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ghi_chu">Ghi chú</label>
                    <textarea class="form-control @error('ghi_chu') is-invalid @enderror" id="ghi_chu" name="ghi_chu" rows="3" placeholder="Nhập ghi chú nếu có">{{ old('ghi_chu') }}</textarea>
                    @error('ghi_chu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Hiển thị thông tin phương thức thanh toán khi chọn
        $('#phuong_thuc_thanh_toan').change(function() {
            // Ẩn tất cả các div thông tin thanh toán
            $('.payment-info').hide();
            
            // Hiển thị div tương ứng với phương thức được chọn
            var selectedMethod = $(this).val();
            if (selectedMethod == 'chuyen_khoan') {
                $('#chuyen_khoan_info').show();
            } else if (selectedMethod == 'vi_dien_tu') {
                $('#vi_dien_tu_info').show();
            } else if (selectedMethod == 'tien_mat') {
                $('#tien_mat_info').show();
            }
        });
        
        // Kích hoạt sự kiện change để hiển thị đúng div khi trang được tải lại
        var initialMethod = $('#phuong_thuc_thanh_toan').val();
        if (initialMethod) {
            $('#phuong_thuc_thanh_toan').trigger('change');
        }
    });
</script>
@endsection 