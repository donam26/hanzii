@extends('layouts.dashboard')

@section('title', 'Làm bài tập trắc nghiệm')
@section('page-heading', 'Làm bài tập trắc nghiệm')

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
                    <h3 class="card-title">{{ $baiTap->tieu_de }}</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p><strong>Lớp học:</strong> {{ $lopHoc->ten }}</p>
                        <p><strong>Bài học:</strong> {{ $baiTap->baiHoc->tieu_de }}</p>
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

                    @if($baiTap->cauHois->count() > 0)
                        <form action="{{ route('hoc-vien.bai-tap.nop-bai-trac-nghiem', $baiTap->id) }}" method="POST">
                            @csrf
                            <h4 class="mt-4 mb-3">Câu hỏi trắc nghiệm</h4>
                            
                            @foreach($baiTap->cauHois as $index => $cauHoi)
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong>Câu {{ $index + 1 }}:</strong> {!! $cauHoi->noi_dung !!}
                                    </div>
                                    <div class="card-body">
                                        @if($cauHoi->dapAns->count() > 0)
                                            <div class="form-group">
                                                @foreach($cauHoi->dapAns as $dapAn)
                                                    <div class="custom-control custom-radio mb-2">
                                                        <input class="custom-control-input" type="radio" 
                                                            name="dap_an[{{ $cauHoi->id }}]" 
                                                            id="dap_an_{{ $cauHoi->id }}_{{ $dapAn->id }}" 
                                                            value="{{ $dapAn->id }}" required>
                                                        <label class="custom-control-label" for="dap_an_{{ $cauHoi->id }}_{{ $dapAn->id }}">
                                                            {!! $dapAn->noi_dung !!}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                Câu hỏi này chưa có đáp án.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div class="form-group text-center mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Nộp bài
                                </button>
                                <a href="{{ route('hoc-vien.bai-tap.show', $baiTap->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="icon fas fa-exclamation-triangle"></i> Bài tập này chưa có câu hỏi trắc nghiệm.
                        </div>
                        <a href="{{ route('hoc-vien.bai-tap.show', $baiTap->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Đếm thời gian làm bài
        let timeStarted = new Date().getTime();
        
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
    });
</script>
@endpush 