@extends('layouts.form-app')

@section('content')
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>TDS</h2>
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
                               Add TDS
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
	                                 <div class="col-lg-3">
	                                    <div class="form-group">
	                                        <button type="button" name="create" id="create" class="btn btn-md bg-blue waves-effect"><i class="material-icons">create</i><span class="icon-name">Create</span></button>
	                                    </div>
	                                </div>                          

	                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	                           <div class="table-responsive" id="tds_table">
	                                
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

    // $('#create').click(function(){
    // 	if($('#branch_location_id').val()=='' || $('#month').val()=='' || $('#year').val()=='')
    // 	{
    // 		if($('#branch_location_id').val()=='')
    // 		$('#branch_location_id').closest('div').find('span.text-danger').html('Branch is required');

    // 		if($('#month').val()=='')
    // 		$('#month').closest('div').find('span.text-danger').html('Month is required');

    // 		if($('#year').val()=='')
    // 		$('#year').closest('div').find('span.text-danger').html('Year is required');
    // 	}
    // 	else
    // 	{
    // 		$('#branch_location_id').closest('div').find('span.text-danger').html('');
    // 		('#month').closest('div').find('span.text-danger').html('');
    // 		('#year').closest('div').find('span.text-danger').html('');

    // 		$.ajax({
	   //          type:'POST',
	   //          datatype: 'text',
	   //          url:'get_other_add_ded',
	   //          data:{branch:$('#branch_location_id').val(),'month':$('#month').val(),'year':$('#year').val()},
	   //          success:function(data){ 
	   //              $('#tds_table').empty().html(data);
	   //          }
    //         });
    // 	}
        
    // });

    $('#create').click(function(){
    	if($('#branch_location_id').val()!=''&& $('#month').val()!='' && $('#year').val()!='')
    	{  
    		$('#branch_location_id').closest('div').find('span.text-danger').html('');
    		$('#month').closest('div').find('span.text-danger').html('');
    		$('#year').closest('div').find('span.text-danger').html('');

    		$.ajax({
	            type:'POST',
	            datatype: 'text',
	            url:'get_tds',
	            data:{branch:$('#branch_location_id').val(),'month':$('#month').val(),'year':$('#year').val()},
	            success:function(data){ 
	                $('#tds_table').empty().html(data);
	            }
            });
    	}
    	else
    	{
    		if($('#branch_location_id').val()=='')
    		$('#branch_location_id').closest('div').find('span.text-danger').html('Branch is required');

    		if($('#month').val()=='')
    		$('#month').closest('div').find('span.text-danger').html('Month is required');

    		if($('#year').val()=='')
    		$('#year').closest('div').find('span.text-danger').html('Year is required');

    		$('#tds_table').empty().html('');
    	}
    });
    
	$(document).on('focusout','.numeric',function(){
		var number= $(this).val();
		    intRegex = /^\d*$/;
			if(!intRegex.test(number)  && $(this).val()!='')
			{
				//alert(1);
				$(this).closest('td').find('span.text-danger').html('enter digits only');
				$(this).focus();
			    //return false;
			}
			else
			{
				$(this).closest('td').find('span.text-danger').html('');
			}
	});

	// $(document).on('submit','#other_add_ded_form', function(e){
	// 	e.preventDefault();
	// 	$.ajax({
	//         type:'POST',
	//         datatype: 'text',
	//         url:'other_add_ded',
	//         data:$('#other_add_ded_form').serialize(),
	//         success:function(data){ 
	//         	var response = JSON.parse(data);

	//         	if(response.status=='success')
	//             {
 //            		$('#success-alert').html('<p class="alert alert-success">'+response.msg+' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>').show();
 //            		$('html, body').animate({
	// 			        'scrollTop' : $("#success-alert").position().top
	// 			    });
 //            		$('#tds_table').empty();
 //            		$('#month,#branch_location_id').val('');
 //            	}
 //            	else
 //            	{
 //            		$('#success-alert').html('<p class="alert alert-danger">'+response.msg+' <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>').show();
 //            		$('html, body').animate({
	// 			        'scrollTop' : $("#success-alert").position().top
	// 			    });
 //            	}
	//         }
	//     });
	// });

    $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
		$("#success-alert").slideUp(500);
	});
    
    
 });
 
</script >


@endsection