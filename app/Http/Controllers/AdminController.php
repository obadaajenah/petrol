<?php

namespace App\Http\Controllers;
use App\admin;
use App\Tank_state;
use App\Http\Requests\RegisterRequest;
use App\User;
use App\Reference_info;
use App\Gas_station;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{

    public function login(Request $request){
     //validate
     $rules =[
        'email' => 'required | exists:admins,email',
        'password' => 'required'
     ];
     $message=[
        'email.reguired'=> 'The email is required',
        'password.reguired'=> 'The  password is required',

      ];
      $validator =Validator::make($request->all(),$rules,$message);

      if($validator->fails()){
         return $validator->errors();
       }
        //login
    $credentials = $request->only(['email','password']);
    $token =Auth::guard('admin-api')->attempt($credentials);
    $admin =Auth::guard('api1')->user();
    $admin->api_token = $token;

      if(!$token)
       return response()->json("invalid data");


      return response()->json(['admin',$admin]);



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
    public function get_all_user(){
        $user=user::select('id','first_name','last_name','phone_number')->get();
        return response()->json(['message'=>'This all users',$user]);
    }

    public function delete_user($id){
        $user=user::destroy($id);
        return response()->json(['message'=>'The user is delete']);
    }
    public function search_user($first_name){
      $user=user::where('first_name','like','%'.$first_name.'%')->get();
      return response()->json([$user]);
    }
    public function sort_user($sort_by){
    $user=user::get()->sortBy($sort_by);
    return response()->json(['message'=>'The user is sort',$user]) ;
    }


    #####################################################################################################################
   public function regester_user(RegisterRequest $request){
    try{
    DB::beginTransaction();

//     $message=[
//       'full_name.reguired'=> 'The first_name is required',
//       'email.reguired'=> 'The email is required',
//       'phone_number.reguired'=> 'The phone_number is required',
//       'phone_number.unique'=> 'The phone_number should be unique',
//       'password.reguired'=> 'The  password is required',

//     ];


    //register  user
   $owner= User::create([
    'full_name'=>$request->full_name,
    'email'=>$request->email,
    'phone_number'=>$request->phone_number,
    'national_number'=>$request->national_number,
    'password'=>bcrypt($request->password),
    ]);


//     $message=[
//       'name_car.reguired'=> 'The name_car is required',
//       'car_number.reguired'=> 'The car_number is required',
//       'type.reguired'=> 'The type is required',
//       'category.unique'=> 'The category should be unique',
//       'amount.reguired'=> 'The amount is required',
//       'manufacturing_year.reguired'=> 'The  manufacuring_year is required',
//       'engine_number.reguired'=> 'The  engine_number is required',
//       'passengers_number.reguired'=> 'The  passenger_number is required',

//     ];

    //register referanc info
    if($request->type =='private'){
        $feol=30;
    }if($request->type =='public'){
        $feol=50;
    }if($request->type =='factory'){
        $feol=100;
    }
  Reference_info::create([
    'owner'=>$owner->id,
    'name_car'=>$request->name_car,
    'car_number'=>$request->car_number,
    'type'=>$request->type,
    'category'=>$request->category,
    'amount'=>$feol,
    'manufacturing_year'=>$request->manufacturing_year,
    'engine_number'=>$request->engine_number,
    'passengers_number'=>$request->passengers_number,


  ]);
  DB::commit();
  return response()->json(['saved successfully']);

   }catch(\Exception $ex){
    DB::rollBack();
    return response()->json('problem',500);
   }

}

public function ff(){
    $time =Carbon::now()->subDays(7);

     $users=User::with('info')->where('updated_at','>',$time)->get();

//    foreach($users as $user){
//         // return $user->info ->car_number ;
//         // $user->updated_at =now();
//     }
}
 public function amount_of_tank(){
  $amounts=Tank_state::value('amount');
  $parcent=$amounts / 20000.00 * 100 ;
 return response()->json(['The parcent is',$parcent.'%','The amount of tank is ',$amounts]);

 }

}
