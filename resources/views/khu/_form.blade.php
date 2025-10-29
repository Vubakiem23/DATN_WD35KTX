@csrf

@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="mb-3">
  <label class="form-label">Tên khu <span class="text-danger">*</span></label>
  <input type="text" name="ten_khu" value="{{ old('ten_khu', $khu->ten_khu ?? '') }}" class="form-control" required>
  <small class="text-muted">Ví dụ: A, B, C... hoặc Ký túc xá 1</small>
  </div>

<div class="mb-3">
  <label class="form-label">Giới tính <span class="text-danger">*</span></label>
  <select name="gioi_tinh" class="form-control" required>
    <option value="">--Chọn--</option>
    <option value="Nam" {{ old('gioi_tinh', $khu->gioi_tinh ?? '')=='Nam'?'selected':'' }}>Nam</option>
    <option value="Nữ" {{ old('gioi_tinh', $khu->gioi_tinh ?? '')=='Nữ'?'selected':'' }}>Nữ</option>
  </select>
</div>

<div class="mb-3">
  <label class="form-label">Mô tả</label>
  <input type="text" name="mo_ta" value="{{ old('mo_ta', $khu->mo_ta ?? '') }}" class="form-control">
</div>

<div class="mb-3">
  <button class="btn btn-success" type="submit">Lưu</button>
  <a href="{{ route('khu.index') }}" class="btn btn-secondary">Hủy</a>
</div>



