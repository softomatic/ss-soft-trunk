<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exception;
use \Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

date_default_timezone_set('Asia/Kolkata');

class HolidayController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth', ['only' => 'store']);
    }
	public function get_holiday()
	{ 	
		try
	 	{
	 		if(Session::get('username')!='')
        	{
		 	Log::info('Trying to fetch holidays');
			$holidays_view = DB::table('holidays')->leftjoin('branch','branch.id','=','holidays.branch_id')->select('holidays.id','holidays.branch_id','holidays.event','holidays.title','holidays.date','branch.branch')
								->orderby('id','desc')
								->get();			 
			$branches = DB::table('branch')->get();			 
			return view('holidays',compact('holidays_view','branches'));
			}
			else
	        {
	            return redirect('/')->with('status',"Please login First");
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
   public function create_holiday(Request $request)
	{
		try
		{	
		 	if(Session::get('username')!='')
	        {
	            $branch_id = $request->input('branch');
				$date = $request->input('date');
				$title = $request->input('title');
				$event = $request->input('event');
				
				$holiday=DB::table('holidays')->insert(
				['branch_id' => $branch_id,'date' => $date,'title'=>$title,'event'=>$event,'created_at'=>Carbon::now('Asia/Kolkata')]
				);

				if($holiday)
				{
			     Log::info('Department '.$holiday.' Created Successfully');
				 $request->session()->flash('alert-success', 'Holidays Created Successfully!');
				 return Redirect::to('holidays');
				}
				else
				{
					Log::info('Department '.$holiday.' cannot be created');
					$request->session()->flash('alert-danger', 'Holidays cannot be created!');
					return Redirect::to('holidays');
				}
			}
			else
	        {
	            return redirect('/')->with('status',"Please login First");
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

   public function update_holiday(Request $request)
    {
		try
			{
				if(Session::get('username')!='')
	        	{
					$id=$request->input('id');
					$date = $request->input('date');
					$branch = $request->input('branch');
					$title = $request->input('title');
					$event = $request->input('event');
					$dept=DB::table('holidays')
						  ->where('id', $id)
						  ->update(['branch_id'=>$branch, 'date'=>$date,'title'=>$title,'event'=>$event,'updated_at'=>Carbon::now('Asia/Kolkata')]);
				if($dept)
					{
					Log::info('Holidays with id '.$id.' Updated Successfully');
					$request->session()->flash('alert-success', 'Holidays Updated Successfully!');
					return Redirect::to('holidays');
					}
				else
					{
					Log::info('Holidays with id '.$id.' cannot be updated');
					$request->session()->flash('alert-danger', 'Holidays cannot be updated!');
					return Redirect::to('holidays');
					}
				}
				else
		        {
		            return redirect('/')->with('status',"Please login First");
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

public function delete_holiday(Request $request)
	{
	  try
	    {
	    	if(Session::get('username')!='')
	        {
				$id=$request->input('id');			
				$dept_delete=DB::table("holidays")->delete($id);
				if($dept_delete)
				{
					Log::info('Holidays with id '.$id.' Deleted');
					$request->session()->flash('alert-success', 'Holidays Deleted Successfully!');
					return redirect()->back(); 
				}
				else
				{
					Log::info('Holidays with id '.$id.'  cannot be Deleted');
				    $request->session()->flash('alert-danger', 'Holidays cannot be deleted!');
					return redirect()->back(); 
				}								
				return redirect()->back(); 
			}
			else
	        {
	            return redirect('/')->with('status',"Please login First");
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
