<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhongRequest extends FormRequest
{
    /**
     * Xác định user có quyền thực hiện request này không
     */
    public function authorize()
    {
        // Cho phép tất cả các request đã authenticated
        // Nếu muốn kiểm tra role cụ thể, sửa ở đây
        return true;
    }

    /**
     * Các quy tắc validation
     */
    public function rules()
    {
        $phongId = $this->route('phong') ? $this->route('phong')->id : null;

        $isCreating = $this->isMethod('post') && !$phongId;
        $statusRule = $isCreating
            ? Rule::in(['Trống'])
            : Rule::in(['Trống', 'Đã ở', 'Bảo trì']);

        return [
            'ten_phong' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z0-9À-ỹ\s\-\.]+$/', // Chỉ cho phép chữ, số, dấu gạch ngang, dấu chấm và khoảng trắng
                // Unique check (trừ phòng hiện tại khi update)
                $phongId 
                    ? Rule::unique('phong', 'ten_phong')->ignore($phongId)
                    : Rule::unique('phong', 'ten_phong')
            ],
            'khu_id' => [
                'required',
                'exists:khu,id'
            ],
            'loai_phong' => [
                'nullable',
                'string',
                'max:50',
            ],
            'gioi_tinh' => [
                'required',
                'in:Nam,Nữ,Cả hai'
            ],
            'suc_chua' => [
                'required',
                'integer',
                'min:1',
                'max:8' // Giới hạn sức chứa tối đa 8 người
            ],
            'gia_phong' => [
                'required',
                'integer',
                'min:0',
                'max:1000000000'
            ],
            'trang_thai' => [
                'required',
                $statusRule
            ],
            'ghi_chu' => [
                'nullable',
                'string',
                'max:500'
            ],
            'hinh_anh' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:5120', // Max 5MB
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000'
            ]
        ];
    }

    /**
     * Custom messages cho các lỗi validation
     */
    public function messages()
    {
        return [
            // ten_phong
            'ten_phong.required' => 'Tên phòng là bắt buộc',
            'ten_phong.string' => 'Tên phòng phải là chuỗi ký tự',
            'ten_phong.min' => 'Tên phòng phải có ít nhất :min ký tự',
            'ten_phong.max' => 'Tên phòng không được vượt quá :max ký tự',
            'ten_phong.regex' => 'Tên phòng chỉ được chứa chữ cái, số, dấu gạch ngang, dấu chấm và khoảng trắng',
            'ten_phong.unique' => 'Phòng tên này đã có, không thể tạo hoặc sửa',

            // khu
            'khu_id.required' => 'Khu là bắt buộc',
            'khu_id.exists' => 'Khu không hợp lệ',

            // loai_phong
            'loai_phong.string' => 'Loại phòng phải là chuỗi ký tự',
            'loai_phong.max' => 'Loại phòng không được vượt quá :max ký tự',

            // gioi_tinh
            'gioi_tinh.required' => 'Giới tính phòng là bắt buộc',
            'gioi_tinh.in' => 'Giới tính phòng phải là: Nam, Nữ hoặc Cả hai',

            // suc_chua
            'suc_chua.required' => 'Sức chứa là bắt buộc',
            'suc_chua.integer' => 'Sức chứa phải là số nguyên',
            'suc_chua.min' => 'Sức chứa phải ít nhất là :min người',
            'suc_chua.max' => 'Sức chứa không được vượt quá :max người',

            // trang_thai
            'trang_thai.required' => 'Trạng thái phòng là bắt buộc',
            'trang_thai.in' => 'Trạng thái phòng không hợp lệ',

            // gia_phong
            'gia_phong.required' => 'Giá phòng là bắt buộc',
            'gia_phong.integer' => 'Giá phòng phải là số nguyên (VND)',
            'gia_phong.min' => 'Giá phòng không được âm',
            'gia_phong.max' => 'Giá phòng quá lớn',

            // ghi_chu
            'ghi_chu.string' => 'Ghi chú phải là chuỗi ký tự',
            'ghi_chu.max' => 'Ghi chú không được vượt quá :max ký tự',

            // hinh_anh
            'hinh_anh.image' => 'File tải lên phải là hình ảnh',
            'hinh_anh.mimes' => 'Hình ảnh phải có định dạng: jpeg, jpg, png, gif hoặc webp',
            'hinh_anh.max' => 'Kích thước hình ảnh không được vượt quá 5MB',
            'hinh_anh.dimensions' => 'Kích thước hình ảnh phải từ 100x100 đến 4000x4000 pixels',
        ];
    }

    /**
     * Custom attributes cho các trường
     */
    public function attributes()
    {
        return [
            'ten_phong' => 'tên phòng',
            'khu_id' => 'khu',
            'loai_phong' => 'loại phòng',
            'gioi_tinh' => 'giới tính',
            'suc_chua' => 'sức chứa',
            'trang_thai' => 'trạng thái',
            'gia_phong' => 'giá phòng',
            'ghi_chu' => 'ghi chú',
            'hinh_anh' => 'hình ảnh',
        ];
    }

    /**
     * Xử lý sau khi validation thành công
     */
    protected function passedValidation()
    {
        // Chuẩn hóa dữ liệu
        $this->merge([
            'ten_phong' => trim($this->ten_phong),
            'ghi_chu' => $this->ghi_chu ? trim($this->ghi_chu) : null,
        ]);
    }
}
