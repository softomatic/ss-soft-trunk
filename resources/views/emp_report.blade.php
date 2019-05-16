@extends('layouts.form-app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Employee Report</h2>
                 <div class="flash-message" id="success-alert">
		            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
				      @if(Session::has('alert-' . $msg))
						<p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
				      @endif
				    @endforeach
				</div>
            </div>

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>Select Employee Report</h2>
                        </div>
                        <form action="notice-board/submit"  method="POST">
                            {{ csrf_field() }}
                        <!--body Start-->   
                            <div class="body">
                                <div class="row clearfix">
                                    <div class="col-lg-4 col-md-4">
                                        <div class="col-md-4 col-sm-6 col-xs-6">
                                            <label for="branch_location_id">Branch</label>
                                        </div>
                                        <div class="col-md-8 col-sm-6 col-xs-6">
                                            <div class="form-line">     
                                                 <select class="form-control show-tick select2" placeholder="Shreeshivam Branch" id="branch_location_id" name="branch_location_id" multiple="true" required>
                                                    <option></option>
                                                    @foreach($branches as $branch)
                                                    @if(old('branch_location_id') == $branch->id)
                                                        <option value="{{$branch->id}}" selected>{{$branch->branch}}</option>   
                                                    @else
                                                        <option value="{{$branch->id}}">{{$branch->branch}}</option>
                                                    @endif  
                                                    @endforeach
                                                </select>
                                                <span class="text-danger">{{ $errors->first('branch_location_id') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-4">
                                        <div class="col-md-4 col-sm-6 col-xs-6">
                                            <label for="department">Department</label>
                                        </div>
                                        <div class="col-md-8 col-sm-6 col-xs-6">
                                            <div class="form-line">     
                                                <select class="form-control show-tick select2" placeholder="Department" id="department" name="department" required>
                                                <option></option>
                                                @foreach($depts as $dept)

                                                        @if(old('department') == $dept->id)
                                                            <option value="{{$dept->id}}" selected>{{$dept->department_name}}</option>  
                                                        @else
                                                            <option value="{{$dept->id}}">{{$dept->department_name}}</option>
                                                        @endif  

                                                @endforeach
                                                </select>
                                                <span class="text-danger">{{ $errors->first('department') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-4">
                                        <div class="col-md-4 col-sm-6 col-xs-6">
                                            <label for="designation">Designation</label>
                                        </div>
                                        <div class="col-md-8 col-sm-6 col-xs-6">
                                            <div class="form-line">     
                                                <select autocomplete="none" type="text" name="designation" id="designation" class="form-control select2" placeholder="Designation" required>
                                                    <option></option>
                                                </select>
                                                <span class="text-danger">{{ $errors->first('designation') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row clearfix">

                                    <div class="col-lg-4 col-md-4">
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <label for="division">Division</label>
                                        </div>
                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                            <div class="form-line">     
                                                <select class="form-control show-tick select2" placeholder="division" id="division" name="division[]" multiple="multiple">
                                                <option></option>
                                                @foreach($division as $div)
                                                        @if(old('division[]')!='')
                                                            @if(in_array($div->id,json_decode(old('division[]'))))
                                                            <option value="{{$div->id}}" selected>{{$div->division}}</option>
                                                            @endif  
                                                        @else
                                                <option value="{{$div->id}}">{{$div->division}}</option>
                                                        @endif                      
                                                @endforeach
                                                </select>
                                                <span class="text-danger">{{ $errors->first('division') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-4">
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <label for="section">Section</label>
                                        </div>
                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                            <div class="form-line">     
                                                 <select class="form-control show-tick select2" placeholder="section" id="section" name="section[]" multiple="multiple">
                                                    <option></option>
        
                                                </select>
                                                <span class="text-danger">{{ $errors->first('section') }}</span>
                                            </div>
                                        </div>
                                    </div>  

                                    <div class="col-lg-4 col-md-4">
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <label for="out_source">OutSource</label>
                                        </div>
                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                            <div class="form-line">     
                                                <select class="form-control show-tick select2" placeholder="out_source" id="out_source" name="out_source" required>
                                                <option></option>
                                                @if(old('out_source') == '1')
                                                    <option value="1" selected>Yes</option> 
                                                @else
                                                    <option value="1">Yes</option>
                                                @endif  
                                                @if(old('out_source') == '0')
                                                    <option value="0" selected>No</option>  
                                                @else
                                                    <option value="0" selected>No</option>
                                                @endif  
                                                </select>
                                                <span class="text-danger">{{ $errors->first('out_source') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row clearfix">

                                               

                                </div>
                            </div>
                        <!--body End--> 
                        </form>

                        <div class="body table-responsive" id="emp_report_table"> 
                           <!--  <table class="table table-bordered table-striped table-hover js-basic-example dataTable" id="mytable">
                                <thead>
                                    <tr class="bg-orange">
                                        <th>#</th>
                                        @if(Session::get('role')=='admin')
                                        <th>Generated For</th>
                                        @endif
                                        <th>Notification</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="notification_table">
                                    
                                </tbody>                   	
                            </table>      -->                                         
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </section>

            <style type="text/css">
                .accept,.reject,.showreason
                {
                    margin:2.5px;
                }
            </style>
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
        var autoLoad = setInterval(
                function ()
                {
                    $('#notification_table').load('notification_table', function() {
                    });

                }, 30000);
    </script>
    <script type="text/javascript">
    	$(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#branch_location_id").select2({
             placeholder: "Select Branch Location",
             //allowClear: true,
             multiple: true
            });

            $("#title").select2({
             placeholder: "Select Title",
             allowClear: true
            });

            $("#department").select2({
             placeholder: "Select Department",
             allowClear: true
            });
            $("#designation").select2({
             placeholder: "Select Designation",
             allowClear: true
            });

            $("#out_source").select2({
             placeholder: "Select Out Source",
             allowClear: true
            });
            $("#division").select2({
             placeholder: "Select Division",
             allowClear: true,
             multiple:true
            });
            $("#section").select2({
             placeholder: "Select Section",
             allowClear: true,
             multiple:true
            });
            $("#blood_group").select2({
             placeholder: "Select Blood Group",
             allowClear: true
            });

            $("#gender").select2({
             placeholder: "Select Gender",
             allowClear: true
            });
            $("#spouse_gender").select2({
             placeholder: "Select Spouse Gender",
             allowClear: true
            });
             $("#status").select2({
             placeholder: "Select Status",
             allowClear: true
            });
             $("#marital_status").select2({
             placeholder: "Select Marital Status",
             allowClear: true
            });
             $("#role").select2({
             placeholder: "Select User Role",
             allowClear: true
            });
             $("#category").select2({
             placeholder: "Select Category",
             allowClear: true
            });
            $("#bank_name").select2({
             placeholder: "Select Bank",
             allowClear: true
            });

            $('#department').change(function(){
                var dept_id = $(this).val();
                $.ajax({
                type:'GET',
                url:'add-employee/get_desig',
                data:{dept_id : dept_id},
                success:function(data){
                    //alert(data);
                    $('#designation').empty().html(data)
                  // refreshTable();
                }
                });
            });

            $('#division').change(function(){
                var division_id = $(this).val();
                $.ajax({
                type:'GET',
                url:'add-employee/get_section',
                data:{division_id : division_id},
                success:function(data){
                    //alert(data);
                    $('#section').empty().html(data)
                  // refreshTable();
                }
                });
            });

            $(document).on('click','.dismiss',function(e){
                e.preventDefault();
                var id = $(this).closest('tr').find('.myid').val();
                
                $.ajax({
                type:'POST',
                datatype: 'text',
                url:'notification/action',
                data:{id:id},
                success:function(data){
                    if(data=='1')
                    refreshTable();
                }
                });
            });
            
    	});

        function refreshTable()
          {
            $('#notification_table').load('notification_table', function() {
            });
          }
    </script>
    @endsection
