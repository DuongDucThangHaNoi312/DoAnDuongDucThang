<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffTitleRequest extends FormRequest
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
    public function rules($id = 0)
    {
        return [
            'code' => 'required|max:50|regex:/^[A-Za-z0-9_.-]+$/|unique:staff_titles,code' . ($id == 0 ? '' : ',' . $id),
            'name' => 'required|max:255',
            'weight'=>'required'
        ];
    }
}
