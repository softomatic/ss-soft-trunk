<?php

namespace App\Http\Controllers;
use App\emp;
use App\User;
use App\salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\File;
use App\Exception;
use \Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class MasterController extends Controller
{
    public function index()
    {
       $branch = DB::table('branch')->get();
       $division = DB::table('division')->get();
       $epf_master = DB::table('epf_master')->join('branch','epf_master.branch_id', '=' , 'branch.id')->
       select('epf_master.epf','epf_master.id','epf_master.esic','epf_master.minimum_wages','branch.branch','epf_master.branch_id')->get();

       $aging_master = DB::table('aging_master')->join('branch','aging_master.branch_id', '=' , 'branch.id')->join('division','division.id','=','aging_master.division_id')->
       select('aging_master.aging_title','aging_master.id','aging_master.aging_per','branch.branch','aging_master.branch_id','aging_master.division_id','division.division')->
       get();
        $professional = DB::table('professional_tax')->join('branch','professional_tax.branch_id', '=' , 'branch.id')->
       select('professional_tax.id','professional_tax.branch_id','professional_tax.calculation_base','professional_tax.amount_from','professional_tax.amount_to','branch.branch','professional_tax.professional_tax','professional_tax.tax_deducted','professional_tax.for_men','professional_tax.for_women','for_11_month','for_last_month')->get();
        $shift=DB::table('shift_master')->get();
        $exemp=DB::table('exemption_time')->join('branch','branch.id','=','exemption_time.branch_id')->join('shift_master','shift_master.id','=','exemption_time.shift_id')->select('exemption_time.id','exemption_time.shift_id','exemption_time.branch_id','shift_master.shift_name','branch.branch','exemption_time.exemption_time')->get();
       return view('master',compact('epf_master','branch','aging_master','division','professional','shift','exemp'));
    }

     public function epf_master(Request $request)
    {
        $location= $request->input('location');
        $epf= $request->input('epf');
        $esic=$request->input('esic');
        $minimum_wage=$request->input('minimum_wage');
        $epf_master= DB::table('epf_master')->insert(['branch_id'=>$location,'epf'=>$epf, 'esic'=>$esic,'minimum_wages'=>$minimum_wage,'created_at'=>now()]);
        return redirect('master')->with('alert-success','Generated Sucessfully');
    }
      public function aging_master(Request $request)
    {
        $location= $request->input('location');
        $division= $request->input('division');
        $i=1;
         if($request->input('aging_title')!='' && $request->input('aging_per')!='' ) 
           { 
            foreach ($request->input('aging_title') as $aging_title ) {             
            $aging_master= DB::table('aging_master')->insert(['branch_id'=>$location,'division_id'=>$division, 'aging_title'=>$aging_title , 'aging_per'=>$request->aging_per[$i] ,'created_at'=>now()]);
            $i++;
        }
    } 
         return redirect('master')->with('alert-success','Generated Sucessfully');
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $branch = $request->input('branch');
        $epf=$request->input('epf');
        $esic=$request->input('esic');
        $minimum=$request->input('minimum');
        $exp=DB::table('epf_master')
        ->where('id', $id)
        ->update(['branch_id' => $branch , 'epf' => $epf ,'esic' =>$esic ,'minimum_wages' =>$minimum ,'updated_at' =>Carbon::now()]);
        return redirect('master')->with('alert-success','EPF Master Updated Successfully!');

    }
     public function update_aging(Request $request)
    {
        $id = $request->input('id');
        $branch = $request->input('branch');
        $division=$request->input('division');
        $title=$request->input('title');
        $per=$request->input('per');
        $exp=DB::table('aging_master')
        ->where('id', $id)
        ->update(['branch_id' => $branch , 'division_id' => $division ,'aging_title' =>$title ,'aging_per' =>$per ,'updated_at' =>Carbon::now()]);
        return redirect('master')->with('alert-success','Aging Master Updated Successfully!');


    }
      public function professional_tax(Request $request)
      {
          
          $branch = $request->input('location');
          $calculation_base=$request->input('calculation_base');
          $amt_from=$request->input('amt_from');

          $amt_to=$request->input('amt_to');
          $prof_tax=$request->input('prof_tax');
          $payment_type=$request->input('payment_type');
          $men=$request->input('men');
          $women=$request->input('women');  
          if($payment_type=='yearly')
          {
            $for_11_month = $request->input('for_11_month');
            $for_last_month = $request->input('for_last_month');
          } 
          else
          {
            $for_11_month = 0;
            $for_last_month = 0;
          }
          if($branch=='' || $calculation_base=='' || $amt_from=='' || $amt_to=='' || $prof_tax=='' || $payment_type=='' || ($men=='' && $women==''))
          {
              return redirect()->back()->with('alert-danger','All fields are required to fill')->with($request->all);
          }        
          $professional_tax= DB::table('professional_tax')->insert(['branch_id'=>$branch,'calculation_base'=>$calculation_base, 'amount_from'=>$amt_from,
          'amount_to'=>$amt_to,'professional_tax'=>$prof_tax,'professional_tax'=>$payment_type,'tax_deducted'=>$prof_tax,'for_11_month'=>$for_11_month,'for_last_month'=>$for_last_month,'for_men'=>$men,'for_women'=>$women,'created_at'=>now()]);
          if($professional_tax==true){
              return redirect('master')->with('alert-success','Professional Tax created Successfully!');
          }
          else {
              return redirect('master')->with('alert-danger','Professional Tax Not Created');
          }
      }

      public function professional_tax_update(Request $request)
      {          
          $branch = $request->input('location');
          $calculation_base=$request->input('calculation_base');
          $amt_from=$request->input('amt_from');
          $amt_to=$request->input('amt_to');
          $prof_tax=$request->input('prof_tax');
          $payment_type=$request->input('payment_type');
          $men=$request->input('men');
          $women=$request->input('women'); 
          if($payment_type=='yearly')
          {
            $for_11_month = $request->input('for_11_month');
            $for_last_month = $request->input('for_last_month');
          } 
          else
          {
            $for_11_month = 0;
            $for_last_month = 0;
          }

          if($branch=='' || $calculation_base=='' || $amt_from=='' || $prof_tax=='' || $payment_type=='' || ($men=='' && $women==''))
          {
              return redirect()->back()->with('alert-danger','All fields are required to fill')->with($request->all);
          }        
          $professional_tax= DB::table('professional_tax')->where('id',$request->input('id'))->update(['branch_id'=>$branch,'calculation_base'=>$calculation_base, 'amount_from'=>$amt_from,
          'amount_to'=>$amt_to,'professional_tax'=>$prof_tax,'professional_tax'=>$payment_type,'tax_deducted'=>$prof_tax,'for_11_month'=>$for_11_month,'for_last_month'=>$for_last_month,'for_men'=>$men,'for_women'=>$women,'updated_at'=>now()]);
          if($professional_tax==true){
              return redirect('master')->with('alert-success','Professional Tax Updated Successfully!');
          }
          else {
              return redirect('master')->with('alert-danger','Professional Tax Not Updated');
          }
      }

      public function shift_master(Request $request){
           
           $shift_name=$request->shift_name;
           $time_out=$request->time_out;
           $time_in= $request->time_in;
           $insert=DB::table('shift_master')->insert(['shift_name'=>$shift_name,'time_in'=>$time_in,'time_out'=>$time_out,'created_at'=>Carbon::now()]);
           if($insert)
           {
                return redirect('master')->with('alert-success','Shift Created successfully');
           }
           else{
                return redirect('master')->with('alert-danger','Shift  Not Created ');
           }
      }

      //  public function shift_master(Request $request){
           
      //      $shift_name=$request->shift_name;
      //      $time_out=$request->time_out;
      //      $time_in= $request->time_in;
      //      $exemption_time= $request->exemption_time;
      //      $insert=DB::table('shift_master')->insert(['shift_name'=>$shift_name,'time_in'=>$time_in,'time_out'=>$time_out,'exemption_time'=>$exemption_time,'created_at'=>Carbon::now()]);
      //      if($insert)
      //      {
      //           return redirect('master')->with('alert-success','Shift Created successfully');
      //      }
      //      else{
      //           return redirect('master')->with('alert-danger','Shift  Not Created ');
      //      }
      // }

      public function update_shift(Request $request)
      {
        try
        {
            $id = $request->id;
            $shift_name=$request->shift_name;
            $time_out=$request->time_out;
            $time_in= $request->time_in;
            $insert=DB::table('shift_master')->where('id',$id)->update(['shift_name'=>$shift_name,'time_in'=>$time_in,'time_out'=>$time_out,'updated_at'=>Carbon::now()]);
            if($insert)
            {
                return redirect('master')->with('alert-success','Shift Created successfully');
            }
            else{
                return redirect('master')->with('alert-danger','Shift  Not Created ');
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

      public function set_exemption(Request $request)
      {
            $branch=$request->branch;
            $shift=$request->shift;
            $exec=$request->exemption_time;
            $insert=DB::table('exemption_time')->insert(['branch_id'=>$branch,'shift_id'=>$shift,'exemption_time'=>$exec,'created_at'=>Carbon::now()]);
             if($insert)
           {
                return redirect('master')->with('alert-success','Exemption Created successfully');
           }
           else{
                return redirect('master')->with('alert-danger','Exemption  Not Created ');
           }
      }

      public function update_exemption(Request $request)
      {
            $id = $request->id;
            $branch=$request->branch;
            $shift=$request->shift;
            $exec=$request->exemption_time;
            $insert=DB::table('exemption_time')->where('id',$id)->update(['branch_id'=>$branch,'shift_id'=>$shift,'exemption_time'=>$exec,'updated_at'=>Carbon::now()]);
             if($insert)
           {
                return redirect('master')->with('alert-success','Exemption Created successfully');
           }
           else{
                return redirect('master')->with('alert-danger','Exemption  Not Created ');
           }
      }



       

}
