<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\validator;

class UserController extends Controller
{
    public function login(Request $request){
    //validate
    $rules=[
        'phone_number'=>'required|max:100',
        'password'=> 'required|max:100',

    ];
    $message=[
      'phone_number.reguired'=> 'The phone_number is required',
      'password.reguired'=> 'The  password is required',

    ];
    $validator =Validator::make($request->all(),$rules,$message);

     if($validator->fails()){
      return $validator->errors();
      }

    //login
    $credentials = $request->only(['phone_number','password']);
    $token =Auth::guard('user-api')->attempt($credentials);
    $user =Auth::guard('api')->user();
    $user->api_token = $token;

      if(!$token)
       return response()->json("invalid data");


      return response()->json(['user',$user]);


 }
    public function logout(Request $request){
        $token =$request->header('auth-token');
        if($token){
            try{
                JWTAuth::setToken($token)->invalidate();
            }catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
               return response()->json(["E000",'some things went wrongs']);
              // return $this->returnError('E000','some things went wrongs');
            }
                return response()->json(['logged out successfully']);
             // return $this->returnSuccessMessage('logged out successfully');
        }
        else{
            return response()->json(['some things went wrongs']);
         // return $this->returnError('E000','some things went wrongs');
        }

    }
    public function UserRegister(Request $request){
        //validate
        $rules=[
            'first_name'=>'required|max:100',
            'last_name'=>'required|max:100',
            'phone_number'=> 'required|unique:users,phone_number',
            'national_number'=> 'required',
            'car_number'=> 'required',
            'password'=> 'required'

        ];
        $message=[
          'first_name.reguired'=> 'The first_name is required',
          'last_name.reguired'=> 'The last_name is required',
          'phone_number.reguired'=> 'The phone_number is required',
          'phone_number.unique'=> 'The phone_number should be unique',
          'car_number.reguired'=> 'The car_number is required',
          'password.reguired'=> 'The  password is required',

        ];
        $validator =Validator::make($request->all(),$rules,$message);

     if($validator->fails()){
        return $validator->errors();
      }

        //register
        User::create([
        'first_name'=>$request->first_name,
        'last_name'=>$request->last_name,
        'phone_number'=>$request->phone_number,
        'national_number'=>$request->national_number,
        'car_number'=>$request->car_number,
        'password'=>bcrypt($request->password),
        ]);
        return response()->json(['saved successfully']);
    }
}
