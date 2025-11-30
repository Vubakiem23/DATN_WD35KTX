{{-- 
    Component: Filter Modal
    Usage: @include('components.filter-modal', [
        'title' => 'Bộ lọc sinh viên',
        'formAction' => route('sinhvien.index'),
        'formId' => 'filterForm',
        'fields' => [
            ['type' => 'text', 'name' => 'q', 'label' => 'Tìm nhanh', 'placeholder' => 'Mã SV, Họ tên...', 'value' => request('q')],
            ['type' => 'select', 'name' => 'gender', 'label' => 'Giới tính', 'options' => [...], 'value' => request('gender')],
        ],
        'resetUrl' => route('sinhvien.index')
    ])
--}}
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">{{ $title ?? 'Bộ lọc' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form method="GET" action="{{ $formAction ?? '' }}" id="{{ $formId ?? 'filterForm' }}">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            @foreach($fields ?? [] as $field)
                                <div class="col-md-{{ $field['col'] ?? 4 }} mb-3">
                                    <label class="small text-muted">{{ $field['label'] ?? '' }}</label>
                                    @if($field['type'] === 'text' || $field['type'] === 'date' || $field['type'] === 'number')
                                        <input type="{{ $field['type'] }}" 
                                               name="{{ $field['name'] }}" 
                                               value="{{ $field['value'] ?? '' }}"
                                               class="form-control" 
                                               placeholder="{{ $field['placeholder'] ?? '' }}"
                                               @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                                               @if(isset($field['max'])) max="{{ $field['max'] }}" @endif>
                                    @elseif($field['type'] === 'select')
                                        <select name="{{ $field['name'] }}" class="form-control">
                                            @if(isset($field['options']))
                                                @foreach($field['options'] as $option)
                                                    @if(is_array($option))
                                                        <option value="{{ $option['value'] }}" 
                                                                @selected(($field['value'] ?? '') == $option['value'])>
                                                            {{ $option['label'] }}
                                                        </option>
                                                    @else
                                                        <option value="{{ $option }}" 
                                                                @selected(($field['value'] ?? '') == $option)>
                                                            {{ $option }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ $resetUrl ?? '#' }}" class="btn btn-outline-secondary">Xóa lọc</a>
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Mở modal bộ lọc (chạy được cho cả Bootstrap 4 và 5)
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('openFilterModalBtn');
            if (!btn) return;

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var modalEl = document.getElementById('filterModal');
                if (!modalEl) return;

                try {
                    // Nếu có Bootstrap 5
                    if (window.bootstrap && bootstrap.Modal) {
                        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    } else if (window.$ && $('#filterModal').modal) {
                        // Fallback cho Bootstrap 4
                        $('#filterModal').modal('show');
                    }
                } catch (err) {
                    // Fallback cuối cùng
                    if (window.$ && $('#filterModal').modal) {
                        $('#filterModal').modal('show');
                    }
                }
            });
        });
    })();
</script>

