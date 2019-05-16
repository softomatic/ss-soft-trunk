@extends('layouts.form-app')
 
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Salary</h2>
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
					Salary
        </h2>
			</div>
         <form action=""  method="POST">
					{{ csrf_field() }}
            <div class="body">
            <div class="row clearfix">  
                  <div class="col-lg-12">                  
                      <div class="col-sm-4">
                        <div class="form-line">        
                          <select class="form-control show-tick" name="month" id="month" placeholder="Month" required >
                            <option  value="">---Select Month---</option>
                            <option value="01">Jan</option>
                            <option value="02">Feb</option>
                            <option value="03">Mar</option>
                            <option value="04">Apr</option>
                            <option value="05">May</option>
                            <option value="06">Jun</option>
                            <option value="07">Jul</option>
                            <option value="08">Aug</option>
                            <option value="09">Sep</option>
                            <option value="10">Oct</option>
                            <option value="11">Nov</option>
                            <option value="12">Dec</option>
                          </select>
                          <span class="text-danger"></span>
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="form-line"> 
                          <select class="form-control show-tick select2" id="year" name="year">
                            <option value="">--Select Year--</option>
                            @for($i=2010;$i<=date("Y");$i++)
                                  <option value="{{$i}}" selected="{{date('Y')}}">{{$i}}</option>
                            @endfor     
                          </select>
                          <span class="text-danger"></span> 
                        </div>
                      </div>
                  </div>
              </div>
              <div class="row clearfix parent_branch_group">    
                    <div class="col-lg-12 branch_group">    
                      <div class="col-lg-4" >
                        <div class="form-line">                        
                          <select class="form-control show-tick select2 branch" placeholder="Shreeshivam Branch" id="branch_location_id" name="branch">
                          <option value="">---Select Branch---</option>
                          @foreach($branch as $branches) 				           	
                            <option value="{{$branches->id}}"  > {{$branches->branch}}</option>
                          @endforeach
                          </select>
                          <span class="text-danger"></span>
                        </div>
                      </div>

                      <div class="col-lg-2" >
                        <div class="form-line">                        
                          <input type="radio" value="all" id="all" class="radio-col-green" name="type" checked>
                          <label for="all">For All</label>
                          <span class="text-danger"></span>
                        </div>
                      </div>

                      <div class="col-lg-2" >
                        <div class="form-line">                        
                          <input type="radio" value="individual" id="individual" class="radio-col-green" name="type">
                          <label for="individual">For Individual</label>
                          <span class="text-danger"></span>
                        </div>
                      </div>

                      <div class="col-lg-4 employee" >
                        <div class="form-line">                        
                          <select class="form-control emp_id" placeholder="Employee" id="emp_id[]" name="emp_id[]" multiple="multiple" style="width: 100%;">
                          <option value="">---Select Employee---</option>

                          </select>
                          <span class="text-danger"></span>
                        </div>
                      </div>


                    </div>
                    
              </div>
              <div class="row clearfix">
                <!--< div class="col-lg-12">   
                  <div class="col-sm-2" >
                    <div class="form-line">
                      <button type="button" id="add_more" name="add_more" class="btn bg-blue waves-effect"><i class="material-icons">add</i><span class="icon-name">ADD MORE</span></button> 
                    </div>
                  </div>    -->

                  <div class="col-sm-2" >
                    <div class="form-line">
                      <button type="button" id="salary_find" name="submit" class="btn bg-green waves-effect"><i class="material-icons">save</i><span class="icon-name">GENERATE</span></button> 
                    </div>
                  </div>   

                <!-- </div> -->
              </div>
                </div> 
          </form>
			</div>
    
		</div>
	</div>
<div id="final_salary">

</div>
<center>
<div class="preloader pl-size-lg" style='display:none; margin-top: 50px;' id="loadingmessage">
    <div class="spinner-layer pl-red-grey">
        <div class="circle-clipper left">
            <div class="circle"></div>
        </div>
        <div class="circle-clipper right">
            <div class="circle"></div>
        </div>
    </div>
