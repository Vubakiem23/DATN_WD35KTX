    @extends('admin.layouts.admin')

    @section('title', 'Thêm thông báo')

    @section('content')
    <div class="container mt-4" style="max-width: 900px; background:#f9f9f9; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <h3 class="mb-3 text-primary"> Thêm thông báo mới</h3>
        <hr>

        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('thongbao.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">

                {{-- Tiêu đề --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Tiêu đề</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <select name="tieu_de_id" id="tieu_de_id" class="form-select" style="flex:1 1 auto;">
                            <option value="">-- Chọn tiêu đề --</option>
                            @foreach($tieuDes as $td)
                            <option value="{{ $td->id }}">{{ $td->ten_tieu_de }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="add_title_btn" class="btn btn-primary">+ Thêm</button>
                        <button type="button" id="delete_title_btn" class="btn btn-danger">Xóa</button>
                    </div>
                    <input type="text" id="input_tieu_de" class="form-control mt-2" style="display:none;" placeholder="Nhập tiêu đề mới và Enter để lưu">
                </div>

                {{-- Mức độ --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Mức độ (tùy chọn)</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <select name="muc_do_id" id="muc_do_id" class="form-select" style="flex:1 1 auto;">
                            <option value="">-- Không chọn mức độ --</option>
                            @foreach($mucDos as $md)
                            <option value="{{ $md->id }}" {{ old('muc_do_id') == $md->id ? 'selected' : '' }}>
                                {{ $md->ten_muc_do }}
                            </option>
                            @endforeach
                        </select>
                        <button type="button" id="add_priority_btn" class="btn btn-success">+ Thêm</button>
                        <button type="button" id="delete_priority_btn" class="btn btn-danger">Xóa</button>
                    </div>
                    <input type="text" id="input_muc_do" class="form-control mt-2" style="display:none;" placeholder="Nhập mức độ mới và Enter để lưu">
                </div>

                {{-- Nội dung --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">Nội dung</label>
                    <textarea name="noi_dung" class="form-control" rows="5" required>{{ old('noi_dung') }}</textarea>
                </div>

                {{-- Ngày đăng --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ngày đăng</label>
                    <input type="date" name="ngay_dang" class="form-control" value="{{ old('ngay_dang') }}" required>
                </div>

                {{-- Đối tượng --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Đối tượng</label>
                    <select name="doi_tuong" class="form-select" required>
                        <option value="">-- Chọn đối tượng --</option>
                        <option value="Sinh viên" {{ old('doi_tuong') == 'Sinh viên' ? 'selected' : '' }}>Sinh viên</option>
                        <option value="Giảng viên" {{ old('doi_tuong') == 'Giảng viên' ? 'selected' : '' }}>Giảng viên</option>
                        <option value="Tất cả" {{ old('doi_tuong') == 'Tất cả' ? 'selected' : '' }}>Tất cả</option>
                    </select>
                </div>

                {{-- Khu --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Chọn khu (có thể chọn nhiều)</label>
                    <select name="khu_id[]" id="khu_id" class="form-select" multiple>
                        @foreach($khus as $khu)
                        <option value="{{ $khu->id }}" {{ collect(old('khu_id'))->contains($khu->id) ? 'selected' : '' }}>
                            {{ $khu->ten_khu }}
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều khu.</small>
                </div>

                {{-- Phòng --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Chọn phòng (có thể chọn nhiều)</label>
                    <select name="phong_id[]" id="phong_id" class="form-select" multiple>
                        @foreach($phongs as $phong)
                        <option value="{{ $phong->id }}" {{ collect(old('phong_id'))->contains($phong->id) ? 'selected' : '' }}>
                            {{ $phong->ten_phong }} ({{ $phong->khu->ten_khu ?? '' }})
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều phòng.</small>
                </div>

                {{-- Ảnh --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ảnh thông báo</label>
                    <input type="file" name="anh" class="form-control" accept="image/*">
                </div>

                {{-- File đính kèm --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">File đính kèm</label>
                    <input type="file" name="file" class="form-control" accept=".doc,.docx,.pdf,.xls,.xlsx">
                </div>

            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">Lưu thông báo</button>
                <a href="{{ route('thongbao.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
    @endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ----------------- TIÊU ĐỀ -----------------
    const tieuDeSelect = document.getElementById('tieu_de_id');
    const addTitleBtn = document.getElementById('add_title_btn');
    const deleteTitleBtn = document.getElementById('delete_title_btn');
    const inputTieuDe = document.getElementById('input_tieu_de');
    const tieudeCreateUrl = "{{ route('tieude.ajaxCreate') }}";
    const tieudeDeleteUrl = "{{ route('tieude.ajaxDelete') }}";

    addTitleBtn.addEventListener('click', () => {
        inputTieuDe.style.display = 'block';
        inputTieuDe.focus();
    });

    inputTieuDe.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const value = inputTieuDe.value.trim();
            if (!value) return;

            fetch(tieudeCreateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ten_tieu_de: value })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const option = document.createElement('option');
                    option.value = data.id;
                    option.text = data.ten_tieu_de;
                    option.selected = true;
                    tieuDeSelect.appendChild(option);
                    inputTieuDe.value = '';
                    inputTieuDe.style.display = 'none';
                    alert('✅ Thêm tiêu đề thành công');
                } else {
                    alert('❌ Không thể thêm tiêu đề');
                }
            })
            .catch(err => {
                console.error(err);
                alert('⚠️ Lỗi khi thêm tiêu đề');
            });
        }
    });

    deleteTitleBtn.addEventListener('click', () => {
        const selected = tieuDeSelect.value;
        if (!selected) return alert('Chọn tiêu đề để xóa');
        if (!confirm('Bạn có chắc chắn muốn xóa không?')) return;

        fetch(tieudeDeleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ id: selected })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                tieuDeSelect.querySelector(`option[value="${selected}"]`).remove();
                alert('🗑️ Xóa tiêu đề thành công');
            } else {
                alert('⚠️ Không thể xóa tiêu đề (đang được sử dụng)');
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Lỗi khi xóa tiêu đề');
        });
    });

    // ================== MỨC ĐỘ ƯU TIÊN ==================
    const prioritySelect = document.getElementById('muc_do_id');
    const addPriorityBtn = document.getElementById('add_priority_btn');
    const deletePriorityBtn = document.getElementById('delete_priority_btn');
    const inputPriority = document.getElementById('input_muc_do');
    const mucdoCreateUrl = "{{ route('mucdo.ajaxCreate') }}";
    const mucdoDeleteUrl = "{{ route('mucdo.ajaxDelete') }}";

    addPriorityBtn.addEventListener('click', () => {
        inputPriority.style.display = 'block';
        inputPriority.focus();
    });

    inputPriority.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const value = inputPriority.value.trim();
            if (!value) return;

            fetch(mucdoCreateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ten_muc_do: value })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const option = document.createElement('option');
                    option.value = data.id;
                    option.text = data.ten_muc_do;
                    option.selected = true;
                    prioritySelect.appendChild(option);
                    inputPriority.value = '';
                    inputPriority.style.display = 'none';
                    alert('✅ Thêm mức độ thành công');
                } else {
                    alert('❌ Không thể thêm mức độ');
                }
            })
            .catch(err => {
                console.error(err);
                alert('⚠️ Lỗi khi thêm mức độ');
            });
        }
    });

    deletePriorityBtn.addEventListener('click', () => {
        const selected = prioritySelect.value;
        if (!selected) return alert('Chọn mức độ để xóa');
        if (!confirm('Bạn có chắc chắn muốn xóa không?')) return;

        fetch(mucdoDeleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: selected })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                prioritySelect.querySelector(`option[value="${selected}"]`).remove();
                alert('🗑️ Xóa mức độ thành công');
            } else {
                alert('⚠️ Không thể xóa mức độ (đang được sử dụng)');
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Lỗi khi xóa mức độ');
        });
    });

});
</script>
@endpush
