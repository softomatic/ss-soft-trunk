@extends('layouts.form-app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Employee Shift</h2>
            </div>
<div class="flash-message" id="success-alert">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
      @if(Session::has('alert-' . $msg))
		<p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
      @endif
    @endforeach
  </div> <!-- end .flash-message -->
            <!-- Content here -->
        <div class="row clearfix">
		    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		        <div class="card">
		            <div class="header">
		                <h2>
		                   Add/Edit Employee Shift
		                 </h2>                          
		            </div>
		            <div class="body">
		                <div class="row clearfix">

		                     <div class="row">                      
		                        <div class="col-lg-1 col-md-6 col-sm-6" >
		                         <label>Branch</label>
		                        </div>
		                        <div class="col-lg-3 col-md-6 col-sm-6">
		                           <div class="form-line">
		                                <select name="branch_location_id" id="branch_location_id" class="form-control select2" placeholder="branch" required>
		                                    <option value="">Select Branch</option>
		                                    @foreach($branches as $branch)
		                                    <option value="{{$branch->id}}">{{$branch->branch}}</option>
		                                    @endforeach
		                                </select>
		                                <span class="text-danger"></span>
		                            </div>
		                        </div> 
		                        <div class="col-lg-1 col-md-2 col-sm-6">
		                            <div class="form-group">
		                                <button type="button" name="create" id="edit" class="btn btn-md bg-orange waves-effect"><i class="material-icons">edit</i><span class="icon-name">Edit</span></button>
		                            </div>
		                        </div>                                        
		                        <div class="col-lg-1 col-md-2 col-sm-6">
		                            <div class="form-group">
		                                <button type="button" name="create" id="create" class="btn btn-md bg-blue waves-effect"><i class="material-icons">save</i><span class="icon-name">Create</span></button>
		                            </div>
		                        </div>   
		                    </div>
		                </div>
		            </div>
		       </div>

		       	<div class="card" id="mycard">
		            <div class="body">
		                <div class="row clearfix"> 	                                 
	                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	                           <div class="table-responsive" id="shift_table">
	                                
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	       </div>
	    </div>
	</div>
</section>


<style type="text/css">
    .table-responsive
    {
        min-height: .01%;
        overflow-x: initial;
    }
</style>

@endsection
@section('jquery')
 <link href="plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">
    <script src="plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
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
<script>
$(document).ready(function() {
	$('#mycard').hide();
	$('.datepicker').datepicker().on('changeDate', function (ev) {
		    $('.dropdown-menu').hide();
		});
		
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

    $('#create').click(function(){
    	if($('#branch_location_id').val()!='')
    	{    		
    		$('#branch_location_id').closest('div').find('span.text-danger').html('');
    		$.ajax({
	            type:'POST',
	            datatype: 'text',
	            url:'get_branch_shift',
	            data:{branch:$('#branch_location_id').val()},
	            success:function(data){
	            	$('#mycard').show();
	                $('#shift_table').html(data);

                  $('#employee_shift_form').find('.datepicker').datepicker().on('changeDate', function (ev) {
                      $('.dropdown-menu').hide();
                  });
	            },
	            error : function(err)
	            {
	                alert('error');
	            }
            });
    	}
    	else
    	{
    		$('#branch_location_id').closest('div').find('span.text-danger').html('select branch');
    		$('#shift_table').empty().html('');
    	}
    });

    $('#edit').click(function(){
    	if($('#branch_location_id').val()!='')
    	{    		
    		$('#branch_location_id').closest('div').find('span.text-danger').html('');
    		$.ajax({
	            type:'POST',
	            datatype: 'text',
	            url:'get_edit_branch_shift',
	            data:{branch:$('#branch_location_id').val()},
	            success:function(data){
	            	$('#mycard').show();
	                $('#shift_table').html(data);

                  $('#edit_employee_shift_form').find('.datepicker').datepicker().on('changeDate', function (ev) {
                      $('.dropdown-menu').hide();
                  });
	            }
            });
    	}
    	else
    	{
    		$('#branch_location_id').closest('div').find('span.text-danger').html('select branch');
    		$('#shift_table').empty().html('');
    	}
    });
    
	$(document).on('change','#emp_shift',function(){
		
		if($(this).val()!='')
		{
			$('.shift').each(function(){
				$(this).val($('#emp_shift').val());
			});
		}
		else
		{
			$('.shift').each(function(){
				$(this).val('');
			});
		}
		
	});

   //  $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
  	// 	$("#success-alert").slideUp(500);
  	// });
    
    $(document).on('submit','#employee_shift_form',function(e){
        e.preventDefault();
        $.ajax({
          type:'POST',
          datatype: 'text',
          url:'add_employee_shift',
          data:$('#employee_shift_form').serialize(),
          success:function(result){
              var data = JSON.parse(result);             
              if(data.status=='success')
              { 
                alert(data.success);
                $('#success-alert').html('<p class="alert alert-success">'+data.success+' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>').show();
                window.location.hash = '#success-alert';
                $('#shift_table').empty();
                $('#mycard').hide();
              }
              else
              {
                alert(data.danger);
                $('#success-alert').html('<p class="alert alert-danger">'+data.danger+' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>').show();
                window.location.hash = '#success-alert';
                $('#success-alert').focus();
              }
          }
        });
      });


    $(document).on('submit','#edit_employee_shift_form',function(e){
        e.preventDefault();
        $.ajax({
          type:'POST',
          datatype: 'text',
          url:'edit_employee_shift',
          data:$('#edit_employee_shift_form').serialize(),
          success:function(result){
              var data = JSON.parse(result);             
              if(data.status=='success')
              { 
                alert(data.success);
                $('#success-alert').html('<p class="alert alert-success">'+data.success+' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>').show();
                window.location.hash = '#success-alert';
                $('#shift_table').empty();
                $('#mycard').hide();
                $('#success-alert').focus();
              }
              else
              {
                alert(data.danger);
                $('#success-alert').html('<p class="alert alert-danger">'+data.danger+' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>').show();
                window.location.hash = '#success-alert';
                $('#success-alert').focus();
              }
          }
        });
      });

    $(document).on("keyup", "#myInput" , function() {
	    var value = $(this).val().toLowerCase().trim();
	    var matched = false;
        $("#employee_shift_table").find("tr.main").each(function(index) {
        		var tdval = $(this).html().toLowerCase().trim();
        		var match = tdval.match(value);
        		if(match)
        			matched = true;
        		else 
        			matched=false;
        	if(!matched)
        		$(this).hide();
        	else
        		$(this).show();
        });
  });
    
 });
 
</script >

@endsection