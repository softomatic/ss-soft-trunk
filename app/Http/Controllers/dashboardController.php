<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\File;
use App\Exception;
use \Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

date_default_timezone_set('Asia/Kolkata');

class dashboardController extends Controller
{
    public function index(){
        if(Session::get('username')!='')
        {
            Log::info('Loading Dashboard for user with id '.Session::get('user_id').' ');

            try{
               
                
            
            $emp= DB::table('emp')->where('status','active')->count();
            $emp_inactive= DB::table('emp')->where('status','!=','active')->count();
            $notice= DB::table('notice_board')->get();        
           
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return redirect()->back()->with('alert-danger','Database Query Exception! ['.$e->getMessage().' ]');
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return redirect()->back()->with('alert-danger',$e->getMessage());
            }

            // $last_insert_id = DB::table('emp')->orderBy('id','desc')->limit('1','1')->value('id');
            // $last_insert_id++;

            return view('dashboard',compact('emp','notice','emp_inactive'));
        }
        else
            return redirect('/')->with('status',"Please login First");
    }

   
     public function update_attendance_tt()
    {
       
        $data=DB::table('emp')->join('branch','emp.branch_location_id','branch.id')->where('emp.designation','35')->select('emp.id','emp.branch_location_id','emp.first_name','emp.middle_name','emp.last_name','branch.branch')->get();
         $export_data="hr_name,branch,in_out_generated_by_system,take_action_by_hr,remaining\n";
        foreach($data as $row){
                $table=DB::table('daily_report')->join('emp','daily_report.emp_id','emp.id')->where('emp.fixed_salary','!=',1)->where('emp.branch_location_id',$row->branch_location_id)->whereMonth('daily_report.date',5)
                ->where('daily_report.total_working_hour','00:00:00')->where('daily_report.updated_at','=',NULL)->count();
                // $table1=DB::table('daily_report')->join('emp','daily_report.emp_id','emp.id')->where('emp.fixed_salary','!=',1)->where('emp.branch_location_id',$row->branch_location_id)->whereMonth('daily_report.date',5)
                // ->where('daily_report.status',NULL)->where('daily_report.updated_at','!=',NULL)->count();
                 $table2=DB::table('routine_task')->join('emp','routine_task.emp_id','emp.id')->where('emp.fixed_salary','!=',1)->where('emp.branch_location_id',$row->branch_location_id)->whereMonth('routine_task.date',5)
                ->count();
                $total=$table+$table2;
                $export_data.=$row->first_name.' '.$row->middle_name.' '.$row->last_name.','.$row->branch.','.$total.','.$table2.','.$table.','."\n";    
                
        }
        return response($export_data)
            ->header('Content-Type','application/csv')               
            ->header('Content-Disposition', 'attachment; filename="in_out_request_report.csv"')
            ->header('Pragma','no-cache')
            ->header('Expires','0');
        
    }

//  public function update_attendance_tt()
// {
//      $count=0;
//   $over_time_request= DB::table('daily_report')->join('emp','daily_report.emp_id','=','emp.id')
//         ->leftjoin('shift_master','daily_report.shift','=','shift_master.id')->whereNotIn('emp.branch_location_id',[1,3])->whereDate('daily_report.date','2019-03-20')
//         ->select('daily_report.id','shift_master.time_in','shift_master.time_out','emp.id as emp_id')->get();
//         return sizeof($over_time_request);
//         foreach( $over_time_request as $row)
//         {
//             $initial_in=$row->time_in;
//             $final_out=$row->time_out;
//             $total= strtotime($final_out) - strtotime($initial_in);
//             $total= gmdate('H:i:s' ,$total);
//             $update=DB::table('daily_report')->where('id',$row->id)->update(['initial_in'=>$initial_in,'final_out'=>$final_out,'total_working_hour'=>$total,'created_at'=>Carbon::now()]);
            
//             if($update >= 0)
//             {
//                 $count++;
//             }
            
//         }
 

