<form method="POST" id="release_salary_form">
    @csrf
    <div class="col-lg-3 col-lg-offset-9"><input type="text" class="form-control" id="myInput" placeholder="Search here.."></div>
    <table class="table table-striped" id="hold_salary_table">
        <thead>
            <tr class="bg-orange">
                <th width="5%">#</th>
                <th width="10%">ID</th>
                <th width="25%">Employee Name</th>
                <th width="25%">Hold Remark</th>
                <th width="30%">Release Remark</th>
                <th width="5%">Release</th>				        
              </tr>
        </thead>
        <tbody>
    		<?php 
    		$i=1; 
        ?>
        @foreach($hold_salary as $hold) 
  		   <tr class="main">
            <td scope="row">{{ $i++ }}<input type="hidden" name="id" value="{{$hold->id}}"></td>
  			  	<td><input type="text" name="emp_id[]" class="form-control" value="{{$hold->emp_id}}" disabled></td>
            <td><input type="text" name="name" class="form-control" value="{{$hold->first_name}} {{$hold->middle_name}} {{$hold->last_name}}" disabled></td>
    				<td>
              <textarea name="remark[{{$hold->id}}]" class="form-control remark" rows="2" disabled>{{$hold->hold_remark}}</textarea>
            </td>
            <td>
              <textarea name="remark[{{$hold->id}}]" class="form-control release_remark" rows="2"></textarea>
            </td>
            <td>
              <input type="checkbox" name="release[{{$hold->id}}]" id="md_checkbox_{{$i}}" class="filled-in chk-col-red release" value="release">
              <label for="md_checkbox_{{$i}}"></label>              
            </td>
  		   </tr>
         @endforeach
         @if($hold_salary=='[]')
         <tr>
           <td colspan="6"><center>No records found</center></td>
         </tr>
         @endif
        </tbody>
    </table>
    <div class="col-lg-12" <?php if($hold_salary=='[]'){ ?> style="display: none;" <?php } ?> >
        <button type="submit" class="btn btn-lg btn-success pull-right">RELEASE</button>
    </div>
</form>