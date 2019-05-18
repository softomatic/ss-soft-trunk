<form method="POST" id="other_add_ded_form" action="add_tds">
    <table class="table table-striped" id="myadd_ded_table">
        <thead>
            <tr class="bg-orange">
                <th width="5%">#</th>
                <th width="10%">ID</th>
                <th width="15%">Biometric-ID</th>
                <th width="40%">Employee Name</th>
				<th width="15%">TDS amount</th>
              </tr>
        </thead>
        <tbody>
		<?php 
		$i=1; $empid = json_encode($emps); $size = sizeof($tds); $j=0;
		?>
		@foreach($emps as $emp)	
		@csrf

    <?php 
    $tds_amt='';
    if($j<$size)
    {
      if($emp->id==$tds[$j]->emp_id)
      {
        if(!empty($tds[$j]->tds_amt))
          $tds_amt = $tds[$j]->tds_amt;

        $j++;
      }

    }
    ?>

		   <tr>
			  <th scope="row">{{ $i++ }}</th>
			  	<td><input type="text" name="id" class="form-control" value="{{$emp->id}}" disabled></td>
			  	<td><input type="text" name="biometric_id" class="form-control" value="{{$emp->biometric_id}}" disabled></td>
                <td><input type="text" name="name" class="form-control" value="{{$emp->first_name}} {{$emp->middle_name}} {{$emp->last_name}}" disabled></td>
				<td><input type="text" name="tds_amt[]" class="form-control numeric" value="{{$tds_amt}}"><br><span class="text-danger"></span></td>
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