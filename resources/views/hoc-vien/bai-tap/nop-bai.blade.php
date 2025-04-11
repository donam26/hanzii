@extends('layouts.hoc-vien')

@section('title', 'Nộp bài tập')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $baiTap->tieu_de }}</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p><strong>Lớp học:</strong> {{ $lopHoc->ten }}</p>
                        <p><strong>Bài học:</strong> {{ $baiTap->baiHoc->tieu_de }}</p>
                        <p><strong>Loại bài tập:</strong> 
                            @if ($baiTap->loai == 'tu_luan')
                                <span class="badge badge-info">Tự luận</span>
                            @else
                                <span class="badge badge-secondary">File</span>
                            @endif
                        </p>
                        <p><strong>Điểm tối đa:</strong> {{ $baiTap->diem_toi_da }}</p>
                        <p><strong>Hạn nộp:</strong> {{ $baiTap->han_nop ? $baiTap->han_nop->format('d/m/Y H:i') : 'Không có hạn nộp' }}</p>
                    </div>

                    @if($baiTap->mo_ta)
                        <div class="form-group">
                            <label>Mô tả bài tập:</label>
                            <div class="p-3 bg-light">
                                {!! $baiTap->mo_ta !!}
                            </div>
                        </div>
                    @endif

                    @if($baiTap->file_dinh_kem)
                        <div class="form-group">
                            <label>File đính kèm:</label>
                            <p>
                                <a href="{{ asset('storage/' . $baiTap->file_dinh_kem) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-download"></i> Tải xuống {{ $baiTap->ten_file }}
                                </a>
                            </p>
                        </div>
                    @endif

                    <form action="{{ route('hoc-vien.bai-tap.nop-bai', $baiTap->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <h4 class="mt-4 mb-3">Nộp bài làm</h4>

                        @if($baiTap->loai == 'tu_luan')
                            <div class="form-group">
                                <label for="noi_dung">Nội dung bài làm:</label>
                                <textarea id="noi_dung" name="noi_dung" class="form-control @error('noi_dung') is-invalid @enderror" rows="10" required>{{ old('noi_dung') }}</textarea>
                                @error('noi_dung')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Nhập nội dung bài làm của bạn vào đây.</small>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="file">Tải lên file bài làm:</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('file') is-invalid @enderror" id="file" name="file" required>
                                    <label class="custom-file-label" for="file">Chọn file...</label>
                                    @error('file')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Kích thước tối đa: 10MB.</small>
                            </div>
                        @endif

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Nộp bài
                            </button>
                            <a href="{{ route('hoc-vien.bai-tap.show', $baiTap->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($baiTap->loai == 'tu_luan')
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<script>
    $(function() {
        // Khởi tạo summernote cho textarea
        $('#noi_dung').summernote({
            height: 300,
            placeholder: 'Nhập nội dung bài làm của bạn vào đây...',
            toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['table']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    alert('Chức năng tải ảnh không được hỗ trợ. Vui lòng dùng định dạng văn bản.');
                }
            }
        });
    });
</script>
@else
<script>
    $(function() {
        // Hiển thị tên file khi chọn
        $('input[type="file"]').change(function(e) {
            var fileName = e.target.files[0].name;
            $('.custom-file-label').html(fileName);
        });
    });
</script>
@endif

<script>
    // Cảnh báo khi học viên cố gắng rời khỏi trang
    window.addEventListener('beforeunload', function (e) {
        // Hủy sự kiện cho các trình duyệt khác nhau
        e.preventDefault();
        e.returnValue = '';
        
        // Hiển thị thông báo
        return 'Bạn đang làm bài tập, nếu rời khỏi trang này bạn sẽ mất dữ liệu đã làm. Bạn có chắc muốn rời đi?';
    });
    
    // Khi submit form thì không hiển thị cảnh báo nữa
    $('form').on('submit', function() {
        window.removeEventListener('beforeunload', function() {});
    });
</script>
@endpush

@push('styles')
@if($baiTap->loai == 'tu_luan')
<link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
@endif
@endpush 