<form method="POST" id="edit_employee_shift_form">
  @csrf
  <h4><center>Edit Employee Shift</center></h4><br>
  <div class="col-lg-3 col-lg-offset-9"><input type="text" class="form-control" id="myInput" placeholder="Search here.."></div>
    <table class="table table-striped table-bordered" id="employee_shift_table">
        <thead>
            <tr class="bg-orange">
                <!-- <th width="5%">#</th> -->
                <th width="10%">EMP ID</th>
                <!-- <th width="15%">Biometric-ID</th> -->
                <th width="25%">Employee Name</th>
                <th width="10%">Designation</th>
				        <th width="10%"> Shift  </th>
                <th width="15%">Start Date </th>
                <th width="15%">End Date </th>
              </tr>
        </thead>
        <tbody>

    		<?php 
    		$i=1; $j=0; $employee_shift = json_decode($employee_shift,true);
        if(sizeof($employee_shift)==0)
        { ?>
          <tr>
            <td colspan="5"><center>No records found</center></td>
          </tr>
  <?php }
        for($m=0;$m<sizeof($employee_shift);$m++)
        { $emp_shift = json_decode($employee_shift[$m]);
         
        ?>
  		   <tr id="{{$i++}}" class="main">
  			  	<td><input type="text" name="emp_id[]" class="form-control" value="{{$emp_shift->emp_id}}" readonly></td>
            <td>{{$emp_shift->first_name}} {{$emp_shift->middle_name}} {{$emp_shift->last_name}}</td>
            <td>{{$emp_shift->designation}}</td>
          <td colspan="3"  style="padding: 0!important; margin: 0!important" class="flexible_td">
            <table style="padding: 0!important; margin: 0!important">
              <?php for($j=0; $j < sizeof($emp_shift->shift); $j++)
              { ?>
              <tr style="padding: 0!important; margin: 0!important" class="first_flexible_tr">
                <td class="shift_td" width="15%">

                  <input type="hidden" name="id[]" class="form-control" value="{{$emp_shift->id[$j]}}">

                  <select class="form-control show-tick select2 shift" placeholder="Shreeshivam shift" name="shift_id[{{$emp_shift->id[$j]}}]" id="shift[]"> 
                    <option value="">Select</option>
                    @foreach($shifts as $shift)                            
                    <option value="{{$shift->id}}" <?php if($shift->id==$emp_shift->shift[$j]) echo "selected"; ?> > {{$shift->shift_name}}</option>
                    @endforeach
                  </select>
                  <br><span class="text-danger"></span>
                </td>
                <td class="start_td" width="15%"><input type="text" name="start_date[{{$emp_shift->id[$j]}}]" id="start_date[{{$emp_shift->id[$j]}}]" class="form-control datepicker start_date" value="{{$emp_shift->start_date[$j]}}" placeholder="Start Date">
                <br><span class="text-danger"></span></td>
                <td class="end_td" width="15%"><input type="text" name="end_date[{{$emp_shift->id[$j]}}]" id="end_date[{{$emp_shift->id[$j]}}]" class="form-control datepicker end_date" value="{{$emp_shift->end_date[$j]}}" placeholder="Start Date">
                <br><span class="text-danger"></span></td>
                <td width="5%"></td>
              </tr>
             <?php } ?>
             
          </table>
        </td>
            <!-- <td><button type="button" name="delete_row" id="delete_row[]" class="btn btn-md bg-red waves-effect delete_row" data-id="{{json_encode($emp_shift->id)}}" data-emp_id="{{$emp_shift->emp_id}}" data-row_id="{{$i-1}}"><i class="material-icons">delete</i></button></td> -->
  		   </tr>
         <?php } ?>
        </tbody>
    </table>
    <div class="col-lg-12" <?php if(sizeof($employee_shift)==0) echo "style='display:none;'"?> >
      <input type="hidden" id="max_i" value="{{$i}}">
        <button type="submit" class="btn btn-lg btn-success pull-right">UPDATE</button>
    </div>
</form>

<!-- <div class="modal fade" id="addshift" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-pink">
                <h4 class="modal-title" id="uModalLabel">ADD SHIFT</h4>
            </div>

            <div class="modal-body">
              <div class="table-responsive" id="shift_modal_table">
                  <table class="table table-striped" id="my_modal_table">  
                    <thead>
                      <tr>
                        <th> Shift </th>
                        <th>Start Date</th>
                        <th>End Date</th>
                      </tr>
                    </thead>
                    <tbody id="modal_body">
                      <tr id="modal_first_tr">
                        <td class="shift_td">
                            <select class="form-control show-tick select2 modal_shift" placeholder="shift" name="modal_shift_id" id="modal_shift" required> 
                              <option value="">Select</option>
                              @foreach($shifts as $shift)                            
                              <option value="{{$shift->id}}" > {{$shift->shift_name}}</option>
                              @endforeach
                            </select>
                            <br><span class="text-danger"></span>
                          </td>
                          <td class="start_td"><input type="text" name="modal_start_date" id="modal_start_date" class="form-control datepicker modal_start_date" placeholder="Start Date">
                          <br><span class="text-danger"></span></td>
                          <td class="end_td"><input type="text" name="modal_end_date" id="modal_end_date" class="form-control datepicker modal_end_date" placeholder="Start Date">
                          <br><span class="text-danger"></span></td>
                      </tr>
                    </tbody>
                  </table>               
              </div>
              <div class="modal-footer">

                  <input type="hidden" id="data_row_id">
                  <input type="hidden" id="data_emp_id">
                  <input type="hidden" id="data_first_name">
                  <input type="hidden" id="data_last_name">
                  <input type="hidden" id="data_middle_name">

                  <button type="button" class="btn  bg-deep-orange add_row waves-effect">Add</button>
                  <button type="button" class="btn bg-blue-grey dismiss_modal waves-effect" data-dismiss="modal">Close</button>
              </div>
        </div>
    </div>
  </div>
