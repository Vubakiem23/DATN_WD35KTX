@extends('admin.layouts.admin')

@section('title', 'Thêm tin tức')

@section('content')
<div class="container mt-4" style="max-width: 900px; background:#f9f9f9; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
    <h3 class="mb-3 text-primary">📰 Thêm tin tức</h3>
    <hr>

    {{-- Hiển thị lỗi --}}
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('tintuc.store') }}" method="POST" enctype="multipart/form-data" id="tintuc-form">
        @csrf
        <div class="row">

            {{-- Tiêu đề --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="tieu_de" class="form-control" value="{{ old('tieu_de') }}" required>
            </div>

            {{-- Nội dung --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Nội dung</label>
                <textarea name="noi_dung" id="noi_dung" class="form-control" rows="5">{{ old('noi_dung') }}</textarea>
            </div>

            {{-- Ngày đăng --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Ngày đăng</label>
                <input type="date" name="ngay_tao" class="form-control" value="{{ old('ngay_tao', date('Y-m-d')) }}" required>
            </div>

            {{-- Hashtags --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Hashtags</label>
                <select name="hashtags[]" id="hashtags" class="form-select select2-tags" multiple>
                    @foreach($hashtags as $hashtag)
                        <option value="{{ $hashtag->id }}">{{ $hashtag->ten }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Hình ảnh --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Hình ảnh</label>
                <input type="file" name="hinh_anh" class="form-control" accept="image/*" id="hinh_anh">

                <small class="text-muted">Chọn ảnh (.jpg, .png, .gif, .webp) tối đa 2MB</small>

                <div class="mt-2">
                    <img id="preview-img"
                        src="https://dummyimage.com/150x100/eff3f9/9aa8b8&text=Preview"
                        alt="Preview"
                        style="max-width:150px; max-height:100px; border-radius:6px; object-fit:cover;">
                </div>
            </div>

        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Thêm</button>
            <a href="{{ route('tintuc.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    let editor;
    ClassicEditor.create(document.querySelector('#noi_dung'))
        .then(ed => editor = ed)
        .catch(error => console.error(error));

    document.getElementById('tintuc-form').addEventListener('submit', function () {
        document.querySelector('#noi_dung').value = editor.getData();
    });
</script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
/* Style giống tag phòng–khu */
.select2-selection__choice {
    background-color: #007bff !important;
    border: none !important;
    color: white !important;
    padding: 4px 8px !important;
    border-radius: 6px !important;
    margin-top: 6px !important;
    font-size: 14px !important;
}
.select2-selection__choice__remove {
    color: white !important;
    margin-right: 6px !important;
}
</style>

<script>
    $('#hashtags').select2({
        placeholder: "🏷️ Chọn hashtag",
        allowClear: true,
        tags: true,
        width: "100%",
    });
</script>

<!-- Preview ảnh -->
<script>
    const input = document.getElementById('hinh_anh');
    const preview = document.getElementById('preview-img');

    input.addEventListener('change', function () {
        const [file] = input.files;
        preview.src = file
            ? URL.createObjectURL(file)
            : "https://dummyimage.com/150x100/eff3f9/9aa8b8&text=Preview";
    });
</script>
@endpush
