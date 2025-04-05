<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'province_id' => 'required|gt:0',
            'district_id' => 'required|gt:0',
            'ward_id' => 'required|gt:0',
            'payment_method' => 'required',
            'products' => 'required|json',
            'total_price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.required' => 'Bạn chưa nhập Họ Tên.',
            'email.required' => 'Bạn chưa nhập Email.',
            'email.email' => 'Email không hợp lệ.',
            'phone.required' => 'Bạn chưa nhập Số điện thoại.',
            'address.required' => 'Bạn chưa nhập địa chỉ.',
            'province_id.required' => 'Bạn chưa chọn Thành Phố.',
            'province_id.gt' => 'Vui lòng chọn Thành Phố.',
            'district_id.required' => 'Bạn chưa chọn Quận Huyện.',
            'district_id.gt' => 'Vui lòng chọn Quận Huyện.',
            'ward_id.required' => 'Bạn chưa chọn Phường Xã.',
            'ward_id.gt' => 'Vui lòng chọn Phường Xã.',
            'payment_method.required' => 'Bạn chưa chọn phương thức thanh toán.',
            'products.required' => 'Bạn chưa thêm sản phẩm vào đơn hàng.',
            'products.json' => 'Dữ liệu sản phẩm không hợp lệ, vui lòng kiểm tra lại.',
            'total_price.required' => 'Tổng giá trị đơn hàng không được để trống.',
            'total_price.numeric' => 'Tổng giá trị đơn hàng phải là một số.',
            'total_price.min' => 'Tổng giá trị đơn hàng không thể nhỏ hơn 0.',
        ];
    }
}
