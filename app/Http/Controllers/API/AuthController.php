<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use App\User; 
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller 
{
  /** 
   * Login API 
   * 
   * @return \Illuminate\Http\Response 
   * 
   * 
   */ 
   
   // public function testing(Request $request){
        
  //      return response()->json(array('status'=>'success'));
   // }

  public function login(Request $request){ 
     // return response()->json(array('status'=>'success'));
     // return response()->json([
//'status' => 'success']);
    if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
      $user = Auth::user(); 
   
       return response()->json([
         'status' => 'success',
         'data' => "user"
       ]); 

    //   Session::put('username',$user->name);
    //   Session::put('useremail',$user->email);
    //   return redirect('dashboard');
    } else { 
       return response()->json([
         'status' => 'error',
         'data' => 'Unauthorized Access'
       ]); 
      return redirect('/')->with('failure','Invalid Username or Password');
    } 
  }
    
  /** 
   * Register API 
   * 
   * @return \Illuminate\Http\Response 
   */ 
  public function register(Request $request) 
  { 
    $validator = Validator::make($request->all(), [ 
      'name' => 'required', 
      'email' => 'required|email', 
      'password' => 'required', 
      'c_password' => 'required|same:password', 
    ]);
    if ($validator->fails()) { 
      return response()->json(['error'=>$validator->errors()]);
    }
    $postArray = $request->all(); 
    $postArray['password'] = bcrypt($postArray['password']); 
    $user = User::create($postArray); 
    $success['token'] =  $user->createToken('LaraPassport')->accessToken; 
    $success['name'] =  $user->name;
    return response()->json([
      'status' => 'success',
      'data' => $success,
    ]); 
  }
    
  /** 
   * details api 
   * 
   * @return \Illuminate\Http\Response 
   */ 
  public function getDetails() 
  { 
     
header ('Access-Control-Allow-Origin:*');
header ('Content-Type:application/json');
 return array("hello");
$data=array(
    'get'=>$_GET,
    'post'=>$_POST
    );
    return json_encode($data); 
  } 

  public function logout()
  {
    Auth::logout();
    Cache::flush();
    Session::forget('username');
    Session::forget('email');
    Session::forget('password');
    Session::flush();
    return redirect('/');
  }
}