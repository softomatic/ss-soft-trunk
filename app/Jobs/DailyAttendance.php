<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\get;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

ini_set('max_execution_time', 10000);
date_default_timezone_set('Asia/Kolkata');


class DailyAttendance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
     
    
    public $file;
    public $file_name;

    public function __construct( $file ,$file_name )
    {
        $this->file= $file;
        $this->file_name = $file_name;
    }

   
    public function handle(Request $request)
    {     
        Schema::dropIfExists('temp_daily_report');
        $header=["biometric_id","machine_id","bio","store_id","time"];
        $delimiter=',';
        $data = array();  
        $row='';
        $job_type="Daily attendance";
        $jobs_status_insert=DB::table('jobs_status')->insert(['job_type' => $job_type,'file_name'=> $this->file_name,'status'=>'Pending','start_time'=>Carbon::now(),'finish_time'=>'']);
        $get_job_id=DB::table('jobs_status')->max('id');
        if (($handle = fopen( $this->file, 'r')) !== FALSE) 
            {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
                {
                    $size = sizeof($row);
                    $length = $size-5;
                    array_splice($row,5,$length);
                
                   if(!$header)
                   {
                            $header = $row;
                   }
                        else
                            
                            $data[] = array_combine($header, $row);
                            
                }
            
              
                fclose($handle);
            }

        Schema::create('temp_daily_report', function (Blueprint $table) {
            $table->increments('temp_daily_report_id');
            $table->string('biometric_id');
            $table->string('date');                    
            $table->string('time');
            $table->timestamps();
            $table->temporary();
        });
            
        $file_date = date('Y-m-d',strtotime(explode('.',$this->file_name)[0]));
        
            $not_inserted=array();
          foreach($data as $value){
            $date=date('Y-m-d',strtotime($value['time']));
            $time=date('H:i:s',strtotime($value['time']));
            $insertrow = array('biometric_id'=>$value['biometric_id'],'date' => $date,'time'=> $time);
            
            $result = DB::table('temp_daily_report')->insert($insertrow);
          }
          
            $temp_table=DB::table('temp_daily_report')->groupBy('biometric_id')->get();
            $biometric=array();
            $attendance = array();
            foreach ($temp_table as $temp_t)
            {
                
                $biometric[]=$temp_t->biometric_id;
                $tame=DB::table('temp_daily_report')->where('biometric_id',$temp_t->biometric_id)->min('time');                   
                $attendance['IN-1']=$tame;
                
                if(DB::table('temp_daily_report')->where('biometric_id',$temp_t->biometric_id)->count() > 1)
                {
                    $tame1=DB::table('temp_daily_report')->where('biometric_id',$temp_t->biometric_id)->max('time');
                    $attendance['OUT-1']=$tame1;
                }
                else{
                    $tame1='00:00:00';
                    $attendance['OUT-1']='00:00:00';
                }
                
                if($tame1!='00:00:00'){
                $dd=strtotime($tame1) - strtotime($tame);
                $Total=gmdate('H:i:s' ,$dd);
                }
                else{
                     $Total='00:00:00';
                }
                
                trim($temp_t->biometric_id);
              
                $emp_id=DB::table('emp')->select('id')->where('biometric_id','=',$temp_t->biometric_id)->value('id');

                $emp_shift = DB::table('employee_shift')->select('shift_id')->where('emp_id',$emp_id)->whereDate('start_date','<=',$file_date)->whereDate('end_date','>=',$file_date)->orderBy('id','desc')->value('shift_id');
                
                if($emp_shift!='')
                    $shift_id = $emp_shift;
                else
                    $shift_id=0;


                $myattendance = json_encode(array('report'=>$attendance));
                 
                if( DB::table('daily_report')->where('emp_id', $emp_id)->where('date',$temp_t->date)->count() > 0)
                {
                   
                     $updated= DB::table('daily_report')->where('emp_id',$emp_id)->where('date',$temp_t->date)->update(['shift'=>$shift_id, 'attendance'=>$myattendance,
                    'initial_in'=>$tame,'final_out'=>$tame1,'total_working_hour'=>$Total]);                
                }
                else 
                {
                    if(DB::table('emp')->where('biometric_id','=', $temp_t->biometric_id)->count() > 0 )
                    {
                        $inserted= DB::table('daily_report')->insert(['user_id'=>1 , 'emp_id'=> $emp_id ,'biometric_id'=> $temp_t->biometric_id , 'shift'=>$shift_id, 'date'=>$temp_t->date,'attendance'=>$myattendance,
                       'initial_in'=>$tame,'final_out'=>$tame1,'total_working_hour'=>$Total,'created_at'=>Carbon::now()]);
                    }   
                    else{

                         $not_inserted[]=$temp_t->biometric_id;   
                    }                  
                }                   
        } 
        
        $atten_not_in=array();
        $atten_not_in['IN-1']='00:00:00';
        $atten_not_in['OUT-1']='00:00:00';
        $final_not=json_encode(array('report'=>$atten_not_in));
        $qqq=json_encode($biometric);
        $que=DB::table('emp')->where('biometric_id','!=',0)->where('biometric_id','!=',NULL)->where('status','active')->whereNotIn('biometric_id',$biometric)->get(['id','biometric_id']);
        foreach($que as $query)
        {
            $emp_shift = DB::table('employee_shift')->select('shift_id')->where('emp_id',$query->id)->whereDate('start_date','<=',$file_date)->whereDate('end_date','>=',$file_date)->orderBy('id','desc')->value('shift_id');

            if($emp_shift!='')
                    $shift_id = $emp_shift;
                else
                    $shift_id=0;
                    
             if( DB::table('daily_report')->where('emp_id', $query->id)->where('date',$file_date)->count() > 0)
                {
                     $updated= DB::table('daily_report')->where('emp_id',$query->id)->where('date',$file_date)->update(['shift'=>$shift_id,'attendance'=>$final_not,
                    'initial_in'=>"00:00:00",'final_out'=>"00:00:00",'total_working_hour'=>"00:00:00"]);                
                }
                else 
                {
                        $inserted= DB::table('daily_report')->insert(['user_id'=>1 , 'emp_id'=>$query->id ,'biometric_id'=> $query->biometric_id , 'shift'=>$shift_id, 'date'=>$file_date,'attendance'=>$final_not,
                       'initial_in'=>"00:00:00",'final_out'=>"00:00:00",'total_working_hour'=>"00:00:00",'created_at'=>Carbon::now()]);
                }
        
        }
        
        $not_insert=json_encode($not_inserted);
        Schema::drop('temp_daily_report');
        $final=DB::table('jobs_status')->where('id',$get_job_id)->update(['status'=>'Successfull','biometric_id_not'=>$not_insert,'finish_time'=>Carbon::now()]);    

        copy($this->file ,'../production_processed/'.$this->file_name);
        unlink($this->file);   


        return redirect()->back()->with('alert-success','Attendance uploaded successfully');
        
    }
 
}