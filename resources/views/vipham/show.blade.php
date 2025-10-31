@extends('admin.layouts.admin')

@section('content')
    <div class="container mt-4">
        <h3 class="page-title mb-3">📋 Chi tiết vi phạm</h3>

        @php
            // ===== Mapping status =====
            $status = $violation->status ?? 'open';
            $statusMap = [
                'open' => ['text' => 'Chưa xử lý', 'class' => 'badge-soft-warning'],
                'resolved' => ['text' => 'Đã xử lý', 'class' => 'badge-soft-success'],
            ];
            $statusConf = $statusMap[$status] ?? $statusMap['open'];

            // ===== Money & dates =====
            $money =
                $violation->penalty_amount !== null
                    ? number_format((float) $violation->penalty_amount, 0, ',', '.') . ' đ'
                    : '—';
            $occured = optional($violation->occurred_at)->format('d/m/Y H:i') ?? '—';
            $created = optional($violation->created_at)->format('d/m/Y H:i') ?? '—';
            $updated = optional($violation->updated_at)->format('d/m/Y H:i') ?? '—';

            // ===== Room & Khu (null-safe) =====
            $tenPhong = optional(optional($violation->student)->phong)->ten_phong;
            $tenKhu = optional(optional(optional($violation->student)->phong)->khu)->ten_khu;
        @endphp

        {{-- Header tổng quan --}}
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-muted small">Trạng thái</div>
                        <div class="mt-1">
                            <span class="badge {{ $statusConf['class'] }}">{{ $statusConf['text'] }}</span>
                        </div>
                        @if (!empty($violation->receipt_no))
                            <div class="mt-2 small">Biên lai: <strong>{{ $violation->receipt_no }}</strong></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-muted small">Tiền phạt</div>
                        <div class="mt-1 h5 mb-0">{{ $money }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-muted small">Thời điểm</div>
                        <div class="mt-1">{{ $occured }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bảng chi tiết --}}
        <div class="card p-0 shadow-sm border-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <th style="width:240px">Mã vi phạm</th>
                            <td>#{{ $violation->id }}</td>
                        </tr>
                        <tr>
                            <th>Sinh viên</th>
                            <td>
                                <div class="font-weight-600">{{ optional($violation->student)->ho_ten ?? '—' }}</div>
                                <div class="text-muted small">{{ optional($violation->student)->ma_sinh_vien ?? '' }}</div>
                            </td>
                        </tr>
                        <tr>
                            <th>Phòng / Khu</th>
                            <td>
                                {{ $tenPhong ?? '—' }}
                                @if (!empty($tenKhu))
                                    <span class="badge badge-soft-secondary ml-1">Khu {{ $tenKhu }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Loại vi phạm</th>
                            <td>{{ optional($violation->type)->name ?? '—' }}</td>
                        </tr>
                        {{-- Migration hiện tại chưa có cột location/mucdo/image/created_by --}}
                        <tr>
                            <th>Ghi chú</th>
                            <td>{{ $violation->note ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Biên lai</th>
                            <td>{{ $violation->receipt_no ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Ngày tạo / Cập nhật</th>
                            <td>{{ $created }} / {{ $updated }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-3 d-flex gap-2">
                <a href="{{ route('vipham.index') }}" class="btn btn-secondary">← Quay lại danh sách</a>
                <a href="{{ route('vipham.edit', $violation->id) }}" class="btn btn-primary">
                    <i class="fa fa-pencil mr-1"></i> Sửa
                </a>
                @if ($status === 'open')
                    <form action="{{ route('vipham.resolve', $violation->id) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <button class="btn btn-success">
                            <i class="fa fa-check mr-1"></i> Đánh dấu đã xử lý
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
