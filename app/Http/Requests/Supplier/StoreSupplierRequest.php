<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    /**
     * Determine if the supplier is authorized to make this request.
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
            'name'  => 'required|string',
            'code'  => 'required|string|unique:suppliers,code|max:100',
            'phone' => ['required', 'string', 'regex:/^(\+84|0)[0-9]{9,10}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Bạn chưa nhập Tên nhà cung cấp',
            'name.string'    => 'Họ Tên phải là dạng ký tự',

            'code.required'  => 'Bạn chưa nhập Mã nhà cung cấp',
            'code.string'    => 'Mã nhà cung cấp phải là chuỗi ký tự',
            'code.unique'    => 'Mã nhà cung cấp đã tồn tại',
            'code.max'       => 'Mã nhà cung cấp tối đa 100 ký tự',

            'phone.required' => 'Bạn chưa nhập số điện thoại.',
            'phone.string' => 'Số điện thoại phải là dạng ký tự.',
            'phone.regex' => 'Số điện thoại không đúng định dạng (phải bắt đầu bằng +84 hoặc 0 và có 9-10 chữ số).',
        ];
    }
}
