<!-- Modal Create Slots -->
<div class="modal fade" id="createSlotsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="createSlotsForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header"><h5>Tạo slot mới</h5></div>
        <div class="modal-body">
          <div class="form-group">
            <label>Chế độ tạo</label>
            <select id="create_mode" name="mode" class="form-control">
              <option value="count">Tạo theo số lượng</option>
              <option value="list">Nhập danh sách mã slot (mỗi mã 1 dòng)</option>
            </select>
          </div>

          <div class="form-group mode-count">
            <label>Số lượng</label>
            <input type="number" name="count" min="1" max="200" class="form-control" value="1">
            <small class="form-text text-muted">Tạo N slot tự sinh mã: P{phongId}-S{n}</small>z
          </div>

          <div class="form-group mode-list d-none">
            <label>Danh sách mã slot (mỗi dòng 1 mã)</label>
            <textarea name="codes" class="form-control" rows="6" placeholder="P14-SA1&#10;P14-SA2"></textarea>
          </div>

          <div class="form-group">
            <label>Cơ sở vật chất riêng cho slot</label>
            <textarea name="cs_vat_chat" class="form-control" rows="2" placeholder="VD: 1 giường, 1 chiếu, ..."></textarea>
          </div>
          <div class="form-group">
            <label>Ảnh thực tế slot (tùy chọn)</label>
            <input type="file" name="hinh_anh" class="form-control" accept="image/*">
          </div>

          <div id="createSlotsError" class="text-danger d-none"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-success">Tạo</button>
        </div>
      </div>
    </form>
  </div>
</div>
