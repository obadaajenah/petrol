<?php

namespace App\Http\Controllers;
use App\empoloyee;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterEmpRequest;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\validator;

class EmpoloyeeController extends Controller
{
    public function login(Request $request){
        //validate
        $rules=[
            'email'=>'required|max:100',
            'password'=> 'required|max:100',

        ];
        $message=[
          'email.reguired'=> 'The email  is required',
          'password.reguired'=> 'The  password is required',

        ];
        $validator =Validator::make($request->all(),$rules,$message);

         if($validator->fails()){
          return $validator->errors();
          }
          $credentials = $request->only(['email','password']);
          $token =Auth::guard('empoloyee-api')->attempt($credentials);
          $empoloyee =Auth::guard('api2')->user();
          $empoloyee->api_token = $token;

            if(!$token)
             return response()->json("invalid data");


            return response()->json(['user',$empoloyee]);


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

        public function register_empoloyee(RegisterEmpRequest $request){
          $empo=  empoloyee::create([
                'full_name'=>$request->full_name,
                'job'=>$request->job,

            ]);
            return response()->json([$empo]);

        }


        public function edit_empoloyee(Request $request,$empoloyee_id){
            $empol=  empoloyee::find($empoloyee_id);
            $empol->update($request->all());
            $empol->save();
            return response()->json(['message'=>'updated the empoloyee',$empol]);


           }

}
