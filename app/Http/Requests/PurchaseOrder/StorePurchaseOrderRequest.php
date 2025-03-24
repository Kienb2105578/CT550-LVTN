<?php

namespace App\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
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
            'code' => 'required|string|unique:purchase_orders,code|max:100',
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required',
            'product_id.*' => 'exists:products,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Bạn chưa nhập mã đơn hàng.',
            'code.unique' => 'Mã đơn hàng đã tồn tại.',
            'code.max' => 'Mã đơn hàng không được vượt quá 100 ký tự.',

            'supplier_id.required' => 'Bạn chưa chọn nhà cung cấp.',
            'supplier_id.exists' => 'Nhà cung cấp không hợp lệ.',

            'product_id.required' => 'Bạn chưa chọn sản phẩm.',
            'product_id.*.exists' => 'Một hoặc nhiều sản phẩm không hợp lệ.',
        ];
    }
}
