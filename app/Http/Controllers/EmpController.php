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
use File;
use App\Exception;
use \Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;

// ini_set('max_execution_time', 30000);
date_default_timezone_set('Asia/Kolkata');

class EmpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getform()
    {
        if(Session::get('username')!='' && (session('role')=='admin' || session('role')=='hr' || session('role')=='hr admin'))
        {
            Log::info('Loading Add Employee page for User with id '.Session::get('user_id').' ');

            try{
            $depts = DB::table('department')->get();
            /*$depts = DB::table('department')->where('department_name','!=','Admin')->orWhere('department_name','!=','admin')->get();*/
            $dept_id = $depts->first()->id;
            $desigs = DB::table('designation')->where('department',$dept_id)->get();
            $roles = DB::table('role')->get();
            $last_insert_id = DB::table('emp')->max('id') + 1;
            if(session('role')=='hr')
                $branches = DB::table('branch')->where('id',session('branch_location_id'))->get();
            else
                $branches = DB::table('branch')->get();
            $bank_list = DB::table('bank_list')->get();
            $division = DB::table('division')->get();
            return view('add-emp',compact('depts','roles','last_insert_id','desigs','branches','bank_list','division'));
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

            
        }
        else
        {
            if(Session::get('username')=='')
                return redirect('/')->with('status',"Please login First");
            else
            return redirect('dashboard')->with('alert-danger',"Only admin and HR can add employee");
        }
    }
        
    public function getdesig(Request $request)
    {
        $dept_id = $request->dept_id;
        $options='<option></option>';
        try
        {
            $desigs = DB::table('designation')->where('department',$dept_id)->get();
            foreach($desigs as $key=>$value)
            {
                //return $value->id;
                $options.='<option value="'.$value->id.'">'.$value->designation.'</option>';
                // return $options;
            }
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
        return $options;
    }

    public function getsection(Request $request)
    {
        $division_id = $request->division_id;
        $options='<option></option>';
        try
        {
            $desigs = DB::table('section')->wherein('division_id',$division_id)->get();
            foreach($desigs as $key=>$value)
            {
                //return $value->id;
                $options.='<option value="'.$value->id.'">'.$value->section.'</option>';
                // return $options;
            }
            return $options;
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
        
    }

    public function create(Request $request)
    {
        try
        {
       
        Log::info('Trying To Add Employee');
        $current_timestamp = Carbon::now()->timestamp;
        $msg='';
        $rules = array(
        'genesis_id'    =>  'nullable|string|unique:emp',
        'genesis_ledger_id'    =>  'required|string|unique:emp',
        'biometric_id'  =>  'nullable|string',
        'branch_location_id'    => 'required|string',
        'title' => 'required|string',
        'first_name'    => 'required|regex:/^[a-zA-Z ]+$/',
        'middle_name'    => 'nullable|regex:/^[a-zA-Z ]+$/',
        'last_name'    => 'nullable|regex:/^[a-zA-Z ]+$/',
        'email'    => 'required|email|unique:users|unique:emp', 
        'blood_group'    => 'nullable|string',
        'dob'    => 'nullable|date|date_format:Y-m-d',
        'mobile'    => 'required|numeric|digits:10',
        'gender'    => 'nullable|string',
        'category' => 'nullable|string',
        'marital_status'    => 'nullable|string',
        'local_address'    => 'nullable|string',
        'adhaar_number'    => 'nullable|numeric|digits:12|unique:emp',
        'pan_number'    => 'nullable|string|unique:emp|max:10|min:10|unique:emp',
        'permanent_address'    => 'nullable|string',
        'distance_from_office'    => 'nullable|numeric',
        'emergency_call_person' => 'nullable|regex:/^[a-zA-Z ]+$/',
        'emergency_call_number' => 'nullable|numeric|digits:10',
        'father_name' => 'nullable|regex:/^[a-zA-Z ]+$/',
        'father_dob' => 'nullable|date_format:Y-m-d',
        'father_adhaar' => 'nullable|numeric|digits:12',
        'father_place' => 'nullable|string',
        'mother_name' => 'nullable|regex:/^[a-zA-Z ]+$/',
        'mother_dob' => 'nullable|date_format:Y-m-d',
        'mother_adhaar' => 'nullable|numeric|digits:12',
        'mother_place' => 'nullable|string',
        'spouse_name' => 'nullable|regex:/^[a-zA-Z ]+$/',
        'spouse_gender' => 'nullable|string',
        'spouse_dob' => 'nullable|date_format:Y-m-d',
        'spouse_adhaar' => 'nullable|numeric|digits:12',
        'spouse_place' => 'nullable|string',
        'no_of_children' => 'nullable|string',
        'child_name' => 'nullable',
        'child_gender' => 'nullable',
        'child_dob' => 'nullable',
        'child_adhaar' => 'nullable',
        'child_place' => 'nullable',
        'department'    => 'required|string',
        'designation'    => 'required|string',
        'out_source'    => 'required|string',
        'division'    => 'nullable|array',
        'section'    => 'nullable|array',
        'status'    => 'required|string',
        'esic_number' => 'nullable|numeric|digits:10',
        'epf_number' => 'nullable|string',
        'lin_number' => 'nullable|string',
        'uan_number' => 'nullable|numeric|digits:12',
        'esic_option' => 'nullable',
        'epf_option' => 'nullable',
        'salary' => 'required|numeric',
        'basic' => 'nullable|numeric',
        'doj'    => 'required|date|date_format:Y-m-d',
        'acc_holder_name'    => 'required|string',
        'acc_no'    => 'required|numeric',
        'ifsc_code'    => 'required|string',
        'bank_name' => 'required|numeric',
        'branch'    => 'required|string',
        'login_email' => 'required|email',
        'password' => 'nullable|string',
        'role' => 'required|numeric',
        'photo'    => 'nullable|image|max:1024',
        'pant' => 'required|string',
        'shirt' => 'required|string',
        'shoes' => 'required|string',
        'scarf_tie' => 'required|string',
        'socks' => 'required|string',
        'belt' => 'nullable|string',
        'waistcoat' => 'nullable|string'
        );
        
        $distance_from_office = -1;
     
        
        if($request->distance_from_office!='')
        {
            $distance_from_office=$request->distance_from_office;
        }
        if($request->salary<0)
        {
            return redirect()->back()->with('alert-danger',"You can not enter salary zero or negative")->withInput($request->all);  
        }
        if($request->division=='')
        $division='';
        else
        $division=json_encode($request->input('division'));
        
        if($request->section=='')
        $section='';
        else
        $section=json_encode($request->input('section'));
            
        if($request->input('password')=='')
        {
            $password='1234';
        }
        else
        {
            $password=$request->input('password');
        }
        $desig = DB::table('designation')->where('id',$request->department)->value('designation');
        if(strtolower($desig)=="fashion consultant")
        {
            if($request->input('genesis_id')=='')
            {
              return redirect()->back()->with('alert-danger',"POSS id is mandatory for Employee of Fashion Consultant")->withInput($request->all);  
            }
        }
        if($request->input('epf_option')==1)
        {
            if($request->input('epf_number')=='')
            {
                 return redirect()->back()->with('alert-danger',"EPF Number Required")->withInput($request->all);  
            }
        }
        if($request->input('esic_option')==1)
        {
            if($request->input('esic_number')=='')
            {
                 return redirect()->back()->with('alert-danger',"ESIC Number Required")->withInput($request->all);  
            }
        }

        $acc_no_limit1 = DB::table('bank_list')->where('id',$request->bank_name)->value('acc_no_limit');
        $acc_no_limit = explode(',', $acc_no_limit1);
        $accno_len = strlen((string)$request->acc_no);
        if(!in_array($accno_len, $acc_no_limit))
        {
            return redirect()->back()->with('alert-danger',"Acc no must be of ".$acc_no_limit1." digits")->withInput($request->all);  
        }

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            Log::info('Add Employee process failed due to Validation Error');
            return redirect()->back()
            ->withErrors($validator)
            ->withInput($request->all)->with('alert-danger','Validation Error');
        } 

            $esic_option='';
            $epf_option='';
            if($request->input('esic_option')=='')
            {
                $esic_option=0;
            }
            else
            {
                $esic_option=$request->input('esic_option');
            }

            if($request->input('epf_option')=='')
            {
                $epf_option=0;
            }
            else
            {
                $epf_option=$request->input('epf_option');
            }

            if($epf_option=='1')
            {
                if($request->basic=='')
                {
                    return redirect()->back()
                    ->with('alert-danger','Basic + DA required')
                    ->withInput($request->all);
                }
                else
                {
                    $basic= $request->basic;
                }
            }
            else
            {
                $basic = 0;
            }

            $salary = array();
            $salary['salary'] = $request->salary;
            $salary['basic'] = $basic;
            $salary = json_encode(array('emp_salary'=>$salary));            
            $email = DB::table('emp')->where('email', $request->email)->value('email');
            $mobile = DB::table('emp')->where('mobile', $request->mobile)->value('mobile');
            
            if($request->file('photo') !=''){
            $target_dir = 'uploads/';
            $file = $request->file('photo');
            $extension = strtolower($file->getClientOriginalExtension()); // getting image extension
            $filename = $request->input('first_name').'_'.$request->input('middle_name').'_'.$request->input('last_name').'_'.$request->input('mobile').'_'.$current_timestamp.'.'.$extension;
            $target_file = $target_dir.$filename;
            }
            if($email!='')
            {
                $msg.='Email-Id is already registered';
            }
            if($mobile!='')
            {
                $msg.=' Mobile number is already registered';
            }
            if($email!='' || $mobile!='')
            {
                Log::info('Add Employee process failed! '.$msg.'');
                return redirect()->back()->with('alert-danger',$msg)->withInput($request->all);
            }
            
            if($request->file('photo')=='')
            {
                $emp = emp::create([
                'genesis_id'    =>  $request->input('genesis_id'),
                'genesis_ledger_id'    =>  $request->input('genesis_ledger_id'),
                'biometric_id'  =>  $request->input('biometric_id'),
                'branch_location_id'  =>  $request->input('branch_location_id'),
                'title' => $request->input('title'),
                'first_name' => $request->input('first_name'),
                'middle_name' => $request->input('middle_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'dob' => $request->input('dob'),
                'mobile' => $request->input('mobile'),
                'blood_group' => $request->input('blood_group'),
                'marital_status' => $request->input('marital_status'),
                'gender' => $request->input('gender'),
                'category' => $request->input('category'),
                'adhaar_number' =>$request->input('adhaar_number'),
                'pan_number' => $request->input('pan_number'),
                'local_address' => $request->input('local_address'),                    
                'permanent_address' => $request->input('permanent_address'),
                'distance_from_office' => $distance_from_office,
                'emergency_call_person' => $request->input('emergency_call_person'),
                'emergency_call_number' => $request->input('emergency_call_number'),
                'department' => $request->input('department'),                
                'designation' => $request->input('designation'),
                'out_source'=>$request->input('out_source'),
                'division'=>$division,
                'section'=>$section,
                'doj' => $request->input('doj'),
                'status' => $request->input('status'),
                'esic_number' => $request->input('esic_number'),
                'epf_number' => $request->input('epf_number'),
                'lin_number' => $request->input('lin_number'),
                'uan_number' => $request->input('uan_number'),
                'esic_option' => $esic_option,
                'epf_option' => $epf_option,
                'acc_holder_name' => $request->input('acc_holder_name'),
                'acc_no' => $request->input('acc_no'),
                'ifsc_code' => $request->input('ifsc_code'),
                'bank_name' => $request->input('bank_name'),
                'branch' => $request->input('branch'),
                'pant' => $request->input('pant'),
                'shirt' => $request->input('shirt'),
                'shoes' => $request->input('shoes'),
                'scarf_tie' => $request->input('scarf_tie'),
                'socks' => $request->input('socks'),
                'belt' => $request->input('belt'),
                'waistcoat' => $request->input('waistcoat')  

                ]);
            }
            elseif($file->move(base_path().'/uploads/', $filename))
            {
                if($extension!='png' && $extension!='jpg' && $extension!='jpeg')
                {
                    Log::info('Add Employee process failed! Require file in png/jpg fromat');
                    return redirect()->back()->with('alert-danger','Please Upload png/jpg file only')->withInput($request->all);;
                }

                $emp = emp::create([
                'genesis_id'    =>  $request->input('genesis_id'),
                'genesis_ledger_id'    =>  $request->input('genesis_ledger_id'),
                'biometric_id'  =>  $request->input('biometric_id'),
                'branch_location_id'  =>  $request->input('branch_location_id'),
                'title' => $request->input('title'),
                'first_name' => $request->input('first_name'),
                'middle_name' => $request->input('middle_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'dob' => $request->input('dob'),
                'mobile' => $request->input('mobile'),
                'blood_group' => $request->input('blood_group'),
                'marital_status' => $request->input('marital_status'),
                'gender' => $request->input('gender'),
                'category' => $request->input('category'),
                'adhaar_number' =>$request->input('adhaar_number'),
                'pan_number' => $request->input('pan_number'),
                'local_address' => $request->input('local_address'),                    
                'permanent_address' => $request->input('permanent_address'),
                'distance_from_office' => $distance_from_office,
                'emergency_call_person' => $request->input('emergency_call_person'),
                'emergency_call_number' => $request->input('emergency_call_number'),
                'department' => $request->input('department'),                
                'designation' => $request->input('designation'),
                'out_source'=>$request->input('out_source'),
                'division'=>$division,
                'section'=>$section,
                'doj' => $request->input('doj'),
                'status' => $request->input('status'),
                'esic_number' => $request->input('esic_number'),
                'epf_number' => $request->input('epf_number'),
                'lin_number' => $request->input('lin_number'),
                'uan_number' => $request->input('uan_number'),
                'esic_option' => $esic_option,
                'epf_option' => $epf_option,
                'acc_holder_name' => $request->input('acc_holder_name'),
                'acc_no' => $request->input('acc_no'),
                'ifsc_code' => $request->input('ifsc_code'),
                'bank_name' => $request->input('bank_name'),
                'branch' => $request->input('branch'),
                'photo' => $target_file,
                'pant' => $request->input('pant'),
                'shirt' => $request->input('shirt'),
                'shoes' => $request->input('shoes'),
                'scarf_tie' => $request->input('scarf_tie'),
                'socks' => $request->input('socks'),
                'belt' => $request->input('belt'),
                'waistcoat' => $request->input('waistcoat') 
                ]);    
            }
            else
            {
                Log::info('Add Employee process failed! File cant be Uploaded');
                return redirect()->back()->with('alert-danger','File cant be Uploaded')->withInput($request->all);
            }
            if($emp)
            {
                Log::info('Employee Added Successfully');  
                $emp_id = emp::max('id');
                $insertsalary = salary::create([
                    'emp_id' => $emp_id,
                    'salary' => $salary
                ]); 
                if($request->input('father_name')!='' || $request->input('mother_name')!='' || $request->input('spouse_name')!='')
                {
                    $child_array = array();
                    $father = json_encode(array('father_name'=>$request->input('father_name'),'father_dob'=>$request->input('father_dob'),'father_adhaar'=>$request->input('father_adhaar'),'father_place'=>$request->input('father_place')));
                    $mother = json_encode(array('mother_name'=>$request->input('mother_name'),'mother_dob'=>$request->input('mother_dob'),'mother_adhaar'=>$request->input('mother_adhaar'),'mother_place'=>$request->input('mother_place')));
                    $spouse = json_encode(array('spouse_name'=>$request->input('spouse_name'),'spouse_gender'=>$request->input('spouse_gender'),'spouse_dob'=>$request->input('spouse_dob'),'spouse_adhaar'=>$request->input('spouse_adhaar'),'spouse_place'=>$request->input('spouse_place')));
                    if($request->input('no_of_children')!=0 || $request->input('no_of_children')!='')
                    {
                        for($i=0;$i<$request->input('no_of_children');$i++)
                        {
                            $child = json_encode(array('child_name'=>$request->input('child_name')[$i],'child_gender'=>$request->input('child_gender')[$i],'child_dob'=>$request->input('child_dob')[$i],'child_adhaar'=>$request->input('child_adhaar')[$i],'child_place'=>$request->input('child_place')[$i]));

                            array_push($child_array, $child);
                        }
                    }

                    $family_insert = DB::table('family_detail')->insert(['father'=>$father,'mother'=>$mother,'spouse'=>$spouse,'children'=>json_encode($child_array)]);
                    
                }
                if($insertsalary)
                {
                    $sal_id = salary::max('id');
                    $update_sal_id = emp::where('id',$emp_id)->update(['salary_id'=>$sal_id]);
                    if($update_sal_id)
                        Log::info('Salary Added for Employee  with id '.$request->input('emp_id'));
                    else
                        Log::info('Salary ID not updated in Employee table for Employee-id '.$request->input('emp_id'));
                }
                else
                {
                   Log::info('Employee Salary not added');   
                }            
                $user = User::create([
                    'name' => $request->input('title').' '.$request->input('first_name').' '.$request->input('middle_name').' '.$request->input('last_name'),
                    'email' => $request->input('email'),
                    'password' => bcrypt($password),
                    'role_id' => $request->input('role')
                ]);
                if(!$user)
                {
                     Log::info('Employee not added to users');   
                }
                if($user && $insertsalary)
                {
                    Log::info('Employee Salary Added Successfully');   
                    Log::info('Employee Added to users Successfully');                        
                    return redirect()->back()->with('alert-success','Employee Added Successfully!');
                }
                else
                {
                    $msg='';
                    if(!$users)
                        $msg.='Login Details cannot be added ';
                    if(!$insertsalary)
                        $msg.='Employee Salary not added. ';
                    Log::error($msg);
                    return redirect()->back()->with('alert-danger',$msg);
                }
            }
            else
            {
                Log::info('Add Employee process failed!');
                unlink($target_file);
                return redirect()->back()->with('alert-danger','Add Employee process failed')->withInput($request->all);
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
            return redirect()->back()->with('alert-danger',$e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_emp_list(Request $request)
    {
        if(Session::get('username')!='' && (session('role')=='admin' || session('role')=='hr'  || session('role')=='hr admin'))
        {
            // if(session('role')!='admin')
            //     return redirect('dashboard')->with('alert-danger','Only Admin can access Employee list');
            Log::info('Fetching Employee List');
            try
            {
                if(session('role')=='hr')
                    $emps = emp::leftjoin('branch','emp.branch_location_id','=','branch.id')->select('branch.branch','emp.first_name','emp.middle_name','emp.last_name','emp.email','emp.mobile','emp.status','emp.id')->where('emp.branch_location_id',session('branch_location_id'))->get();
                else
                    $emps = emp::leftjoin('branch','emp.branch_location_id','=','branch.id')->select('branch.branch','emp.first_name','emp.middle_name','emp.last_name','emp.email','emp.mobile','emp.status','emp.id')->get();

                return view('emp-list',compact('emps'));
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return redirect()->back()->with('alert-danger','Database Query Error ['.$e->getMessage().' ]');
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return redirect()->back()->with('alert-danger',$e->getMessage());
            }
        }
        else
        {
            if(Session::get('username')=='')
                return redirect('/')->with('status',"Please login First");
            else
            return redirect('dashboard')->with('alert-danger',"Only admin and HR can see employee list");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\emp  $emp
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $id = $request->emp_id;
        Log::info('Trying to Delete Employee with ID '.$id.' ');
        try
        {
            $email_id = emp::select('email')->where('id',$id)->value('email');
            $delete = emp::where('id',$id)->delete();
            if($delete)
            {
                $delete_users = User::where('email',$email_id)->delete();
                if($delete_users)
                    return redirect()->back()->with('alert-success','Employee Deleted Successfully from Employee and Users Table');
                else
                    return redirect()->back()->with('alert-success','Employee Deleted Successfully from Employee Table but not from Users Table');
            }
            else
            {
                return redirect()->back()->with('alert-success','Employee Cant be Deleted!');
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\emp  $emp
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        if(Session::get('username')!='')
        {
        $id = $request->id;
        Log::info('Showing Edit employee page with employee id '.$id);
        try
        {
            $emps = emp::where('id',$id)->get();
            $family = DB::table('family_detail')->where('emp_id',$id)->get();
            if($family=='[]')
            {
                /*$family = json_encode(array('father'=>array('father_name'=>'','father_dob'=>'','father_adhaar'=>'','father_place'=>''),'mother'=>array('mother_name'=>'','mother_dob'=>'','mother_adhaar'=>'','mother_place'=>''),'spouse'=>array('spouse_name'=>'','spouse_gender'=>'','spouse_dob'=>'','spouse_adhaar'=>'','spouse_place'=>''),'child'=>array('child_name'=>'','child_gender'=>'','child_dob'=>'','child_adhaar'=>'','child_place'=>'')));*/
                $no_of_children = 0;
            }
            else
            {
                $no_of_children = sizeof(json_decode($family[0]->children,true));
            }
            $depts = DB::table('department')->get();
            $division = DB::table('division')->get();
            /*$depts = DB::table('department')->where('department_name','!=','Admin')->orWhere('department_name','!=','admin')->get();*/
            $email = emp::where('id',$id)->limit('1','1')->value('email'); 
            $emp_role_id = User::where('email',$email)->value('role_id'); 
            $dept_id = emp::select('department')->where('id',$id)->value('department');
            $desigs = DB::table('designation')->where('department',$dept_id)->get();
            $div_id = $emps[0]->division;
            if($div_id!='')                
            {
                $section = DB::table('section')->wherein('division_id',json_decode($div_id))->get();
            }
            else
            {
                $section = DB::table('section')->get();
            }
            $roles = DB::table('role')->get();
            //$logins = User::where('email',$email)->get();
            $salaries = DB::table('salary')->select('salary')->where('id',$emps[0]->salary_id)->orderBy('id','DESC')->limit('1','1')->value('salary');
            $branches = DB::table('branch')->get();
            $bank_list = DB::table('bank_list')->get();
            //return $salary;
            return view('edit-emp',compact('emps','depts','desigs','roles','salaries','emp_role_id','branches','bank_list','family','no_of_children','division','section'));
        }
        catch(QueryException $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger','Database Query Error! ['.$e->getMessage().' ]');
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',$e->getMessage());
        }
        }
        else
            return redirect('/')->with('status',"Please login First");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\emp  $emp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $file_uploaded = 0; 

        /*$i=0; $mydata=array(); $salary='';
        if($request->input('salary_type')!='') 
           { 
        foreach ($request->input('salary_type') as $salary_type) {
            $i++;
            $data[$salary_type]=$request->salary_value[$i];
        }
        $salary = json_encode(array('emp_salary'=>$data));
        }
        else
        {
           $salary = '';
        }*/
        //return $salary;
        if(Session::get('username')!='')
        {
        try
        {

            Log::info('Trying To update Employee with id '.$request->emp_id);
            $old_email = emp::select('email')->where('id',$request->emp_id)->value('email');
            $old_roleid = User::select('role_id')->where('email',$old_email)->value('role_id');
            $old_name = User::select('name')->where('email',$old_email)->value('name');
            // if(($old_email!=$request->input('email')) || ($request->input('password')!=''))
            // {
            //     return 1;
            // }
            // else
            // {
            //     return 0;
            // }
            $current_timestamp = Carbon::now()->timestamp;
            $msg='';
            $rules = array(
            'genesis_id'    =>  'nullable|string',
            'genesis_ledger_id'    =>  'required|string',
            'biometric_id'  =>  'nullable|string',
            'branch_location_id'    => 'required|string',
            'title' => 'required|string',
            'first_name'    => 'required|regex:/^[a-zA-Z ]+$/',
            'middle_name'    => 'nullable|regex:/^[a-zA-Z ]+$/',
            'last_name'    => 'nullable|regex:/^[a-zA-Z ]+$/',
            'email'    => 'required|email', 
            'blood_group'    => 'nullable|string',
            'dob'    => 'nullable|date|date_format:Y-m-d',
            'mobile'    => 'required|numeric|digits:10',
            'gender'    => 'nullable|string',
            'category' => 'nullable|string',
            'marital_status'    => 'nullable|string',
            'local_address'    => 'nullable|string',
            'adhaar_number'    => 'nullable|numeric|digits:12',
            'pan_number'    => 'nullable|string|max:10|min:10',
            'permanent_address'    => 'nullable|string',
            'distance_from_office'    => 'nullable|numeric',
            'emergency_call_person' => 'nullable|regex:/^[a-zA-Z ]+$/',
            'emergency_call_number' => 'nullable|numeric|digits:10',
            'father_name' => 'nullable|regex:/^[a-zA-Z ]+$/',
            'father_dob' => 'nullable|date_format:Y-m-d',
            'father_adhaar' => 'nullable|numeric|digits:12',
            'father_place' => 'nullable|string',
            'mother_name' => 'nullable|regex:/^[a-zA-Z ]+$/',
            'mother_dob' => 'nullable|date_format:Y-m-d',
            'mother_adhaar' => 'nullable|numeric|digits:12',
            'mother_place' => 'nullable|string',
            'spouse_name' => 'nullable|regex:/^[a-zA-Z ]+$/',
            'spouse_gender' => 'nullable|string',
            'spouse_dob' => 'nullable|date_format:Y-m-d',
            'spouse_adhaar' => 'nullable|numeric|digits:12',
            'spouse_place' => 'nullable|string',
            'no_of_children' => 'nullable|string',
            'child_name' => 'nullable',
            'child_gender' => 'nullable',
            'child_dob' => 'nullable',
            'child_adhaar' => 'nullable',
            'child_place' => 'nullable',
            'department'    => 'required|string',
            'designation'    => 'required|string',
            'out_source'    => 'required|string',
            'division'    => 'nullable|array',
            'section'    => 'nullable|array',
            'status'    => 'required|string',
            'esic_number' => 'nullable|numeric|digits:10',
            'epf_number' => 'nullable|string',
            'lin_number' => 'nullable|string',
            'uan_number' => 'nullable|numeric|digits:12',
            'esic_option' => 'nullable',
            'epf_option' => 'nullable',
            'reason_code_0'=>'nullable',
            'last_working_day'=>'nullable',
            'salary' => 'required|numeric',
            'basic' => 'nullable|numeric',
            'doj'    => 'required|date|date_format:Y-m-d',
            'acc_holder_name'    => 'required|string',
            'acc_no'    => 'required|numeric',
            'ifsc_code'    => 'required|string',
            'bank_name' => 'required|numeric',
            'branch'    => 'required|string',
            'login_email' => 'required|email',
            'password' => 'nullable|string',
            'role' => 'nullable|numeric',
            'photo'    => 'nullable|image|max:1024'
            );
            
            if($request->division=='')
            $division='';
            else
            $division=json_encode($request->input('division'));
            
            if($request->section=='')
            $section='';
            else
            $section=json_encode($request->input('section'));
            
            
            $desig = DB::table('designation')->where('id',$request->department)->value('designation');
            if(strtolower($desig)=="fashion consultant")
            {
                if($request->input('genesis_id')=='')
                {
                  return json_encode(array('status'=>'error','danger'=>"POSS id is mandatory for Employee of Fashion Consultant"));  
                }
            }
            if($request->input('epf_option')==1)
            {
                if($request->input('epf_number')=='')
                {
                     return json_encode(array('status'=>'error','danger'=>"EPF Number Required"));
                }
            }
            if($request->input('esic_option')==1)
            {
                if($request->input('esic_number')=='')
                {
                     return json_encode(array('status'=>'error','danger'=>"ESIC Number Required"));
                }
            }
        
            $acc_no_limit1 = DB::table('bank_list')->where('id',$request->bank_name)->value('acc_no_limit');
            if($acc_no_limit1!='')
            {
                $acc_no_limit = explode(',', $acc_no_limit1);
                $accno_len = strlen((string)$request->acc_no);
                if(!in_array($accno_len, $acc_no_limit))
                {
                    return json_encode(array('status'=>'error','danger'=>"Acc no must be of ".$acc_no_limit1." digits"));
                }
            }
            
            if($request->input('genesis_id')!='')
            {
                $check_unique = DB::table('emp')->where('id','!=',$request->emp_id)->where('genesis_id',$request->input('genesis_id'))->count();
                if($check_unique>0)
                {
                    $msg.=' POSS id already registered';
                }
            }
            if($request->input('genesis_ledger_id')!='')
            {
                $check_unique = DB::table('emp')->where('id','!=',$request->emp_id)->where('genesis_ledger_id',$request->input('genesis_ledger_id'))->count();
                if($check_unique>0)
                {
                    $msg.=' Genesis Ledger ID already registered';
                }
            }
            /*if($request->input('biometric_id')!='')
            {
                $check_unique = DB::table('emp')->where('id','!=',$request->emp_id)->where('biometric_id',$request->input('biometric_id'))->count();
                if($check_unique>0)
                {
                    $msg.=' Biometric ID already registered';
                }
            }*/
            if($request->input('pan_number')!='')
            {
                $check_unique = DB::table('emp')->where('id','!=',$request->emp_id)->where('pan_number',$request->input('pan_number'))->count();
                if($check_unique>0)
                {
                    $msg.=' Pan Number already registered';
                }
            }
            if($request->input('adhaar_number')!='')
            {
                $check_unique = DB::table('emp')->where('id','!=',$request->emp_id)->where('adhaar_number',$request->input('adhaar_number'))->count();
                if($check_unique>0)
                {
                    $msg.=' Adhaar Number already registered';
                }
            }
            
            $email = DB::table('emp')->where('email', $request->email)->where('id','!=',$request->emp_id)->value('email');
            $mobile = DB::table('emp')->where('mobile', $request->mobile)->where('id','!=',$request->emp_id)->value('mobile');
            if($email!='')
            {
                $msg.='Email-Id is already registered';
            }
            if($mobile!='')
            {
                $msg.=' Mobile number is already registered';
            }
            if($msg!='')
            {
                Log::info('Update Employee process failed! '.$msg.'');
                return json_encode(array('status'=>'error','danger'=>$msg));
            }

            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                Log::info('Update Employee process failed due to Validation Error');
               // return redirect()->back()->withErrors($validator)->withInput($request->all);
                return json_encode(array('status'=>'error','danger'=>$validator->messages()->toJson()));
            }

            


            $esic_option='';
            $epf_option='';
            if($request->input('esic_option')=='')
            {
                $esic_option=0;
            }
            else
            {
                $esic_option=$request->input('esic_option');
            }

            if($request->input('epf_option')=='')
            {
                $epf_option=0;
            }
            else
            {
                $epf_option=$request->input('epf_option');
            }
            if($epf_option=='1')
            {
                if($request->basic=='' ||$request->basic=='0')
                {
                    return json_encode(array('status'=>'error','danger'=>'Basic + DA required'));
                }
                else
                {
                    $basic= $request->basic;
                }
            }
            else
            {
                $basic = 0;
            }

            $salary = array();
            $salary['salary'] = $request->salary;
            $salary['basic'] = $basic;
            $salary = json_encode(array('emp_salary'=>$salary));
            

            if($request->photo!='')
            {
                $target_dir = 'uploads/';
                $file = $request->file('photo');
                $extension = strtolower($file->getClientOriginalExtension()); 
                $filename = $request->input('first_name').'_'.$request->input('middle_name').'_'.$request->input('last_name').'_'.$request->input('mobile').'_'.$current_timestamp.'.'.$extension;
                $target_file = $target_dir.$filename;

                if($extension!='png' && $extension!='jpg' && $extension!='jpeg')
                {
                    Log::info('Update Employee process failed! Require file in png/jpg fromat');
                    return json_encode(array('status'=>'error','danger'=>'Please Upload png/jpg file only'));
                }
                 $file_uploaded = $file->move(base_path().'/uploads/', $filename);
                if($file_uploaded)
                { 
                    $emp = emp::where('id',$request->input('emp_id'))->update([
                    'genesis_id'    =>  $request->input('genesis_id'),
                    'genesis_ledger_id'    =>  $request->input('genesis_ledger_id'),
                    'biometric_id'  =>  $request->input('biometric_id'),
                    'branch_location_id'  =>  $request->input('branch_location_id'),
                    'title' => $request->input('title'),
                    'first_name' => $request->input('first_name'),
                    'middle_name' => $request->input('middle_name'),
                    'last_name' => $request->input('last_name'),
                    'email' => $request->input('email'),
                    'dob' => $request->input('dob'),
                    'mobile' => $request->input('mobile'),
                    'blood_group' => $request->input('blood_group'),
                    'marital_status' => $request->input('marital_status'),
                    'gender' => $request->input('gender'),
                    'category' => $request->input('category'),
                    'adhaar_number' =>$request->input('adhaar_number'),
                    'pan_number' => $request->input('pan_number'),
                    'local_address' => $request->input('local_address'),                    
                    'permanent_address' => $request->input('permanent_address'),
                    'distance_from_office' => $request->input('distance_from_office'),
                    'emergency_call_person' => $request->input('emergency_call_person'),
                    'emergency_call_number' => $request->input('emergency_call_number'),
                    'department' => $request->input('department'),                    
                    'designation' => $request->input('designation'),
                    'out_source'=>$request->input('out_source'),
                    'division'=>$division,
                    'section'=>$section,
                    'doj' => $request->input('doj'),
                    'status' => $request->input('status'),
                    'esic_number' => $request->input('esic_number'),
                    'epf_number' => $request->input('epf_number'),
                    'lin_number' => $request->input('lin_number'),
                    'uan_number' => $request->input('uan_number'),
                    'esic_option' => $esic_option,
                    'epf_option' => $epf_option,
                    'reason_code_0'=>$request->input('reason_code_0'),
                    'last_working_day'=>$request->input('last_working_day'),
                    'acc_holder_name' => $request->input('acc_holder_name'),
                    'acc_no' => $request->input('acc_no'),
                    'ifsc_code' => $request->input('ifsc_code'),
                    'bank_name' => $request->input('bank_name'),
                    'branch' => $request->input('branch'),
                    'photo' => $target_file,
                    'pant' => $request->input('pant'),
                    'shirt' => $request->input('shirt'),
                    'shoes' => $request->input('shoes'),
                    'scarf_tie' => $request->input('scarf_tie'),
                    'socks' => $request->input('socks'),
                    'belt' => $request->input('belt'),
                    'waistcoat' => $request->input('waistcoat'), 
                    'updated_at'=>Carbon::now()
                    ]);
                                   
                }
                else
                {
                    Log::info('Update Employee process failed! File cant be Uploaded');
                    return json_encode(array('status'=>'error','danger'=>'File cant be Uploaded'));
                }

            }
            else
            { 
                $emp = emp::where('id',$request->input('emp_id'))->update([
                    'genesis_id'    =>  $request->input('genesis_id'),
                    'genesis_ledger_id'    =>  $request->input('genesis_ledger_id'),
                    'biometric_id'  =>  $request->input('biometric_id'),
                    'branch_location_id'  =>  $request->input('branch_location_id'),
                    'title' => $request->input('title'),
                    'first_name' => $request->input('first_name'),
                    'middle_name' => $request->input('middle_name'),
                    'last_name' => $request->input('last_name'),
                    'email' => $request->input('email'),
                    'dob' => $request->input('dob'),
                    'mobile' => $request->input('mobile'),
                    'blood_group' => $request->input('blood_group'),
                    'marital_status' => $request->input('marital_status'),
                    'gender' => $request->input('gender'),
                    'category' => $request->input('category'),
                    'adhaar_number' =>$request->input('adhaar_number'),
                    'pan_number' => $request->input('pan_number'),
                    'local_address' => $request->input('local_address'),                    
                    'permanent_address' => $request->input('permanent_address'),
                    'distance_from_office' => $request->input('distance_from_office'),
                    'emergency_call_person' => $request->input('emergency_call_person'),
                    'emergency_call_number' => $request->input('emergency_call_number'),
                    'department' => $request->input('department'),                    
                    'designation' => $request->input('designation'),
                    'out_source'=>$request->input('out_source'),
                    'division'=>$division,
                    'section'=>$section,
                    'doj' => $request->input('doj'),
                    'status' => $request->input('status'),
                    'esic_number' => $request->input('esic_number'),
                    'epf_number' => $request->input('epf_number'),
                    'lin_number' => $request->input('lin_number'),
                    'uan_number' => $request->input('uan_number'),
                    'esic_option' => $esic_option,
                    'epf_option' => $epf_option,
                    'reason_code_0'=>$request->input('reason_code_0'),
                    'last_working_day'=>$request->input('last_working_day'),
                    'acc_holder_name' => $request->input('acc_holder_name'),
                    'acc_no' => $request->input('acc_no'),
                    'ifsc_code' => $request->input('ifsc_code'),
                    'bank_name' => $request->input('bank_name'),
                    'branch' => $request->input('branch'),
                    'pant' => $request->input('pant'),
                    'shirt' => $request->input('shirt'),
                    'shoes' => $request->input('shoes'),
                    'scarf_tie' => $request->input('scarf_tie'),
                    'socks' => $request->input('socks'),
                    'belt' => $request->input('belt'),
                    'waistcoat' => $request->input('waistcoat')  
                ]);
            }

            if($emp)
            {
                Log::info('Employee with id '.$request->input('emp_id').' Updated Successfully');

                if($request->input('father_name')!='' || $request->input('mother_name')!='' || $request->input('spouse_name')!='')
                {
                    $child_array = array();
                    $father = json_encode(array('father_name'=>$request->input('father_name'),'father_dob'=>$request->input('father_dob'),'father_adhaar'=>$request->input('father_adhaar'),'father_place'=>$request->input('father_place')));
                    $mother = json_encode(array('mother_name'=>$request->input('mother_name'),'mother_dob'=>$request->input('mother_dob'),'mother_adhaar'=>$request->input('mother_adhaar'),'mother_place'=>$request->input('mother_place')));
                    $spouse = json_encode(array('spouse_name'=>$request->input('spouse_name'),'spouse_gender'=>$request->input('spouse_gender'),'spouse_dob'=>$request->input('spouse_dob'),'spouse_adhaar'=>$request->input('spouse_adhaar'),'spouse_place'=>$request->input('spouse_place')));
                    if($request->input('no_of_children')!=0 || $request->input('no_of_children')!='')
                    {
                        for($i=0;$i<$request->input('no_of_children');$i++)
                        {
                            $child = json_encode(array('child_name'=>$request->input('child_name')[$i],'child_gender'=>$request->input('child_gender')[$i],'child_dob'=>$request->input('child_dob')[$i],'child_adhaar'=>$request->input('child_adhaar')[$i],'child_place'=>$request->input('child_place')[$i]));

                            array_push($child_array, $child);
                        }
                    }
                    $family_id = DB::table('family_detail')->where('emp_id',$request->input('emp_id'))->value('id');
                    if($family_id=='')
                        $family_update = DB::table('family_detail')->insert(['emp_id'=>$request->input('emp_id'),'father'=>$father,'mother'=>$mother,'spouse'=>$spouse,'children'=>json_encode($child_array)]);  
                    else
                        $family_update = DB::table('family_detail')->where('emp_id',$request->input('emp_id'))->update(['father'=>$father,'mother'=>$mother,'spouse'=>$spouse,'children'=>json_encode($child_array)]);                    
                }
                $old_salary_id = emp::where('id',$request->input('emp_id'))->value('salary_id');
                $old_sal = DB::table('salary')->select('salary')->where('id',$old_salary_id)->orderBy('id','desc')->limit('1','1')->value('salary');
                $salary_cmp = strcasecmp($salary, $old_sal);
                if($salary_cmp!=0)
                {
                    $sal_update = salary::create(['emp_id'=>$request->input('emp_id'),'salary'=>$salary]);
                    if($sal_update)
                    {
                        $sal_id = salary::max('id');
                        $update_sal_id = emp::where('id',$request->input('emp_id'))->update(['salary_id'=>$sal_id]);
                        if($update_sal_id)
                        Log::info('Salary Updated for Employee with id '.$request->input('emp_id'));
                        else
                        {
                            salary::where('id',$sal_id)->delete();
                            Log::info('Salary Updation failed for Employee with id '.$request->input('emp_id'));
                        }
                    }
                    else
                    {
                        Log::info('Salary Updation failed for Employee with id '.$request->input('emp_id'));
                    }
                }
                
                $new_name = $request->input('title').' '.$request->input('first_name').' '.$request->input('middle_name').' '.$request->input('last_name');
                if($old_email!=$request->input('email') || $request->input('password')!='' || ($old_name!=$new_name) || ($old_roleid!=$request->input('role')))
                {
                    if($request->input('password')!='')
                    {
                        $user = User::where('email',$old_email)->update([
                            'name' => $request->input('title').' '.$request->input('first_name').' '.$request->input('middle_name').' '.$request->input('last_name'),
                            'email' => $request->input('email'),
                            'password' => bcrypt($request->input('password')),
                            'role_id' => $request->input('role')
                        ]);
                        if($user)
                        {
                            Log::info('Employee with email '.$old_email.' Updated to email '.$request->input('email').' and password changed to '.$request->input('password').' in users table!');                        
                            return json_encode(array('status'=>'success','success'=>'Employee Updated Successfully!'));
                        }
                        else
                        {
                            Log::info('Employee with email '.$old_email.' cant be updated in users table');
                            
                            return json_encode(array('status'=>'error','danger'=>'Login Details cannot be added'));
                        }
                    }
                    else
                    {
                        $user = User::where('email',$old_email)->update([
                            'name' => $request->input('title').' '.$request->input('first_name').' '.$request->input('middle_name').' '.$request->input('last_name'),
                            'email' => $request->input('email'),
                            'role_id' => $request->input('role')
                        ]);
                        if($user)
                        {
                            Log::info('Employee with email '.$old_email.' Updated to email '.$request->input('email').' in users table!');                        
                            return json_encode(array('status'=>'success','success'=>'Employee Updated Successfully!'));
                        }
                        else
                        {
                            Log::info('Employee with email '.$old_email.' cant be updated in users table');
                            
                            return json_encode(array('status'=>'error','danger'=>'Login Details cannot be added'));
                        }

                    }
                }
                else
                {
                    Log::info('Employee with email '.$old_email.' Updated Successfully');                        
                    return json_encode(array('status'=>'success','success'=>'Employee Updated Successfully!'));
                }
                
            }
            else
            {
                Log::info('Update Employee process failed!');
                if($file_uploaded)unlink($target_file);
                return json_encode(array('status'=>'error','danger'=>'File cant be Uploaded'));
            } 

        }
        catch(QueryException $e)
        {
            Log::error($e->getMessage());
            return json_encode(array('status'=>'error','danger'=>"Database Query Error! [ ".$e->getMessage()." ]"));
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return json_encode(array('status'=>'error','danger'=>$e->getMessage()));
        }
        }
        else
            return redirect('/')->with('status',"Please login First");
    }

    public function getDetails(Request $request)
    {
         
       
        //$user_id = $request->id;
       //$user_data = emp::where('id', $user_id)->get();
        $user_id = $request->id;
        $user_data= emp::where('emp.id', $user_id)
                            ->join('department','emp.department', '=' , 'department.id')
                            ->join('designation','emp.designation', '=' , 'designation.id')
                            ->join('branch','emp.branch_location_id', '=' , 'branch.id')
                            ->join('salary','emp.salary_id','=','salary.id')
                            ->select('emp.id','emp.title','emp.first_name','emp.middle_name','emp.last_name','emp.blood_group','emp.photo','emp.dob','emp.mobile','emp.email','emp.marital_status','emp.gender','emp.category','emp.adhaar_number','emp.pan_number','emp.local_address','emp.permanent_address','emp.distance_from_office','emp.emergency_call_person','emp.emergency_call_number','branch.branch as branch_location_name','emp.genesis_id','emp.genesis_ledger_id','emp.biometric_id','emp.epf_number','emp.esic_number','emp.lin_number','emp.uan_number','emp.reason_code_0','emp.last_working_day','emp.doj','emp.ifsc_code','emp.status','emp.esic_option','emp.epf_option','salary.salary','emp.acc_holder_name','emp.acc_no','emp.bank_name','emp.branch','department.department_name','designation.designation')
                            ->get();
        return response()->json($user_data);
        
    }
    
    public function get_emp_search(Request $request)
    {
        $emp_name = emp::select('title','first_name','middle_name','last_name','id')->get();
        return view('emp-search',compact('emp_name'));
    }

    public function get_emp_upload(Request $request)
    {
        $branches = DB::table('branch')->get();
        return view('upload-emp',compact('branches'));
    }

    public function get_emp_details(Request $request)
    {

        if(Session::get('username')!='')
        {
        $id = $request->id;
        Log::info('Showing Edit employee page with employee id '.$id);
        try
        {
            $emps = emp::where('id',$id)->get();
            
            $depts = DB::table('department')->get();
            /*$depts = DB::table('department')->where('department_name','!=','Admin')->orWhere('department_name','!=','admin')->get();*/
            $email = emp::where('id',$id)->limit('1','1')->value('email'); 
            $emp_role_id = User::where('email',$email)->value('role_id'); 
            $dept_id = emp::select('department')->where('id',$id)->value('department');
            $desigs = DB::table('designation')->where('department',$dept_id)->get();
            $roles = DB::table('role')->get();
            $salary_id = emp::where('id',$id)->value('salary_id');
            $salaries = DB::table('salary')->select('salary')->where('id',$salary_id)->orderBy('id','DESC')->limit('1','1')->value('salary');
            $branches = DB::table('branch')->get();
            //return $salary;
            return view('editable-emp',compact('emps','depts','desigs','roles','salaries','emp_role_id','branches'));
        }
        catch(QueryException $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger','Database Query Error! ['.$e->getMessage().' ]');
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',$e->getMessage());
        }
        }
        else
            return redirect('/')->with('status',"Please login First");


        $id = $request->id;
        
        $emps = emp::where('id',$id)->get();
        
        $output='';
        foreach ($emps as $emp) {
                $output.= '<div class="row clearfix">
                <div class="col-md-12">
                                    <h4 class="">
                                        Personal Details
                                    </h4>

                                    <div class="col-md-6">
                                                    <div class="row clearfix">
                                        <div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="name">First Name</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                
                                                   '.$emp->first_name.' 
                                                    
                                            </div>
                                        </div>
                                        </div>
                                        <div class="row clearfix">
                                        <div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="name">Middle Name</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                
                                                   '.$emp->middle_name.'
                                            </div>
                                        </div>
                                        </div>
                                        <div class="row clearfix">
                                        <div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="name">Last Name</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                
                                                 '.$emp->last_name.'
                                                    
                                            </div>
                                        </div>
                                        </div>
                                        <div class="row clearfix"><div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="blood_group">Blood Group</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                               
                                                  '.$emp->blood_group.'
                                            </div>
                                        </div>
                                        </div>
                                        <div class="row clearfix"><div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="email">Email</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                               '.$emp->email.'
                                                    
                                            </div>
                                        </div>
                                        </div>
                                        <div class="row clearfix"><div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="dob">Date Of Birth</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                '.date('Y-m-d',strtotime($emp->dob)).'
                                            </div>
                                        </div>
                                        </div>
                                        <div class="row clearfix"><div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="mobile">Mobile</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                 '.$emp->mobile.'
                                                   
                                            </div>
                                        </div>
                                        </div>
                                        <div class="row clearfix"><div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="gender">Gender</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                
                                                       ';
                                                       if($emp->gender == 'female')
                                                           $output.='Female';
                                                        elseif($emp->gender == 'male')
                                                        $output.='Male';                                                  
                                                   
                                            $output.='</div>
                                        </div>
                                        </div>

                                        <div class="row clearfix"><div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="category">Category</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">';
                                                
                                                        if($emp->category == 'GENERAL')
                                                            $output.='General';                                                        
                                                        if($emp->category == 'OBC')
                                                            $output.='OBC';
                                                       
                                                        if($emp->category == 'ST/SC')
                                                            $output.='ST/SC';
                                                    
                                                        if($emp->category == 'Other')
                                                            $output.='Other';
                                                        
                                           $output.=' </div>
                                        </div>
                                        </div>

                                        <div class="row clearfix"><div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="marital_status">Marital Status</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">';
                                                
                                                if($emp->marital_status == 'married')
                                                    $output.='married';
                                                
                                                if($emp->marital_status == 'single')
                                                    $output.='single';
                                               
                                                if($emp->marital_status == 'other')
                                                    $output.='other';
                                            
                                                       
                                            $output.='</div>
                                        </div>                                      
                                    </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row clearfix"><div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="local_address">Adhaar Number</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                '.$emp->adhaar_number.'
                                            </div>
                                        </div>
                                        </div>

                                        <div class="row clearfix"><div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="local_address">PAN Number</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                '.$emp->pan_number.'
                                            </div>
                                        </div>
                                        </div>

                                        <div class="row clearfix">
                                        <div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="local_address">Local Address</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                               '.$emp->local_address.'
                                            </div>
                                        </div>
                                        </div>
                                       
                                        <div class="row clearfix"><div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="permanent_address">Permanent Address</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                '.$emp->permanent_address.'
                                            </div>
                                        </div>  
                                        </div>
                                        
                                        <div class="row clearfix"><div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="distance_from_office">Distance from office</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                '.$emp->distance_from_office.'
                                            </div>
                                        </div>  
                                        </div>
                                        
                                        <div class="row clearfix">                              
                                        <div class="form-group">
                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <label for="photo">Photograph</label>
                                            </div>
                                            <div class="col-md-8 col-sm-6 col-xs-12">
                                                <img src="'.$emp->photo.'">
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                     
                                        <label><h4><u>On Emergency Contact To</u></h4></label>
                                        
                                            <div class="form-group">
                                                <div class="col-md-2 col-sm-6 col-xs-12">
                                                    <label for="emergency_call_person">Person Name</label>
                                                </div>
                                                <div class="col-md-4 col-sm-6 col-xs-12">
                                                    '.$emp->emergency_call_person .'
                                                </div>
                                            
                                                <div class="col-md-2 col-sm-6 col-xs-12">
                                                    <label for="emergency_call_number">Contact Number</label>
                                                </div>
                                                <div class="col-md-4 col-sm-6 col-xs-12">
                                                     '.$emp->emergency_call_number.'
                                                </div>
                                           
                                        </div>
                                    </div>
                                    </div>

                                    <div class="row clearfix">
                                        <div class="col-md-12">
                                        <h4>
                                            Company Details
                                        </h4>

                                        <div class="col-md-6">
                                                <div class="row clearfix">                                       
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <label for="genesis_id">POSS id</label>
                                                        </div>
                                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                                           '.$emp->genesis_id.'
                                                        
                                                    </div>
                                                    </div>

                                                    <div class="row clearfix">
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <label for="branch_location_id">Branch</label>
                                                        </div>
                                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                                          ';
                                                        $branch_name = DB::table('branch')->select('branch')->where('id',$emp->branch_location_id)->value('branch');

                                                          $output.=$branch_name;
                                                               
                                                        $output.='</div>
                                                    
                                                    </div>


                                                    <div class="row clearfix">
                                                    
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <label for="department">Department</label>
                                                        </div>
                                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                                           ';
                         $department = DB::table('department')->select('department_name')->where('id',$emp->department)->value('department_name');

                        $output.=$department;
                                                                
                                                       $output.='</div>
                                                    
                                                    </div>
                                                    <div class="row clearfix">
                                                    
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <label for="designation">Designation</label>
                                                        </div>
                                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                                            ';
                                                 $designation= DB::table('designation')->select('designation')->where('id',$emp->designation)->value('designation');
                                                $output.=$designation;
                                                                
                                                     $output.='</div>
                                                                                  
                                                </div>

                                                <div class="row clearfix">
                                                    
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <label for="doj">Date Of Joining</label>
                                                        </div>
                                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                                            '.date('Y-m-d',strtotime($emp->doj)).'
                                                        </div>
                                                    
                                                    </div>

                                                <div class="row clearfix">
                                                   
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <label for="status">Status</label>
                                                        </div>
                                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                                            ';
                                                        if($emp->status == 'active')
                                                           $output.='Active';
                                                        

                                                        if($emp->status == 'inactive')
                                                           $output.='Inactive';

                                                       $output.='</div>
                                                   
                                                </div>

                                                </div>
                                                <div class="col-md-6">

                                                    <div class="row clearfix">                                      
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <label for="emplayee_id">Biometric Id</label>
                                                        </div>
                                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                                            '.$emp->biometric_id.'
                                                        </div>
                                                    
                                                    </div>

                                                    <div class="row clearfix">
                                                    
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <label for="esic_number">ESIC Number</label>
                                                        </div>
                                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                                            '.$emp->esic_number.'
                                                        </div>
                                                    
                                                </div>
                                                <div class="row clearfix">
                                                   
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <label for="epf_number">EPF Number</label>
                                                        </div>
                                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                                            '.$emp->epf_numbe.'
                                                        </div>
                                                    
                                                </div>

                                                <div class="row clearfix">
                                                    
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <label for="lin_number">LIN Number</label>
                                                        </div>
                                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                                            '.$emp->lin_number.'
                                                        </div>
                                                    
                                                </div>

                                                <div class="row clearfix">
                                                    
                                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                                            <label for="uan_number">UAN Number</label>
                                                        </div>
                                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                                            '.$emp->uan_number.'
                                                        </div>
                                                    
                                                </div>

                                                    
                                            </div>

                                    </div>
                                    </div>

                                <div class="row clearfix">
                                <div class="col-md-6">
                                <h4 class="">
                                 Salary Details
                                </h4>

                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                      ESIC : '; if($emp->esic_option=='1') $output.='Active'; else $output.='Inactive'; 

                                        
                                        $output.='
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        EPF : '; if($emp->epf_option=='1') $output.='Active';  else $output.='Inactive'; 
                                    $output.='</div>
                                    <table class="table table-stripped">
                                                    <tbody id="salary_body" name="salary_body">';

                                    $salaries = DB::table('salary')->where('id',$emp->salary_id)->value('salary');
                                 $i=0;
                                $salary = (array) json_decode($salaries,true);
                                if($salaries!='') 
                                {
                                
                                foreach($salary['emp_salary'] as $salary_type=>$salary_value){
                                 $i++; 
                                   $output.=' <tr>
                                        <td>
                                             '.$salary_type.'
                                        </td>
                                        <td>
                                            '.$salary_value.'
                                        </td>
                                        
                                    </tr>';
                                        }
                                 }   
                                $output.='</tbody>
                            </table>

                             </div>
                         </div>';

        }
        return $output;
    }

  public function get_file_form()
    {
        if(Session::get('username')!='' && (session('role')=='admin' || session('role')=='hr'  || session('role')=='hr admin'))
        {
            $department = DB::table('department')->get();
            $file_type = DB::table('document_type')->get();
            if(session('role')=='hr')
                $branch = DB::table('branch')->where('id',session('branch_location_id'))->get();
            else
                $branch = DB::table('branch')->get();
            $document = DB::table('upload_docs')
            ->leftjoin('department','department.id','=','upload_docs.department')
            ->leftjoin('branch','branch.id','=','upload_docs.branch')
            ->leftjoin('emp','emp.id','=','upload_docs.emp_id')
            ->select('upload_docs.id','upload_docs.title as heading','upload_docs.file','upload_docs.branch','upload_docs.created_at',
            'department.department_name','branch.branch','emp.title','emp.first_name','emp.middle_name','emp.last_name')->where('upload_docs.status','active')->get();
            $doc = DB::table('emp_doc')
            ->join('emp','emp_doc.emp_id','=','emp.id')
            ->join('users','emp_doc.uploaded_by','=','users.id')
            ->join('document_type','emp_doc.document_name','=','document_type.id')
            ->select('users.id','users.email as user_email','emp.email as emp_email','emp_doc.document_name','emp_doc.emp_id','emp_doc.uploaded_by','emp_doc.path','users.name','emp_doc.verify','emp_doc.created_at','emp_doc.status','document_type.name as document')->orderBy('emp_doc.id','DESC')->get();
            Log::info('Fetching Upload File Page');
           
            return view('emp-doc',compact('department','document','branch','file_type','doc'));
        }
        else
        {
            if(Session::get('username')=='')
                return redirect('/')->with('status',"Please login First");
            else
            return redirect('dashboard')->with('alert-danger',"Only admin and HR can access upload file page");
        }
    }

    public function get_emp(Request $request)
    {
       if(Session::get('username')!='') 
       {
            $branch = $request->branch;
            $employee=DB::table('emp')
                    ->where('branch_location_id',$branch)
                    ->get();
            $data = '<option></option>';
            foreach($employee as $key=>$emp)
            {
            $data.='<option value="'.$emp->id.'">'.$emp->title.' '. $emp->first_name.' '. $emp->middle_name.' '.$emp->last_name.'</option>';
            }
            return $data;
       }
       else
       {
            return redirect('/')->with('status',"Please login First");
       }
       
    }
    public function getemp(Request $request)
    {
       if(Session::get('username')!='') 
       {
            $branch = $request->branch;
            $department = $request->department;
            $employee=DB::table('emp')
                    ->where('branch_location_id',$branch)
                    ->where('department',$department)
                    ->get();
            $data = '<option></option>';
            foreach($employee as $key=>$emp)
            {
            $data.='<option value="'.$emp->id.'">'.$emp->title.' '. $emp->first_name.' '. $emp->middle_name.' '.$emp->last_name.'</option>';
            }
            return $data;
       }
       else
       {
            return redirect('/')->with('status',"Please login First");
       }
    }
    public function upload_file(Request $request)
    {
        if(Session::get('username')!='' && (session('role')=='admin' || session('role')=='hr' || session('role')=='hr admin'))
        {
            try
            {
                Log::info('Trying to Upload file');
                 $rules = array(
                'file_type'    =>  'required|numeric',
                'file' =>     'required|file|mimes:jpeg,png,jpg,pdf,docx,doc,xls,xlsx,csv|max:1024',
                'branch' => 'required|numeric',
                 'department' => 'nullable|numeric',
                 'employee' => 'nullable|numeric',
     );
                $validator = Validator::make(Input::all(), $rules);
                if ($validator->fails()) {
                    Log::info('upload process failed due to Validation Error');
                    return redirect()->back()->withErrors($validator)->withInput($request->all);
                }

                $file = $request->file;
                $upload_type = $request->input('file_type');
                $branch = $request->input('branch');
                $department  = $request->input('department');
                $employee  = $request->input('employee');
                if($request->has('file'))
                {
                    $folder=base_path().'/personal_doc/'.$employee;
                    if (!File::exists($folder)) {
                        File::makeDirectory($folder, 0775, true, true);
                    }
                  
                    $target_dir = 'personal_doc/'.$employee.'/';
                    $filename = strtolower($file->getClientOriginalName()); 
                    $extension = strtolower($file->getClientOriginalExtension());
                    $file_name = pathinfo($filename, PATHINFO_FILENAME);
                    $filename = $upload_type.'_'.$employee.'_'.strtotime(date('Y-m-d H:i:s')).'.'.$extension;
                    $target_file = $target_dir.$filename;
                    if($file->move(base_path().'/personal_doc/'.$employee.'/', $filename))
                    {
                        Log::info('Upload file process Successful for user with id '.session('user_id'));
                        $add_to_upload = DB::table('emp_doc')->insert(['uploaded_by' => session('user_id'),'document_name'=>$upload_type,'emp_id'=>$employee,'path'=>$target_file,'created_at'=>now(),'status'=>'Success','verify'=>'Verified']);
                        if($add_to_upload)
                         return redirect()->back()->with('alert-success',"Uploaded Successfully");
                        else
                        return redirect()->back()->with('alert-danger',"upload process not updated to Database");
                    }
                    else
                    {
                        Log::info('Upload file process failed for user with id '.session('user_id'));
                        return redirect()->back()->with('alert-danger',"Upload Process Failed");
                    }
                }
                else
                {
                    return redirect()->back()->with('alert-danger','Request data does not have any files to import.');
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
        else
        {
            if(Session::get('username')=='')
                return redirect('/')->with('status',"Please login First");
            else
            return redirect('dashboard')->with('alert-danger',"Only admin and HR can access upload file page");
        }
    }
    

    
    public function get_doc_type()
    {

        if(Session::get('username')!='')
        {
          
          
            $file_type = DB::table('document_type')->get();
            
            $doc = DB::table('emp_doc')
            ->join('emp','emp_doc.emp_id','=','emp.id')
            ->join('users','emp_doc.uploaded_by','=','users.id')
            ->join('document_type','emp_doc.document_name','=','document_type.id')
            ->select('users.id','users.email as user_email','emp.email as emp_email','emp_doc.document_name','emp_doc.emp_id','emp_doc.uploaded_by','emp_doc.path','users.name','emp_doc.verify','emp_doc.created_at','emp_doc.status','document_type.name as document')->orderBy('emp_doc.id','DESC')->get();
          
           
            Log::info('Fetching Upload File Page');
           
            return view('mobile_upload_doc',compact('file_type','doc'));
        }
        else
        {
            if(Session::get('username')=='')
                return redirect('/')->with('status',"Please login First");
            else
            return redirect('dashboard')->with('alert-danger',"Only admin and HR can access upload file page");
        }
    }
    public function upload_file_emp(Request $request)
    {
        if(Session::get('username')!='')
            {
        try
        {
            
        
                Log::info('User ID '.session('user_id').' Trying To upload file');
                 $rules = array(
                'file_type'    =>  'required|numeric',
                'file' =>     'required|file|mimes:jpeg,png,jpg,pdf,docx,doc,xls,xlsx,csv|max:1024',
                
                     );
                $validator = Validator::make(Input::all(), $rules);
                if ($validator->fails()) {
                    Log::info('upload process failed due to Validation Error');
                    return redirect()->back()->withErrors($validator)->withInput($request->all);
                }

                $file = $request->file;
                $upload_type = $request->input('file_type');
                $useremail = Session::get('useremail');
                $emp_id=DB::table('emp')->where('email',$useremail)->value('id');
                $branch =DB::table('emp')->where('id', $emp_id)->value('branch_location_id');
                $department  =DB:: table('emp')->where('id', $emp_id)->value('department');
                $employee  =$emp_id;
                if($request->has('file'))
                {
                    $folder=base_path().'/personal_doc/'.$employee;
                    if (!File::exists($folder)) {
                        File::makeDirectory($folder, 0775, true, true);
                    }
                  
                    $target_dir = 'personal_doc/'.$employee.'/';
                    $filename = strtolower($file->getClientOriginalName()); 
                    $extension = strtolower($file->getClientOriginalExtension());
                    $file_name = pathinfo($filename, PATHINFO_FILENAME);
                    $filename = $upload_type.'_'.$employee.'_'.strtotime(date('Y-m-d H:i:s')).'.'.$extension;
                    $target_file = $target_dir.$filename;
                    if($file->move(base_path().'/personal_doc/'.$employee.'/', $filename))
                    {
                        Log::info('Upload file process Successful for user with id '.session('user_id'));
                        $add_to_upload = DB::table('emp_doc')->insert(['uploaded_by' => session('user_id'),'document_name'=>$upload_type,'emp_id'=>$employee,'path'=>$target_file,'created_at'=>now(),'status'=>'Pending','verify'=>'Pending']);
                        if($add_to_upload)
                         return redirect()->back()->with('alert-success',"Uploaded Successfully");
                        else
                        return redirect()->back()->with('alert-danger',"upload process not updated to Database");
                    }
                    else
                    {
                        Log::info('Upload file process failed for user with id '.session('user_id'));
                        return redirect()->back()->with('alert-danger',"Upload Process Failed");
                    }
                }
                else
                {
                    return redirect()->back()->with('alert-danger','Request data does not have any files to import.');
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
        else
        {
            if(Session::get('username')=='')
                return redirect('/')->with('status',"Please login First");
            else
            return redirect('dashboard')->with('alert-danger',"Only admin and HR can access upload file page");
        }
    }
    public function show_doc_table()
    {
      
        try
        {
            if(Session::get('username')!='')
            {  
                $file_type = DB::table('document_type')->get();
              
                $doc = DB::table('emp_doc')
                ->join('emp','emp_doc.emp_id','=','emp.id')
                ->join('users','emp_doc.uploaded_by','=','users.id')->select('users.id','emp_doc.emp_id','emp_doc.document_name','emp_doc.uploaded_by','emp_doc.path','users.name','emp_doc.verify')->orderBy('emp_doc.id','DESC')->get();
               $doc=array('a','b');
                return view('mobile_upload_doc',compact('doc','file_type'));
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
    public function update_verify(Request $request)
    {
        
        try
        {
            if(Session::get('username')!='')
            {
                $id=$request->id;
                $verify = $request->verify;

                Log::info(' Updating  Verify column of emp_doc table');

                $emp_doc=DB::table('emp_doc')
                  ->where('id', $id)
                  ->update(['verify' => $verify,'updated_at'=>Carbon::now()]);
        
                if($emp_doc)
                {
                    Log::info('emp_doc with id '.$id.' and verify '.$verify.' Updated Successfully');
                    $request->session()->flash('success', 'Uploaded Successfully');
                    return redirect()->back();
                }
                else
                {
                    Log::info('emp_doc with id '.$id.' and verify '.$verify.' cannot be updated');
                    $request->session()->flash('alert-danger', 'Employee Document cannot be updated!');
                    return redirect()->back();
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

    public function delete_emp_doc(Request $request)
    { 
       
        try
        {
            if(Session::get('username')!='')
            {
          $id=$request->id;
          $image_path =$request->path;
            if($image_path!='')
            {
                if(file_exists($image_path)) {
                  File::delete($image_path);
                }
                
            }   
            $delete_resume = DB::table('emp_doc')->where('id',$id)->delete();
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

    public function add_doc_type(Request $request)
    {        
        try
        {
            if(Session::get('username')!='')
            {
                Log::info('Trying to create Document Type');
                $doc_type = $request->doc_type;
                
                $doc_type=DB::table('document_type')->insert(
                ['name'=>$doc_type,'created_at'=>now()]
                );
                $doc=DB::table('document_type')->get();
                $data = '<option></option>';
                foreach($doc as $key=>$docs)
                {
                $data.='<option value="'.$docs->id.'">'.$docs->name.'</option>';
                }
                return $data;
                 if($doc_type)
                {
                    Log::info('Document Type '.$doc_type.' Created Successfully');
                    $request->session()->flash('alert-success', 'Document Type Created Successfully!');
                    return redirect()->back()->with('alert-Success','Document Type Created Successfully!');
                }
                else
                {
                    Log::info('Document Type  '.$doc_type.' cannot be created');
                    $request->session()->flash('alert-danger', 'Document Type cannot be created!');
                    return redirect()->back()->with('alert-danger','Document Type cannot be created!');
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
            return redirect()->back()->with('failure',"Database Query Error! [".$e->getMessage()." ]");
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',$e->getMessage());
        }
    }

    public function mobile_add_doc_type(Request $request)
    {    
        try
        {
            if(Session::get('username')!='')
            {
                Log::info('Trying to create Document Type');
                $doc_type = $request->doc_type;
                
                $doc_type=DB::table('document_type')->insert(
                ['name'=>$doc_type,'created_at'=>now()]
                );
                
               
                 if($doc_type)
                {
                    Log::info('Document Type '.$doc_type.' Created Successfully');
                    $request->session()->flash('alert-success', 'Document Type Created Successfully!');
                    return Redirect::to('mobile_upload_doc');
                }
                else
                {
                    Log::info('Document Type  '.$doc_type.' cannot be created');
                    $request->session()->flash('alert-danger', 'Document Type cannot be created!');
                    return Redirect::to('mobile_upload_doc');
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
            return redirect()->back()->with('failure',"Database Query Error! [".$e->getMessage()." ]");
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',$e->getMessage());
        }
    }

    public function find_per_detail()
    {
      
        try
        {
            if(Session::get('username')!='')
            {  
                Log::info("Fetch Employees id already exist or not in family_detail and family_detail_temp table");
                $emp_id = emp::select('id')->where('email',session('useremail'))->value('id');
                $count_temp =DB::table('family_detail_temp')->where('emp_id',$emp_id)->count(); 
                $count =DB::table('family_detail')->where('emp_id',$emp_id)->count(); 
                if($count_temp>0 || $count>0)
                {
                    $flag=1;
                }
                else
                {
                    $flag=0;
                }
                if($count_temp>0)
                {
                    $data=DB::table('family_detail_temp')->where('emp_id',$emp_id)->get(); 
                }
               else if($count>0)
                {
                    $data=DB::table('family_detail')->where('emp_id',$emp_id)->get();  
                }
                else
                {
                    $data='';
                }
                return view('mobile_personal_detail',compact('flag','data'));
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

    public function emp_personal_detail(Request $request)
    {  
        try
        {
            if(Session::get('username')!='')
            {
                Log::info('Trying to create Employee Personal Detail');
                $if_married=$request->input('if_married');
                $if_children=$request->input('if_children');
                $emp_id = emp::select('id')->where('email',session('useremail'))->value('id');
                $father_name = $request->input('father_name');
                $father_dob = $request->input('father_dob');
                $father_aadhar = $request->input('father_aadhar');
                $father_cur_place = $request->input('father_cur_place');
                $mother_name = $request->input('mother_name');
                $mother_dob = $request->input('mother_dob');
                $mother_aadhar = $request->input('mother_aadhar');
                $mother_cur_place = $request->input('mother_cur_place');
                $father_array=array('father_name'=>$father_name,'father_dob'=>$father_dob,'father_aadhar'=>$father_aadhar,'father_place'=>$father_cur_place);
                $father_array= json_encode($father_array);
                $mother_array=array('mother_name'=>$mother_name,'mother_dob'=>$mother_dob,'mother_aadhar'=>$mother_aadhar,'mother_place'=>$mother_cur_place);
                $mother_array= json_encode($mother_array);
               if($if_married=='yes')
               {
                $spouse_name = $request->input('spouse_name');
                $spouse_dob = $request->input('spouse_dob');
                $spouse_aadhar = $request->input('spouse_aadhar');
                $spouse_gender = $request->input('spouse_gender');
                $spouse_cur_place = $request->input('spouse_cur_place');
                $spouse_array=array('spouse_name'=>$spouse_name,'spouse_dob'=>$spouse_dob,'spouse_gender'=>$spouse_gender,'spouse_aadhar'=>$spouse_aadhar,'spouse_place'=>$spouse_cur_place);
                $spouse_array= json_encode($spouse_array);
            }
               else{
                $spouse_array='';
               }
               if($if_children=='yes')
               {
                $child = $request->input('child');
                $child_dob = $request->input('child_dob');
                $child_gender = $request->input('child_gender');
                $child_aadhar = $request->input('child_aadhar');
                $child_cur_place = $request->input('child_cur_place');
                
                $child_array=array();
                $i=0;
                foreach($child as $children)
                {
                   $child_array[$i+1]=array('child_name'=>$children,'child_dob'=>$child_dob[$i],'child_gender'=>$child_gender[$i],'child_aadhar'=>$child_aadhar[$i],'child_place'=>$child_cur_place[$i]);
                   
                   $i++;
                }
                $child_array=json_encode($child_array);
                }
                else{
                    $child_array=''; 
                }
                $personal_detail=DB::table('family_detail_temp')->insert(['emp_id'=>$emp_id,'father'=>$father_array,'mother'=>$mother_array,'spouse'=>$spouse_array,'children'=>$child_array,'status'=>'pending','created_at'=>now()]);
                
              
                 if($personal_detail)
                {
                    $notification = DB::table('notification')->insert([
                        'user_id'=>Session::get('user_id'),'requester_id'=>$emp_id,'notification'=>'Personal Detail','notification_status'=>'pending','link'=>'personal_detail_list','status'=>'active']);
                    Log::info('Family detail Created Successfully');
                    $request->session()->flash('alert-success', 'Personal Detail Created Successfully!');
                    return redirect()->back();
                }
                else
                {
                    Log::info('Family detail cannot be created');
                    $request->session()->flash('alert-danger', 'Personal Detail cannot be created!');
                    return redirect()->back();
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
            return redirect()->back()->with('failure',"Database Query Error! [".$e->getMessage()." ]");
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',$e->getMessage());
        }
    }

    public function update_emp_personal_detail(Request $request)
    {  
        try
        {
            if(Session::get('username')!='')
            {
                Log::info('Trying to Update Employee Personal Detail');
                $id=$request->input('id');
                $if_married=$request->input('if_married');
                $if_children=$request->input('if_children');
                $emp_id = emp::select('id')->where('email',session('useremail'))->value('id');
                $father_name = $request->input('father_name');
                $father_dob = $request->input('father_dob');
                $father_aadhar = $request->input('father_aadhar');
                $father_cur_place = $request->input('father_cur_place');
                $mother_name = $request->input('mother_name');
                $mother_dob = $request->input('mother_dob');
                $mother_aadhar = $request->input('mother_aadhar');
                $mother_cur_place = $request->input('mother_cur_place');
                $father_array=array('father_name'=>$father_name,'father_dob'=>$father_dob,'father_aadhar'=>$father_aadhar,'father_place'=>$father_cur_place);
                $father_array= json_encode($father_array);
                $mother_array=array('mother_name'=>$mother_name,'mother_dob'=>$mother_dob,'mother_aadhar'=>$mother_aadhar,'mother_place'=>$mother_cur_place);
                $mother_array= json_encode($mother_array);
               if($if_married=='yes')
               {
                $spouse_name = $request->input('spouse_name');
                $spouse_dob = $request->input('spouse_dob');
                $spouse_aadhar = $request->input('spouse_aadhar');
                $spouse_gender = $request->input('spouse_gender');
                $spouse_cur_place = $request->input('spouse_cur_place');
                $spouse_array=array('spouse_name'=>$spouse_name,'spouse_dob'=>$spouse_dob,'spouse_gender'=>$spouse_gender,'spouse_aadhar'=>$spouse_aadhar,'spouse_place'=>$spouse_cur_place);
                $spouse_array= json_encode($spouse_array);
            }
               else{
                $spouse_array='';
               }
               if($if_children=='yes')
               {
                $child = $request->input('child');
                $child_dob = $request->input('child_dob');
                $child_gender = $request->input('child_gender');
                $child_aadhar = $request->input('child_aadhar');
                $child_cur_place = $request->input('child_cur_place');
                
                $child_array=array();
                $i=0;
                foreach($child as $children)
                {
                   $child_array[$i+1]=array('child_name'=>$children,'child_dob'=>$child_dob[$i],'child_gender'=>$child_gender[$i],'child_aadhar'=>$child_aadhar[$i],'child_place'=>$child_cur_place[$i]);
                   
                   $i++;
                }
                $child_array=json_encode($child_array);
                }
                else{
                    $child_array=''; 
                }
                $count_temp =DB::table('family_detail_temp')->where('emp_id',$emp_id)->count(); 
                $count =DB::table('family_detail')->where('emp_id',$emp_id)->count(); 
                if($count_temp>0)
                {
                $personal_detail=DB::table('family_detail_temp')
                  ->where('id', $id)
                  ->update(['emp_id'=>$emp_id,'father'=>$father_array,'mother'=>$mother_array,'spouse'=>$spouse_array,'children'=>$child_array,'updated_at'=>Carbon::now()]);
                }
                if($count>0)
                {
                    $personal_detail=DB::table('family_detail')
                    ->where('id', $id)
                    ->update(['emp_id'=>$emp_id,'father'=>$father_array,'mother'=>$mother_array,'spouse'=>$spouse_array,'children'=>$child_array,'status'=>'accepted','updated_at'=>Carbon::now()]);    
                }
                 if($personal_detail)
                {
                    $notification = DB::table('notification')->insert([
                        'user_id'=>Session::get('user_id'),'requester_id'=>$emp_id,'notification'=>'Personal Detail','notification_status'=>'pending','link'=>'personal_detail_list','status'=>'active']);
                    Log::info('Family detail Created Successfully');
                    $request->session()->flash('alert-success', 'Personal Detail Updated Successfully!');
                    return redirect()->back();
                }
                else
                {
                    Log::info('Family detail cannot be created');
                    $request->session()->flash('alert-danger', 'Personal Detail cannot be Updated!');
                    return redirect()->back();
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
            return redirect()->back()->with('failure',"Database Query Error! [".$e->getMessage()." ]");
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return redirect()->back()->with('alert-danger',$e->getMessage());
        }
    }

    public function personal_detail_list()
    {
        if(Session::get('username')!='' && (session('role')=='admin' || session('role')=='hr' || session('role')=='hr admin'))
        {
            Log::info('feching all uploaded pesonal detail of all employees!');
            try
            {
            $personal_detail_temp =DB::table('family_detail_temp')
            ->join('emp','family_detail_temp.emp_id', '=' , 'emp.id')
            ->select('family_detail_temp.status','family_detail_temp.id','family_detail_temp.emp_id','family_detail_temp.father','family_detail_temp.mother','family_detail_temp.spouse','family_detail_temp.children','emp.first_name','emp.middle_name','emp.last_name')
           ->get();
           $personal_detail =DB::table('family_detail')
            ->join('emp','family_detail.emp_id', '=' , 'emp.id')
            ->select('family_detail.status','family_detail.id','family_detail.emp_id','family_detail.father','family_detail.mother','family_detail.spouse','family_detail.children','emp.first_name','emp.middle_name','emp.last_name')
           ->get();
            return view('personal_detail_list',compact('personal_detail','personal_detail_temp'));
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

        }
        else
        {
            if(Session::get('username')=='')
                return redirect('/')->with('status',"Please login First");
            else
            return redirect('dashboard')->with('alert-danger',"Only admin and HR can add employee");
        }
    }
    
    public function get_per_details(Request $request)
    {
        
        if(Session::get('username')!='' && (session('role')=='admin' || session('role')=='hr' || session('role')=='hr admin'))
        {
            Log::info('fetch employees personal detail');

            try{
                $emp_id=$request->emp_id;
                $id=$request->id;
                $status=$request->status;
                if($status=='accepted')
                {
                    $user_data= DB::table('family_detail')
                    ->join('emp','family_detail.emp_id', '=' , 'emp.id')
                    ->select('family_detail.status','family_detail.id','family_detail.emp_id','family_detail.father','family_detail.mother','family_detail.spouse','family_detail.children','emp.first_name','emp.middle_name','emp.last_name')
                    ->where('emp_id',$emp_id)
                    ->get();
                }
                else
                {
                $user_data= DB::table('family_detail_temp')
                                    ->join('emp','family_detail_temp.emp_id', '=' , 'emp.id')
                                    ->select('family_detail_temp.status','family_detail_temp.id','family_detail_temp.emp_id','family_detail_temp.father','family_detail_temp.mother','family_detail_temp.spouse','family_detail_temp.children','emp.first_name','emp.middle_name','emp.last_name')
                                    ->where('emp_id',$emp_id)
                                    ->get();
                }
             $output='';
             $i=0;
              foreach ($user_data as $emp) 
              {
                date_diff(date_create('1970-02-01'), date_create('today'))->y;
                
                $father=json_decode($emp->father,true);
                $mother=json_decode($emp->mother,true);
                $spouse=json_decode($emp->spouse,true);
                $children=json_decode($emp->children,true);
                $father_age=date_diff(date_create($father['father_dob']), date_create('today'))->y;
                $mother_age=date_diff(date_create($mother['mother_dob']), date_create('today'))->y;
                $spouse_age=date_diff(date_create($spouse['spouse_dob']), date_create('today'))->y;
              $output.='
              <div class="modal-header bg-pink">
                            <h4 class="modal-title" id="defaultModalLabel"> Personal Detail :- '.$emp->first_name.' '.$emp->middle_name.' '.$emp->last_name.'</h4>
                        </div>
                        <div class="modal-body" >   
                           <div class="row clearfix">
                            <div class="col-md-8" id="blood_group">
                           <input type="hidden" name="id" class="form-control family_id" value='.$emp->id.'> 
                            </div>
                          </div> 
                         <div class="row clearfix">
                            <div class="col-md-3">
                                <label for="blood_group">Father`s Name:</label>
                            </div>
                            <div class="col-md-4" id="blood_group">
                            '.$father['father_name'].'
                            </div>
                            <div class="col-md-2">
                            <label for="blood_group">DOB:</label>
                        </div>
                        <div class="col-md-3" id="blood_group">
                        '.$father['father_dob'].'
                        </div>
                          </div> 
                        <div class="row clearfix">
                          <div class="col-md-3">
                          <label for="blood_group">Aadhar No:</label>
                      </div>
                      
                      <div class="col-md-2" id="blood_group">
                      '.$father['father_aadhar'].'
                      </div>
                         <div class="col-md-1 align="left">
                          <label for="blood_group">Age:</label>
                             </div>
                          <div class="col-md-1" id="blood_group">
                          '.$father_age.'yr
                          </div>
                          <div class="col-md-2">
                              <label for="blood_group">Current Place:</label>
                          </div>
                          <div class="col-md-3" id="blood_group">
                        '.$father['father_place'].'
                        </div>
                         </div> 
                        <div class="row clearfix">
                          
                      </div>  
                      <hr>
                <div class="row clearfix">
                    <div class="col-md-3">
                        <label for="blood_group">Mather`s Name</label>
                    </div>
                    <div class="col-md-4" id="blood_group">
                        '.$mother['mother_name'].'
                    </div>
                    <div class="col-md-2">
                        <label for="blood_group">DOB</label>
                    </div>
                    <div class="col-md-3" id="blood_group">
                        '.$mother['mother_dob'].'
                    </div>
                </div> 
                <div class="row clearfix">
                    <div class="col-md-3">
                        <label for="blood_group">Aadhar No</label>
                    </div>
                    <div class="col-md-2" id="blood_group">
                        '.$mother['mother_aadhar'].'
                     </div>
                    <div class="col-md-1">
                        <label for="blood_group">Age</label>
                    </div>
                    <div class="col-md-1" id="blood_group">
                        '.$mother_age.'yr
                    </div>
                    <div class="col-md-2">
                        <label for="blood_group">Current Place</label>
                    </div>
                    <div class="col-md-3" id="blood_group">
                        '.$mother['mother_place'].'
                    </div>
                </div> 
                 <hr>  
                <div class="row clearfix">
                    <div class="col-md-3">
                        <label for="blood_group">Spouse`s Name</label>
                    </div>
                    <div class="col-md-4" id="blood_group">
                        '.$spouse['spouse_name'].'
                    </div>
                    <div class="col-md-2">
                        <label for="blood_group">DOB</label>
                    </div>
                    <div class="col-md-3" id="blood_group">
                        '.$spouse['spouse_dob'].'
                    </div>
                </div> 
                <div class="row clearfix">
                    <div class="col-md-3">
                        <label for="blood_group">Aadhar No</label>
                    </div>
                    <div class="col-md-2" id="blood_group">
                        '.$spouse['spouse_aadhar'].'
                    </div>
                    <div class="col-md-1">
                        <label for="blood_group">Age</label>
                    </div>
                    <div class="col-md-1" id="blood_group">
                        '.$spouse_age.'yr
                    </div>
                    <div class="col-md-2">
                        <label for="blood_group">Current Place</label>
                    </div>
                    <div class="col-md-3" id="blood_group">
                        '.$spouse['spouse_place'].'
                    </div>
                </div> 
                <hr>';
                $total_child=sizeof($children); 
                $output.='
                <div class="row clearfix">
                <div class="col-md-4">
                    <label for="blood_group">No Of Child</label>
                </div>
                <div class="col-md-8" id="blood_group">
                 '.$total_child.'
                 </div>
               </div><hr> ';
                foreach($children as $child)
                {

                    $child_age=date_diff(date_create($child['child_dob']), date_create('today'))->y; 
                   
               $output.='<div class="row clearfix">
               <div class="col-md-3">
                   <label for="blood_group">Child`s Name</label>
               </div>
               <div class="col-md-4" id="blood_group">
                '.$child['child_name'].'
                </div>
                <div class="col-md-2">
                  <label for="blood_group">DOB</label>
              </div>
              <div class="col-md-3" id="blood_group">
              '.$child['child_dob'].'
              </div>
              </div> 
              <div class="row clearfix">
              <div class="col-md-3">
              <label for="blood_group">Aadhar No</label>
          </div>
          <div class="col-md-2" id="blood_group">
          '.$child['child_aadhar'].'
          </div>
              <div class="col-md-1">
              <label for="blood_group">Age</label>
                 </div>
              <div class="col-md-1" id="blood_group">
              '.$child_age.'yr
              </div>
              <div class="col-md-2">
              <label for="blood_group">Current Place</label>
          </div>
          <div class="col-md-3" id="blood_group">
        '.$child['child_place'].'
        </div>
            </div> 
            <hr>
          ';
                }
              }
              if($emp->status=='pending')
              {
             $output.='</div>
             <div class="modal-footer">
                 <button type="Button" class="btn  bg-green waves-effect accept">Accept</button>
                 <button type="Button" class="btn  bg-deep-orange waves-effect reject">Reject</button>
                 <button type="button" class="btn bg-blue-grey  waves-effect" data-dismiss="modal">close</button><br>
                 <br>
                 <div class="row clearfix" id="rejectdiv" style="display:none">
                 <div class="col-md-8" id="blood_group">
                 </div>
                 <div class="col-md-2">
                 <textarea class="form-control remark"></textarea>
                 </div>
                 <div class="col-md-2">
                 <button type="button" class="btn bg-teal waves-effect final_reject">submit</button>
                 </div>
             </div> ';
              }
              else{
                $output.='</div>
                <div class="modal-footer">
                   <button type="button" class="btn bg-blue-grey  waves-effect" data-dismiss="modal">close</button>
                </div> ';
              }
              return $output;

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

           
            
        }
        else
        {
            if(Session::get('username')=='')
                return redirect('/')->with('status',"Please login First");
            else
            return redirect('dashboard')->with('alert-danger',"Only admin and HR can add employee");
        }
    }
    public function update_per_details(Request $request)
    {
        
        try
        {
            if(Session::get('username')!='')
            {
                $id=$request->id;
            

                Log::info(' Updating Family Personal details for id '.$id.'vrify by admin as accept and remove data from family_detail_temp table  and insert in family_detail table');

                $family_detail=DB::table('family_detail_temp')
                  ->where('id', $id)
                  ->get();
                foreach($family_detail as $family_details )
                {
                   $emp_id=$family_details->emp_id; 
                   $father=$family_details->father;
                   $mother=$family_details->mother;
                   $spouse=$family_details->spouse;
                   $children=$family_details->children;
                   

                }
            
                $insert=DB::table('family_detail')->insert(['emp_id'=>$emp_id,'father'=>$father,'mother'=>$mother,'spouse'=>$spouse,'children'=>$children,'created_at'=>now()]);
                if($insert)
                {
                    Log::info('remove data from family temp table');  
                    $delete=DB::table("family_detail_temp")->delete($id);
                }
                if($insert && $delete)
                {
                    Log::info('Data in family_detail table inserted successfully');
                    $request->session()->flash('alert-success', 'Data verified successfully!');
                    return Redirect::to('personal_detail_list');
                }
                else
                {
                    Log::info('Data in family_detail table  can not be inserted');
                    $request->session()->flash('alert-danger', 'Data cna not be verified!');
                    return Redirect::to('personal_detail_list');
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
            return Redirect::to('department')->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return Redirect::to('department')->with('alert-danger',$e->getMessage());
        }
    
    }       
    public function reject_per_details(Request $request)
    {
    
        try
        {
            if(Session::get('username')!='')
            {
                $id=$request->id;
                $remark=$request->remark;

                Log::info('Personal detail of '.$id.' is rejected');

                $emp_id=DB::table('family_detail_temp')
                  ->where('id', $id)
                  ->value('emp_id');
                
            
                $update=DB::table('family_detail_temp')
                ->where('id', $id)
                ->update(['status' => 'rejected','remark'=>$remark,'updated_at'=>Carbon::now()]);
                
                if($update)
                {
                    $notification = DB::table('notification')->insert([
                        'user_id'=>Session::get('user_id'),'requester_id'=>$emp_id,'notification'=>'Your Personal detail is rejected Reson:  '.$remark,'notification_status'=>'Rejected','link'=>'mobile_personal_detail','status'=>'active']);
                    Log::info('Personal Detail of '.$emp_id.' is verified successfully');
                    $request->session()->flash('alert-success', 'Personal Detail is verified successfully!');
                    return Redirect::to('personal_detail_list');
                }
                else
                {
                    return 2;
                    Log::info('Personal Detail of '.$emp_id.' is connot be verified');
                    $request->session()->flash('alert-danger', 'Personal Detail is can not verified!');
                    return Redirect::to('personal_detail_list');
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
            return Redirect::to('personal_detail_list')->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return Redirect::to('personal_detail_list')->with('alert-danger',$e->getMessage());
           }
        }


        public function get_emp_transfer()
        {
             try
        {
            if(Session::get('username')!='')
            {
               $emp= DB::table('emp')->get();
               $branch=DB::table('branch')->get();
               $transfer=DB::table('transfer')->get();
               $details=DB::table('transfer')->join('emp','transfer.emp_id','=','emp.id')->select('emp.first_name','emp.branch_location_id','emp.middle_name','emp.last_name','transfer.transfer_to_branch','transfer.effective_date')->get();
            //   foreach($transfer as $trans)
            //   {
            //       return $trans->emp_id;
            //   }
            //     return  $detailsss=DB::table('transfer')->join('emp','transfer.emp_id','=','emp.id')->join('branch','emp.branch_location_id','=','branch.id')->select('branch.name','emp.first_name','emp.middle_name',
            //   'emp.last_name','transfer.transfer_to_branch','transfer.effective_date')->get();
               return view('transfer_master',compact('emp','branch','details'));
            }
            else
            {
                return redirect('/')->with('status',"Please login First");
            }
        } 
        catch(QueryException $e)
        {
            Log::error($e->getMessage());
            return Redirect::to('personal_detail_list')->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return Redirect::to('personal_detail_list')->with('alert-danger',$e->getMessage());
           }
        }

         public function get_branch(Request $request)
        {
            if(Session::get('username')!='') 
            {
                    $employee_id = $request->emp;
                    $employee=DB::table('emp')->select('branch_location_id')->where('id',$employee_id)->value('branch_location_id');
                    $branch=DB::table('branch')->select('branch')->where('id',$employee)->value('branch');
                    $data = $branch;
                    // $data.='<option value="'.$employee.'">'.$branch.'</option>';
                    return $data;
            }
            else
            {
                    return redirect('/')->with('status',"Please login First");
            }
        }

        public function create_transfer(Request $request)
        {
        
            try
            {
            if(Session::get('username')!='') 
                {
                $emp_id=$request->emp;
                $tranfer_to=$request->transfer_to;
                $effecive_date=$request->effecive_date;
                $transfer= DB::table('transfer')->insert(
                    ['emp_id'=>$emp_id,'transfer_to_branch'=>$tranfer_to,'effective_date'=>$effecive_date,'created_at'=>Carbon::now('Asia/Kolkata')]);
                $emp_transfer=DB::table('emp')->where('id',$emp_id)->update(['transfer'=>'yes','updated_at'=>Carbon::now('Asia/Kolkata')]);
                if($transfer && $emp_transfer )
                {
                 Log::info('Transfer '.$transfer.' Created Successfully');
                 $request->session()->flash('alert-success', 'Transfer Created Successfully!');
                 return Redirect::to('transfer_master');
                }
                else
                {
                    Log::info('Transfer '.$transfer.' cannot be created');
                    $request->session()->flash('alert-danger', 'Holidays cannot be created!');
                    return Redirect::to('transfer_master');
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
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }

        public function other_add_ded_index(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                $branches = DB::table('branch')->get();
                return view('other_add_ded',compact('branches'));
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }

        public function get_other_add_ded(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                $branch = $request->branch;
                $month = $request->month;
                $year = $request->year;
                $emps = DB::table('emp')->select('first_name','middle_name','last_name','id','biometric_id')->where('branch_location_id',$branch)->where('status','active')->get();

                $empids = DB::table('emp')->select('id')->where('branch_location_id',$branch)->where('status','active')->get();
                $other_add_ded =  DB::table('other_add_ded')->wherein('emp_id',[DB::raw("select id from emp where branch_location_id = ".$branch)])->where('month',$month)->where('year',$year)->get();
                
                return view('other_add_ded_table',compact('emps','month','year','other_add_ded'));
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }

        public function other_add_ded(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                $emps = json_decode($request->empids,true);
                $other_add = $request->other_add;
                $other_ded = $request->other_ded;
                $remark = $request->remark;

                $month = $request->month;
                $year = $request->year;
                
                $inserted=$b=0; $msg='';

                for($i=0;$i<sizeof($emps);$i++)
                {
                    if($other_add[$i]==NULL && $other_ded[$i]==NULL)
                    {
                        continue;
                    }
                    else
                    {
                        $b++;
                        $check = DB::table('other_add_ded')->where('year',$year)->where('month',$month)->where('emp_id',$emps[$i]['id'])->count();
                        if($check>0)
                        {
                            $update = DB::table('other_add_ded')->where('year',$year)->where('month',$month)->where('emp_id',$emps[$i]['id'])->update([ 'other_add'=>$other_add[$i], 'other_ded'=>$other_ded[$i], 'remark'=>$remark[$i] ]);

                            if($update>=0)
                                $inserted++;
                        }
                        else
                        {
                            $insert = DB::table('other_add_ded')->insert([
                                'year'=>$year,
                                'month'=>$month,
                                'emp_id'=>$emps[$i]['id'],
                                'other_add'=>$other_add[$i],
                                'other_ded'=>$other_ded[$i],
                                'remark'=>$remark[$i],
                            ]);

                            if($insert)
                                $inserted++;  
                        }
                                              
                    }
                }

                if($inserted==$b)
                {
                    //$request->session()->flash('success', 'Inserted successfully');
                    return redirect()->back()->with('alert-success','Data inserted successfully');
                }
                else
                {
                   // $request->session()->flash('success', 'Data not inserted');
                    return redirect()->back()->with('alert-danger','Data not inserted');
                }
                    

            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }


        public function tds_index(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                $branches = DB::table('branch')->get();
                return view('add_tds',compact('branches'));
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }

        public function get_tds(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                $branch = $request->branch;
                $month = $request->month;
                $year = $request->year;
                $emps = DB::table('emp')->select('first_name','middle_name','last_name','id','biometric_id')->where('branch_location_id',$branch)->where('status','active')->get();

                $tds =  DB::table('tds')->wherein('emp_id',[DB::raw("select id from emp where branch_location_id = ".$branch)])->where('month',$month)->where('year',$year)->get();

                return view('tds_table',compact('emps','month','year','tds'));
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }

        public function add_tds(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                $emps = json_decode($request->empids,true);
                $tds_amt = $request->tds_amt;

                $month = $request->month;
                $year = $request->year;
                
                $inserted=0; $b=0; $msg='';

                for($i=0;$i<sizeof($emps);$i++)
                {
                    if($tds_amt[$i]==NULL || $tds_amt[$i]=='')
                    {
                        continue;
                    }
                    else
                    {
                        $b++;
                        
                        $check = DB::table('tds')->where('year',$year)->where('month',$month)->where('emp_id',$emps[$i]['id'])->count();
                        if($check>0)
                        {
                            $update = DB::table('tds')->where('year',$year)->where('month',$month)->where('emp_id',$emps[$i]['id'])->update([ 'tds_amt'=>$tds_amt[$i]]);

                            if($update>=0)
                                $inserted++;
                        }
                        else
                        {
                            $insert = DB::table('tds')->insert([
                                'year'=>$year,
                                'month'=>$month,
                                'emp_id'=>$emps[$i]['id'],
                                'tds_amt'=>$tds_amt[$i],
                            ]);

                            if($insert)
                                $inserted++;  
                        }
                                              
                    }
                }

                if($inserted==$b)
                {
                    return redirect()->back()->with('alert-success','Data inserted successfully');
                }
                else
                {
                    return redirect()->back()->with('alert-danger','Data not inserted');
                }
                    

            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }

        public function employee_shift_index(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                if(session('role')=='hr')
                    $branches = DB::table('branch')->where('id',session('branch_location_id'))->get();
                else
                    $branches = DB::table('branch')->get();
                return view('employee_shift',compact('branches'));
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }

        public function get_branch_shift(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                $branch = $request->branch;
                $emps = DB::table('emp')->leftjoin('designation','designation.id','=','emp.designation')->select('emp.first_name','emp.middle_name','emp.last_name','emp.id','emp.biometric_id','designation.designation')->where('branch_location_id',$branch)->where('status','active')->get();
                $shifts = DB::table('shift_master')->get();
                
               // $employee_shift = DB::table('employee_shift')->wherein('emp_id',[DB::raw("select id from emp where branch_location_id = ".$branch)])->get();
                return view('employee_shift_table',compact('emps','shifts'));
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }

        public function get_edit_branch_shift(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                $branch = $request->branch;
                
                $shifts = DB::table('shift_master')->get();
                
                $emp_id = DB::table('emp')->join('employee_shift','emp.id','=','employee_shift.emp_id')->leftjoin('designation','designation.id','=','emp.designation')->select('emp_id','first_name','middle_name','last_name','designation.designation')->wherein('emp_id',[DB::raw("select id from emp where branch_location_id = ".$branch)])->whereDate('end_date','>=',date('Y-m-d'))->groupBy('emp_id')->orderBy('employee_shift.id')->get();

                // $emp_id = DB::table('employee_shift')->join('emp','emp.id','=','employee_shift.emp_id')->leftjoin('designation','designation.id','=','emp.designation')->select('emp_id','first_name','middle_name','last_name')->wherein('emp_id',[DB::raw("select id from emp where branch_location_id = ".$branch)])->whereDate('end_date','>=',date('Y-m-d'))->groupBy('emp_id')->orderBy('employee_shift.id')->get();
                $employee_shift = array();
                for($i=0;$i<sizeof($emp_id);$i++)
                {
                    $id = $shift = $start_date = $end_date = array();

                    $shift_array = DB::table('employee_shift')->where('emp_id',$emp_id[$i]->emp_id)->whereDate('end_date','>=',date('Y-m-d'))->get();

                    for($j=0;$j<sizeof($shift_array);$j++)
                    {
                        array_push($id,$shift_array[$j]->id);
                        array_push($shift,$shift_array[$j]->shift_id);
                        array_push($start_date,$shift_array[$j]->start_date);
                        array_push($end_date, $shift_array[$j]->end_date);
                    }

                    array_push($employee_shift, json_encode(array('id'=>$id,'emp_id'=>$emp_id[$i]->emp_id,'first_name'=>$emp_id[$i]->first_name,'middle_name'=>$emp_id[$i]->middle_name,'last_name'=>$emp_id[$i]->last_name,'designation'=>$emp_id[$i]->designation,'shift'=>$shift,'start_date'=>$start_date,'end_date'=>$end_date)));
                }
               
                //$employee_shift = DB::table('employee_shift')->wherein('emp_id',[DB::raw("select id from emp where branch_location_id = ".$branch)])->get();
                $employee_shift = json_encode($employee_shift);
                return view('edit_employee_shift_table',compact('shifts','employee_shift'));
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }

        public function add_employee_shift(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                     return json_encode(array('status'=>'danger','danger'=>"Please login First"));

                $emp_id = $request->emp_id;
                $shift_id = $request->shift_id;
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                
                Schema::dropIfExists('temp_employee_shift');
                Schema::create('temp_employee_shift', function (Blueprint $table) {
                    $table->increments('temp_employee_shift_id');
                    $table->string('user_id');
                    $table->string('emp_id');
                    $table->string('shift_id');
                    $table->date('start_date');                    
                    $table->date('end_date');
                    $table->timestamps();
                    $table->temporary();
                });

                $inserted=$b=0;
                for($i=0;$i<sizeof($emp_id);$i++)
                {
                    for($j=0; $j<sizeof($shift_id[$emp_id[$i]]);$j++)
                    {
                        $id = $emp_id[$i];
                        $shift = $shift_id[$emp_id[$i]][$j];
                        $start = $start_date[$emp_id[$i]][$j];
                        $end = $end_date[$emp_id[$i]][$j];

                        // if(strtotime($start) < strtotime(date('Y-m-d')) || strtotime($end) < strtotime(date('Y-m-d')))
                        // {
                        //     Schema::drop('temp_employee_shift');
                        //     return json_encode(array('status'=>'danger','danger'=>'elapsed date cannot be set as start or end date for employee id '.$id.'. Please change the date.'));
                        // }

                        if($shift!='' && $start!='' && $end!='')
                        {
                            if(strtotime($start) > strtotime($end))
                            {
                                Schema::drop('temp_employee_shift');
                                return json_encode(array('status'=>'danger','danger'=>'start date cannot be greater than end date for employee id '.$id.'. Please change the date.'));
                            }

                            $check = DB::table('temp_employee_shift')->join('shift_master','shift_master.id','=','temp_employee_shift.shift_id')->where('emp_id',$id)->where(function($q) use ($start,$end){
                                 return $q->whereBetween('start_date', [$start,$end])->orWhereBetween('end_date', [$start,$end]); })->get();

                            if(sizeof($check)==0)
                            {
                                $check = DB::table('employee_shift')->join('shift_master','shift_master.id','=','employee_shift.shift_id')->where('emp_id',$id)->where(function($q) use ($start,$end){
                                 return $q->whereBetween('start_date', [$start,$end])->orWhereBetween('end_date', [$start,$end]); })->get();
                            }
     
                            if(sizeof($check)>0)
                            {
                                Schema::drop('temp_employee_shift');

                                $danger='';
                                for($m=0;$m<sizeof($check);$m++)
                                {
                                    $danger.=' Shift - '.$check[$m]->shift_name.', start date - '.$check[$m]->start_date.', end date - '.$check[$m]->end_date.', id '.$check[$m]->emp_id;
                                }
                                return json_encode(array('status'=>'danger','danger'=>'Date clash for employee id '.$id.'. Please change the date. ('.$danger.')'));
                                
                            }
                            else
                            {
                                $b++;
                                $tempinsert = DB::table('temp_employee_shift')->insert([
                                    'user_id'=>session('user_id'),
                                    'emp_id'=>$id,
                                    'shift_id'=>$shift,
                                    'start_date'=>$start,
                                    'end_date'=>$end,
                                ]);

                                if($tempinsert)
                                    $inserted++;  
                            }
                        }

                    }
                    
                }

                if($inserted==$b && $inserted!=0)
                {
                    $insertedrows = json_decode(DB::table('temp_employee_shift')->select('user_id','emp_id','shift_id','start_date','end_date')->get(),true);

                    $insert = DB::table('employee_shift')->insert($insertedrows);
                    Schema::drop('temp_employee_shift');
                    if($insert)
                    {
                        return json_encode(array('status'=>'success','success'=>'Data inserted successfully'));
                    }
                    else
                    {
                        return json_encode(array('status'=>'danger','danger'=>'Data not inserted'));
                    }
                }
                else
                {
                    Schema::drop('temp_employee_shift');
                    return json_encode(array('status'=>'danger','danger'=>'Data not inserted'));
                }
            }
            catch(QueryException $e)
            {
                Schema::dropIfExists('temp_employee_shift');
                Log::error($e->getMessage());
                return json_encode(array('status'=>'danger','danger'=>"Database Query Error! [".$e->getMessage()." ]"));
            }
            catch(Exception $e)
            {
                Schema::dropIfExists('temp_employee_shift');
                Log::error($e->getMessage());
                return json_encode(array('status'=>'danger','danger'=>$e->getMessage()));
            }
        }

        public function edit_employee_shift(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                $table_id = $request->id;
                $emp_id = $request->emp_id;
                $shift_id = $request->shift_id;
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                
                $updated=0;
                $m=0;
                // for($i=0;$i<sizeof($emp_id);$i++)
                // {
                    for($j=0; $j<sizeof($table_id);$j++)
                    {
                        $id = $table_id[$j];
                        $shift = $shift_id[$id];
                        $start = $start_date[$id];
                        $end = $end_date[$id];

                        $update = DB::table('employee_shift')->where('id',$id)->update([
                            'user_id'=>session('user_id'),
                            'shift_id'=>$shift,
                            'start_date'=>$start,
                            'end_date'=>$end,
                        ]);

                        if($update>=0)
                            $updated++;  

                    }
                    $m+=$j;
                    
                // }

                if($updated==$m && $updated>0)
                {
                    return json_encode(array('status'=>'success','success'=>'Data updated successfully'));
                }
                else
                {
                    return json_encode(array('status'=>'danger','danger'=>'Data not updated'));
                }
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return json_encode(array('status'=>'danger','danger'=>"Database Query Error! [".$e->getMessage()." ]"));
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return json_encode(array('status'=>'danger','danger'=>$e->getMessage()));
            }
        }

        public function hold_release_salary_index(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                if(session('role')=='hr')
                {
                    $branches = DB::table('branch')->where('id',session('branch_location_id'))->get();
                }
                else
                {
                    $branches = DB::table('branch')->get();                    
                }
                return view('hold_release_salary',compact('branches'));
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }

        public function get_hold_salary_table(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                $branch = $request->branch;
                $month = $request->month;
                $year = $request->year;

                // $hold_salary = DB::table('hold_salary')->join('emp','emp.id','=','hold_salary.emp_id')->select('emp_id','hold_remark')->where('hold_salary',1)->where('emp.branch_location_id',$branch)->get();

                // $hold_remark=$hold_emp=array();
                // for($i=0;$i<sizeof($hold_salary);$i++)
                // {
                //     array_push($hold_emp, $hold_salary[$i]->emp_id);
                // }

                $emps = DB::table('emp')->select('first_name','middle_name','last_name','id','biometric_id')->where('branch_location_id',$branch)->whereNotIn('id',[DB::raw('select emp_id from hold_salary where month='.$month.' and year='.$year.' and hold_salary=1')])->where('status','active')->get();

                return view('hold_salary_table',compact('emps','month','year'));
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }

        public function get_release_salary_table(Request $request)
        {
            try
            {
                if(Session::get('username')=='') 
                    return redirect('/')->with('status',"Please login First");

                $branch = $request->branch;
                $month = $request->month;
                $year = $request->year;

                $hold_salary = DB::table('hold_salary')->join('emp','emp.id','=','hold_salary.emp_id')->select('hold_salary.id','first_name','middle_name','last_name','biometric_id','emp_id','hold_remark')->where('hold_salary',1)->where('emp.branch_location_id',$branch)->where('month',$month)->where('year',$year)->get();

                return view('release_salary_table',compact('emps','hold_salary'));
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',"Database Query Error! [".$e->getMessage()." ]");
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
                return Redirect::back()->with('alert-danger',$e->getMessage());
            }
        }


        public function hold_salary(Request $request)
        {
            try
            {
                $month = $request->month;
                $year = $request->year;
                $user_id = session('user_id');
                $remark = $request->remark;
                $hold = $request->hold;
                $emp_id = array_keys($hold);
                if(sizeof($hold)==0)
                {
                    return json_encode(array('status'=>'danger','danger'=>'Please check atleast one employee'));
                }

                $inserted= $b=0;
                for($i=0;$i<sizeof($emp_id);$i++)
                {
                    if($hold[$emp_id[$i]]=='hold')
                    {
                        $b++;

                        $check = DB::table('hold_salary')->where('month',$month)->where('year',$year)->where('emp_id',$emp_id[$i])->count();
                        if($check>0)
                        {
                            $update = DB::table('hold_salary')->where('month',$month)
                            ->where('year',$year)
                            ->where('emp_id',$emp_id[$i])
                            ->update([
                                        'user_id'=>$user_id,
                                        'hold_salary'=>'1',
                                        'release_salary'=>'0',
                                        'hold_remark'=>$remark[$emp_id[$i]],                        
                                    ]);

                            $inserted++;
                        }
                        else
                        {
                            $insert = DB::table('hold_salary')->insert([
                                'user_id'=>$user_id,
                                'emp_id'=>$emp_id[$i],
                                'month'=>$month,
                                'year'=>$year,
                                'hold_salary'=>'1',
                                'release_salary'=>'0',
                                'hold_remark'=>$remark[$emp_id[$i]],                          
                            ]);

                            if($insert)
                                $inserted++;
                        }
                            
                    }
                }

                if($b==$inserted)
                {
                    return json_encode(array('status'=>'success','success'=>'Data uploaded successfully'));
                }
                else
                {
                    return json_encode(array('status'=>'danger','danger'=>'Data not uploaded'));
                }
            }
            catch(QueryException $e)
            {
                Log::error($e->getMessage());
                return json_encode(array('status'=>'danger','danger'=>"Database Query Error! [".$e->getMessage()." ]"));
            }
        }
        
    public function release_salary(Request $request)
    {
        try
        {
            $user_id = session('user_id');                
            $remark = $request->remark;
            $release = $request->release;
            $table_id = array_keys($release);
            if(sizeof($release)==0)
            {
                return json_encode(array('status'=>'danger','danger'=>'Please check atleast one employee'));
            }

            $updated=$b=0;
            for($i=0;$i<sizeof($table_id);$i++)
            {
                $b++;

                $update = DB::table('hold_salary')->where('id',$table_id[$i])->update([
                    'user_id'=>$user_id,
                    'hold_salary'=>'0',
                    'release_salary'=>'1',
                    'release_remark'=>$remark[$table_id[$i]],                            
                ]);

                if($update>=0)
                    $updated++;
            }

            if($b==$updated)
            {
                return json_encode(array('status'=>'success','success'=>'Data updated successfully'));
            }
            else
            {
                return json_encode(array('status'=>'danger','danger'=>'Data not updated'));
            }
        }
        catch(QueryException $e)
        {
            Log::error($e->getMessage());
            return json_encode(array('status'=>'danger','danger'=>"Database Query Error! [".$e->getMessage()." ]"));
        }
        catch(Exception $e)
        {
            Log::error($e->getMessage());
            return json_encode(array('status'=>'danger','danger'=>$e->getMessage()));
        }
    }

    public function emp_report_index()
    {
        if(Session::get('username')!='')
        {
            try{
            $depts = DB::table('department')->get();
            /*$depts = DB::table('department')->where('department_name','!=','Admin')->orWhere('department_name','!=','admin')->get();*/
            $dept_id = $depts->first()->id;
            $desigs = DB::table('designation')->where('department',$dept_id)->get();
            $roles = DB::table('role')->get();
            $last_insert_id = DB::table('emp')->max('id') + 1;
            if(session('role')=='hr')
                $branches = DB::table('branch')->where('id',session('branch_location_id'))->get();
            else
                $branches = DB::table('branch')->get();
            $bank_list = DB::table('bank_list')->get();
            $division = DB::table('division')->get();
            return view('emp_report',compact('depts','roles','last_insert_id','desigs','branches','bank_list','division'));
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
        }
        else
        {
            if(Session::get('username')=='')
                return redirect('/')->with('status',"Please login First");
            else
            return redirect('dashboard')->with('alert-danger',"Only admin and HR can add employee");
        }
    }

    public function get_emp_report(Request $request)
    {
        try
        {
            $branch_location_id = $request->branch_location_id;
            $department = $request->department;
            $designation = $request->designation;
            $out_source = $request->out_source;
            $gender = $request->gender;
            $marital_status = $request->marital_status;
            $status = $request->status;
            $where_doj = $request->where_doj;
            $where_lwd = $request->where_lwd;
            $from_doj = date('Y-m-d',strtotime($request->from_doj));
            $to_doj = date('Y-m-d',strtotime($request->to_doj));
            $from_lwd = date('Y-m-d',strtotime($request->from_lwd));
            $to_lwd = date('Y-m-d',strtotime($request->to_lwd));
            $select = $selective_field = array();

            $field_map = array('id'=>'EMP ID', 'genesis_ledger_id'=>'Genesis ledger ID', 'genesis_id'=>'Genesis ID', 'biometric_id'=>'Biometric ID', 'branch_location_id'=>'Branch', 'title'=>'Title', 'first_name'=>'First Name', 'middle_name'=>'Middle Name', 'last_name'=>'Last Name', 'blood_group'=>'Blood Group', 'email'=>'Email', 'dob'=>'DOB', 'marital_status'=>'Marital Status', 'mobile'=>'Mobile', 'gender'=>'Gender', 'category'=>'Category', 'adhaar_number'=>'Adhaar Number', 'pan_number'=>'PAN Number', 'local_address'=>'Local Address', 'permanent_address'=>'Permanent Address', 'emergency_call_person'=>'Emergency Call Person', 'distance_from_office'=>'Distance From Office', 'emergency_call_number'=>'Emergency Call Number', 'department_name'=>'Department', 'designation'=>'Designation', 'out_source'=>'Out Source', 'division'=>'Division', 'section'=>'Section', 'doj'=>'Date Of Joining', 'esic_number'=>'ESIC Number', 'epf_number'=>'EPF Number', 'lin_number'=>'LIN Number', 'uan_number'=>'UAN Number', 'status'=>'Status', 'acc_holder_name'=>'Account Holder Name', 'acc_no'=>'Account Number', 'ifsc_code'=>'IFSC Code', 'epf_option'=>'EPF Option', 'esic_option'=>'ESIC Option', 'reason_code_0'=>'Reason For Code 0', 'last_working_day'=>'Last Working Day', 'bank_name'=>'Bank Name', 'bank_branch'=>'Bank Branch', 'transfer'=>'Transfered Branch', 'branch'=>'Branch'
                );
            if($request->fields=='all')
            {
                $select = ['emp.id','emp.genesis_ledger_id','emp.genesis_id','emp.biometric_id','branch.branch','transfer.branch as transfer','emp.title','emp.first_name','emp.middle_name','emp.last_name','emp.blood_group','emp.email','emp.dob','emp.marital_status','emp.mobile','emp.gender','emp.category','emp.adhaar_number','emp.pan_number','emp.local_address','emp.permanent_address','emp.emergency_call_person','emp.distance_from_office','emp.emergency_call_number','department.department_name','designation.designation','emp.out_source','emp.division','emp.section','emp.doj','emp.esic_number','emp.epf_number','emp.lin_number','emp.uan_number','salary.salary','emp.status','emp.acc_holder_name','emp.acc_no','emp.ifsc_code','emp.epf_option','emp.esic_option','emp.reason_code_0','emp.last_working_day','bank_list.bank_name','emp.branch as bank_branch'];
            }
            else
            {
                $selective_field = $request->selective_field;
                foreach($selective_field as $field)
                {
                    if($field=='salary')
                        array_push($select, 'salary.salary');
                    elseif($field=='department')
                        array_push($select, 'department.department_name');
                    elseif($field=='designation')
                        array_push($select, 'designation.designation');
                    elseif($field=='transfer')
                        array_push($select, 'transfer.branch as transfer');
                    elseif($field=='bank_name')
                        array_push($select, 'bank_list.bank_name');
                    elseif($field=='branch_location_id')
                        array_push($select, 'branch.branch');
                    elseif($field=='branch')
                        array_push($select, 'emp.branch as bank_branch');
                    else
                        array_push($select,'emp.'.$field);

                }
            }            
            
            $query = DB::table('emp')->leftjoin('branch','emp.branch_location_id','=','branch.id')->leftjoin('branch as transfer','emp.transfer','=','transfer.id')->leftjoin('department',
                'department.id','=','emp.department')->leftjoin('designation','designation.id','=','emp.designation')->leftjoin('salary','salary.id','=','emp.salary_id')->leftjoin('bank_list','bank_list.id','=','emp.bank_name')->select($select);

            $query = $query->whereIn('branch_location_id',$branch_location_id);

            if($department!='')
                $query = $query->where('emp.department',$department);
            if($designation!='')
                $query = $query->where('emp.designation',$designation);
            if($out_source!='')
                $query = $query->where('emp.out_source',$out_source);
            if($gender!='')
                $query = $query->where('emp.gender',$gender);
            if($marital_status!='')
                $query = $query->where('emp.marital_status',$marital_status);
            if($status!='')
                $query = $query->where('emp.status',$status);
            
            
            if($where_doj!='' && $where_lwd=='')
            {
                if($where_doj=='between')
                    $query = $query->whereBetween('emp.doj',[$from_doj,$to_doj]);
                elseif($where_doj=='before')
                    $query = $query->whereDate('emp.doj','<',$from_doj);
                elseif($where_doj=='after')
                    $query = $query->whereDate('emp.doj','>',$from_doj);
                //$query = $query->$wheredoj;
            }
            elseif($where_doj=='' && $where_lwd!='')
            {
                if($where_lwd=='between')
                    $query = $query->whereBetween('emp.last_working_day',[$from_lwd,$to_lwd]);
                elseif($where_lwd=='before')
                    $query = $query->whereDate('emp.last_working_day','<=',$from_lwd);
                elseif($where_lwd=='after')
                    $query = $query->whereDate('emp.last_working_day','>=',$from_lwd);

                //$query = $query->$wherelwd;
            }
            elseif($where_doj!='' && $where_lwd!='')
            {
                $query = $query->where(function($q) use ($from_doj, $to_doj,$from_lwd,$to_lwd,$where_lwd,$where_doj) {
                            $q->where(function($query1) use ($from_doj, $to_doj,$where_doj){
                                    if($where_doj=='between')
                                        $query1 = $query1->whereBetween('emp.doj',[$from_doj,$to_doj]);
                                    elseif($where_doj=='before')
                                        $query1 = $query1->whereDate('emp.doj','<',$from_doj);
                                    elseif($where_doj=='after')
                                        $query1 = $query1->whereDate('emp.doj','>',$from_doj);
                                })
                              ->orWhere(function($query2) use ($from_lwd,$to_lwd,$where_lwd) {
                                    if($where_lwd=='between')
                                        $query2 = $query2->whereBetween('emp.last_working_day',[$from_lwd,$to_lwd]);
                                    elseif($where_lwd=='before')
                                        $query2 = $query2->whereDate('emp.last_working_day','<=',$from_lwd);
                                    elseif($where_lwd=='after')
                                        $query2 = $query2->whereDate('emp.last_working_day','>=',$from_lwd);
                            });
                        });
            }
            
            $emp_data = json_decode($query->get(),true);

            if(in_array('division', $selective_field) || in_array('section', $selective_field))
            {
                $i=0;
                foreach ($emp_data as $emp) {
                    if($emp['division']!='' || $emp['section']!='')
                    {
                        if(in_array('division', $selective_field) && $emp['division']!='')
                        {
                            $div = json_decode(DB::table('division')->select('division')->whereIn('id',json_decode($emp['division']))->get());
                            $divs = array_column($div, 'division');
                            $division = str_replace(']', '', str_replace('[', '', str_replace('"', '', json_encode($divs))));
                            $emp_data[$i]['division']=$division;

                        } 
                        if(in_array('section', $selective_field) && $emp['section']!='')
                        {
                            $sect = json_decode(DB::table('section')->select('section')->whereIn('id',json_decode($emp['section']))->get());
                            $sects = array_column($sect, 'section');
                            $section = str_replace(']', '', str_replace('[', '', str_replace('"', '', json_encode($sects))));
                            $emp_data[$i]['section']=$section;
                        } 
                   }
                   $i++;
                }
            }

            for($i=0;$i<sizeof($select);$i++)
            {
                if(strpos($select[$i],' as ')!==false)
                {
                    $select[$i] = explode(' as ', $select[$i])[1];
                }
                else
                {
                    $select[$i] = explode('.', $select[$i])[1];
                }
            }

            return view('emp_report_table',compact('emp_data','field_map','select'));

            dd(DB::getQueryLog());
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
    }
}
   