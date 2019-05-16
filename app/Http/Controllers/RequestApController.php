<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\File;
use App\Exception;
use \Illuminate\Database\QueryException;
date_default_timezone_set('Asia/Kolkata');
class RequestApController extends Controller
{
   public function get_index()
   {
       $row12=array();
       $final=array();
       $ot=array();
       $email=Session::get('useremail');
       $designation=DB::table('emp')->where('email',$email)->value('designation');
       $role_id= Session::get('role_id');
       $location=Session::get('location');
       $raised_by=DB::table('emp')->where('email',$email)->value('id');
       $raised_request=DB::table('raised_request')->join('request_master','raised_request.request_type_id','=','request_master.request_id')->join('emp','raised_request.raised_for_emp','=','emp.id')
       ->select('raised_request.*','request_master.request_type','emp.first_name','emp.middle_name','emp.last_name')->where('raised_request.raised_by',$raised_by)->get();
       
        $raised_request1=DB::table('raised_request')->join('emp','raised_request.raised_for_emp','=','emp.id')->join('request_master','raised_request.request_type_id','=','request_master.request_id')->select('raised_request.*','emp.first_name','emp.middle_name','emp.last_name','request_master.request_type','request_master.request_id')
        ->where('emp.branch_location_id',$location)->get();
        foreach($raised_request1 as $request)
        {
            
            $reqest_master= DB::table('request_master')->join('emp','request_master.reporting_level_one','=','emp.designation')->where('emp.designation',$designation)->where('emp.branch_location_id',$location)->where('request_master.request_id',$request->request_type_id)->select('request_master.*')->get();
            foreach($reqest_master as $row)
            {
               
               if($request->request_id == $row->request_id)
               {    
                   $final[]=['id'=>$request->id,'request_type_id'=>$request->request_type_id,'raised_by'=>$request->raised_by,'raised_for_emp'=>$request->raised_for_emp,'request_details'=>$request->request_details,'approved_by_level_one'=>$request->approved_by_level_one,'approved_by_level_two'=>$request->approved_by_level_two,'rejected_by'=>$request->rejected_by,'	reject_reason'=>$request->reject_reason,'status'=>$request->status,'created_at'=>$request->created_at,'updated_at'=>$request->updated_at,'request_type'=>$request->request_type,'middle_name'=>$request->middle_name,'last_name'=>$request->last_name,'first_name'=>$request->first_name];
                    
               }
               
            }
            // $reqest_master1= DB::table('request_master')->join('emp','request_master.reporting_level_two','=','emp.designation')->where('emp.designation',$designation)->where('emp.branch_location_id',$location)->where('request_master.request_id',$request->request_type_id)->select('request_master.*')->get();
            // foreach($reqest_master1 as $row1)
            // {
               
            //   if($request->request_id == $row1->request_id)
            //   {    
            //         $final[]=['id'=>$request->id,'request_type_id'=>$request->request_type_id,'raised_by'=>$request->raised_by,'raised_for_emp'=>$request->raised_for_emp,'request_details'=>$request->request_details,'approved_by_level_one'=>$request->approved_by_level_one,'approved_by_level_two'=>$request->approved_by_level_two,'rejected_by'=>$request->rejected_by,'	reject_reason'=>$request->reject_reason,'status'=>$request->status,'created_at'=>$request->created_at,'updated_at'=>$request->updated_at,'request_type'=>$request->request_type,'middle_name'=>$request->middle_name,'last_name'=>$request->last_name,'first_name'=>$request->first_name];
                    
            //   }
               
            // }
            
        }
        // return $final;
        $final=json_decode(json_encode($final));
       if($role_id==1){
        $emp=DB::table('emp')->get(['id','first_name','middle_name','last_name']);
       }
       else{
           $emp=DB::table('emp')->where('branch_location_id', $location)->get(['id','first_name','middle_name','last_name']);
       }
       
        
        $over_time_request= DB::table('daily_report')->join('shift_master','daily_report.shift','=','shift_master.id')
        ->join('emp','daily_report.emp_id','=','emp.id')->where('emp.branch_location_id',$location)->where('emp.fixed_salary',0)->where('daily_report.overtime_hours','=',NULL)
        ->select('daily_report.*','shift_master.id as master_id','shift_master.time_in','shift_master.time_out',
        'emp.first_name','emp.middle_name','emp.last_name')->get();
      
        
        
        $over_time_request_final=array();
        foreach($over_time_request as $ot_request){
          
          if($ot_request->master_id==$ot_request->shift)
          
            {
                
            $in_overtime=0;
            $out_overtime=0;
            $total_hour = explode(':',$ot_request->total_working_hour);
		    $total_hour_final = (int)$total_hour[0];
		   
             if($total_hour_final > 7  )
             { 
                    
                    $intime= strtotime("-60 minutes", strtotime($ot_request->time_in));
                    $in_margin_time=date('H:i', $intime);
                    $outtime= strtotime("+30 minutes", strtotime($ot_request->time_out));
                    $out_margin_time= date('H:i', $outtime);
                    
                    
                   if ((strtotime($in_margin_time) - strtotime($ot_request->initial_in ) ) > 0)
                    {
                    // $in_overtime=strtotime("+60 minutes", strtotime($ot_request->time_in));  
                    $in_overtime=(strtotime($ot_request->time_in) - strtotime($ot_request->initial_in ));
                    // $ot[]=  gmdate('H:i:s', $in_overtime).' '.$ot_request->emp_id.' '.$ot_request->date;
                    }
                    
                    if ((strtotime($ot_request->final_out) - strtotime($out_margin_time)) > 0)
                    {
                        
                     $out_overtime=(strtotime( $ot_request->final_out) - strtotime($out_margin_time ));
                    //  $ot[]=  gmdate('H:i', $in_overtime).' '.$ot_request->emp_id.' '.$ot_request->date;
                     
                    }
                    if($in_overtime+$out_overtime > 0){
                          $final_ot=  gmdate('H:i',$in_overtime+$out_overtime);
                          $wh = explode(':',$final_ot);
		                  $hour = $wh[0];
		                  $min = $wh[1];
		                  if( $min>30 && $min< 45)
		                  {
		                      $min=30;
		                  }
		                  
		                   elseif( $min>0 && $min< 15)
		                  {
		                      $min="00";
		                  }
		                  elseif( $min>15 && $min< 30)
		                  {
		                      $min=30;
		                  }
		                  elseif( $min>45 && $min< 60)
		                  {
		                      $hour= $hour+1;
		                      $min="00";
		                  }
		                  elseif( $min < 15 )
		                  {
		                      $min="00";
		                  }
		                  if( $hour !=0 || $min>= 30){
		                     $Final_overtime= $hour.':'.$min;
		                    
		                    $over_time_request_final[]=['emp_id'=>$ot_request->emp_id,'date'=>$ot_request->date,'shift'=>$ot_request->shift,'first_name'=>$ot_request->first_name,
		                   'middle_name'=>$ot_request->middle_name,'last_name'=>$ot_request->last_name,'calculated_overtime'=>$Final_overtime] ; 
		                  }
                   }
                }
            }
        }
       
        $in_out_miss= DB::table('daily_report')->join('emp','daily_report.emp_id','=','emp.id')->where('emp.fixed_salary','!=',1)->where('emp.branch_location_id',$location)->where('daily_report.updated_at','=',NULL)
        ->select('daily_report.*','emp.first_name','emp.middle_name','emp.last_name')->get();
        
        $miss_punch_final=array();
        foreach($in_out_miss as $mis_punch)
        {  
            if(  $mis_punch->initial_in=='00:00:00' || $mis_punch->final_out =='00:00:00' )
            {  
             
               $miss_punch_final[]= ['emp_id'=>$mis_punch->emp_id,'initial_in'=>$mis_punch->initial_in,'final_out'=>$mis_punch->final_out,'date'=>$mis_punch->date,'first_name'=>$mis_punch->first_name,
               'middle_name'=>$mis_punch->middle_name,'last_name'=>$mis_punch->last_name,];
            
              
            }
        }
       $over_time_request_final=array_slice($over_time_request_final,0,5000);
       $miss_punch_final=array_slice($miss_punch_final,0,5000);
       $miss_punch_final=json_decode(json_encode($miss_punch_final));
    
       $over_time_request_final= json_decode(json_encode($over_time_request_final));
       $request=DB::table('request_master')->get();
       return view('request_approvel',compact('request','emp','raised_request','final','over_time_request_final','miss_punch_final'));
   }