</div> -->
<style type="text/css">
  .dropdown-menu
  {
    z-index: 999999;
  }
  td>table>tr
  {
    padding: 0px!important;
  }
  th>table>tr
  {
    padding: 0px!important;
    margin: 0px!important;

  }
</style>

<script type="text/javascript">

  $(document).ready(function() {

        $('#employee_shift_form.datepicker').datepicker().on('changeDate', function (ev) {
            $('.dropdown-menu').hide();
        });

      $('#employee_shift_form #all_start_date').datepicker().on('changeDate', function (ev) {
        $('.dropdown-menu').hide();
        $(this).change();
      });

      $('#employee_shift_form #all_end_date').datepicker().on('changeDate', function (ev) {
        $('.dropdown-menu').hide();
        $(this).change();
      });

      $('#employee_shift_form #all_start_date').change(function(){
        
        if($(this).val()!='')
        {
          $('#employee_shift_form .start_date').each(function(){
            $(this).val($('#all_start_date').val());
          });
        }
        else
        {
          $('#employee_shift_form .start_date').each(function(){
            $(this).val('');
          });
        }
        
      });

      $('#employee_shift_form #all_end_date').change(function(){
        
        if($(this).val()!='')
        {
          $('#employee_shift_form .end_date').each(function(){
            $(this).val($('#all_end_date').val());
          });
        }
        else
        {
          $('#employee_shift_form .end_date').each(function(){
            $(this).val('');
          });
        }
        
      });

      $('.add_more').click(function(){
        var emp_id = $(this).data('emp_id');
        var flexible_td = '<table style="padding: 0!important; margin: 0!important"> <tr style="padding: 0!important; margin: 0!important"> <td class="shift_td" width="15%"><select class="form-control show-tick select2 shift" placeholder="Shreeshivam shift" name="shift_id['+emp_id+'][]" id="shift[]" required> <option value="">Select</option>@foreach($shifts as $shift)<option value="{{$shift->id}}" > {{$shift->shift_name}}</option>@endforeach</select><br><span class="text-danger"></span></td><td class="start_td" width="15%"><input type="text" name="start_date['+emp_id+'][]" id="start_date['+emp_id+'][]" class="form-control datepicker start_date" placeholder="Start Date"><br><span class="text-danger"></span></td><td class="end_td" width="15%"><input type="text" name="end_date['+emp_id+'][]" id="end_date['+emp_id+'][]" class="form-control datepicker end_date" placeholder="Start Date"><br><span class="text-danger"></span></td><td width="5%"><button type="button" name="delete" id="delete[]" class="btn btn-md bg-red waves-effect delete"><i class="material-icons">delete</i></button></td> </tr></table>';

        $(this).closest('tr').find('.flexible_td').append(flexible_td); 

        $(this).closest('tr').find('.datepicker').datepicker().on('changeDate', function (ev) {
            $('.dropdown-menu').hide();
        });

      });

      $(document).on('click','.delete',function(){
        $(this).closest('table').remove();
      });


      $(document).on('click','.add_row',function(){
        var row_id = $('#data_row_id').val();
        var first_name = $('#data_first_name').val();
        var middle_name = $('#data_middle_name').val();
        var last_name = $('#data_last_name').val();
        var emp_id = $('#data_emp_id').val();
        var new_id = parseInt($('#max_i').val());
        var shift = $('#modal_shift').val();
        var start_date = $('#modal_start_date').val();
        var end_date = $('#modal_end_date').val();
        $('#max_i').val(new_id+1);

        
          $('tr#'+row_id).append('</tr><tr id="'+new_id+'"> <td><input type="text" name="emp_id[]" class="form-control" value="'+emp_id+'" readonly></td> <td><input type="text" name="name" class="form-control" value="'+first_name+' '+middle_name+' '+last_name+'" disabled></td> <td class="shift_td"> <select class="form-control show-tick select2 shift" placeholder="Shreeshivam shift" name="shift_id[][]" id="shift[]" required> <option value="">Select</option>   @foreach($shifts as $shift) <option value="{{$shift->id}}" <?php if('+shift+'==$shift->id) echo "selected"; ?> > {{$shift->shift_name}}</option>   @endforeach </select> <br><span class="text-danger"></span> </td> <td class="start_td"><input type="text" name="start_date[][]" id="start_date[][]" class="form-control datepicker start_date" value="'+start_date+'" placeholder="Start Date"> <br><span class="text-danger"></span></td> <td class="end_td"><input type="text" name="end_date[][]" id="end_date[][]" class="form-control datepicker end_date" value="'+end_date+'" placeholder="Start Date"> <br><span class="text-danger"></span></td> <td><button type="button" name="add_more" id="add_more[]" class="btn btn-md bg-blue waves-effect add_more" data-emp_id="'+emp_id+'" data-first_name="'+first_name+'" data-row_id="'+new_id+'" data-middle_name="'+middle_name+'" data-last_name="'+last_name+'"><i class="material-icons">add</i></button></td>');
        
      });
      
  });
</script>
