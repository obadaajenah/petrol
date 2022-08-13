<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterEmpRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'full_name' => 'required|max:100',

            'job' => 'required',
        ];
    }
    public function massages(){
        return [];
    }

    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json($validator->errors()),422);
    }
}
