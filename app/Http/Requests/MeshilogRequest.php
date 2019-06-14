<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MeshilogRequest extends FormRequest
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
      return [
        'title' => 'required',
        'body' => 'required'
      ];
    }

    public function messages() {
      return [
        'title.required' => '必須入力です！',
        'body.required' => '必須入力です！'
      ];
    }
}
