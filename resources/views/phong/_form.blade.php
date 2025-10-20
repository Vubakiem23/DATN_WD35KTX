@csrf

<div class="mb-3">
  <label class="form-label">Tên phòng</label>
  <input type="text" name="ten_phong" value="{{ old('ten_phong', $phong->ten_phong ?? '') }}" class="form-control" required>
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">Khu</label>
    <input type="text" name="khu" value="{{ old('khu', $phong->khu ?? '') }}" class="form-control">
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label">Loại phòng</label>
    <select name="loai_phong" class="form-control">
      <option value="">--Chọn--</option>
      <option value="Đơn" {{ old('loai_phong', $phong->loai_phong ?? '')=='1'?'selected':'' }}>Phòng 1</option>
      <option value="Đôi" {{ old('loai_phong', $phong->loai_phong ?? '')=='2'?'selected':'' }}>Phòng 2</option>
      <option value="Đôi" {{ old('loai_phong', $phong->loai_phong ?? '')=='3'?'selected':'' }}>Phòng 3</option>
      <option value="Đôi" {{ old('loai_phong', $phong->loai_phong ?? '')=='4'?'selected':'' }}>Phòng 4</option>
    </select>
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label">Giới tính</label>
    <select name="gioi_tinh" class="form-control">
      <option value="">--Không ràng buộc--</option>
      <option value="Nam" {{ old('gioi_tinh', $phong->gioi_tinh ?? '')=='Nam'?'selected':'' }}>Nam</option>
      <option value="Nữ" {{ old('gioi_tinh', $phong->gioi_tinh ?? '')=='Nữ'?'selected':'' }}>Nữ</option>
      <option value="Cả hai" {{ old('gioi_tinh', $phong->gioi_tinh ?? '')=='Cả hai'?'selected':'' }}>Cả hai</option>
    </select>
  </div>
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">Sức chứa</label>
    <input type="number" name="suc_chua" value="{{ old('suc_chua', $phong->suc_chua ?? 1) }}" class="form-control" min="1" required>
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label">Trạng thái</label>
    <select name="trang_thai" class="form-control" required>
      <option value="Trống" {{ old('trang_thai', $phong->trang_thai ?? '')=='Trống'?'selected':'' }}>Trống</option>
      <option value="Đã ở" {{ old('trang_thai', $phong->trang_thai ?? '')=='Đã ở'?'selected':'' }}>Đã ở</option>
      <option value="Bảo trì" {{ old('trang_thai', $phong->trang_thai ?? '')=='Bảo trì'?'selected':'' }}>Bảo trì</option>
    </select>
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label">Ghi chú</label>
    <input type="text" name="ghi_chu" value="{{ old('ghi_chu', $phong->ghi_chu ?? '') }}" class="form-control">
  </div>
</div>
{{-- Upload ảnh --}}
<div class="mb-3">
  <label class="form-label">Ảnh phòng (tùy chọn)</label>
  <input type="file" name="hinh_anh" class="form-control" accept="image/*">
</div>
<div class="mb-3">
  <button class="btn btn-success" type="submit">Lưu</button>
  <a href="{{ route('phong.index') }}" class="btn btn-secondary">Hủy</a>
</div>

