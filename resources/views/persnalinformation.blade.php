@extends('layouts.form-app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .fixedblog{
       min-height: 280px ;
        width: 100%;
    }
    .margin{ 
        margin-bottom: unset !important;
    }
    @media (max-width: 768px) {
  .body {
    height: unset !important;
  }
}
</style>
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <h2>Personal Information</h2>
        </div>
        <div class="flash-message" id="success-alert">
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close"
                    data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
            @endforeach
        </div> <!-- end .flash-message -->
        <!-- Content here -->
        <div class="row clearfix">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card fixedblog">
                    <div class="header">
                        <h2 style="color:darkorange;">
                            Salary Details
                        </h2>
                    </div>
                    <div class="body">
                        @foreach ($persnal_infos as $persnal_info)
                        <div class="row margin">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Name </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">
                                {{
                                   $persnal_info->first_name.' '.$persnal_info->middle_name.' '.$persnal_info->last_name }}
                            </div>
                        </div>

                        <div class="row margin">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Email </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">
                                {{
                                $persnal_info->email }}
                            </div>
                        </div>

                        <div class="row margin">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Contact No.</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">
                                {{
                                $persnal_info->mobile }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Adhaar Number </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">
                                {{
                                $persnal_info->adhaar_number }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Pan Number </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">
                                {{
                                $persnal_info->pan_number }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Epf Number </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">
                                {{
                                $persnal_info->epf_number }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Esic Number </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-md-6v">
                                {{
                                $persnal_info->esic_number }}
                            </div>
                        </div>

                        @endforeach

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Salary </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">
                                <?php 
                       $salary1='';
                        $emp_sal="";
                        $user_sal="";
				        foreach ($sal as $sal_user){
                        $salary1 = ($sal_user->salary);
                        $salary1= json_decode($salary1,true);
					     foreach ($salary1 as $item) {
						  foreach ($item as $item2) {
							  $user_sal = $item2;
                             echo  $user_sal."<br>";
                             break;
						  }
					    }
				      }
                    ?>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Basic Salary </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">
                                <?php 
                            $basic_salary1='';
                            $emp_sal="";
                            $user_sal1="";
                            foreach ($sal as $sal_user2){
                                
                            $basic_salary1 = ($sal_user2->salary);
                            $basic_salary1= json_decode($basic_salary1,true);
                            foreach ($basic_salary1 as $item) {
                                if($item['basic']=='' || $item['basic']=='0'){
                                    echo  $item['salary']."<br>";
                                    break;
                                }
                                else {
                                    echo  $item['basic']."<br>";
                                 break; 
                                }
                           
                            }
                            }
                        ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card fixedblog">
                    <div class="header">
                        <h2 style="color:darkorange;">
                            Family Details
                        </h2>
                    </div>
                    <div class="body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Father`s Name </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">

                            <?php 
                               $father1='';
                                $father="";
                                $user_father="";
                                foreach ($fam as $fam_father){
                                $father1 = ($fam_father->father);
                                $father1= json_decode($father1,true);
                                 foreach ($father1 as $item) {
                                
                                      $user_father = $item;
                                     echo  $user_father."<br>";
                                     break;
                                  
                                }
                              }
                            ?>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Mother`s Name </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">


                                <?php 
                               $mother1='';
                                $mother="";
                                $user_mother="";
                                foreach ($fam as $fam_father){
                                $mother1 = ($fam_father->mother);
                                $mother1= json_decode($mother1,true);
                                 foreach ($mother1 as $item) {
                                      $user_mother = $item;
                                     echo  $user_mother."<br>";
                                     break;
                                  
                                }
                              }
                            ?>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Spouse Name </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">

                                <?php 
                               $spouse1='';
                                $mother="";
                                $user_spouse="";
                                foreach ($fam as $fam_father){
                                $spouse1 = ($fam_father->spouse);
                                $spouse1= json_decode($spouse1,true);
                                 foreach ($spouse1 as $item) {
                                      $user_spouse = $item;
                                     echo  $user_spouse."<br>";
                                     break;
                                  
                                }
                              }
                            ?>

                            </div>
                        </div>
                        <div class="row">
                        </div>

                        <div class="row">
                            <div class="col-xs-11 col-md-3 col-lg-3 margin">
                                <b>Children`s </b>
                            </div>
                            <div class="col-xs-1 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-md-6 col-lg-6 margin">

                                <?php 
                               $child1='';
                                $mother="";
                                $user_child="";
                                foreach ($fam as $fam_child){
                                $child1 = $fam_child->children;
                                $child1= json_decode($child1,true);
                            $child2= json_decode($child1[0]);
                           
                              foreach ($child2 as $item) {
                                  echo ($item ."<br>");
                               break;
                              }
                           
                              }
                            ?>

                            
                           
                        </div>
                        </div>
                        <div class="row"><br><br>
                            <div class=" col-sm-3"  >
                            <button type="button" name="submit" id="bar" class="btn bg-green waves-effect edit-modal"
                                data-toggle="modal" data-target="#updateModal" data-id="{{$emp_id}}" data-fname="{{$user_father}}"
                                data-mname="{{$user_mother}}" data-sname="{{$user_spouse}}"><i class="material-icons">create</i><span
                                    class="icon-name">Edit</span></button>
                        </div>
                        </div>

                     
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card fixedblog">
                    <a href="attendance-report">
                        <div class="header">
                            <h2 style="color:darkorange;">
                                Attendance
                            </h2>
                        </div>
                    </a>
                    <div class="body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Attendance </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">
                              
                                {{ $att}}
                              
                            </div>
                            
                        </div>
                        <div class="row"><br><br><br><br>
                            <div class="col-sm-3">
                            <a href="attendance-report"><button type="button" name="submit" id="bar" class="btn bg-green waves-effect"><i class="material-icons">collections_bookmark</i><span
                            class="icon-name"> Attaindance Details</span></button></a>
                        </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card fixedblog">
                    <div class="header">
                        <h2 style="color:darkorange;">
                            Bank Details
                        </h2>
                    </div>
                    <div class="body">
                        @foreach ($persnal_infos as $persnal_info)
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Bank Name </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">
                             
                                {{ $persnal_info->bank_name }}
                               
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>Account Number </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">
                                
                                {{ $persnal_info->acc_no }}
                             
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 margin">
                                <b>IFSC code </b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-1 col-lg-1 margin">
                                <b> :</b>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 margin">
                               
                                {{ $persnal_info->ifsc_code }}
                                
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

</section>

<div class="modal fade" id="updateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-pink">
                <h4 class="modal-title" id="uModalLabel">Edit Family Details</h4>
            </div>

            <div class="modal-body">
                <form action="update_family_details" method="POST">
                    {{ csrf_field() }}
                    <div class="row clearfix">
                        <div class="col-sm-3" align="right">
                            <label> Father`s Name</label>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-line">
                                <input type="hidden" id="fid" name="id" class="form-control" placeholder="Department Name"
                                    required />
                                <input type="text" id="f" name="father_name" class="form-control" placeholder="Father`s Name" />
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row clearfix">
                        <div class="col-sm-3" align="right">
                            <label> Mother`s Name</label>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-line">

                                <input type="text" id="m" name="mother_name" class="form-control" placeholder="Mother`s Name" />
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row clearfix">
                        <div class="col-sm-3" align="right">
                            <label> Spouse</label>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-line">

                                <input type="text" id="s" name="spouse_name" class="form-control" placeholder="Spouse Name" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn  bg-deep-orange waves-effect">Save Changes</button>
                    <button type="button" class="btn bg-blue-grey  waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('jquery')
<script>
    $(document).ready(function () {
        $(document).on('click', '.edit-modal', function () {
            $('#fid').val($(this).data('id'));
            $('#f').val($(this).data('fname'));
            $('#m').val($(this).data('mname'));
            $('#s').val($(this).data('sname'));
            $('#updateModal').modal('show');
        });

        $("#success-alert").fadeTo(2000, 500).slideUp(500, function () {
            $("#success-alert").slideUp(500);
        });
    });
</script>

@endsection