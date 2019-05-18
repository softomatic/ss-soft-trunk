<?php

namespace App\Http\Controllers;

use App\emp;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Exception;
use \Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

ini_set('max_execution_time', 10000);
date_default_timezone_set('Asia/Kolkata');

class AttendanceController extends Controller
{
	public function getweek(Request $request)
	{
		$month = $request->month;
		$maxdays = cal_days_in_month(CAL_GREGORIAN, $month, date('Y'));
		$data = '<option></option>';
		$data.='<option value="1">01-'.$month.'-'.date('Y').' - 07-'.$month.'-'.date('Y').'</option>';
		$data.='<option value="2">08-'.$month.'-'.date('Y').' - 14-'.$month.'-'.date('Y').'</option>';
		$data.='<option value="3">15-'.$month.'-'.date('Y').' - 21-'.$month.'-'.date('Y').'</option>';
		$data.='<option value="4">22-'.$month.'-'.date('Y').' - 28-'.$month.'-'.date('Y').'</option>';
		if($maxdays>28)
		{
			
				$data.='<option value="5">29-'.$month.'-'.date('Y').' - '.$maxdays.'-'.$month.'-'.date('Y').'</option>';
		}
		return $data;
	}
	public function getdate(Request $request)
	{
		$month = $request->month;
		$week = $request->week;
		$maxdays = cal_days_in_month(CAL_GREGORIAN, $month, date('Y'));
		$data = '<option></option>';
		if($week>4)
		{
			for($i=29;$i<=$maxdays;$i++)
			{
				$data.='<option value='.$i.'>'.$i.'-'.$month.'-'.date('Y').'</option>';
			}
		}
		else
		{
			if($week==1)
			{
				$data.='<option value="01">01-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="02">02-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="03">03-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="04">04-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="05">05-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="06">06-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="07">07-'.$month.'-'.date('Y').'</option>';
			}
			if($week==2)
			{
				$data.='<option value="08">08-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="09">09-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="10">10-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="11">11-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="12">12-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="13">13-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="14">14-'.$month.'-'.date('Y').'</option>';
			}
			if($week==3)
			{
				$data.='<option value="15">15-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="16">16-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="17">17-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="18">18-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="19">19-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="21">21-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="22">22-'.$month.'-'.date('Y').'</option>';
			}
			if($week==4)
			{
				$data.='<option value="23">23-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="24">24-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="25">25-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="26">26-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="27">27-'.$month.'-'.date('Y').'</option>';
				$data.='<option value="28">28-'.$month.'-'.date('Y').'</option>';
			}
		}
		
		return $data;
	}

	public function get_edit_attendance(Request $request)
	{
		if(Session::get('username')!='' && (session('role')=='admin' || session('role')=='hr'  || session('role')=='hr admin'))
        {
        	try
        	{
        		if(session('role')=='hr')
        			$branches = DB::table('branch')->where('id',session('branch_location_id'))->get();
        		else
        			$branches = DB::table('branch')->get();
        		$department = DB::table('department')->get();
        		return view('edit_attendance',compact('branches','department'));
        	}
        	catch(QueryException $e)
		   	{
		   		Log::error($e->getMessage());
				return Redirect::to('branch')->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
		   	}
			catch(Exception $e)
		   	{
		   		Log::error($e->getMessage());
				return Redirect::to('branch')->with('alert-danger',$e->getMessage());
		   	}	
        }
        else
        {
        	if(Session::get('username')=='')
                return redirect('/')->with('status',"Please login First");
            else
            return redirect('dashboard')->with('alert-danger',"Only admin , HR and accountant can add Bank");
        }
	}

	public function get_date_attendance(Request $request)
	{
		try
		{
			if(Session::get('username')!='')
			{
				$branch = $request->branch;
				$emp_id = $request->emp_id;
				$date = $request->date;

				$attendance = DB::table('daily_report')->select('initial_in','final_out')->where('date',$date)->where('emp_id',$emp_id)->get();				
				return view('edit_attendance_table',compact('attendance','emp_id','date'));
			}
			else
			{
				return redirect('/')->with('status',"Please login First");
			}

		}
		catch(QueryException $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',"Database Query Error! [ ".$e->getMessage()." ]");
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',$q->getMessage());
        }
	}

