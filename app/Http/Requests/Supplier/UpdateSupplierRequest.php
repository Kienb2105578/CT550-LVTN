<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:100|unique:suppliers,code,' . $this->id,
            'phone' => ['required', 'string', 'regex:/^(\+84|0)[0-9]{9,10}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập Họ Tên.',
            'name.string' => 'Họ Tên phải là dạng ký tự.',
            'name.max' => 'Họ Tên không được vượt quá 191 ký tự.',

            'code.required' => 'Bạn chưa nhập Mã nhà cung cấp.',
            'code.string' => 'Mã nhà cung cấp phải là dạng ký tự.',
            'code.max' => 'Mã nhà cung cấp không được vượt quá 100 ký tự.',
            'code.unique' => 'Mã nhà cung cấp đã tồn tại.',

            'phone.required' => 'Bạn chưa nhập số điện thoại.',
            'phone.string' => 'Số điện thoại phải là dạng ký tự.',
            'phone.regex' => 'Số điện thoại không đúng định dạng (phải bắt đầu bằng +84 hoặc 0 và có 9-10 chữ số).',
        ];
    }
}