    //  public function update_attendance_tt()
    // {
    //         $daily_report_data= DB::table('emp')->join('department','emp.department','=','department.id')->join('designation','emp.designation','=','designation.id')
    //         ->join('salary','emp.salary_id','=','salary.id') ->join('branch','emp.branch_location_id','=','branch.id')->where('emp.status','active')
    //         ->select('emp.first_name','emp.middle_name','emp.last_name','department.department_name','designation.designation as designation_name','salary.salary','branch.branch as branch_name')->get();
    //         $export_data="emp_name,branch,department,designation,salary,basic\n";
    //         foreach($daily_report_data as $value){
    //           $salry=  json_decode($value->salary);
    //                 foreach($salry as $sal)
    //                 {
    //                   $basic=  $sal->basic;
    //                   $salary= $sal->salary;
    //                 }
                 
    //                  $export_data.=$value->first_name.' '.$value->middle_name.' '.$value->last_name.','.$value->branch_name.','.$value->department_name.','.$value->designation_name.','.$salary.','.$basic.','."\n";    
                

                
    //         }
    //         return response($export_data)
    //             ->header('Content-Type','application/csv')               
    //             ->header('Content-Disposition', 'attachment; filename="employee_download.csv"')
    //             ->header('Pragma','no-cache')
    //             ->header('Expires','0');    
    // }
    
    //  public function update_attendance_tt()
    // {
    //         $daily_report_data= DB::table('emp')->join('department','emp.department','=','department.id')->join('employee_shift','emp.id','=','employee_shift.emp_id')
    //         ->join('bank_list','emp.bank_name','=','bank_list.id')
    //         ->join('salary','emp.salary_id','=','salary.id')->join('branch','emp.branch_location_id','=','branch.id')->where('emp.status','active')
    //         ->select('emp.*','employee_shift.shift_id','department.department_name','salary.salary','branch.branch as branch_name'
    //         ,'bank_list.bank_name')->get();
    //         $export_data="biometric_id,poss_id,genesis_id,emp_name,shift,branch,department,division,section,blood_group,email,date_of_birth,mobile_no,gender,category,marital_status,adhaar_number,pan_number,local_address,permanent_address,distance_from_office,emergency_call_person,emergency_call_number,out_source,date_of_joining,esic_number,epf_number,lin_number,uan_number,epf_option,esic_option,reason_for_code_zero,last_working_day,fixed_salary,account_holder_name,account_no,ifsc_code,bank_name,salary,basic\n";
    //         foreach($daily_report_data as $value){
                
    //       str_replace(',','',$value->division);
    //         $divi=array();
    //         $sec_all=array();
    //         $salry=  json_decode($value->salary);
                    
    //                 foreach($salry as $sal)
    //                 {
    //                   $basic=  $sal->basic;
    //                   $salary= $sal->salary;
    //                 }
                    
    //                 if($value->division ==' ' || $value->division ==NUll){
    //                   $divi='';

    //                 }
    //                 else
    //                 {
    //                   $desig=json_decode($value->division);
    //                   $division =DB::table('division')->whereIn('id',$desig)->get(['division']);
    //                   foreach( $division as $div)
    //                   {
    //                       $divi[]=$div->division;
    //                   }
    //                   $divi= json_encode( $divi);
    //                   $divi=str_replace(',','', $divi);
    //                 }
                    
    //                 if($value->section ==' ' || $value->section ==NUll){
    //                   $sec_all='';

    //                 }
    //                 else
    //                 {
    //                   $sec=json_decode($value->section);
    //                   $section =DB::table('section')->whereIn('id',$sec)->get(['section']);
    //                   foreach( $section as $row)
    //                   {
    //                       $sec_all[]=$row->section;
    //                   }
    //                   $sec_all= json_encode( $sec_all);
    //                   $sec_all=str_replace(',', '', $sec_all);
                      
    //                 }
                    
