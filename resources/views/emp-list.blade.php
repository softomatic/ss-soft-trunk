@extends('layouts.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>EMPLOYEE LIST</h2>

                @if(session('success'))
				    <div class="msg alert alert-success">
				   		<h3><center>{{session('success')}}</center></h3>
				    </div> 
				@endif

				@if(session('failure'))
				    <div class="msg alert alert-danger">
				   		<h4>{!! session('failure') !!}</h4>
				    </div> 
				@endif
            </div>

            <div class="row clearfix">
			       <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                               Employee List
                            </h2>
                          </div>
                        <div class="body table-responsive">
                            <table class="table table-bordered table-striped table-hover js-basic-example dataTable myemptable">
                                <thead>
                                    <tr class="bg-orange">
                                        <th>Emp ID</th>
                                        <th>Employee Name</th>
                                        <th>Branch</th>
										 <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                      </tr>
                                </thead>
                                <tbody>
								<?php 
								$i=1;
								?>
								@foreach($emps as $emp)
                                  
								   <tr>
									 {{ csrf_field() }}
									  <th scope="row">{{ $emp->id }}</th>
                                        <td>{{ $emp->title }} {{ $emp->first_name }} {{$emp->middle_name}} {{$emp->last_name}}</td>
                                        <td>{{ $emp->branch }}</td>
										 <td>{{ $emp->email }}</td>
										 <td>{{ $emp->mobile }}</td>
                                         <td>{{ ucfirst(trans($emp->status)) }}</td>
                                        <td>
										<button type="button" id="" 
												class="btn bg-amber waves-effect edit-modal" data-toggle="modal" 
												data-target="#updateModal" data-id="{{$emp->id}}"
												data-name="">
											<i class="material-icons">open_with</i>
										</button>
			                                <a type="button" target="_blank" href="edit-employee-{{ $emp->id }}" class="btn bg-teal waves-effect">
			                                    <i class="material-icons">create</i>
			                                </a>
											<!-- <button type="button" data-id="{{$emp->id}}" data-target="#deleteModal" data-toggle="modal" class="btn bg-red waves-effect delete-modal">
			                                    <i class="material-icons">delete</i>
			                                </button> -->
	                                    </td>   
                                
								   </tr>
                                 @endforeach       
                                 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
				
        </div>
            
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-pink">
                            <h4 class="modal-title" id="defaultModalLabel">Delete Employee</h4>
                        </div>
						<form action="employee/delete"  method="POST">
                        <div class="modal-body">						
						{{ csrf_field() }}
						<input type="hidden" id="emp_id" name="emp_id"  class="form-control"/>
						       <h5> Are you sure you want to delete this?</h5>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn  bg-deep-orange waves-effect">Yes</button>
                            <button type="button" class="btn bg-blue-grey  waves-effect" data-dismiss="modal">No</button>
                        </div>
						</form>
                    </div>
                </div>
            </div>
    </section>
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-pink">
                            <h4 class="modal-title" id="uModalLabel">Employee profile</h4>
                        </div>
						<div class="modal-body">
			<div class="row clearfix">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					  <div id="profile_pic"></div>
					  <div class="header"><h4 id="emp_name"></h4></div>
						<hr>
				<!-- panel-group start -->		
				 <div class="panel-group" id="accordion_9" role="tablist" aria-multiselectable="true">
                    <fieldset>
					<!-- panel start -->
                        <div class="panel panel-col-orange">
						<!-- panel-heading start -->
                            <div class="panel-heading" role="tab" id="heading1">
                               <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion_9" href="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                       <i class="material-icons">remove_circle</i>  Personal Details
                                    </a>

                                </h4>
                            </div>
				  <!-- panel-heading end -->
					<!-- panel-collapse start -->	
					<div id="collapse1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading1">
                      <!-- panel-body start --> 
					   <div class="panel-body">
                           <div class="row clearfix">
							   <div class="col-md-2">
									<label for="blood_group">Blood Group:</label>
							   </div>
							   <div class="col-md-2" id="blood_group">
							   </div>
							   <div class="col-md-2">
									<label for="DOB">DOB:</label>
							   </div>
							   <div class="col-md-2" id="dob">
							   </div>
							   <div class="col-md-2">
									<label for="DOB">Email-Id:</label>
							   </div>
							   <div class="col-md-2" id="email">
							   </div>
						  </div>
						  <div class="row clearfix">
							   <div class="col-md-2">
									<label for="Mobile">Mobile:</label>
							   </div>
							   <div class="col-md-2" id="mobile">
							   </div>
							   <div class="col-md-2">
									<label for="Gender">Gender:</label>
							   </div>
							   <div class="col-md-2" id="gender">
							   </div>
							   <div class="col-md-2">
									<label for="category">Category:</label>
							   </div>
							   <div class="col-md-2" id="category">
							   </div>
						  </div>
						   	<div class="row clearfix">
						   		<div class="col-md-2">
									<label for="Marital Status">Marital Status:</label>
							   </div>
							   <div class="col-md-2" id="marital_status">
							   </div>

							   	<div class="col-md-2">
										<label for="Local Adress">Adhaar Number:</label>
							   	</div>
							   	<div class="col-md-2" id="adhaar_number">
							   	</div>
							   	<div class="col-md-2">
										<label for="Local Adress">PAN Number:</label>
							   	</div>
							   	<div class="col-md-2" id="pan_number">
							   	</div>
							</div>
							<div class="row clearfix">
								<div class="col-md-2">
									<label for="Local Adress">Local Adress:</label>
							   </div>
							   <div class="col-md-2" id="local_address">
							   </div>

							   <div class="col-md-2">
									<label for="Permanent Adress">Permanent Adress:</label>
							   </div>
							   <div class="col-md-2" id="permanent_address">
							   </div>
							   
							   <div class="col-md-2">
									<label for="Distance from office">Distance from office:</label>
							   </div>
							   <div class="col-md-2" id="distance_from_office">
							   </div>
							</div>
							
							<div class="col-md-12">
                            	<label><h4><u>On Emergency Contact To</u></h4></label>
                            	<div class="row clearfix">
                            		<div class="form-group">
	                                	<div class="col-md-2 col-sm-6 col-xs-12">
	                                		<label for="emergency_call_person">Person Name</label>
	                                	</div>
	                                	 <div class="col-md-4" id="emergency_call_person">
			            				</div>
			            			
	                                	<div class="col-md-2 col-sm-6 col-xs-12">
	                                		<label for="emergency_call_number">Contact Number</label>
	                                	</div>
	                                	 <div class="col-md-4" id="emergency_call_number">
			            				</div>
			            			</div>
        						</div>
                            </div>
						</div> <!-- panel-body end -->
					  </div><!-- panel-collapse end -->	
				  </div><!-- panel end --> 
				</div><!-- panel-group end -->	
				  
				<!-- panel-group start -->		
				 <div class="panel-group" id="accordion_9" role="tablist" aria-multiselectable="true">
                    <fieldset>
					<!-- panel start -->
                        <div class="panel panel-col-orange">
						<!-- panel-heading start -->
                            <div class="panel-heading" role="tab" id="heading5">
                              <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion_9" href="#collapse5" aria-expanded="true" aria-controls="collapse5">
                                        <i class="material-icons">remove_circle</i>    Family Details
                                    </a>

                                </h4>
                            </div>
				  <!-- panel-heading end -->
					<!-- panel-collapse start -->	
					<div id="collapse5" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading5">
                      <!-- panel-body start --> 
					   <div class="panel-body">

					   	 <div class="row clearfix">
					   	 	<div class="col-md-12">
					   	 		<h4>Father</h4>
					   	 	</div>
							  <div class="col-md-1">
									<label for="father_name">Name:</label>
							   </div>
							   <div class="col-md-2" id="father_name">
							   </div>
							   <div class="col-md-1">
									<label for="father_dob">DOB:</label>
							   </div>
							   <div class="col-md-2" id="father_dob">
							   </div>							   
							   <div class="col-md-1">
									<label for="father_adhaar">Adhaar No:</label>
							   </div>
							   <div class="col-md-2" id="father_adhaar">
							   </div>
							   <div class="col-md-1">
									<label for="father_place">Place:</label>
							   </div>
							   <div class="col-md-2" id="father_place">
							   </div>
						  </div>

						   <div class="row clearfix">
						   		<div class="col-md-2">
									<label for="genesis_id">POSS ID:</label>
							   </div>
							   <div class="col-md-2" id="genesis_id">
							   </div>
							   <div class="col-md-2">
									<label for="Department">Department</label>
							   </div>
							   <div class="col-md-2" id="department">
							   </div>
							   <div class="col-md-2">
									<label for="designation">Designation:</label>
							   </div>
							   <div class="col-md-2" id="designation">
							   </div>
							   						   
						  </div>
						  <div class="row clearfix">
						  	<div class="col-md-2">
									<label for="doj">Date of Joining:</label>
							   </div>
							   <div class="col-md-2" id="doj">
							   </div>	
						  		<div class="col-md-2">
									<label for="Status">Status:</label>
							   </div>
							   <div class="col-md-2" id="status">
							   </div>
							   <div class="col-md-2">
									<label for="esic_number">ESIC Number:</label>
							   </div>
							   <div class="col-md-2" id="esic_number">
							   </div>
							   
						  </div>
						  <div class="row clearfix">
						  	   <div class="col-md-2">
									<label for="epf_number">EPF Number:</label>
							   </div>
							   <div class="col-md-2" id="epf_number">
							   </div>
							   <div class="col-md-2">
									<label for="lin_number">LIN Number:</label>
							   </div>
							   <div class="col-md-2" id="lin_number">
							   </div>
							   <div class="col-md-2">
									<label for="uan_number">UAN Number:</label>
							   </div>
							   <div class="col-md-2" id="uan_number">
							   </div>							   
						  </div>
						  <div class="row clearfix">
						  	   <div class="col-md-2">
									<label for="reason_code_0">Reason for code 0:</label>
							   </div>
							   <div class="col-md-2" id="reason_code_0">
							   </div>
							   <div class="col-md-2">
									<label for="last_working_day">Last Working Day:</label>
							   </div>
							   <div class="col-md-2" id="last_working_day">
							   </div>
						  </div> 
						</div> <!-- panel-body end -->
					  </div><!-- panel-collapse end -->	
				  </div><!-- panel end --> 
				</div><!-- panel-group end -->	
				   <!-- panel-group start -->	

			<!-- panel-group start -->		
				 <div class="panel-group" id="accordion_9" role="tablist" aria-multiselectable="true">
                    <fieldset>
					<!-- panel start -->
                        <div class="panel panel-col-orange">
						<!-- panel-heading start -->
                            <div class="panel-heading" role="tab" id="heading2">
                              <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion_9" href="#collapse2" aria-expanded="true" aria-controls="collapse1">
                                        <i class="material-icons">remove_circle</i>    Company Details
                                    </a>

                                </h4>
                            </div>
				  <!-- panel-heading end -->
					<!-- panel-collapse start -->	
					<div id="collapse2" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading2">
                      <!-- panel-body start --> 
					   <div class="panel-body">

					   	 <div class="row clearfix">
							  <div class="col-md-2">
									<label for="Branch">Branch:</label>
							   </div>
							   <div class="col-md-2" id="branch">
							   </div>
							   <div class="col-md-2">
									<label for="genesis_ledger_id">Genesis Ledger ID:</label>
							   </div>
							   <div class="col-md-2" id="genesis_ledger_id">
							   </div>							   
							   <div class="col-md-2">
									<label for="biometric_id">Biometric ID:</label>
							   </div>
							   <div class="col-md-2" id="biometric_id">
							   </div>
						  </div>

						   <div class="row clearfix">
						   	<div class="col-md-2">
									<label for="genesis_id">Genesis ID:</label>
							   </div>
							   <div class="col-md-2" id="genesis_id">
							   </div>
							   <div class="col-md-2">
									<label for="Department">Department</label>
							   </div>
							   <div class="col-md-2" id="department">
							   </div>
							   <div class="col-md-2">
									<label for="designation">Designation:</label>
							   </div>
							   <div class="col-md-2" id="designation">
							   </div>
							   						   
						  </div>
						  <div class="row clearfix">
						  	<div class="col-md-2">
									<label for="doj">Date of Joining:</label>
							   </div>
							   <div class="col-md-2" id="doj">
							   </div>	
						  		<div class="col-md-2">
									<label for="Status">Status:</label>
							   </div>
							   <div class="col-md-2" id="status">
							   </div>
							   <div class="col-md-2">
									<label for="esic_number">ESIC Number:</label>
							   </div>
							   <div class="col-md-2" id="esic_number">
							   </div>
							   
						  </div>
						  <div class="row clearfix">
						  	   <div class="col-md-2">
									<label for="epf_number">EPF Number:</label>
							   </div>
							   <div class="col-md-2" id="epf_number">
							   </div>
							   <div class="col-md-2">
									<label for="lin_number">LIN Number:</label>
							   </div>
							   <div class="col-md-2" id="lin_number">
							   </div>
							   <div class="col-md-2">
									<label for="uan_number">UAN Number:</label>
							   </div>
							   <div class="col-md-2" id="uan_number">
							   </div>							   
						  </div>
						  <div class="row clearfix">
						  	   <div class="col-md-2">
									<label for="reason_code_0">Reason for code 0:</label>
							   </div>
							   <div class="col-md-2" id="reason_code_0">
							   </div>
							   <div class="col-md-2">
									<label for="last_working_day">Last Working Day:</label>
							   </div>
							   <div class="col-md-2" id="last_working_day">
							   </div>
						  </div> 
						</div> <!-- panel-body end -->
					  </div><!-- panel-collapse end -->	
				  </div><!-- panel end --> 
				</div><!-- panel-group end -->	
				   <!-- panel-group start -->	

				<div class="panel-group" id="accordion_9" role="tablist" aria-multiselectable="true">
                    <fieldset>
					    <div class="panel panel-col-orange">
					      <div class="panel-heading" role="tab" id="heading3">
                              <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion_9" href="#collapse3" aria-expanded="true" aria-controls="collapse1">
                                        <i class="material-icons">remove_circle</i>    Salary Details
                                    </a>

                                </h4>
                            </div>
				  	<div id="collapse3" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading3">
                    	 <div class="panel-body">
						   <div class="row clearfix">
							   <div class="col-md-2">
									<label for="epf_option">EPF Option:</label>
							   </div>
							   <div class="col-md-1" id="epf_option">
							   </div>
							   <div class="col-md-2">
									<label for="esic_option">Esic Option:</label>
							   </div>
							   <div class="col-md-1" id="esic_option">
							   </div>
							   <div class="col-md-2">
									<label for="salary">Salary </label>
							   </div>
							   <div class="col-md-1" id="salary">
							   </div>
							   <div class="col-md-2">
									<label for="basic">Bsic + DA :</label>
							   </div>
							   <div class="col-md-1" id="basic">
							   </div>
						  </div>
						
						 </div> 
					  </div>
				  </div>
				</div>	

				 <div class="panel-group" id="accordion_9" role="tablist" aria-multiselectable="true">
                    <fieldset>
					    <div class="panel panel-col-orange">
					        <div class="panel-heading" role="tab" id="heading4">
                              <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion_9" href="#collapse4" aria-expanded="true" aria-controls="collapse4">
                                        <i class="material-icons">remove_circle</i>    Bank Details
                                    </a>

                                </h4>
                            </div>
				  <div id="collapse4" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading4">
                       <div class="panel-body">
						   <div class="row clearfix">
							   <div class="col-md-2">
									<label for="Acc Holder Name">Acc Holder Name:</label>
							   </div>
							   <div class="col-md-2" id="acc_holder_name">
							   </div>
							   <div class="col-md-2">
									<label for="Account No">Account No:</label>
							   </div>
							   <div class="col-md-2" id="acc_no">
							   </div>
							   <div class="col-md-2">
									<label for="Ifsc Code">Ifsc Code:</label>
							   </div>
							   <div class="col-md-2" id="ifsc_code">
							   </div>
						  </div>
						  <div class="row clearfix">
							   <div class="col-md-2">
									<label for="Bank Name">Bank Name:</label>
							   </div>
							   <div class="col-md-2" id="bank_name">
							   </div>
							   <div class="col-md-2">
									<label for="Branch">Branch:</label>
							   </div>
							   <div class="col-md-2" id="bank_branch">
							   </div>
							   
						  </div>
						 </div> <!-- panel-body end -->
					  </div><!-- panel-collapse end -->	
				  </div><!-- panel end --> 
				</div><!-- panel-group end -->	
					  </div>
				  </div>
            </div>
            <div class="modal-footer">
				<button type="button" class="btn bg-blue-grey  waves-effect" data-dismiss="modal">CLOSE</button>
			</div>
		</div>

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
    
    <script type="text/javascript">
	
$(document).ready(function(){  
$.ajaxSetup({
     headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
              });
	  $(document).on('click' , '.edit-modal', function(e){
		
		   var user_id = $(this).data('id');
		 $.ajax({
      url: 'get-details',
      type:'POST',
      data: {'id':user_id},
      dataType: 'json',
      success: function(data){ 
		$.each(data, function(index, element) {	
		var element1 = element;

		if(element1.title==null) element1.title=' ';
		if(element1.first_name==null) element1.first_name=' ';
		if(element1.middle_name==null) element1.middle_name=' ';
		if(element1.last_name==null) element1.last_name=' ';
		if(element1.photo==null) element1.photo=' ';
		if(element1.blood_group==null) element1.blood_group=' ';
		if(element1.dob==null) element1.dob=' ';
		if(element1.email==null) element1.email=' ';
		if(element1.mobile==null) element1.mobile=' ';
		if(element1.gender==null) element1.gender=' ';
		if(element1.category==null) element1.category=' ';
		if(element1.marital_status==null) element1.marital_status=' ';
		if(element1.adhaar_number==null) element1.adhaar_number=' ';
		if(element1.pan_number==null) element1.pan_number=' ';
		if(element1.local_address==null) element1.local_address=' ';
		if(element1.permanent_address==null) element1.permanent_address=' ';
		if(element1.distance_from_office==null) element1.distance_from_office=' ';
		if(element1.emergency_call_number==null) element1.emergency_call_number=' ';
		if(element1.emergency_call_person==null) element1.emergency_call_person=' ';
		if(element1.branch_location_name==null) element1.branch_location_name=' ';
		if(element1.genesis_id==null) element1.genesis_id=' ';
		if(element1.genesis_ledger_id==null) element1.genesis_ledger_id=' ';
		if(element1.biometric_id==null) element1.biometric_id=' ';
		if(element1.esic_number==null) element1.esic_number=' ';
		if(element1.epf_number==null) element1.epf_number=' ';
		if(element1.lin_number==null) element1.lin_number=' ';
		if(element1.uan_number==null) element1.uan_number=' ';
		if(element1.reason_code_0==null) element1.reason_code_0=' ';
		if(element1.last_working_day==null) element1.last_working_day=' ';
		if(element1.department_name==null) element1.department_name=' ';
		if(element1.designation==null) element1.designation=' ';
		if(element1.doj==null) element1.doj=' ';
		if(element1.status==null) element1.status=' ';
		if(element1.acc_holder_name==null) element1.acc_holder_name=' ';
		if(element1.acc_no==null) element1.acc_no=' ';
		if(element1.ifsc_code==null) element1.ifsc_code=' ';
		if(element1.bank_name==null) element1.bank_name=' ';
		if(element1.branch==null) element1.branch=' ';

		if(element1.epf_option==null) element1.epf_option=' ';
		if(element1.esic_option==null) element1.esic_option=' ';

          $('#emp_name').text(element1.title+" "+element1.first_name+" "+element1.middle_name+" "+element1.last_name);
		  $("#profile_pic").html('<img src="' + element1.photo + '" width="100px" height="100px" />');
		  $('#blood_group').text(element1.blood_group);
		  $('#dob').text(element1.dob);
		  $('#email').text(element1.email);
		  $('#mobile').text(element1.mobile);
		  $('#gender').text(element1.gender);
		  $('#category').text(element1.category);
		  $('#marital_status').text(element1.marital_status);
		  $('#adhaar_number').html(element1.adhaar_number);
		  $('#pan_number').html(element1.pan_number);
		  $('#local_address').html(element1.local_address);
		  $('#permanent_address') .html(element1.permanent_address);
		  $('#distance_from_office') .html(element1.distance_from_office);
		  $('#emergency_call_number').text(element1.emergency_call_number);
		  $('#emergency_call_person').text(element1.emergency_call_person);
		  $('#branch').text(element1.branch_location_name);
		  $('#genesis_id').text(element1.genesis_id);
		  $('#genesis_ledger_id').text(element1.genesis_ledger_id);
		  $('#biometric_id').text(element1.biometric_id);
		  $('#esic_number').text(element1.esic_number);
		  $('#epf_number').text(element1.epf_number);
		  $('#lin_number').text(element1.lin_number);
		  $('#uan_number').text(element1.uan_number);
		  $('#reason_code_0').text(element1.reason_code_0);
		  $('#last_working_day').text(element1.last_working_day);
		  $('#department').text(element1.department_name);
		  $('#designation').text(element1.designation);
		  $('#doj') .html(element1.doj);
		  $('#status') .html(element1.status);
		  $('#acc_holder_name') .html(element1.acc_holder_name);
		  $('#acc_no').text(element1.acc_no);
		  $('#ifsc_code').text(element1.ifsc_code);
		  $('#bank_name') .html(element1.bank_name);
		  $('#bank_branch') .html(element1.branch);
		  var sal = JSON.parse(element1.salary);
		  	if(sal.emp_salary['salary']==null) sal.emp_salary['salary']=' ';
			if(sal.emp_salary['basic']==null) sal.emp_basic['salary']=' ';
		
		  $('#salary').html(sal.emp_salary['salary']);
		  $('#basic').html(sal.emp_salary['basic']);
		  if(element1.epf_option==1)
		  	$('#epf_option').html('Yes');
		  else
		  	$('#epf_option').html('No');
		  if(element1.esic_option==1)
		  	$('#esic_option').html('Yes');
		  else
		  	$('#esic_option').html('No');
        });
      }
      
    });    
  });
});
    </script>
    @endsection