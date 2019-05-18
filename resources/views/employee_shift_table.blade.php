<form method="POST" id="employee_shift_form">
  <h4><center>Add Employee Shift</center></h4><br>
    <div class="col-lg-3 col-lg-offset-9"><input type="text" class="form-control" id="myInput" placeholder="Search here.."></div>
    <table class="table table-striped" id="employee_shift_table">
        <thead>
            <tr class="bg-orange">
                <!-- <th width="5%">#</th> -->
                <th width="10%">ID</th>
                <!-- <th width="15%">Biometric-ID</th> -->
                <th width="25%">Employee Name</th>
                <th width="10%">Designation</th>
                <th width="10%">
                  <select class="form-control show-tick select2" placeholder="Shreeshivam shift" name="shift_for_all" id="emp_shift">
                      <option>Select Shift</option>
                      @foreach($shifts as $shift)                            
                      <option value="{{$shift->id}}" > {{$shift->shift_name}}</option>
                      @endforeach
                  </select>
                </th>
                <th width="15%"><input type="text" name="all_start_date" id="all_start_date" class="form-control datepicker" placeholder="Start Date"></th>
                <th width="15%"><input type="text" name="all_end_date" id="all_end_date" class="form-control datepicker" placeholder="Start Date"></th>
                <th width="5%">Add</th>
              </tr>
        </thead>
        <tbody>
        <?php 
        $i=1; $empid = json_encode($emps);
        ?>
        @foreach($emps as $emp) 
        @csrf


         <tr id="{{$i++}}" class="main">
            <td><input type="text" name="emp_id[]" class="form-control" value="{{$emp->id}}" readonly></td>
            <td>{{$emp->first_name}} {{$emp->middle_name}} {{$emp->last_name}}</td>
            <td>{{$emp->designation}}</td>
          <td colspan="3"  style="padding: 0!important; margin: 0!important" class="flexible_td">
            <table style="padding: 0!important; margin: 0!important">
              <tr style="padding: 0!important; margin: 0!important" class="first_flexible_tr">
                <td class="shift_td" width="15%">
                  <select class="form-control show-tick select2 shift" placeholder="Shreeshivam shift" name="shift_id[{{$emp->id}}][]" id="shift[]"> 
                    <option value="">Select</option>
                    @foreach($shifts as $shift)                            
                    <option value="{{$shift->id}}" > {{$shift->shift_name}}</option>
                    @endforeach
                  </select>
                  <br><span class="text-danger"></span>
                </td>
                <td class="start_td" width="15%"><input type="text" name="start_date[{{$emp->id}}][]" id="start_date[{{$emp->id}}][]" class="form-control datepicker start_date" placeholder="Start Date">
                <br><span class="text-danger"></span></td>
                <td class="end_td" width="15%"><input type="text" name="end_date[{{$emp->id}}][]" id="end_date[{{$emp->id}}][]" class="form-control datepicker end_date" placeholder="Start Date">
                <br><span class="text-danger"></span></td>
                <td width="5%"></td>
              </tr>
          </table>
        </td>
            <td><button type="button" name="add_more" id="add_more[]" class="btn btn-md bg-blue waves-effect add_more" data-emp_id="{{$emp->id}}" data-first_name="{{$emp->first_name}}" data-row_id="{{$i-1}}" data-middle_name="{{$emp->middle_name}}" data-last_name="{{$emp->last_name}}"><i class="material-icons">add</i></button></td>
         </tr>
         @endforeach
        </tbody>
    </table>
    <div class="col-lg-12">
      <input type="hidden" id="max_i" value="{{$i}}">
        <button type="submit" class="btn btn-lg btn-success pull-right">SUBMIT</button>
    </div>
</form>

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

      $('.shift').change(function(){
        if($(this).val()!='')
        { 
          $(this).closest('tr').find('.start_date').prop('required',true);
          $(this).closest('tr').find('.end_date').prop('required',true);
        }
        else
        {
          $(this).closest('tr').find('.start_date').prop('required',false);
          $(this).closest('tr').find('.end_date').prop('required',false)
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