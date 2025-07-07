<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequestNewPassword extends FormRequest
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

    public function rules()
    {
        return [
            'password' => [
                'required',
                'string',
                'min:8',             // Tối thiểu 8 ký tự
                'regex:/[a-z]/',      // Ít nhất một chữ cái thường
                'regex:/[0-9]/',      // Ít nhất một chữ số
                'regex:/[@$!%*#?&]/', // Ít nhất một ký tự đặc biệt
            ],
        ];
    }

    public function messages()
    {
        return [
            'password.required'         => 'Mật khẩu không được để trống.',
            'password.string'           => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min'              => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.regex'            => 'Mật khẩu phải chứa ít nhất một chữ cái thường, một chữ số và một ký tự đặc biệt.',

            'password_confirm.required'      => 'Dữ liệu không được để trống',
            'password.required'              => 'Dữ liệu không được để trống',
            'password_confirm.same'          => 'Mật khẩu không khớp'
        ];
    }
}
