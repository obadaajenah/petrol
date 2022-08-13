<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Tank_state;
use App\Requestfeol;
use App\Turnforfeol;
use App\bill;
use App\Http\Requests\ChangePassRequest;
use Illuminate\Support\Facades\Hash;
use App\empoloyee;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\validator;
use Carbon\Carbon;

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



    public function edit_password(Request $request,$user_id){
     $user= user::find($user_id);
     $user->update([bcrypt($request->password)]);
     return response()->json(['message'=>'updated the password',$user]);


    }



     public function Request(Request $request,$user_id ){

        Requestfeol::create([
         'user'=>$user_id,
         'amount'=>$request->amount,

        ]);

        Turnforfeol::create([
          'user'=>$user_id,
        ]);

        return response()->json(['message'=>'your turn is input']);




    }

    public function turn(Request $request){
        $record=Tank_state::first();
        $record->amount;
        $time =Carbon::now()->subDays(2);
        $users=Turnforfeol::get();
        $count=0;
         foreach($users as $user){
        if($user->updated_at < $time){
         $count=$count+1;
         $user->amount;
         $record->amount=$record->amount -$request->amount ;
        $record->save();
        $price=$user->amount *5000;
        $bbb=empoloyee::find(27);
        $worker=$bbb->full_name;
          bill::create([
           'amount'=>$request->amount,
           'payment'=>$price,
           'user_id'=>$user->id,
           'employee_id'=>$worker,
          ]);
        }

         }
         return $count;


    }
    public function get_bill(request $request,$user_id){
        $bill=bill::get()->where('user_id',$user_id);
        return response()->json(['message'=>'these all bill',$bill]);


    }
    public function  changePassword(ChangePassRequest $request)
    {
        $user_id = Auth::guard('user-api')->user()->id;
        $user = User::find($user_id);

        if (Hash::check($request->oldPassword, $user->password)) {
            if ($request->newPassword == $request->oldPassword) {
                return response()->json(['message' => ['new password  match old password ']], 450);
            }
            if ($request->newPassword != $request->confirmation_password) {
                return response()->json(['message' => ['new password not match confirmation password']], 450);
            }

            $user->password = bcrypt($request->newPassword);
            $user->save();
            return response()->json(['message' => ['password changed successfully']]);
        } else {
            return response()->json(['message' => ['old password not match ']], 450);
        }
    }
}
