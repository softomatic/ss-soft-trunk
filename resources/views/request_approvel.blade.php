@extends('layouts.form-app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />
<link href="plugins/DatePickerBootstrap/css/datepicker.css" rel="stylesheet"/>
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2> Aproval/Request</h2>
            </div>
            <div class="flash-message" id="success-alert">
	            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
			      @if(Session::has('alert-' . $msg))
					<p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
			      @endif
			    @endforeach
			</div>
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                              Create Request
                            </h2>
                        </div>
                        <div class="body">
                        	 <form id="form_search" method="POST" action="raised/request" enctype="multipart/form-data">
                        	 	{{ csrf_field() }}
                        		<div class="row clearfix">
                        			<div class="form-group">
		                            	<div class="col-md-3 col-sm-6 col-xs-12">
			            					<div class="">
			            						<select class="form-control "  placeholder="Shreeshivam Branch" id="requestType" name="request_type" required>
			                                        <option> Select Request</option>
                                                    @foreach($request as $res)
                                                        <option value="{{$res->request_id}}"> {{$res->request_type}} </option>
                                                    @endforeach
			                                    </select>
			                                    
			            					</div>
										</div>
									</div>
								</div>
								<div id="overtime">
								<div class="row clearfix">
                        			<div class="form-group">
										<div class="col-md-3 col-sm-6 col-xs-12">
			            					<select autocomplete="none" type="text" name="emp_id" id="emp_id" class="form-control emp_id select2" placeholder="Employee" value="{{ old('emp_id') }}" >
			                                        <option value=''> Select Request</option>
                                                    @foreach($emp as $emps)
                                                        <option value="{{$emps->id}}"> {{$emps->first_name.' '.$emps->middle_name.' '.$emps->last_name}} </option>
                                                    @endforeach
			                                </select>
			            				</div>
			            				<div class="col-md-3 col-sm-6 col-xs-12">
			            					<input autocomplete="none" type="text" name="from_date" id="dob" class="form-control datepicker" placeholder="From date" value="{{ old('dob') }}" >
			            				</div>
										<div class="col-md-3 col-sm-6 col-xs-12">
			            					<input autocomplete="none" type="text" name="to_date" id="dob" class="form-control datepicker" placeholder="To date" value="{{ old('dob') }}" >
										</div>
										
			            				<div class="col-md-3 col-sm-6 col-xs-12">
											 <input type="text" name="grace_period" id="" class="timepicker form-control time_picker1" placeholder="Choose exemption period" required>
			            					
			            				</div>
									</div>
								</div>
								<div class="row clearfix">
									<div class="col-md-3 col-sm-6 col-xs-12">
			            				<div class="">
			            					<textarea class="form-control" rows="4" name="reason" placeholder='Reason for raising request'></textarea>
			            				</div>
			            			</div>
								</div>
								<div class="row clearfix">
									<div class="col-md-3 col-sm-6 col-xs-12">
			            				<div class="">
			            					<input autocomplete="none" type="submit"  class="form-control btn btn-xs btn-success" placeholder="Submit" >
			            				</div>
			            			</div>
								</div>
							</div>
                        	</form>
                        	<!--<?php echo $login_type= Session::get('role_id'); ?>-->
                        	<div id="leave_request">
								<div class="row clearfix">
                        			<div class="form-group">
                        			    <input autocomplete="none" type="checkbox" id="md_checkbox_22" class="filled-in chk-col-pink">
			            						<label for="md_checkbox_22" style="font-size: 14px"><b>For me</b></label>
										<div class="col-md-3 col-sm-6 col-xs-12">
			            					<select autocomplete="none" type="text" name="emp_id" id="check_for" class="form-control emp_id select2" placeholder="Employee" value="{{ old('emp_id') }}" >
			                                        <option value='0'> Select Request</option>
                                                    @foreach($emp as $emps)
                                                        <option value="{{$emps->id}}"> {{$emps->first_name.' '.$emps->middle_name.' '.$emps->last_name}} </option>
                                                    @endforeach
			                                </select>
			            				</div>
			            				<div class="col-md-3 col-sm-6 col-xs-12">
			            					<input autocomplete="none" type="text" name="from_date" id="dob" class="form-control datepicker" placeholder="From date" value="{{ old('dob') }}" >
			            				</div>
										<div class="col-md-3 col-sm-6 col-xs-12">
			            					<input autocomplete="none" type="text" name="to_date" id="dob" class="form-control datepicker" placeholder="To date" value="{{ old('dob') }}" >
										</div>
										
			            				<div class="col-md-3 col-sm-6 col-xs-12">
											 <input type="text" name="grace_period" id="" class="timepicker form-control time_picker1" placeholder="Choose exemption period" required>
			            					
			            				</div>
									</div>
								</div>
								<div class="row clearfix">
									<div class="col-md-3 col-sm-6 col-xs-12">
			            				<div class="">
			            					<textarea class="form-control" rows="4" name="reason" placeholder='Reason for raising request'></textarea>
			            				</div>
			            			</div>
								</div>
								<div class="row clearfix">
									<div class="col-md-3 col-sm-6 col-xs-12">
			            				<div class="">
			            					<input autocomplete="none" type="submit"  class="form-control btn btn-xs btn-success" placeholder="Submit" >
			            				</div>
			            			</div>
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>          
        </div>
		{{-- Tab View started --}}
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                VIEW REQUEST
                                <small> </small>
                            </h2>
                             
                        </div>
                        <div class="body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs tab-nav-right" role="tablist">
                                <li role="presentation" class="active"><a href="#home" data-toggle="tab">Raised Requests</a></li>
                                <li role="presentation" class="{{}}"><a href="#profile" data-toggle="tab">Requests For Approval</a></li>
                             <?php if(Session::get('role')=='hr') { ?>   <li role="presentation" class=""><a href="#attendance" data-toggle="tab">OT  request </a></li> 
                                <li role="presentation" class=""><a href="#ot" data-toggle="tab"> I/O punch miss request </a></li> <?php }?>
                                
                               
                               
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane fade in active" id="home">
                                   
                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable myemptable">
                                <thead>
                                     
                                    <tr class="bg-orange">
                                        <th>#</th>
                                        <th>Request Type</th>
                                        <th>Raised For</th>
                                        <th>Accept/Reject</th>
                                        <th>Status</th>
                                        <th>Raised on date/time</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                <?php  	$k=1; ?>
								@foreach($raised_request as $row)
								<tr> 
									<td>{{$k++}} </td>
									<td>{{$row->request_type}} </td>
									<td>{{$row->first_name.' '.$row->middle_name.' '.$row->last_name}}  </td>
									<td>{{' '}}  </td>
									<td>{{$row->status}}  </td>
									<td>{{$row->created_at}}  </td>
								</tr>
								@endforeach
                               
                                </tbody>
                                </table>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="profile">
                                    <table class="table table-bordered table-striped table-hover js-basic-example dataTable myemptable">
                                <thead>
                                     
                                    <tr class="bg-orange">
                                        <th>#</th>
                                        <th>Request Type</th>
                                        <th>Raised For</th>
										<th>Approve</th>
										<th>Reject</th>
                                        <th>Status</th>
                                        <th>Raised on date/time </th>
                                        <th>Approve/Reject On  </th>
                                        <th>Of date</th>
                                        <th>Overtime hours</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php  	$i=1;
                                $id='';?>
                                
								@foreach($final as $row1)
								<?php if($row1->id != $id) {?>
								<tr> 
									<td>{{$i}}  </td>
										<td>{{$row1->request_type}}  </td>
									<td>{{$row1->first_name.' '.$row1->middle_name.' '.$row1->last_name}}  </td>
									<td><?php if($row1->status=='Pending'){?>
										 <form id="" method="POST" action="approve/request" enctype="">
											 {{ csrf_field() }}
											 <input type="hidden" name="aid" value="{{$row1->id}}">
											 <input type="hidden" name="request_type" value="{{$row1->request_type_id}}">
										<button type="submit" class="btn btn-success waves-effect">Approve</button><?php } ?> {{' '}}  </td>	 
										 </form>
										
									<td><?php if($row1->status=='Pending'){?>
										<button class="btn btn-danger waves-effect" id="edit-modal"
										class="btn bg-teal waves-effect " data-toggle="modal" 
										data-target="#updateModal" data-requestid="{{$row1->id}}">Reject</button>
									<?php } ?> {{''}}</td>
									<td>{{$row1->status}}  </td>
									<td>{{$row1->created_at}}  </td>
									<td>{{$row1->updated_at}}  </td>
									<td><?php echo $date=DB::table('daily_report')->where('request_id',$row1->id)->value('date');
									 ?> </td>
									<td> <?php echo $date=DB::table('daily_report')->where('request_id',$row1->id)->value('overtime_hours'); ?></td>
								</tr>
								
							<?php 
							$id=$row1->id;
							$i++; } ?>
								@endforeach
                               
                                </tbody>
                                </table>
                                </div>     
                                
                                <!--new-->
                                <div role="tabpanel" class="tab-pane fade" id="ot"> 
                                <p style="color:red;"> * Time is showing in <b>Add In out</b> we consider it as  in time if you want to change it please fill both in and out</p>
                                    <table class="table table-bordered table-striped table-hover js-basic-example dataTable myemptable">
                                    <thead>
                                        <tr class="bg-orange">
                                            <th>#</th>
                                            <th>Raised For</th>
    										<th>Request</th>
    										<th>Of date</th>
                                            <th>Add in out</th>
                                            <th> Add Attendance </th>
                                            <th> Absent </th>
                                        </tr>
                                    </thead>
                                    <?php $m=1;?>
                                    <tbody>
                                         <?php $role_id= Session::get('role_id'); 
                                    if($role_id==1 || $role_id==2){
                                   ?>
                                       @foreach($miss_punch_final as $row_p)
                                        <tr>
                                            <form action="add_attendance" method="POST">
                                                 {{ csrf_field() }}
                                            <td>{{$m++}} </td>
                                            <td>{{$row_p->first_name.' '.$row_p->middle_name.' '.$row_p->last_name}} </td>
                                            <td> In Out punch miss</td>
                                            <td>{{$row_p->date}}</td>
                                            <td> <span class="badge badge-info">@if($row_p->initial_in !='00:00:00') {{'In time:'.$row_p->initial_in}} @endif</span><br>
                                           
                                            <input type="hidden" name="emp_id" value="{{$row_p->emp_id}}">
                                            <input type="hidden" name="date" value="{{$row_p->date}}">
                                            <input type="hidden" name="in_time" value="{{$row_p->initial_in}}">
                                            <input type="text" name="attendace_in"  class="timepicker  time_picker1" placeholder="Enter in time" ><br>
								            <input type="text" name="attendace_out" class="timepicker  time_picker1" placeholder="Enter out time" >
								            </td>
								            <td><button type="submit" class="btn bg-green  waves-effect">Submit</button></td>
								            </form>
								             <td>
								            <form action="mark_absent" method="POST">
                                                 {{ csrf_field() }}
                                            <input type="hidden" name="emp_id_a" value="{{$row_p->emp_id}}">
                                            <input type="hidden" name="date_a" value="{{$row_p->date}}">
                                           <button type="submit" class="btn bg-red  waves-effect">Absent</button></td>
                                            </form>
                                        </tr>
                                        @endforeach
                                       <?php } ?> 
                                    </tbody>
                                </table>
                                </div>              
                                <!--end new-->
                                <!--new 1-->
                               
                                <div role="tabpanel" class="tab-pane fade" id="out_punch">
                                   
                                    <table class="table table-bordered table-striped table-hover js-basic-example dataTable myemptable">
                                        
                                    <thead>
                                        <tr class="bg-orange">
                                            <th>#</th>
                                            <th>Raised For</th>
    										<th>Possible Request</th>
    										<th>Of date</th>
                                            <th>OT</th>
                                            <th>Out punch miss</th>
                                            <th> Attendance </th>
                                        </tr>
                                    </thead>
                               
                                </table>
                                </div>              
                                <!--end new 1-->
                                <!--new 2-->
                                
                                <div role="tabpanel" class="tab-pane fade" id="attendance">
                                    <table class="table table-bordered table-striped table-hover js-basic-example dataTable myemptable">
                                    <thead>
                                        <tr class="bg-orange">
                                            <th>#</th>
                                            <th>Raised For</th>
    										<th>Of date</th>
                                            <th>OT</th>
                                            <th> Action </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        <?php $j=1;?>
                                        @foreach($over_time_request_final as $overtime)
                                        <tr>
                                          
                                           <td>{{$j++}}</td>
                                           <td>{{$overtime->first_name.' '.$overtime->middle_name.' '.$overtime->last_name}} </td>
                                           <td>{{$overtime->date}}</td>
                                           <td> <span class="badge badge-info">{{$overtime->calculated_overtime}} </span></td>
                                           <td>
                                                <form action="add_overtime"  method="POST">
        						            {{ csrf_field() }}
        						         
        						            <input type="hidden" name="overtime_id" value="{{$overtime->emp_id}}" >
        						            <input type="hidden" name="date" value="{{$overtime->date }}" >
        						            <input type="hidden" name="overtime" value="{{$overtime->calculated_overtime}}" >
        									<button type="submit" class="btn bg-green waves-effect">Raise</button>
        									</form>
                                               
                                               
                                           </td>
                                        </tr>
                                        @endforeach
                                    
                                    </tbody>
                                    
                                      
                                </table>
                               
                                </div>   
                               
                                <!--end new 2-->
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		
		</section>
			<div class="modal fade" id="updateModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-pink">
                            <h4 class="modal-title" id="uModalLabel">Reject request</h4>
                        </div>
						<form action="reject/request"  method="POST">
						{{ csrf_field() }}
                        <div class="modal-body">
						
						<div class="row clearfix">
						<div class="col-sm-2" align="right">
								 <label> Reason</label>
								</div>
                        <div class="col-sm-8">
                            <div class="form-line">
								    <input type="hidden" id="rid" name="request_id"  class="form-control">
                                    <textarea class="form-control" name="reject_reason" placeholder="Write reason to reject" rows="3" required></textarea><br>
									
                            </div>
                        </div>
                      
                            </div> 
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn  bg-deep-orange waves-effect">Submit</button>
                            <button type="button" class="btn bg-blue-grey  waves-effect" data-dismiss="modal">Close</button>
                        </div>
						</form>
                    </div>
                </div>
            </div>
@endsection
@section('jquery')
    <link href="plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">
    
    <script src="plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>
    <script src="js/pages/tables/jquery-datatable.js"></script>
    
    
    <script src="plugins/momentjs/moment.js"></script>
    <script src="plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
    
    <script src="plugins/DatePickerBootstrap/js/bootstrap-datepicker.js"></script>
    <script src="plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>
    <script src="plugins/jquery-inputmask/inputmask/jquery.inputmask.js"></script>
	<script type="text/javascript">
		
      $(document).ready(function() {
		$("#overtime").hide();
		$("#leave_request").hide();
        $(".employee").select2({
	     placeholder: "Approvel Level 1",
	     allowClear: true,
	     multiple: true
	    }); 

		$(".employee2").select2({
	     placeholder: "Approvel Level 2",
	     allowClear: true,
	     multiple: true
	    }); 

		$("#requestType" ).change(function() {
		var value=	$("#requestType option:selected" ).text();
		if(value.indexOf('Exemption period') != -1){
		
			$("#overtime").show();
		}
		if(value.indexOf('Leave request') != -1){
		
			$("#leave_request").show();
		}
		
		});
		$('.datepicker').datepicker().on('changeDate', function (ev) {
		    $('.dropdown-menu').hide();
		});	

	});
	 $(".emp_id").select2({
	     placeholder: "Select Employee",
	     allowClear: true
	    });
	
	$('.time_picker1').bootstrapMaterialDatePicker
			({
				date: false,
				shortTime: false,
				format: 'HH:mm'
			});
			
			$('#md_checkbox_22').click(function(){
		
            if($(this).prop("checked") == true){
                $('#check_for').attr('disabled',true);
                $('#check_for').val("0");

            }
            else
            {
            	$('#check_for').attr('disabled',false);
            }
		});
	</script>
<script>
$(document).ready(function() {
	
	$(document).on('click', '#edit-modal', function(){
	
	$('#rid').val($(this).data('requestid'));
	$('#updateModal').modal('show');
    });
	 
 });
</script>
@endsection