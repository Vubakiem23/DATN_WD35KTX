{{-- Component bộ lọc tháng/năm có thể tái sử dụng --}}
@php
  $formId = 'monthYearFilterForm_' . uniqid();
  $monthSelectId = 'monthSelect_' . uniqid();
  $yearSelectId = 'yearSelect_' . uniqid();
@endphp

<div class="month-year-filter mb-3">
  <form method="GET" action="{{ $action ?? request()->url() }}" class="row g-3 align-items-end" id="{{ $formId }}">
    {{-- Giữ lại các query params khác --}}
    @foreach(request()->except(['month', 'year']) as $key => $value)
      @if(is_array($value))
        @foreach($value as $v)
          <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
        @endforeach
      @else
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
      @endif
    @endforeach

    <div class="col-md-4">
      <label class="form-label">
        <i class="fa fa-calendar text-primary"></i> Tháng
      </label>
      <select name="month" class="form-select form-control month-select">
        <option value="">-- Tất cả tháng --</option>
        @for($i = 1; $i <= 12; $i++)
          <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
            Tháng {{ $i }}
          </option>
        @endfor
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">
        <i class="fa fa-calendar-alt text-primary"></i> Năm
      </label>
      <select name="year" class="form-select form-control year-select">
        <option value="">-- Tất cả năm --</option>
        @for($y = now()->year; $y >= now()->year - 5; $y--)
          <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
            {{ $y }}
          </option>
        @endfor
      </select>
    </div>

    <div class="col-md-4 d-flex gap-2 align-items-end">
      <button type="submit" class="btn btn-primary flex-fill">
        <i class="fa fa-filter"></i> Lọc
      </button>
      <a href="{{ $action ?? request()->url() }}" class="btn btn-outline-secondary">
        <i class="fa fa-rotate-left"></i> Đặt lại
      </a>
    </div>
  </form>
</div>

