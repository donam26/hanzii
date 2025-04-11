@extends('layouts.hoc-vien')

@section('title', 'Kết quả bài tập')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kết quả bài tập: {{ $baiTap->tieu_de }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <p><strong>Lớp học:</strong> {{ $lopHoc->ten }}</p>
                                <p><strong>Bài học:</strong> {{ $baiTap->baiHoc->tieu_de }}</p>
                                <p><strong>Loại bài tập:</strong> 
                                    @if ($baiTap->loai == 'trac_nghiem')
                                        <span class="badge badge-primary">Trắc nghiệm</span>
                                    @elseif ($baiTap->loai == 'tu_luan')
                                        <span class="badge badge-info">Tự luận</span>
                                    @else
                                        <span class="badge badge-secondary">File</span>
                                    @endif
                                </p>
                                <p><strong>Điểm tối đa:</strong> {{ $baiTap->diem_toi_da }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <p><strong>Ngày nộp:</strong> {{ $baiTapDaNop->ngay_nop->format('d/m/Y H:i') }}</p>
                                <p><strong>Trạng thái:</strong> 
                                    @if ($baiTapDaNop->trang_thai == 'da_nop')
                                        <span class="badge badge-warning">Đã nộp - Chờ chấm</span>
                                    @else
                                        <span class="badge badge-success">Đã chấm</span>
                                    @endif
                                </p>
                                <p><strong>Điểm đạt được:</strong> 
                                    @if ($baiTapDaNop->diem !== null)
                                        <span class="badge badge-primary">{{ $baiTapDaNop->diem }}/{{ $baiTap->diem_toi_da }}</span>
                                    @else
                                        <span class="badge badge-secondary">Chưa có điểm</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($baiTap->loai == 'trac_nghiem')
                        <div class="mt-4">
                            <h4 class="mb-3">Chi tiết bài làm trắc nghiệm</h4>
                            
                            @php
                                $dapAns = json_decode($baiTapDaNop->noi_dung, true) ?? [];
                                $totalQuestions = $baiTap->cauHois->count();
                                $correctCount = 0;
                            @endphp
                            
                            @foreach($baiTap->cauHois as $index => $cauHoi)
                                @php
                                    $userAnswerId = $dapAns[$cauHoi->id] ?? null;
                                    $userAnswer = null;
                                    $correctAnswer = $cauHoi->dapAns->where('la_dap_an_dung', true)->first();
                                    $isCorrect = false;
                                    
                                    if ($userAnswerId && $correctAnswer) {
                                        $userAnswer = $cauHoi->dapAns->where('id', $userAnswerId)->first();
                                        $isCorrect = $userAnswerId == $correctAnswer->id;
                                        if ($isCorrect) $correctCount++;
                                    }
                                @endphp
                                
                                <div class="card mb-4 {{ $isCorrect ? 'border-success' : ($userAnswer ? 'border-danger' : 'border-warning') }}">
                                    <div class="card-header {{ $isCorrect ? 'bg-success' : ($userAnswer ? 'bg-danger' : 'bg-warning') }} text-white">
                                        <strong>Câu {{ $index + 1 }}:</strong> {!! $cauHoi->noi_dung !!}
                                        
                                        <div class="float-right">
                                            @if ($isCorrect)
                                                <i class="fas fa-check-circle"></i> Đúng
                                            @elseif ($userAnswer)
                                                <i class="fas fa-times-circle"></i> Sai
                                            @else
                                                <i class="fas fa-exclamation-circle"></i> Không trả lời
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @foreach($cauHoi->dapAns as $dapAn)
                                            <div class="form-group">
                                                <div class="custom-control custom-radio mb-2">
                                                    <input class="custom-control-input" type="radio" 
                                                        id="ket_qua_{{ $cauHoi->id }}_{{ $dapAn->id }}" 
                                                        {{ $userAnswerId == $dapAn->id ? 'checked' : '' }}
                                                        disabled>
                                                    <label class="custom-control-label {{ $dapAn->la_dap_an_dung ? 'text-success font-weight-bold' : '' }}" 
                                                           for="ket_qua_{{ $cauHoi->id }}_{{ $dapAn->id }}">
                                                        {!! $dapAn->noi_dung !!}
                                                        
                                                        @if ($dapAn->la_dap_an_dung)
                                                            <i class="fas fa-check text-success"></i> (Đáp án đúng)
                                                        @endif
                                                        
                                                        @if ($userAnswerId == $dapAn->id && !$dapAn->la_dap_an_dung)
                                                            <i class="fas fa-times text-danger"></i> (Bạn đã chọn)
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="alert alert-info">
                                <p><strong>Tổng số câu hỏi:</strong> {{ $totalQuestions }}</p>
                                <p><strong>Số câu trả lời đúng:</strong> {{ $correctCount }}</p>
                                <p><strong>Tỷ lệ đúng:</strong> {{ $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0 }}%</p>
                                <p><strong>Điểm đạt được:</strong> {{ $baiTapDaNop->diem }}/{{ $baiTap->diem_toi_da }}</p>
                            </div>
                        </div>
                    @elseif ($baiTap->loai == 'tu_luan')
                        <div class="mt-4">
                            <h4 class="mb-3">Bài làm tự luận của bạn</h4>
                            <div class="card">
                                <div class="card-body">
                                    {!! $baiTapDaNop->noi_dung !!}
                                </div>
                            </div>
                        </div>
                    @elseif ($baiTap->loai == 'file')
                        <div class="mt-4">
                            <h4 class="mb-3">File bài làm của bạn</h4>
                            @if ($baiTapDaNop->file_path)
                                <a href="{{ asset('storage/' . $baiTapDaNop->file_path) }}" target="_blank" class="btn btn-info">
                                    <i class="fas fa-download"></i> Tải xuống {{ $baiTapDaNop->ten_file }}
                                </a>
                            @else
                                <div class="alert alert-warning">
                                    Không tìm thấy file bài làm.
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($baiTapDaNop->phan_hoi)
                        <div class="mt-4">
                            <h4 class="mb-3">Phản hồi từ giáo viên</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {!! $baiTapDaNop->phan_hoi !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group text-center mt-4">
                        <a href="{{ route('hoc-vien.bai-tap.show', $baiTap->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại chi tiết bài tập
                        </a>
                        <a href="{{ route('hoc-vien.bai-hoc.show', $baiTap->baiHoc->id) }}" class="btn btn-info">
                            <i class="fas fa-book"></i> Quay lại bài học
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 