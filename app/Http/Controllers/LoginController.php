<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Cache;
use App\Http\Controllers\Cookie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

use App\Exception;
use \Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

date_default_timezone_set('Asia/Kolkata');

/*use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;*/

class LoginController extends Controller
{
  
    
    public function __construct()
    {
        $this->middleware('auth', ['only' => 'store']);
    }

 public function login_api(Request $request){ 
     // return response()->json(array('status'=>'success'));
     // return response()->json([
//'status' => 'success']);
    if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
      $user = Auth::user(); 
      $email=$user['email'];
      $desig = DB::table('emp')->where('email',$email)->value('designation'); 
      $branch_location_id = DB::table('emp')->where('email',$email)->value('branch_location_id'); 
      $designation = DB::table('designation')->where('id',$desig)->value('designation');
       $emp_id = DB::table('emp')->select('id')->where('email',$email)->value('id');


				   $persnal_infos = DB::table('emp')
				        ->where('emp.id',$emp_id)
			            ->join('bank_list', 'emp.bank_name', '=', 'bank_list.id')
			            ->select('emp.first_name','emp.middle_name','emp.last_name','emp.mobile','emp.acc_no','emp.epf_number','emp.esic_number','emp.uan_number','emp.genesis_ledger_id','emp.ifsc_code', 'bank_list.bank_name')
			            ->get();
                    
                    $salary=DB::table('payslip_master')
                    ->join('payslip_latest','payslip_latest.master_id','payslip_master.id')
                    ->select('payslip_latest.month','payslip_master.net_payable')
                    ->where('payslip_latest.emp_id',$emp_id)
                    ->orderBy('payslip_latest.month', 'DESC')
                    ->get();
				   $fam = DB::table('family_detail')->where('emp_id',$emp_id)->get();
				   $doc = DB::table('emp_doc')->select('document_name','path')->where('emp_id',$emp_id)->get();
				   $sal1 = DB::table('salary')->where('emp_id',$emp_id)->max('id');
				   $sal = DB::table('salary')->where('id',$sal1)->get();
				   Log::info('Send data to mobile app');
       return response()->json([
         'status' => 'success',
          'username' => $user->name,
           'email' => $user->email,
         'designation' => $designation,
         'salary'=>$salary,
           'data'=>['fam' =>$fam,
          'doc'=>$doc,
          'sal' => $sal,
          'persnal_infos'=> $persnal_infos]
          
       ]); 

    } else { 
       return response()->json([
         'status' => 'error',
         'msg' => 'Invalid Username or Password'
       ]); 
    } 
  }
    public function showlogin(Request $request){

        if(Session::has('username'))
           {
           return Redirect::to('/dashboard');}
        else
            return view('login');
    }
    public function show_emp_login(Request $request){

        if(Session::has('username'))
           return Redirect::to('/mobile-dashboard');
        else
            return view('emp-login');
    }

    public function login(Request $request){

        $rules = array(
        'username'    => 'required|email', // make sure the email is an actual email
        'password' => 'required|min:3' // password can only be alphanumeric and has to be greater than 3 characters
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('/')
            ->withErrors($validator) // send back all errors to the login form
            ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
        } 
        else {
            // create our user data for the authentication
            // $userdata = array(
            // 'email'     => Input::get('username'),
            // 'password'  => Input::get('password')
            // );

            if(Auth::attempt(['email' => $request->username, 'password' => $request->password]))
            { 
                $user = Auth::user(); 
             // if (Auth::attempt($userdata)) {
                $username = $user->name;
                $email = $user->email;
                
                try
                {
                    $role = DB::table('role')->where('role_id',$user->role_id)->value('role');  
                    $status = DB::table('emp')->where('email',$email)->value('status') ;
                    if($role!='admin' && $status!='active')
                    {
                         return redirect()->back()->with('status','User not active.');
                    }    
                    $desig = DB::table('emp')->where('email',$email)->value('designation'); 
                    $branch_location_id = DB::table('emp')->where('email',$email)->value('branch_location_id'); 
                    $designation = DB::table('designation')->where('id',$desig)->value('designation');
                    $location_id = DB::table('emp')->where('email',$email)->value('branch_location_id');
                    // $role = $user->role;
                    Session::put('username',$username);
                    Session::put('useremail',$email);
                    Session::put('role',$role);
                    Session::put('designation',$designation);
                    Session::put('user_id',$user->id);
                     Session::put('role_id',$user->role_id);
                    Session::put('location',$location_id);
                    if($role=='admin' || $role=='hr admin')
                    {
                        $notification_list = DB::table('notification')->where('status','active')->where('notification_status','!=','Accepted')->Where('notification_status','!=','Rejected')->get();

                        Session::put('notification',sizeof($notification_list));
                        
                        Session::put('notification_list',$notification_list);
                    }
                    else
                    {
                       $notification_list = DB::table('notification')->where('status','active')->where('requester_id',Session::get('user_id'))->where('user_id','!=',Session::get('user_id'))->get();

                        Session::put('notification',sizeof($notification_list));
                        Session::put('notification_list',$notification_list);
                        Session::put('branch_location_id',$branch_location_id);
                    }

                    Log::info('user logged in');
                    if($role=="purchase admin")
                    {
                        return Redirect::to('purchase_admin_dashboard');
                    }
                    else
                    {
                    return Redirect::to('dashboard');
                    }
                  //  return Redirect::to('dashboard');
                }
                catch(QueryException $e)
                {
                    // Log::error($e->getMessage());
                    return redirect()->back()->with('failure',"Database Query Error! [".$e->getMessage()." ]");
                }
                catch(Exception $e)
                {
                    // Log::error($e->getMessage());
                    return redirect()->back()->with('alert-danger',$e->getMessage());
                }
            }
            else
            {        
                return redirect()->back()->with('status','Invalid Username or Password.');
            }

        }
    }
    public function emp_login(Request $request){ 

        $rules = array(
        'username'    => 'required|email',
        'password' => 'required|min:3' 
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::to('employee-login')
            ->withErrors($validator) 
            ->withInput(Input::except('password')); 
        } 
        else {
           
            if(Auth::attempt(['email' => $request->username, 'password' => $request->password])){ 
                $user = Auth::user(); 
                $username = $user->name;
                $email = $user->email;

                try
                {
                    $role = DB::table('role')->where('role_id',$user->role_id)->value('role');
                    $status = DB::table('emp')->where('email',$email)->value('status') ;
                    if($role!='admin' && $status!='active')
                    {
                         return redirect()->back()->with('status','User not active.');
                    }
                    $desig = DB::table('emp')->where('email',$email)->value('designation'); 
                    $designation = DB::table('designation')->where('id',$desig)->value('designation');          
                    Session::put('username',$username);
                    Session::put('useremail',$email);
                    Session::put('role','employee');
                    Session::put('user_id',$user->id);
                    Session::put('designation',$designation);
                    Log::info('user logged in');
                   if($role=='purchase admin')
                    {                   
                        return Redirect::to('purchase_admin_dashboard');  
                    }
                    else
                    {
                    return Redirect::to('mobile-dashboard');
                    }
                }
                catch(QueryException $e)
                {
                    return redirect()->back()->with('failure',"Database Query Error! [".$e->getMessage()." ]");
                }
                catch(Exception $e)
                {
                    return redirect()->back()->with('alert-danger',$e->getMessage());
                }
                
            }
            else {        
                return redirect()->back()->with('status','Invalid Username or Password.');
            }

        }
    }
    public function logout(Request $request)
    {
        $role = session('role');
        Log::info('user logged out');
        Auth::logout();
        \Cache::flush();
        Session::forget('username');
        Session::forget('email');
        Session::forget('password');
        Session::flush();
        if($role=='employee')
            return redirect('employee-login')->withCookie(\Cookie::forget('laravel_token'))->with('action','Successfully Logout');
        else
            return redirect('/')->withCookie(\Cookie::forget('laravel_token'))->with('action','Successfully Logout');
    }
}

?>
