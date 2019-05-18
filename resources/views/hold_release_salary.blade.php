@extends('layouts.form-app')

@section('content')
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Hold/Release Salary</h2>
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
                               Hold Salary
                             </h2>                          
                        </div>
                        <div class="body">
                            <div class="row clearfix">
 
	                             <div class="row">                      
	                                <div class="col-lg-1 col-md-6 col-sm-6">
	                                 <label>Branch</label>
	                                </div>
	                                <div class="col-lg-2 col-md-6 col-sm-6">
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

	                                <div class="col-lg-1 col-md-6 col-sm-6">
	                                 <label>Month</label>
	                                </div>
	                                <div class="col-lg-2 col-md-6 col-sm-6">
	                                   <div class="form-line">
	                                        <select name="month" id="month" class="form-control select2" placeholder="Month" required>
												<option value="" >Select Month</option>
												<option value="{{date('m',strtotime(date('Y-m')." -1 month"))}}">{{date('M',strtotime(date('Y-m')." -1 month"))}}</option>
												<option value="{{date('m')}}">{{date('M')}}</option>
	                                 	 	</select>
	                                 	 	<span class="text-danger"></span>
	                                	</div>
	                                </div>

	                                <div class="col-lg-1 col-md-6 col-sm-6">
	                                 <label>Year</label>
	                                </div>
	                                <div class="col-lg-2 col-md-6 col-sm-6">
	                                   <div class="form-line">
	                                        <select name="year" id="year" class="form-control select2" placeholder="Year" required>
												<option value="">Select Year</option>
												@for($i=date('Y',strtotime("-1 year")); $i < date('Y',strtotime("+2 year")); $i++)
												<option value="{{$i}}" <?php if(date('Y')==$i) echo 'selected'; ?> >{{$i}}</option>
												@endfor
	                                 	 	</select>
	                                 	 	<span class="text-danger"></span>
	                                	</div>
	                                </div>

	                                <div class="col-lg-1 col-md-3 col-sm-3">
			                            <div class="form-group">
			                                <button type="button" name="hold_button" id="hold_button" class="btn btn-md bg-blue waves-effect"><i class="material-icons">not_interested</i><span class="icon-name">Hold</span></button>
			                            </div>
			                        </div> 

	                                <div class="col-lg-1 col-md-3 col-sm-3">
			                            <div class="form-group">
			                                <button type="button" name="release_button" id="release_button" class="btn btn-md bg-orange waves-effect"><i class="material-icons">new_releases</i><span class="icon-name">Release</span></button>
			                            </div>
			                        </div>                                        
			                                                             
			                    </div>
			                </div>
				            <div class="row clearfix">
				               	<div class="row">
			                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			                           <div class="table-responsive" id="hold_release_table">
			                                
			                            </div>
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

	$('.datepicker').datepicker().on('changeDate', function (ev) {
		    $('.dropdown-menu').hide();
		});

    $('#hold_button').click(function(){

    	if($('#branch_location_id').val()!='' && $('#month').val()!='' && $('#year').val()!='')
    	{    		
    		$('#branch_location_id').closest('div').find('span.text-danger').html('');
    		$('#month').closest('div').find('span.text-danger').html('');
    		$('#year').closest('div').find('span.text-danger').html('');
    		$.ajax({
	            type:'POST',
	            datatype: 'text',
	            url:'hold_salary_table',
	            data:{branch:$('#branch_location_id').val(),month:$('#month').val(),year:$('#year').val()},
	            success:function(data){ 
	                $('#hold_release_table').empty().html(data);
	            }
            });
    	}
    	else
    	{
    		$('#hold_release_table').empty().html('');
    		if($('#branch_location_id').val()=='')    		
    			$('#branch_location_id').closest('div').find('span.text-danger').html('Branch is required');
    		else
    			$('#branch_location_id').closest('div').find('span.text-danger').html('');

    		if($('#month').val()=='')
    			$('#month').closest('div').find('span.text-danger').html('Month is required');
    		else
    			$('#month').closest('div').find('span.text-danger').html('');

    		if($('#year').val()=='')
    			$('#year').closest('div').find('span.text-danger').html('Year is required');
    		else
    			$('#year').closest('div').find('span.text-danger').html('');
    	}
    });


    $('#release_button').click(function(){

    	if($('#branch_location_id').val()!='' && $('#month').val()!='' && $('#year').val()!='')
    	{    		
    		$('#branch_location_id').closest('div').find('span.text-danger').html('');
    		$('#month').closest('div').find('span.text-danger').html('');
    		$('#year').closest('div').find('span.text-danger').html('');
    		$.ajax({
	            type:'POST',
	            datatype: 'text',
	            url:'release_salary_table',
	            data:{branch:$('#branch_location_id').val(),month:$('#month').val(),year:$('#year').val()},
	            success:function(data){ 
	                $('#hold_release_table').empty().html(data);
	            }
            });
    	}
    	else
    	{
    		$('#hold_release_table').empty().html('');
    		if($('#branch_location_id').val()=='')    		
    			$('#branch_location_id').closest('div').find('span.text-danger').html('Branch is required');
    		else
    			$('#branch_location_id').closest('div').find('span.text-danger').html('');

    		if($('#month').val()=='')
    			$('#month').closest('div').find('span.text-danger').html('Month is required');
    		else
    			$('#month').closest('div').find('span.text-danger').html('');

    		if($('#year').val()=='')
    			$('#year').closest('div').find('span.text-danger').html('Year is required');
    		else
    			$('#year').closest('div').find('span.text-danger').html('');
    	}
    });

	$(document).on('click','.hold', function(){
		if($(this).prop('checked')==true)
		{
			$(this).closest('tr').find('.remark').prop('required',true);
			$(this).closest('tr').find('.remark').prop('placeholder','remark is required');
			if($(this).closest('tr').find('.remark').val()=='')
				$(this).closest('tr').find('.remark').focus();
		}
		else
		{
			$(this).closest('tr').find('.remark').prop('required',false);
			$(this).closest('tr').find('.remark').prop('placeholder','');
			$(this).closest('tr').find('.remark').val('');
		}
	});

	$(document).on('click','.release', function(){
		if($(this).prop('checked')==true)
		{
			$(this).closest('tr').find('.release_remark').prop('required',true);
			$(this).closest('tr').find('.release_remark').prop('placeholder','remark is required');
			if($(this).closest('tr').find('.release_remark').val()=='')
				$(this).closest('tr').find('.release_remark').focus();
		}
		else
		{
			$(this).closest('tr').find('.release_remark').prop('required',false);
			$(this).closest('tr').find('.release_remark').prop('placeholder','');
			$(this).closest('tr').find('.release_remark').val('');
			
		}
	});

    $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
		$("#success-alert").slideUp(500);
	});
    

    $(document).on('submit','#hold_salary_form',function(e){
    	e.preventDefault();
		$.ajax({
            type:'POST',
            datatype: 'text',
            url:'hold_salary',
            data:$('#hold_salary_form').serialize(),
            success:function(result){

                var data = JSON.parse(result);             
				if(data.status=='success')
				{ 
					alert(data.success);
					$('#success-alert').html('<p class="alert alert-success">'+data.success+' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>').show();
					window.location.hash = '#success-alert';
					$('#hold_release_table').empty();
				}
				else
				{
					alert(data.danger);
					$('#success-alert').html('<p class="alert alert-danger">'+data.danger+' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>').show();
					window.location.hash = '#success-alert';
				}
            }
        });
    });

    $(document).on('submit','#release_salary_form',function(e){
    	e.preventDefault();
		$.ajax({
            type:'POST',
            datatype: 'text',
            url:'release_salary',
            data:$('#release_salary_form').serialize(),
            success:function(result){

                var data = JSON.parse(result);             
				if(data.status=='success')
				{ 
					alert(data.success);
					$('#success-alert').html('<p class="alert alert-success">'+data.success+' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>').show();
					window.location.hash = '#success-alert';
					$('#hold_release_table').empty();
				}
				else
				{
					alert(data.danger);
					$('#success-alert').html('<p class="alert alert-danger">'+data.danger+' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>').show();
					window.location.hash = '#success-alert';
				}
            }
        });
    });

    $(document).on("keyup", "#myInput" , function() {
	    var value = $(this).val().toLowerCase().trim();
	    var matched = false;
        $("#hold_salary_table").find("tr.main").each(function(index) {
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