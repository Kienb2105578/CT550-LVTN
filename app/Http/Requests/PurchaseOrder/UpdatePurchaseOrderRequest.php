<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Log;

class UpdatePurchaseOrderRequest extends FormRequest
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
            'code' => 'required|string|max:100|unique:purchase_orders,code,' . $this->id,  // Kiểm tra mã đơn hàng duy nhất khi cập nhật
            'supplier_id' => 'required|exists:suppliers,id',  // Kiểm tra nhà cung cấp tồn tại
            //  'product_id' => 'required_without_all:product_id.*',  // Kiểm tra nếu không có sản phẩm mới thì phải có sản phẩm cũ
            'product_id.*' => 'exists:products,id',  // Kiểm tra từng sản phẩm có tồn tại
        ];
    }

    /**
     * Get the custom error messages for validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Bạn chưa nhập mã đơn hàng.',
            'code.unique' => 'Mã đơn hàng đã tồn tại.',
            'code.max' => 'Mã đơn hàng không được vượt quá 100 ký tự.',
            'supplier_id.required' => 'Bạn chưa chọn nhà cung cấp.',
            'supplier_id.exists' => 'Nhà cung cấp không hợp lệ.',
            //'product_id.required_without_all' => 'Bạn chưa chọn sản phẩm. Vui lòng chọn ít nhất một sản phẩm.',
            'product_id.*.exists' => 'Một hoặc nhiều sản phẩm không hợp lệ.',
        ];
    }
}
