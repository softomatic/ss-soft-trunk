<form method="POST" id="hold_salary_form">
    @csrf
    <div class="col-lg-3 col-lg-offset-9"><input type="text" class="form-control" id="myInput" placeholder="Search here.."></div>
    <input type="hidden" name="month" value="{{$month}}">
    <input type="hidden" name="year" value="{{$year}}">

    <table class="table table-striped" id="hold_salary_table">
        <thead>
            <tr class="bg-orange">
                <th width="5%">#</th>
                <th width="10%">ID</th>
                <th width="30%">Employee Name</th>
                <th width="30%">Remark</th>
                <th width="10%">Hold</th>				        
              </tr>
        </thead>
        <tbody>
    		<?php 
    		$i=1;
        ?>
        @foreach($emps as $emp)
  		   <tr class="main">
            <td scope="row">{{ $i++ }}</td>
  			  	<td><input type="text" name="emp_id[]" class="form-control" value="{{$emp->id}}" readonly></td>
            <td><input type="text" name="name" class="form-control" value="{{$emp->first_name}} {{$emp->middle_name}} {{$emp->last_name}}" disabled></td>
    				<td>
              <textarea name="remark[{{$emp->id}}]" class="form-control remark" rows="1"></textarea>
            </td>
            <td>
              <input type="checkbox" name="hold[{{$emp->id}}]" id="md_checkbox_{{$i}}" class="filled-in chk-col-red hold" value="hold">
              <label for="md_checkbox_{{$i}}"></label>              
            </td>
  		   </tr>
         @endforeach
          @if($emps=='[]')
         <tr>
           <td colspan="6"><center>No records found</center></td>
         </tr>
         @endif
        </tbody>
    </table>
    <div class="col-lg-12" <?php if($emps=='[]'){ ?> style="display: none;" <?php } ?> >
        <button type="submit" class="btn btn-lg btn-success pull-right">SUBMIT</button>
    </div>
</form>