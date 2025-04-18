<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockTakingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Có thể bạn sẽ muốn kiểm tra quyền sửa ở đây nếu cần
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
            'code' => 'required|exists:inventory_batches,code',
        ];
    }

    /**
     * Get the custom messages for the validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Vui lòng chọn lô hàng cần cập nhật.',
            'code.exists' => 'Lô hàng được chọn không tồn tại trong hệ thống.',
        ];
    }
}
