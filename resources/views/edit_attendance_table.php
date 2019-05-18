
<?php
    $initial_in = $attendance[0]->initial_in;
    $final_out = $attendance[0]->final_out; 
    echo '<h4>'.$date.'</h4><br>'; ?>
    <table class="table table-bordered table-striped table-hover js-basic-example dataTable myemptable">
        <thead>
            <tr>
                <th>IN</th>
                <th>OUT</th>
            </tr>
        </thead>
        <tbody >

        <tr>
            <td>
                <input type="text" class="form-control" name="in" value="<?php echo $initial_in; ?>">
            </td>
            <td>
                <input type="text" class="form-control" name="out" value="<?php echo $final_out; ?>">
            </td>
        </tr>
        </tbody>
    </table> 
   
    <input type="hidden" name="emp_id" value="<?php echo $emp_id; ?>">
    <input type="hidden" name="date" value="<?php echo $date; ?>">