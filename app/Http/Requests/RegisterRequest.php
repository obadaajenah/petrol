<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'full_name' => 'required|max:100',
            'email' => 'required|unique:users,email',
            'phone_number' => 'required|unique:users,phone_number',
            'national_number' => 'required|unique:users,national_number',
            'password' => 'required',
            'name_car' => 'required|max:100',
            'car_number' => 'required|max:100|unique:reference_infos,car_number',
            'type' => 'required|max:100',
            'category' => 'required',
          //  'amount' => 'required',
            'manufacturing_year' => 'required',
            'engine_number' => 'required',
            'passengers_number' => 'required',
        ];
    }

    public function massages(){
        return [];
    }

    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json($validator->errors()),422);
    }
}