</div> 
</center>
</section>

@endsection

@section('jquery')

<script type="text/javascript">
  $(document).ready(function(){

    $('.employee').hide();

        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
       });

      $(".emp_id").select2({
        placeholder: "Select Employee",
        allowClear: true,
        multiple:true
      });

      $('#all').click(function(){
        if($(this).prop('checked')==true)
        {
          $('.employee').hide();
        }
      });

      $('#individual').click(function(){
        if($(this).prop('checked')==true)
        {
          $('.employee').show();
        }
      });

     $(document).on('change','.branch',function(){
        if($(this).val()!='')
        {
          var branch = $(this);
           $.ajax({
                type:'POST',
                url:'get_emp',
                data:{branch:$(this).val()},
                success:function(data){
                   branch.closest('.branch_group').find('.emp_id').empty().html(data);
                }
            });
        }
      });

      $(document).on("keyup", ".other_ded", function(){
          var add = $(this).closest('tr').find('.other_add').val();
          var sub = $(this).val();

          if(add=='')
            add=0;
          if(sub=='')
            sub=0;

          var total=parseFloat($(this).closest('tr').find('.net_payable_hide').val())+parseFloat(add)-parseFloat(sub);
          total=parseFloat(total);
         
          $(this).closest('tr').find('.net_payable').val(total);
       });


      $(document).on("keyup", ".other_add", function(){
          var add = $(this).val();
            var sub = $(this).closest('tr').find('.other_ded').val();

            if(add=='')
              add=0;
            if(sub=='')
              sub=0;

            var total=parseFloat($(this).closest('tr').find('.net_payable_hide').val())-parseFloat(sub)+parseFloat(add);
            total=parseFloat(total);
         
            $(this).closest('tr').find('.net_payable').val(total);
      });


      $('#salary_find').click(function(){
        $('.msg').hide();
        if($('#month').val()=='' || $('#year').val()=='' || $('#branch_location_id').val()=='' || ($('.emp_id').val()=='' && $('#individual').prop('checked')==true))
        {
          if($('#month').val()=='')
          {
            $('#month').closest('div').find('.text-danger').html('month required');
            $('#month').focus();
          }
          else
          {
            $('#month').closest('div').find('.text-danger').html('');
          }

          if($('#branch_location_id').val()=='')
          {
            $('#branch_location_id').closest('div').find('.text-danger').html('branch required');
            $('#branch_location_id').focus();
          }
          else
          {
            $('#branch_location_id').closest('div').find('.text-danger').html('');
          }

          if($('#year').val()=='')
          {
            $('#year').closest('div').find('.text-danger').html('year required');
            $('#year').focus();
          } 
          else
          {
            $('#year').closest('div').find('.text-danger').html('');
          }

          if($('.emp_id').val()=='' && $('#individual').prop('checked')==true)
          {
            $('.emp_id').closest('div').find('.text-danger').html('Employee required');
            $('.emp_id').focus();
          }
          else
          {
            $('.emp_id').closest('div').find('.text-danger').html('');
          }

        }
        else
        {
          var type = '';
          if($('#individual').prop('checked')==true)
            type='individual';
          else
            type='all';

          //$('#loadingmessage').show(); 
          $.ajax({
              type:'POST',
              url:'payslip-withdata',
              data:{id:$('#branch_location_id').val(),month:$('#month').val(),year:$('#year').val(),emp_id:$('.emp_id').val(),type:type},
              beforeSend: function() {
                  $('#salary_find').prop('disabled',true);
                   $('#final_salary').empty();
                   $('#loadingmessage').show();
                },
              success:function(data){
                 $('#final_salary').empty().html(data);
                 $('#loadingmessage').hide();
                 $('#salary_find').prop('disabled',false);
              },
              error: function (err) {
                  $('#salary_find').prop('disabled',false);
                  $('#loadingmessage').hide();
                  $('#final_salary').empty().html("<span style='font-size:18px; color:red;'><center>Something went wrong. Try again later</center></span>");
                 }
          });
        }
        
      });


  });
</script> 


@endsection