<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequestAccount extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'email'     => 'required|max:190|min:3|email|unique:admins,email,' . $this->id,
            'name'      => 'required|string|max:255',
            'phone'     => [
                'required',
                'unique:admins,phone,',
                'regex:/^0[^0]\d{8}$/', // Quy tắc số điện thoại Việt Nam
            ] . $this->id,
        ];

        if (!$this->id) {
            $rules['password'] = [
                'required',
                'string',
                'min:8',             // Độ dài tối thiểu 8 ký tự (thường khuyến nghị 8 hơn 6)
                'regex:/[a-z]/',      // Yêu cầu ít nhất một chữ cái thường
                'regex:/[0-9]/',      // Yêu cầu ít nhất một chữ số
                'regex:/[@$!%*#?&]/', // Yêu cầu ít nhất một ký tự đặc biệt (có thể thêm các ký tự khác nếu muốn)
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required'             => 'Tên không được để trống.',
            'name.string'               => 'Tên phải là chuỗi ký tự.',
            'name.max'                  => 'Tên không được vượt quá :max ký tự.',

            'email.required'            => 'Email không được để trống.',
            'email.max'                 => 'Email không được vượt quá :max ký tự.',
            'email.min'                 => 'Email phải có ít nhất :min ký tự.',
            'email.unique'              => 'Email này đã được sử dụng.',

            'phone.required'            => 'Số điện thoại không được để trống.',
            'phone.unique'              => 'Số điện thoại này đã được sử dụng.',
            'phone.regex'               => 'Vui lòng nhập số điện thoại 10 chữ số (ví dụ: 0912345678).',

            'password.required'         => 'Mật khẩu không được để trống.',
            'password.string'           => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min'              => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.regex'            => 'Mật khẩu phải chứa ít nhất một chữ cái thường, một chữ số và một ký tự đặc biệt.',
        ];
    }
}
