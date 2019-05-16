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

class PersonalInfoController extends Controller
{
   
    public function Index_personalinformation()
	{
			try
			{
				
				if(Session::get('username')=='Admin'){
					return redirect('dashboard')->with('alert-danger',"You are an admin  and you have no personal data");
				}
				
				else{
				   $email = Session::get('useremail');
				   $emp_id = DB::table('emp')->select('id')->where('email',$email)->value('id');


				   $persnal_infos = DB::table('emp')
				        ->where('emp.id',$emp_id)
			            ->join('bank_list', 'emp.bank_name', '=', 'bank_list.id')
			            ->select('emp.first_name','emp.middle_name','emp.last_name','emp.email','emp.mobile','adhaar_number','emp.pan_number','emp.epf_number','emp.esic_number','emp.acc_no','emp.ifsc_code', 'bank_list.bank_name')
			            ->get();

				   $month=date('m');
				   $att = DB::table('daily_report')->where('emp_id',$emp_id)->whereMonth('date',$month)->count();
				   $fam = DB::table('family_detail')->where('id',$emp_id)->get();
				   $sal1 = DB::table('salary')->where('emp_id',$emp_id)->max('id');
				   $sal = DB::table('salary')->where('emp_id',$sal1)->get();
				   Log::info('Display target');
				   return view('persnalinformation',compact('persnal_infos','att_fam','att','fam','sal','emp_id'));
				}

			}
			catch(QueryException $e)
	        {
	        	Log::error($e->getMessage());
	            return redirect()->back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
	        } 
			catch(Exception $e)
		   	{
		   		Log::error($e->getMessage());
				return redirect()->back()->with('alert-danger',$e->getMessage());
		   	}
	   
	}

	public function update_family_details(Request $request)
	{
			try
			{
				
				$member_id = $request->id;
				$father_name = $request->father_name;
				$mother_name = $request->mother_name;
				$spouse_name = $request->spouse_name;
					
				$update= DB::table('family_detail')
                    ->where('id', $member_id)
                    ->update(['father'=>$father_name,
                            'mother'=>$mother_name,
					        'spouse'=>$spouse_name,
                            'updated_at'=>now()]);
				if($update){
					Log::info('Family Details Updated Successfully!');
					return redirect()->back()->with('alert-success', 'Family Details Updated Successfully!');
				}
				else{
					Log::info('Family Details Not Updated!');
					return redirect()->back()->with('alert-danger', 'Family Details Not Updated!');
				}
					
		    }
			catch(QueryException $e)
	        {
	        	Log::error($e->getMessage());
	            return redirect()->back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
	        } 
			catch(Exception $e)
		   	{
		   		Log::error($e->getMessage());
				return redirect()->back()->with('alert-danger',$e->getMessage());
		   	}
		
	}

}