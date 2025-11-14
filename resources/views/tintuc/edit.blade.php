@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa tin tức')

@section('content')
<div class="container mt-4" style="max-width: 900px; background:#f9f9f9; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
    <h3 class="mb-3 text-primary">✏️ Chỉnh sửa tin tức</h3>
    <hr>

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

    <form action="{{ route('tintuc.update', $tintuc->id) }}" method="POST" enctype="multipart/form-data" id="tintuc-form">
        @csrf
        @method('PUT')
        <div class="row">

            {{-- Tiêu đề --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="tieu_de" class="form-control" value="{{ old('tieu_de', $tintuc->tieu_de) }}" required>
            </div>

            {{-- Nội dung --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Nội dung</label>
                <textarea name="noi_dung" id="noi_dung" class="form-control" rows="5">{{ old('noi_dung', $tintuc->noi_dung) }}</textarea>
            </div>

            {{-- Ngày đăng --}}
            <div class="col-md-6 mb-3">
    <label class="form-label">Ngày đăng</label>
    <input type="date" name="ngay_tao" class="form-control" 
           value="{{ old('ngay_tao', \Carbon\Carbon::parse($tintuc->ngay_tao)->format('Y-m-d')) }}" 
           required>
</div>

            {{-- Hashtags --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Hashtags</label>
                <select name="hashtags[]" class="form-select" id="hashtags" multiple>
                    @foreach($hashtags as $hashtag)
                        <option value="{{ $hashtag->id }}" 
                            {{ $tintuc->hashtags->contains($hashtag->id) ? 'selected' : '' }}>
                            {{ $hashtag->ten }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Cập nhật</button>
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
ClassicEditor
    .create(document.querySelector('#noi_dung'))
    .then(ed => { editor = ed; })
    .catch(error => console.error(error));

document.getElementById('tintuc-form').addEventListener('submit', function(e){
    document.querySelector('#noi_dung').value = editor.getData();
});
</script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$('#hashtags').select2({
    placeholder: 'Chọn hoặc nhập hashtags',
    tags: true,
    tokenSeparators: [',']
});
</script>
@endpush
