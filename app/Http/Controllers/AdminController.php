<?php

namespace App\Http\Controllers;
use App\admin;
use App\Bill;
use App\empoloyee;
use App\Tank_state;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\compoRequest;
use App\User;
use App\complaint;
use App\send_mes;
use App\Reference_info;
use App\Gas_station;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\validator;
use Tymon\JWTAuth\Facades\JWTAuth;
////////////////sms//////////////////

require 'vendor/autoload.php';

use SMSGatewayMe\Client\ApiClient;
use SMSGatewayMe\Client\Configuration;
use SMSGatewayMe\Client\Api\MessageApi;
use SMSGatewayMe\Client\Model\SendMessageRequest;

/////////////////////////////////////


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


      return response()->json([$admin]);



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
        $users=User::with('info')->get();
        //$user=user::select('id','full_name','phone_number')->get();
        return response()->json($users);
    }

    public function delete_user($user_id){
       $user=user::find($user_id)->delete();

        $refran=Reference_info::where('owner',$user_id)->delete();
       // $users=User::with('info')->where('owner',$user_id)->first();
      //  $users=Reference_info::with('user')->where('id',$user_id)->first();

        return response()->json(['message'=>'The user is delete']);
    }
    public function search_user($full_name){
      $user=user::where('full_name','like','%'.$full_name.'%')->get();
      return response()->json([$user]);
    }
    public function sort_user($sort_by){
    $user=user::get()->sortBy($sort_by);
    return response()->json(['message'=>'The user is sort',$user]) ;
    }
    public function get_all_empoloyee(){
      $empoloyees=empoloyee::get();
      return response()->json($empoloyees);

    }

    public function dellete_empm($empol_id){
     $empo=empoloyee::find($empol_id)->delete();
     return response()->json(['messages'=>'The empoloyee is delete']);

    }


    public function edit_user(request $request,$user_id ){
        $user=user::find($user_id );
        $refran=Reference_info::where('owner',$user_id)->first();
        $user->full_name =$request->input('full_name',$user->full_name);
        $user->email =$request->input('email',$user->email);
        $user->phone_number =$request->input('phone_number',$user->phone_number);
        $user->national_number =$request->input('national_number',$user->national_number);
        $user->save();
        $refran->type =$request->input('type',$user->type);
        $refran->name_car =$request->input('name_car',$user->name_car);
        $refran->car_number =$request->input('car_number',$user->car_number);
        $refran->save();



        return response()->json(['message'=> 'update the user']);





    }



    #####################################################################################################################
   public function regester_user(RegisterRequest $request){

    DB::beginTransaction();
    try{

    //register  user
   $owner= User::create([
    'full_name'=>$request->full_name,
    'email'=>$request->email,
    'phone_number'=>$request->phone_number,
    'national_number'=>$request->national_number,
    'password'=>bcrypt($request->password),
    ]);


////////////////////////sms/////////////////////////////////////////////////////////////////

$phone=$request->input("phone_number");

$pass=$request->input("password");

///////////////////////////////////////////////////////////
// Configure client
$config = Configuration::getDefaultConfiguration();
$config->setApiKey('Authorization', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhZG1pbiIsImlhdCI6MTY1OTUzMDI5NywiZXhwIjo0MTAyNDQ0ODAwLCJ1aWQiOjk1NDczLCJyb2xlcyI6WyJST0xFX1VTRVIiXX0.kRydYmpnLSvjNuq_A3eXeVKAGlF2HtnX5g5Ew8uJsFw');
$apiClient = new ApiClient($config);
$messageClient = new MessageApi($apiClient);

// Sending a SMS Message
$sendMessageRequest1 = new SendMessageRequest([
    'phoneNumber' => $phone,
    'message' => "You have been successfully registered Your password is {$pass}",
    'deviceId' => 128877
]);
$sendMessages = $messageClient->sendMessages([
    $sendMessageRequest1
]);
print_r($sendMessages);

/////////////sms//////////







    //register referanc info
    if($request->type =='private'){
        $feol=30;
    }if($request->type =='public'){
        $feol=50;
    }if($request->type =='factory'){
        $feol=100;
    }
     $refer= Reference_info::create([
    'owner'=>$owner->id,
    'name_car'=>$request->name_car,
    'car_number'=>$request->car_number,
    'type'=>$request->type,
    'amount'=>$feol,



  ]);



  DB::commit();
  return response()->json([$owner, $refer]);

   }
   catch(\Exception $ex){
    echo ($ex);
    DB::rollBack();
    return response()->json('problem',500);
   }



}


public function ff(){
    $time =Carbon::now()->subDays(7);
    $users=user::get();
    $count=0;

   foreach($users as $user){
       if($user->updated_at < $time){
          $count=$count + 1 ;

/////////////////////////sms////////////////////////////////////////////////////////

   /*******************/

    $phone=$user->phone_number;

/////////////////////////////////////////////////////////////////////////////////////////

// Configure client
$config = Configuration::getDefaultConfiguration();
$config->setApiKey('Authorization', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhZG1pbiIsImlhdCI6MTY1OTUzMDI5NywiZXhwIjo0MTAyNDQ0ODAwLCJ1aWQiOjk1NDczLCJyb2xlcyI6WyJST0xFX1VTRVIiXX0.kRydYmpnLSvjNuq_A3eXeVKAGlF2HtnX5g5Ew8uJsFw');
$apiClient = new ApiClient($config);
$messageClient = new MessageApi($apiClient);

// Sending a SMS Message
$sendMessageRequest1 = new SendMessageRequest([
    'phoneNumber' => $phone,
    'message' => "You can move to receive your fuel",
    'deviceId' => 128877
]);
$sendMessages = $messageClient->sendMessages([
    $sendMessageRequest1
]);
print_r($sendMessages);

////////////////////////sms//////////////////////////////


          send_mes::create([
            'user_id'=>$user->id,
            'varifty'=>0
          ]);

           $user->updated_at=now();
           $user->save();
        }

    }
   return $count;


   }


    public function UserReciveMessage(){
      $users =send_mes::with('user')->where('varifty',0)->get();
    /* $users =send_mes::with(['user'=>function ($q){
        $q->select('full_name');
    }])->get();*/
        return response()->json($users);
    }




  public function soso(Request $request,$user_id){
     $record=Tank_state::first();
     $record->amount;
      $user=User::find($user_id);
      $rafah=$user->info->amount;
      $owner=$user->full_name;
      $send_mes = send_mes::where('user_id',$user_id)->first();

    //  return $sss=send_mes::value('varifty');
     if($send_mes->varifty == 1){
     $record->amount=$record->amount - $rafah;
     $record->save();
     $price=$rafah*3500;
     $bbb=empoloyee::find(27);
     $worker=$bbb->full_name;
       bill::create([
        'amount'=>$rafah,
        'payment'=>$price,
        'user_id'=>$user->id,
        'employee_id'=>$worker,
       ]);
       send_mes::where('user_id',$user_id)->delete();
     return response()->json(['message'=>'the bill is input']);
     }



  }






 public function amount_of_tank(){
  $amounts=Tank_state::value('amount');
  $percentage=(int)($amounts / 20000 * 100) ;
 return response()->json([$percentage,$amounts]);
 }

   public function add_amount(){
    $record=Tank_state::first();
    $time =Carbon::now()->subDays(3);
    if( $record->start_full < $time){
     if($record->amount < 20000){
        if($record->amount <= 15000 ){
            $record->amount =$record->amount + 5000 ;
            $record->save();
            $record->start_full=now();
           return response()->json('updated amount successfully');
        }
         else if($record->amount > 15000){
            $defrenc=20000 - $record->amount ;
            $record->amount =20000;
            $record->save();
            $record->start_full=now();
            return response()->json(['defernce'=> $defrenc]);
        }
    }else
        return response()->json('amount equal or bigger than 20000');


    }return response()->json('no change');


 }
 public function compo(compoRequest $request){
         complaint::create([
        'complaint'=>$request->complaint,

    ]);
    return response ()->json(['message'=>'The complaint is input']);


 }
 public function get_all_compo(){
   $compo= complaint::get()->all();

   return response()->json($compo);
 }
 public function get_all_bill(){
    $bill=bill::get();
    return response()->json($bill);
 }

 public function acounting(){
   $acos=bill::get();
   $total_amount=0;
   $total_price=0;
    foreach($acos as $bill){
    $total_amount= $total_amount+ $bill->amount;
    $total_price= $total_price+ $bill->payment;

    }
    return response()->json(['message'=>'This is all amount and price',$total_amount,$total_price]);
 }
 


}