   public function save_request(Request $request)
   {
       $raised_details=array();
       $emp_mail=Session::get('useremail');
       $raised_by=DB::table('emp')->where('email',$emp_mail)->value('id');
       $request_id=$request->request_type;
       $emp_id=$request->emp_id;
       $to_date=$request->to_date;
       $from_date=$request->from_date;     
       $grace_period=$request->grace_period;
       $reason=$request->reason;
       $raised_details=['from_date'=>$from_date,'to_date'=>$to_date,'grace_period'=>$grace_period,'reason'=>$reason];
       $raised_details=json_encode($raised_details);
       if(blank($to_date) && blank($from_date) && blank($grace_period) && blank($grace_period) && blank($reason) )
       {
           return redirect('request_approvel')->with('alert-danger','Please fill all details to create ');
       }

       $insert=DB::table('raised_request')->insert(['request_type_id'=>$request_id,'raised_by'=>$raised_by,'raised_for_emp'=>$emp_id,'request_details'=>$raised_details , 'status'=>'Pending','created_at'=>Carbon::now()]);
        if($insert)
           {
                return redirect('request_approvel')->with('alert-success','Request Created successfully');
           }
           else{
                return redirect('request_approvel')->with('alert-danger','Request  Not Created ');
           }

   }

   public function reject_request(Request $request)
   {
        $email=Session::get('useremail');
        $raised_by=DB::table('emp')->where('email',$email)->value('id');
        $request_id=$request->request_id;
        $reject_reason=$request->reject_reason;
        $update=DB::table('raised_request')->where('id',$request_id)->update(['rejected_by'=>$raised_by,'reject_reason'=>$reject_reason,'status'=>'Rejected','updated_at'=>Carbon::now()]);
        if($update){
              $request_type_id=DB::table('raised_request')->where('id',$request_id)->value('request_type_id');
              if($request_type_id==4){
                $daily_report_update=DB::table('daily_report')->where('request_id',$request_id)->update(['status'=>'Rejected','updated_at'=>Carbon::now()]);
              }
             return redirect('request_approvel')->with('alert-danger','Request  rejected successfully ');
        }

   }
   public function approve_request(Request $request)
   {
       
        $email=Session::get('useremail');
        $raised_by=DB::table('emp')->where('email',$email)->value('id');
         $request_id=$request->aid;
        $designation=DB::table('emp')->where('id',$raised_by)->value('designation');
        $request_type_id= $request->request_type;
        $check=DB::table('request_master')->where('request_id',$request_type_id)->value('reporting_level_one');
        
        if($designation==$check){
           
            $update=DB::table('raised_request')->where('id',$request_id)->update(['approved_by_level_one'=>$raised_by,'status'=>'Approved','updated_at'=>Carbon::now()]);
        }
        else{ 
            
            $update=DB::table('raised_request')->where('id',$request_id)->update(['approved_by_level_two'=>$raised_by,'status'=>'Approved','updated_at'=>Carbon::now()]);    
        }
        
        if($update){
            $update_daily_report= DB::table('daily_report')->where('request_id',$request_id)->update(['status'=>'Approved','updated_at'=>Carbon::now()]); 
             return redirect('request_approvel')->with('alert-success','Request  approved successfully ');
        }

   }
   public function showing_tasks(){
        $final=array();
       $email=Session::get('useremail');
       $designation=DB::table('emp')->where('email',$email)->value('designation');
       $role_id= Session::get('role_id');
       $location=Session::get('location');
       $raised_by=DB::table('emp')->where('email',$email)->value('id');
       $raised_request=DB::table('raised_request')->join('request_master','raised_request.request_type_id','=','request_master.request_id')->join('emp','raised_request.raised_for_emp','=','emp.id')
       ->select('raised_request.*','request_master.request_type','emp.first_name','emp.middle_name','emp.last_name')->where('raised_request.raised_by',$raised_by)->get();
       
        $raised_request1=DB::table('raised_request')->join('emp','raised_request.raised_for_emp','=','emp.id')->join('request_master','raised_request.request_type_id','=','request_master.request_id')->where('raised_request.status','Pending')->select('raised_request.*','emp.first_name','emp.middle_name','emp.last_name','request_master.request_type','request_master.request_id','request_master.eta_days','request_master.eta_hours')->get();
        foreach($raised_request1 as $request)
        {
            
            $reqest_master= DB::table('request_master')->join('emp','request_master.reporting_level_one','=','emp.designation')->where('emp.designation',$designation)->where('emp.branch_location_id',$location)->where('request_master.request_id',$request->request_type_id)->select('request_master.*')->get();
            foreach($reqest_master as $row)
            {
               
               if($request->request_id == $row->request_id)
               {    
                    $final[]=['eta_days'=>$request->eta_days,'eta_hours'=>$request->eta_hours,'id'=>$request->id,'request_type_id'=>$request->request_type_id,'raised_by'=>$request->raised_by,'raised_for_emp'=>$request->raised_for_emp,'request_details'=>$request->request_details,'approved_by_level_one'=>$request->approved_by_level_one,'approved_by_level_two'=>$request->approved_by_level_two,'rejected_by'=>$request->rejected_by,'	reject_reason'=>$request->reject_reason,'status'=>$request->status,'created_at'=>$request->created_at,'updated_at'=>$request->updated_at,'request_type'=>$request->request_type,'middle_name'=>$request->middle_name,'last_name'=>$request->last_name,'first_name'=>$request->first_name];
                    
               }
               
            }
            $reqest_master1= DB::table('request_master')->join('emp','request_master.reporting_level_two','=','emp.designation')->where('emp.designation',$designation)->where('emp.branch_location_id',$location)->where('request_master.request_id',$request->request_type_id)->select('request_master.*')->get();
            foreach($reqest_master1 as $row1)
            {
               
               if($request->request_id == $row1->request_id)
               {    
                    $final[]=['eta_days'=>$request->eta_days,'eta_hours'=>$request->eta_hours,'id'=>$request->id,'request_type_id'=>$request->request_type_id,'raised_by'=>$request->raised_by,'raised_for_emp'=>$request->raised_for_emp,'request_details'=>$request->request_details,'approved_by_level_one'=>$request->approved_by_level_one,'approved_by_level_two'=>$request->approved_by_level_two,'rejected_by'=>$request->rejected_by,'	reject_reason'=>$request->reject_reason,'status'=>$request->status,'created_at'=>$request->created_at,'updated_at'=>$request->updated_at,'request_type'=>$request->request_type,'middle_name'=>$request->middle_name,'last_name'=>$request->last_name,'first_name'=>$request->first_name];
                    
               }
               
            }
            
        }
     $final=json_decode(json_encode($final));
      Session::put('task_notification',$final);
       
   }
   public function add_in_out(Request $request)
   { 
       $email=Session::get('useremail');
       $raised_by=DB::table('emp')->where('email',$email)->value('id');
       if($request->attendace_out=='')
     {
          return redirect('request_approvel')->with('alert-danger','Please Fill   out time ');
     }
     if($request->attendace_in =='' && $request->attendace_out=='')
     {
          return redirect('request_approvel')->with('alert-danger','Please Fill  in and out time ');
     }
     if($request->attendace_in =='' && $request->in_time=='00:00:00')
     {
          return redirect('request_approvel')->with('alert-danger','Please Fill  in  time ');
     }
    
     $emp_id=   $request->emp_id;
     $date=   $request->date;
     $attendace_in =  $request->attendace_in;
     $attendace_out =  $request->attendace_out;
     $request_type=2;
     $actual_in=  $request->in_time;
      if($request->attendace_in =='')
     {
         $attendace_in=$request->in_time;
     }
     $dd=strtotime($attendace_out) - strtotime($attendace_in);
     $Total=gmdate('H:i:s' ,$dd);
     $update=DB::table('daily_report')->where('emp_id',$emp_id)->where('date',$date)->update(['initial_in'=>$attendace_in,'final_out'=>$attendace_out,'total_working_hour'=>$Total,'updated_at'=>Carbon::now()]);
     if($update)
     {
         $insert=DB::table('routine_task')->insert(['task_id'=>2,'emp_id'=>$emp_id,'approved_by_id'=>$raised_by,'date'=>$date,'created_at'=>Carbon::now()]);
         return redirect('request_approvel')->with('alert-success','Attendance inserted Successfully');
     }
     
   }
   public function mark_absent(Request $request)
   {
        $email=Session::get('useremail');
        $raised_by=DB::table('emp')->where('email',$email)->value('id');
        $emp_id= $request->emp_id_a;
        $date=   $request->date_a;
        $update=DB::table('daily_report')->where('emp_id',$emp_id)->where('date',$date)->update(['updated_at'=>Carbon::now()]);
        if($update)
     {
         $insert=DB::table('routine_task')->insert(['task_id'=>2,'emp_id'=>$emp_id,'approved_by_id'=>$raised_by,'date'=>$date,'created_at'=>Carbon::now()]);
         return redirect('request_approvel')->with('alert-success','Employee marked absent Successfully');
     }
           
   }
   
    public function overtime_request(Request $request)
   {   
        $email=Session::get('useremail');
        $overtime_hours=array();
        $raised_by=DB::table('emp')->where('email',$email)->value('id');
        $overtime_hours['overtime']= $request->overtime;
        $raised_for=$request->overtime_id;
        $date=$request->date;
        $insert=DB::table('raised_request')->insert(['request_type_id'=>4,'raised_by'=>$raised_by,'raised_for_emp'=>$raised_for,'request_details'=>json_encode($overtime_hours),'status'=>'Pending','created_at'=>Carbon::now()]);
        if($insert)
        {
            $max_id=DB::table('raised_request')->where('raised_for_emp',$raised_for)->max('id');
            $daily_report_update=DB::table('daily_report')->whereDate('date',$date)->where('emp_id',$raised_for)->update(['overtime_hours'=>$request->overtime,'request_type'=>4,'request_id'=>$max_id,'status'=>'Pending','updated_at'=>Carbon::now()]);
            return redirect('request_approvel')->with('alert-success','Request  raised successfully ');
        }
        
        
   }
}
