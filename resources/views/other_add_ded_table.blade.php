<form method="POST" id="other_add_ded_form" action="other_add_ded">
    <table class="table table-striped" id="myadd_ded_table">
        <thead>
            <tr class="bg-orange">
                <th width="5%">#</th>
                <th width="10%">ID</th>
                <th width="15%">Biometric-ID</th>
                <th width="30%">Employee Name</th>
				<th width="10%">Other Deduction</th>
                <th width="10%">Other Addition</th>
                <th width="20%">Remarks</th>
              </tr>
        </thead>
        <tbody>
		<?php 
		$i=1; $empid = json_encode($emps); $size = sizeof($other_add_ded); $j=0;
		?>
		@foreach($emps as $emp)	
		@csrf
    <?php 
    $other_add='';
    $other_ded='';
    $remark='';
    if($j<$size)
    {
      if($emp->id==$other_add_ded[$j]->emp_id)
      {
        $remark = $other_add_ded[$j]->remark;
        
        if(!empty($other_add_ded[$j]->other_add))
          $other_add = $other_add_ded[$j]->other_add;
        if(!empty($other_add_ded[$j]->other_ded))
          $other_ded = $other_add_ded[$j]->other_ded;
        
        $j++;
      }

    }
    ?>
		   <tr>
			  <th scope="row">{{ $i++ }}</th>
			  	<td><input type="text" name="id" class="form-control" value="{{$emp->id}}" disabled></td>
			  	<td><input type="text" name="biometric_id" class="form-control" value="{{$emp->biometric_id}}" disabled></td>
                <td><input type="text" name="name" class="form-control" value="{{$emp->first_name}} {{$emp->middle_name}} {{$emp->last_name}}" disabled></td>
				<td><input type="text" name="other_ded[]" class="form-control numeric" value="{{$other_ded}}"><br><span class="text-danger"></span></td>
				<td><input type="text" name="other_add[]" class="form-control numeric" value="{{$other_add}}"><br><span class="text-danger"></span></td>
				<td><textarea type="text" name="remark[]" class="form-control">{{$remark}}</textarea><br><span class="text-danger"></span></td>
		   </tr>
         @endforeach
         <input type="hidden" name="empids" value="{{$empid}}">
         <input type="hidden" name="month" value="{{$month}}">
         <input type="hidden" name="year" value="{{$year}}">
        </tbody>
    </table>
    <div class="col-lg-12">
        <button type="submit" class="btn btn-lg btn-success pull-right">SUBMIT</button>
    </div>
</form>