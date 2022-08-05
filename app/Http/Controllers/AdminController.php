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
        $users=User::with('info')->get();
        //$user=user::select('id','full_name','phone_number')->get();
        return response()->json(['message'=>'This all users',$users]);
    }

    public function delete_user($id){
        $user=user::destroy($id);
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
      $empoloyees=empoloyee::select('id','full_name','phone_number');
      return response()->json(['mesaage'=>'Those all empoloyees']);

    }



    #####################################################################################################################
   public function regester_user(RegisterRequest $request){
    try{
    DB::beginTransaction();


    //register  user
   $owner= User::create([
    'full_name'=>$request->full_name,
    'email'=>$request->email,
    'phone_number'=>$request->phone_number,
    'national_number'=>$request->national_number,
    'start_tur'=>$request->start_tur,
    'password'=>bcrypt($request->password),
    ]);


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
  return response()->json(['saved successfully']);

   }catch(\Exception $ex){
    DB::rollBack();
    return response()->json('problem',500);
   }

}

public function ff(){
    $time =Carbon::now()->subDays(7);
   // $users=user::get()->all();
    $users=user::get();
    $count=0;
    //$turns=user::value('updated_at');
    // $users=User::with('info')->where('updated_at','>',$time)->get();
   foreach($users as $user){
       if($user->updated_at < $time){
          $count=$count + 1 ;
          send_mes::create([
            'user_id'=>$user->id,
            'varifty'=>0
          ]);
           // $turns==now();
           $user->updated_at=now();
           $user->save();
        }

    }
  //       $user->info ->amount ;
    //      $user->updated_at =now();
   return $count;
//

   }




    public function UserReciveMessage(){
        // $users =user::with(['send_mes'=>function($q){
        //     $q->where('varifty',0);
        // }])->get();

        $users =send_mes::with('user')->where('varifty',0)->get();
        return response()->json($users);
    }




  public function soso(Request $request,$user_id,$empo_id ){
     $record=Tank_state::first();
     $record->amount;
      $user=User::find($user_id);
      $rafah=$user->info->amount;
      $owner=$user->full_name;
      $send_mes = send_mes::where('user_id',$user_id)->first();

    //  return $sss=send_mes::value('varifty');
     if($send_mes->varfity == 1){
     $record->amount=$record->amount - $rafah;
     $record->save();
     $price=$rafah*3500;
     $bbb=empoloyee::find($empo_id);
     $worker=$bbb->full_name;
       bill::create([
        'amount'=>$rafah,
        'payment'=>$price,
        'user_id'=>$owner,
        'employee_id'=>$worker,
       ]);
     return response()->json(['message'=>'the bill is input']);
     }



  }






 public function amount_of_tank(){
  $amounts=Tank_state::value('amount');
  $percentage=$amounts / 20000.00 * 100 ;
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

   return response()->json(['message'=>'This all complaints ',$compo]);
 }


}