	public function update_attendance(Request $request)
	{
		$emp_id = $request->emp_id;
		$date = $request->date;
		$initial_in = $request->in;
		$final_out = $request->out;
		if($final_out=='00:00:00' || $initial_in=='00:00:00')
		{
            $working_hour= '00:00:00';
		}
		else
		{
		    $total_working_hour = strtotime($final_out) - strtotime($initial_in);
    		$seconds = $total_working_hour % 60;
            $minutes = floor(($total_working_hour % 3600) / 60);
            $hours = floor($total_working_hour / 3600);
            $working_hour= $hours.":".$minutes.":".$seconds;
		}
            

		$update = DB::table('daily_report')->where('emp_id',$emp_id)->where('date',$date)->update(['total_working_hour'=>$working_hour,'initial_in'=>$initial_in,'final_out'=>$final_out
		]);
		if($update)
		{
			return redirect()->back()->with('alert-success','Attendance Updated');
		}
		else
		{
			return redirect()->back()->with('alert-danger','Attendance Not Updated');
		}
	}

	public function getattendance(Request $request)
	{
		try
		{
			if(Session::get('username')!='')
			{
				$mydata = array();
				$month = $request->month;
				$week = $request->week;
				$date = $request->date;
				$maxdays = cal_days_in_month(CAL_GREGORIAN, $month, date('Y'));

				if(Session::get('role')=="admin")
	    		{
	    			$isadmin = true;
	    		}
	    		elseif(Session::get('role')=="hr admin" || Session::get('role')=="hr")
	    		{
	    			$isadmin = true;
	    		}
	    		else
	    		{
	    			$emp_id = DB::table('emp')->select('id')->where('email',session('useremail'))->value('id');
	    			$where = array('daily_report.emp_id'=>$emp_id);
	    			$isadmin = false;
	    		}


				if($request->date!='')
				{
					if(!$isadmin)
					{
						$mydata = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$month.'-'.$date))->where($where)->orderBy('daily_report.id','asc')->get();
					}
					else
					{
						if(Session::get('role')=="hr")
							$mydata = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$month.'-'.$date))->where('emp.branch_location_id',session('branch_location_id'))->orderBy('daily_report.id','asc')->get();
						else
							$mydata = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$month.'-'.$date))->orderBy('daily_report.id','asc')->get();
					}
				}
				elseif($request->week!='')
				{
					if($week==5)
					{
						$fromdate = date('Y-'.$month.'-29');
						$todate = date('Y-'.$month.'-'.$maxdays);
					}
					elseif($week==4)
					{
						$fromdate = date('Y-'.$month.'-22');
						$todate = date('Y-'.$month.'-28');
					}
					elseif($week==3)
					{
						$fromdate = date('Y-'.$month.'-15');
						$todate = date('Y-'.$month.'-21');
					}
					elseif($week==2)
					{
						$fromdate = date('Y-'.$month.'-08');
						$todate = date('Y-'.$month.'-14');
					}
					elseif($week==1)
					{
						$fromdate = date('Y-'.$month.'-01');
						$todate = date('Y-'.$month.'-07');
					}

					if(!$isadmin)
					{
						$mydata = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date','>=',$fromdate)->where('daily_report.date','<=',$todate)->where($where)->orderBy('daily_report.id','asc')->get();
					}
					else
					{
						if(Session::get('role')=="hr")
							$mydata = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date','>=',$fromdate)->where('daily_report.date','<=',$todate)->orderBy('daily_report.id','asc')->get();
						else
							$mydata = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date','>=',$fromdate)->where('daily_report.date','<=',$todate)->orderBy('daily_report.id','asc')->get();
					}

				}
				else
				{
					if(!$isadmin)
					{
						$mydata = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date','>=',date('Y-'.$month.'-01'))->where('daily_report.date','<=',date('Y-'.$month.'-'.$maxdays))->where($where)->orderBy('daily_report.id','asc')->get();
					}
					else
					{
						if(Session::get('role')=="hr")
							$mydata = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date','>=',date('Y-'.$month.'-01'))->where('daily_report.date','<=',date('Y-'.$month.'-'.$maxdays))->orderBy('daily_report.id','asc')->get();
						else
							$mydata = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date','>=',date('Y-'.$month.'-01'))->where('daily_report.date','<=',date('Y-'.$month.'-'.$maxdays))->orderBy('daily_report.id','asc')->get();
					}
				}

				return view('report-table',compact('mydata'));
			}
			else
			{
				return redirect('/')->with('status',"Please login First");
			}

		}
		catch(QueryException $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',"Database Query Error! [ ".$e->getMessage()." ]");
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',$q->getMessage());
        }
	}


    public function show(Request $request)
    {	$isadmin = true;
    	if(Session::get('username')!='')
    	{
    		 Log::info('Loading Attendance Report for User with id '.Session::get('user_id').' ');
	    		try
	    		{
	    			if(Session::get('role')=="admin")
		    		{
		    			$isadmin = true;
		    		}
		    		elseif(Session::get('role')=="hr")
		    		{
		    			$isadmin = true;
		    		}
		    		else
		    		{
		    			$emp_id = DB::table('emp')->select('id')->where('email',session('useremail'))->value('id');
		    			$where = array('daily_report.emp_id'=>$emp_id);
		    			$isadmin = false;
		    		}

		    	$weeks=0; $data=array(); $mydata = array(); $myweekdata = array();
		    	$today = date('d');
		    	$month = date('m');
		    	$months = array();
		    	$mymonth = array();
		    	$mon=0;
		    	for($m=($month-1);$m>=1;$m--)
		    	{ 
		    		$maxdays = cal_days_in_month(CAL_GREGORIAN, $m, date('Y'));
		    		$weeks=0;
		    		$counter = 0;
		    		if($maxdays>28)
		    		{ 
		    			$l=0;
						$myweekdata = array();
						for($k=$maxdays;$k>=29;$k--)
						{ 
							if(!$isadmin)
							{
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$m.'-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
							}
							else
							{
								if(Session::get('role')=="hr")
									$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-'.$m.'-'.$k))->orderBy('daily_report.id','asc')->get();
								else
									$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$m.'-'.$k))->orderBy('daily_report.id','asc')->get();
							}
							if($data[$l]=="[]")
			   				{
			   					continue;
			   				}
			   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
			   				$l++;
						}
			    		$week[$counter] = json_encode(array('week'=>$myweekdata));
			    		$counter++;
		    		}
		    		

		    			$l=0;
						$myweekdata = array();
						for($k=28;$k>=22;$k--)
						{
							if(!$isadmin)
								{$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$m.'-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
							}
							else
							{
								if(Session::get('role')=="hr")
									$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-'.$m.'-'.$k))->orderBy('daily_report.id','asc')->get();
								else
									$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$m.'-'.$k))->orderBy('daily_report.id','asc')->get();
							}
							if($data[$l]=="[]")
			   				{
			   					continue;
			   				}
			   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
			   				$l++;
						}
			    		$week[$counter] = json_encode(array('week'=>$myweekdata));
			    		$counter++;

						$l=0;
						$myweekdata = array();
						for($k=21;$k>=15;$k--)
						{
							if(!$isadmin)
							{
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$m.'-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
							}
							else
							{
								if(Session::get('role')=="hr")
								   	$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-'.$m.'-'.$k))->orderBy('daily_report.id','asc')->get();
								else
								   	$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$m.'-'.$k))->orderBy('daily_report.id','asc')->get();
							}
							if($data[$l]=="[]")
			   				{
			   					continue;
			   				}
			   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
			   				$l++;
						}
			    		$week[$counter] = json_encode(array('week'=>$myweekdata));
			    		$counter++;

						$l=0;
						$myweekdata = array();
						for($k=14;$k>=8;$k--)
						{
							if(!$isadmin)
								{$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$m.'-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
							}
							else
							{
								if(Session::get('role')=="hr")
									$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-'.$m.'-'.$k))->orderBy('daily_report.id','asc')->get();
								else
									$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$m.'-'.$k))->orderBy('daily_report.id','asc')->get();
							}
							if($data[$l]=="[]")
			   				{
			   					continue;
			   				}
			   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
			   				$l++;
						}
			    		$week[$counter] = json_encode(array('week'=>$myweekdata));
			    		$counter++;

			    		$l=0;
			    		$myweekdata = array();
						for($k=7;$k>=1;$k--)
						{
							if(!$isadmin)
								{
									$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$m.'-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
								}
								else
								{
									if(Session::get('role')=="hr")
										$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-'.$m.'-'.$k))->orderBy('daily_report.id','asc')->get();
									else
										$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-'.$m.'-'.$k))->orderBy('daily_report.id','asc')->get();
								}
							if($data[$l]=="[]")
			   				{
			   					continue;
			   				}
			   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
			   				$l++;
						}
			    		$week[$counter] = json_encode(array('week'=>$myweekdata));
			    		$counter++;

			    		$mymonth[$mon] = json_encode(array('month'=>$week));
			    		$mon++;
			    		//return $week;
		    	}
		    	$months = json_encode(array('mymonth'=>$mymonth));
		    	$week=array();

		    	if(($today-1)<=7)
		    	{
		    		$weeks = 0;
		    		$i=0;
		    		$j=1;
		    		$new=$today-1;
		    		while($j<=$new)
		    		{ 
		    			if(!$isadmin)
		    				{$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$new))->where($where)->orderBy('daily_report.id','asc')->get();
			    		}
			    		else
			    		{
			    			if(Session::get('role')=="hr")
			    				$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$new))->orderBy('daily_report.id','asc')->get();	
			    			else
		    					$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$new))->orderBy('daily_report.id','asc')->get();	
		    			}

		   				// $data[$i] = DB::table('daily_report')->where('date',date('Y-m-'.$j))->orderBy('id','asc')->get();
		   				if($data[$i]=="[]")
		   				{
		   					$new--;
		   					continue;
		   				}
		   				array_push($mydata, $data[$i]);
		   				$i++;
		   				$new--;
		    		}
		    		// $current_week = DB::table('daily_report')->where('date','<',date('Y-m-d'))->where('date','>=',date('Y-m-1'))->orderBy('date','desc')->get();
		    		$current_week = json_encode(array('mycurrent'=>$mydata));
		    		return view('attendance-report',compact('weeks','current_week','months'));
		    	}
		    	if(($today-1)<=14 && ($today-1)>7)
		    	{
		    		$weeks = 1;
		    		// $current_week = DB::table('daily_report')->where('date','<',date('Y-m-d'))->where('date','>=',date('Y-m-08'))->orderBy('date','desc')->get();
		    		$i=0;
		    		$j=8;
		    		$new=$today-1;
		    		while($j<=$new)
		    		{ 
		    			if(!$isadmin)
		    				{$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$new))->where($where)->orderBy('daily_report.id','asc')->get();
			    		}
			    		else
			    		{
			    			if(Session::get('role')=="hr")
			    				$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$new))->orderBy('daily_report.id','asc')->get();	
			    			else
		    					$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$new))->orderBy('daily_report.id','asc')->get();	
		    			}

		   				// $data[$i] = DB::table('daily_report')->where('date',date('Y-m-'.$j))->orderBy('id','asc')->get();
		   				if($data[$i]=="[]")
		   				{
		   					$new--;
		   					continue;
		   				}
		   				array_push($mydata, $data[$i]);
		   				$i++;
		   				$new--;
		    		}
					$current_week = json_encode(array('mycurrent'=>$mydata));

					$l=0;
					$myweekdata = array();
					for($k=7;$k>=1;$k--)
					{
						if(!$isadmin){
							$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
						}
						else
						{
							if(Session::get('role')=="hr")
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
							else
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
						}
						if($data[$l]=="[]")
		   				{
		   					continue;
		   				}
		   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
		   				$l++;
					}
		    		$week[0] = json_encode(array('week'=>$myweekdata));/*DB::table('daily_report')->where('date','<=',date('Y-m-07'))->orderBy('date','desc')->get();*/
		    		$week_report = json_encode(array('myweeks'=>$week));
		    		return view('attendance-report',compact('weeks','current_week','week_report','months'));
		    	}
		    	if(($today-1)<=21 && ($today-1)>14)
		    	{
		    		$weeks = 2;
		    		// $current_week = DB::table('daily_report')->where('date','<',date('Y-m-d'))->where('date','>=',date('Y-m-15'))->orderBy('date','desc')->get();
		    		$i=0;
		    		$j=15;
		    		$new = $today;
		    		while($j<=$new)
		    		{ 
		    			if(!$isadmin)
		    				{$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$new))->where($where)->orderBy('daily_report.id','asc')->get();
			    		}
			    		else
			    		{
			    			if(Session::get('role')=="hr")
			    				$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$new))->orderBy('daily_report.id','asc')->get();	
			    			else
		    					$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$new))->orderBy('daily_report.id','asc')->get();	
		    			}

		   				// $data[$i] = DB::table('daily_report')->where('date',date('Y-m-'.$j))->orderBy('id','asc')->get();
		   				if($data[$i]=="[]")
		   				{
		   					$new--;
		   					continue;
		   				}
		   				array_push($mydata, $data[$i]);
		   				$i++;
		   				$new--;
		    		}
					$current_week = json_encode(array('mycurrent'=>$mydata));

					$l=0;
					$myweekdata = array();
					for($k=14;$k>=8;$k--)
					{
						if(!$isadmin)
							{$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
							}
							else
							{	
								if(Session::get('role')=="hr")
									$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
								else
									$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
							}
						if($data[$l]=="[]")
		   				{
		   					continue;
		   				}
		   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
		   				$l++;
					}
		    		$week[0] = json_encode(array('week'=>$myweekdata));
		    		$l=0;
		    		$myweekdata = array();
					for($k=7;$k>=1;$k--)
					{
						if(!$isadmin)
						{
						$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
					}
					else
					{
						if(Session::get('role')=="hr")
							$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
						else
							$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
					}
						if($data[$l]=="[]")
		   				{
		   					continue;
		   				}
		   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
		   				$l++;
					}
		    		$week[1] = json_encode(array('week'=>$myweekdata));
		    		// $week[0] = DB::table('daily_report')->where('date','>=',date('Y-m-08'))->where('date','<=',date('Y-m-14'))->orderBy('date','desc')->get();
		    		// $week[1] = DB::table('daily_report')->where('date','>=',date('Y-m-01'))->where('date','<=',date('Y-m-07'))->orderBy('date','desc')->get();
		    		$week_report = json_encode(array('myweeks'=>$week));
		    		return view('attendance-report',compact('weeks','current_week','week_report','months'));
		    	}
		    	if(($today-1)<=28 && ($today-1)>21)
		    	{
		    		$weeks = 3;
		    		// $current_week = DB::table('daily_report')->where('date','<',date('Y-m-d'))->where('date','>=',date('Y-m-22'))->orderBy('date','desc')->get();
		    		$i=0;
		    		$j=22;
		    		$new=$today-1;
		    		while($j<=$new)
		    		{ 
		    			if(!$isadmin)
		    				{$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$new))->where($where)->orderBy('daily_report.id','asc')->get();
		    		}
		    		else
		    		{
		    			if(Session::get('role')=="hr")
		    				$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$new))->orderBy('daily_report.id','asc')->get();	
		    			else
		    				$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$new))->orderBy('daily_report.id','asc')->get();	
		    		}

		   				// $data[$i] = DB::table('daily_report')->where('date',date('Y-m-'.$j))->orderBy('id','asc')->get();
		   				if($data[$i]=="[]")
		   				{
		   					$new--;
		   					continue;
		   				}
		   				array_push($mydata, $data[$i]);
		   				$i++;
		   				$new--;
		    		}

					$current_week = json_encode(array('mycurrent'=>$mydata));

					$l=0;
					$myweekdata = array();
					for($k=21;$k>=15;$k--)
					{
						if(!$isadmin)
						{
							$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
						}
						else
						{
							if(Session::get('role')=="hr")
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
							else
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
						}
						if($data[$l]=="[]")
		   				{
		   					continue;
		   				}
		   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
		   				$l++;
					}
		    		$week[0] = json_encode(array('week'=>$myweekdata));

					$l=0;
					$myweekdata = array();
					for($k=14;$k>=8;$k--)
					{
						if(!$isadmin)
							{$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
					}
					else
					{
						if(Session::get('role')=="hr")
							$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
						else
							$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
					}
						if($data[$l]=="[]")
		   				{
		   					continue;
		   				}
		   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
		   				$l++;
					}
		    		$week[1] = json_encode(array('week'=>$myweekdata));
		    		$l=0;
		    		$myweekdata = array();
					for($k=7;$k>=1;$k--)
					{
						if(!$isadmin)
							{$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
					}
					else
					{
						if(Session::get('role')=="hr")
							$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
						else
							$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
					}
						if($data[$l]=="[]")
		   				{
		   					continue;
		   				}
		   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
		   				$l++;
					}
		    		$week[2] = json_encode(array('week'=>$myweekdata));

		    		$week_report = json_encode(array('myweeks'=>$week));

		    		// $week[0] = DB::table('daily_report')->where('date','>=',date('Y-m-15'))->where('date','<=',date('Y-m-21'))->orderBy('date','desc')->get();
		    		// $week[1] = DB::table('daily_report')->where('date','>=',date('Y-m-08'))->where('date','<=',date('Y-m-14'))->orderBy('date','desc')->get();
		    		// $week[2] = DB::table('daily_report')->where('date','>=',date('Y-m-01'))->where('date','<=',date('Y-m-07'))->orderBy('date','desc')->get();
		    		// $week_report = json_encode(array('myweeks'=>$week));
		    		return view('attendance-report',compact('weeks','current_week','week_report','months'));
		    	}
		    	if(($today-1)>28)
		    	{
		    		$weeks = 4;
		    		// $current_week = DB::table('daily_report')->where('date','<',date('Y-m-d'))->where('date','>',date('Y-m-29'))->orderBy('date','desc')->get();
		    		$i=0;
		    		$j=29;
		    		$new=$today-1;
		    		while($j<=$new)
		    		{ 
		    			if(!$isadmin)
		    			{
		    				$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$new))->where($where)->orderBy('daily_report.id','asc')->get();
			    		}
			    		else
			    		{
			    			if(Session::get('role')=="hr")
		    					$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$new))->orderBy('daily_report.id','asc')->get();
		    				else
		    					$data[$i] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$new))->orderBy('daily_report.id','asc')->get();	
		    			}

		   				// $data[$i] = DB::table('daily_report')->where('date',date('Y-m-'.$j))->orderBy('id','asc')->get();
		   				if($data[$i]=="[]")
		   				{
		   					$new--;
		   					continue;
		   				}
		   				array_push($mydata, $data[$i]);
		   				$i++;
		   				$new--;
		    		}

					$current_week = json_encode(array('mycurrent'=>$mydata));

					$l=0;
					$myweekdata = array();
					for($k=28;$k>=22;$k--)
					{
						if(!$isadmin)
						{
							$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
						}
						else
						{
							if(Session::get('role')=="hr")
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
							else
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
						}
						if($data[$l]=="[]")
		   				{
		   					continue;
		   				}
		   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
		   				$l++;
					}
		    		$week[0] = json_encode(array('week'=>$myweekdata));

					$l=0;
					$myweekdata = array();
					for($k=21;$k>=15;$k--)
					{
						if(!$isadmin)
							{$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
						}
						else
						{
							if(Session::get('role')=="hr")
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
							else
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
						}
						if($data[$l]=="[]")
		   				{
		   					continue;
		   				}
		   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
		   				$l++;
					}
		    		$week[1] = json_encode(array('week'=>$myweekdata));

					$l=0;
					$myweekdata = array();
					for($k=14;$k>=8;$k--)
					{
						if(!$isadmin)
							{$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
						}
						else
						{
							if(Session::get('role')=="hr")
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
							else
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
						}
						if($data[$l]=="[]")
		   				{
		   					continue;
		   				}
		   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
		   				$l++;
					}
		    		$week[2] = json_encode(array('week'=>$myweekdata));
		    		$l=0;
		    		$myweekdata = array();
					for($k=7;$k>=1;$k--)
					{
						if(!$isadmin)
							{
								$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->where($where)->orderBy('daily_report.id','asc')->get();
							}
							else
							{
								if(Session::get('role')=="hr")
									$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('emp.branch_location_id',session('branch_location_id'))->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
								else
									$data[$l] = emp::join('daily_report','daily_report.emp_id','=','emp.id')->select('daily_report.id', 'daily_report.user_id', 'daily_report.emp_id','daily_report.date','daily_report.attendance','daily_report.initial_in','daily_report.final_out','daily_report.total_working_hour','emp.title','emp.first_name', 'emp.middle_name', 'emp.last_name', 'emp.email','emp.genesis_id','emp.biometric_id')->where('daily_report.date',date('Y-m-'.$k))->orderBy('daily_report.id','asc')->get();
							}
						if($data[$l]=="[]")
		   				{
		   					continue;
		   				}
		   				array_push($myweekdata, json_encode(array('mydate'=>$data[$l])));
		   				$l++;
					}
		    		$week[3] = json_encode(array('week'=>$myweekdata));

		    		// $week[0] = DB::table('daily_report')->where('date','>=',date('Y-m-022'))->where('date','<=',date('Y-m-29'))->orderBy('date','desc')->get();
		    		// $week[1] = DB::table('daily_report')->where('date','>=',date('Y-m-15'))->where('date','<=',date('Y-m-21'))->orderBy('date','desc')->get();
		    		// $week[2] = DB::table('daily_report')->where('date','>=',date('Y-m-08'))->where('date','<=',date('Y-m-14'))->orderBy('date','desc')->get();
		    		// $week[3] = DB::table('daily_report')->where('date','>=',date('Y-m-01'))->where('date','<=',date('Y-m-07'))->orderBy('date','desc')->get();
		    		$week_report = json_encode(array('myweeks'=>$week));
		    		
		    		return view('attendance-report',compact('weeks','current_week','week_report','months'));
		    	}
		    }
	        catch(QueryException $e)
	        {
	            Log::error($e->getMessage());
	            return redirect()->back()->with('alert-danger',"Database Query Error! [ ".$e->getMessage()." ]");
	        }
	        catch(Exception $e)
	        {
	            Log::error($e->getMessage());
	            return redirect()->back()->with('alert-danger',$q->getMessage());
	        }
	    }
	    else
	    	return redirect('/')->with('status',"Please login First");
    }

    public function add_mobile_attendance(Request $request)
    {
    	$upload_dir = 'marketing_attendance\\';
    	$latitude = $request->input('latitude');
    	$longitude = $request->input('longitude');
		$img = $request->input('image_name');
		
    	$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		$file = session('user_id').'_'.strtotime(date('Y-m-d H:i:s')).'_'. ".png";
		if(file_put_contents(base_path().'\\marketing_attendance\\'.$file, $data))
		{
		  $insert = DB::table('marketing_attendance')->insert([
						'useremail' => Session::get('useremail'),
						'photo' => $upload_dir.$file,
						'longitude' => $longitude,
						'latitude' => $latitude
					]);
		  	if($insert)
				return redirect('mobile-dashboard')->with('alert-success',"Attendance Uploaded Successfully");
			else
				return redirect('mobile_attendance')->with('alert-danger',"Attendance Upload Process Failed");
		}
		else
			return redirect('mobile_attendance')->with('alert-danger',"Attendance Upload Process Failed");
    }

    public function monthly_report(Request $request)
    {
    	try
		{
			if(Session::get('username')!='')
			{ 
				if(session('role')=='hr')
				{
					$emp_name = emp::select('title','first_name','middle_name','last_name','id')->where('branch_location_id',session('branch_location_id'))->where('status','active')->get();
					$branch = DB::table('branch')->where('id',session('branch_location_id'))->get();
				}
				else
				{
					$branch = DB::table('branch')->get();
					$emp_name = emp::select('title','first_name','middle_name','last_name','id')->where('status','active')->get();
				}
				return view('attendance_monthly_report',compact('emp_name','branch'));
			}
			else
			{
				return redirect('/')->with('status',"Please login First");
			}

		}
		catch(QueryException $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',"Database Query Error! [ ".$e->getMessage()." ]");
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',$q->getMessage());
        } 	
    }
    public function get_monthly_attendance(Request $request)
    {
    	try
		{
			$month = $request->month;
			$emp_id = $request->emp_id;
			$branch = $request->branch;
			$mymonth = date('M',strtotime(date('Y-'.$month.'-d')));
          	$year = date('Y');
          	$first_sunday = date('d', strtotime('First Sunday Of '.$mymonth.' '.$year));
          	$number_of_sundays=1;
          	$total_days=cal_days_in_month(CAL_GREGORIAN, $month, date('Y'));
          	$days = $total_days-$first_sunday;
          	while(true)
          	{
            	$days=$days-7;
            	if($days>=0)
            		$number_of_sundays++;
         		else
            		break;
          	}
          	$holidays = DB::table('holidays')->whereMonth('date',$month)->count();
          	$holidays = $number_of_sundays+$holidays;
          	$total_working_days=$total_days-$holidays;
          	
          	$i=0; $j=0;
			if($emp_id=='')
			{	
				if($branch=='')
					$emp = DB::table('emp')->where('status','active')->get();
				else
					$emp = DB::table('emp')->where('branch_location_id',$branch)->where('status','active')->get();
				for($i=0;$i<sizeof($emp);$i++)
				{
					$min = 0;
		          	$present =0;
		           	$full_day = 0;
		          	$half_day = 0;
		          	$overtime_day = 0;
		          	$overtime_days=0;
		          	$overtime_hours=0;
		          	$overtime = 0;
		          	$overmin=0;
  					$overtime_mins=0;
  					$full_day=$half_day=$over_time_day=$over_time_days=$overtime_day=$overtime_half_day=0;


					$attendance_array = DB::table('daily_report')->wheremonth('date',$month)->where('emp_id',$emp[$i]->id)->where('total_working_hour','!=',date('H:i:s',strtotime('00.00.00')))->get();

					for($j=0;$j<sizeof($attendance_array);$j++)
					{

						$shift = DB::table('shift_master')->select('time_in','time_out')->where('id',$attendance_array[$j]->shift)->get();
		                $time_in = explode(':',$shift[0]->time_in);
		                $time_out = explode(':',$shift[0]->time_out);
		                
		                $working_hour = ($time_out[0]*3600+$time_out[1]*60)-($time_in[0]*3600+$time_in[1]*60);
		                
		                $full_day_to = $working_hour;
		                $full_day_from = $working_hour-1800;
		                $half_day_to = $working_hour/2;
		                $half_day_from = ($working_hour/2)-1800;


						if($attendance_array[$j]->overtime_hours!='' && $attendance_array[$j]->status=='Approved')
			        	{
			        		$over = explode(':',$attendance_array[$j]->overtime_hours);
			        		$overtime+=$over[0];
			        		$overtime_mins+=$over[1];
			        	}

						$exp = explode(':',$attendance_array[$j]->total_working_hour);
						$attendance = $exp[0]*3600;
						$attendance+= $exp[1]*60;
						// $attendance = strtotime($attendance_array[$j]->total_working_hour);
						if($full_day==$total_working_days)
						{
						    
						    if($attendance>=$full_day_from)
						 	{
						 		$overtime_day++;
						 	}
						 	elseif($attendance<$full_day_from && $attendance>=$half_day_from)
		                    {
		                      	$overtime_half_day++;
		                    }
						 	
						}
						elseif($attendance>=$full_day_from)
						{
							  
						  	$full_day++;
						}
						elseif($attendance<$full_day_from && $attendance>=$half_day_from)
		                {
		                  	$half_day++;
		                }

	              	}
	              	if($overtime_mins>60)
          			{
		                $min = $overtime_mins%60;
		                $overtime+=($overtime_mins-$min)/60;
		                if($min>=45)
	                  	{
		                    $overtime++;
		                    $overtime_mins = 0;
	                  	}
	                  	else
	                  	{
		                    if($min>=15)
		                        $overtime_mins=30;
		                    else
		                        $overtime_mins=0;
	                  	}
          			}

		              // if($overmin>=60)
		              // {
		              //   $min = $overmin%3600;
		              //   $overtime+=($overmin-$min)/3600;
		              //   $overmin = $min/60;
		              // }
		              // $overtime_hours = $overtime%8;
		              // $overtime_day+= ($overtime-$overtime_hours)/8;

          			$overtime_hours = $overtime;
        			$overtime_days = $overtime_day + ($overtime_half_day/2);


		              $present = $full_day+$half_day+$overtime_days;
		              $report[$i] = array('name'=>$emp[$i]->first_name.' '.$emp[$i]->middle_name.' '.$emp[$i]->last_name,'biometric_id'=>$emp[$i]->biometric_id,'present'=>$present,'overtime'=>$overtime_days,'overtime_hours'=>$overtime_hours,'overmin'=>$overtime_mins,'full_days'=>$full_day,'half_days'=>$half_day);
				}
				return view('monthly_report_table',compact('report','emp_id'));
			}
			else
			{
				$emp = DB::table('emp')->where('id',$emp_id)->where('status','active')->get();
				$report = DB::table('daily_report')->whereMonth('date',$month)->where('emp_id',$emp[$i]->id)->get();
				return view('monthly_report_table',compact('report','emp_id'));
			}

		}
		catch(QueryException $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',"Database Query Error! [ ".$e->getMessage()." ]");
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',$q->getMessage());
        } 
    }

}