    //                 $shift=DB::table('shift_master')->where('id',$value->shift_id)->value('shift_name');
    //                 $export_data.=$value->biometric_id.','.$value->genesis_id.','.$value->genesis_ledger_id.','.$value->first_name.' '.$value->middle_name.' '.$value->last_name.','.$shift.','.$value->branch_name.','.$value->department_name.','.$divi.','.$sec_all.','.$value->blood_group.','.$value->email.','.$value->dob.','.$value->mobile.','.$value->gender.','.$value->category.','.$value->marital_status.','.$value->adhaar_number.','.$value->pan_number.','.$value->local_address.','.$value->permanent_address.','.$value->distance_from_office.','.$value->emergency_call_person.','.$value->emergency_call_number.','.$value->out_source.','.$value->doj.','.$value->esic_number.','.$value->epf_number.','.$value->lin_number.','.$value->uan_number.','.$value->epf_option.','.$value->esic_option.','.$value->reason_code_0.','.$value->last_working_day.','.$value->fixed_salary.','.$value->acc_holder_name.','.$value->acc_no.','.$value->ifsc_code.','.$value->bank_name.','.$salary.','.$basic.','."\n";    
                

                
    //         }
    //         return response($export_data)
    //             ->header('Content-Type','application/csv')               
    //             ->header('Content-Disposition', 'attachment; filename="employee_download.csv"')
    //             ->header('Pragma','no-cache')
    //             ->header('Expires','0');    
    // }
    
    //   public function update_attendance_tt()
    // {
    //         $daily_report_data= DB::table('emp')->where('gender','female')->where('status','active')->get();
    //         $export_data="emp_name,Gender\n";
    //         foreach($daily_report_data as $value){
              
                 
    //             $export_data.=$value->first_name.' '.$value->middle_name.' '.$value->last_name.','.$value->gender.','."\n";    
                

                
    //         }
    //         return response($export_data)
    //             ->header('Content-Type','application/csv')               
    //             ->header('Content-Disposition', 'attachment; filename="employee_download.csv"')
    //             ->header('Pragma','no-cache')
    //             ->header('Expires','0');    
    // }
    
    
    //   public function update_attendance_tt()
    // {
    //         return $daily_report_data= DB::table('raised_request')->join('emp','raised_request.raised_for_emp','=','emp.id')->where('emp.branch_location_id',1)->where('raised_request.status','Pending')
    //         ->count();
           
             
    // }
    
    //  public function update_attendance_tt()
    // {
    //         $daily_report_data= DB::table('emp')
    //         ->join('salary','emp.salary_id','=','salary.id') ->join('branch','emp.branch_location_id','=','branch.id')->where('emp.status','active')
    //         ->select('emp.first_name','emp.middle_name','emp.last_name','emp.biometric_id','salary.salary','branch.branch as branch_name')->get();
    //         $export_data="emp_name,biometric_id,branch,salary,basic\n";
    //         foreach($daily_report_data as $value){
    //           $salry=  json_decode($value->salary);
    //                 foreach($salry as $sal)
    //                 {
    //                   $basic=  $sal->basic;
    //                   $salary= $sal->salary;
    //                 }
                 
    //                  $export_data.=$value->first_name.' '.$value->middle_name.' '.$value->last_name.','.$value->biometric_id.','.$value->branch_name.','.$salary.','.$basic.','."\n";    
                

                
    //         }
    //         return response($export_data)
    //             ->header('Content-Type','application/csv')               
    //             ->header('Content-Disposition', 'attachment; filename="employee_download.csv"')
    //             ->header('Pragma','no-cache')
    //             ->header('Expires','0');    
    // }
    
    
    
    //  public function update_attendance_tt()
    // {
       
        
    //      $daily_report_data= DB::table('emp')->join('branch','emp.branch_location_id','=','branch.id')
    //     ->where('pant','!=',NULL)->select('emp.first_name','emp.middle_name','emp.last_name','branch.branch','emp.id')->get();
    //      $export_data="emp_id,emp_name,branch\n";
    //     foreach($daily_report_data as $value){
               
    //             $export_data.=$value->id.','.$value->first_name.' '.$value->middle_name.' '.$value->last_name.','.$value->branch.','."\n";    
                
    //         }
    //         return response($export_data)
    //             ->header('Content-Type','application/csv')               
    //             ->header('Content-Disposition', 'attachment; filename="employee_download.csv"')
    //             ->header('Pragma','no-cache')
    //             ->header('Expires','0');
        
    // }
    
    
 
    


}


