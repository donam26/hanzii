@extends('layouts.dashboard')

@section('title', 'Danh sách lương')
@section('page-heading', 'Danh sách lương')

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
                    <h6>Danh sách lương</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0">Tổng lương đã nhận: <span class="text-success">{{ number_format($tongLuongDaNhan, 0, ',', '.') }} VND</span></p>
                            <p class="mb-0">Tổng lương chưa nhận: <span class="text-danger">{{ number_format($tongLuongChuaNhan, 0, ',', '.') }} VND</span></p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">STT</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mã lớp</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tên lớp</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Số tiền</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tháng</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($luongs as $index => $luong)
                                <tr>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $luong->lop_hoc->ma_lop }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $luong->lop_hoc->ten_lop }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($luong->so_tien, 0, ',', '.') }} VND</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ \Carbon\Carbon::parse($luong->thang)->format('m/Y') }}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if($luong->da_nhan)
                                            <span class="badge badge-sm bg-gradient-success">Đã nhận</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-danger">Chưa nhận</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('giao-vien.luong.show', $luong->id) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Chi tiết">
                                            Chi tiết
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $luongs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 