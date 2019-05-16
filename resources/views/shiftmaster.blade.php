@extends('layouts.form-app')
@section('content')

 <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                 <div class="flash-message" id="success-alert">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                    @endif
                    @endforeach
                </div> 
                <h2>Shift Master</h2>
               
            </div>
            <!-- Example -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">                   
                         
                        </div>
                        <div class="body">  
                            <div class="row">
                                
                                <div class="col-sm-1 col-md- col-lg-1">
                                    <label for="group_id">Shift group id </label>
                                </div>
                                 <div class="col-sm-3 col-md- col-lg-3">
                                    <input type="text" class="form-control" placeholder="Shift group name">
                                </div>
                               <div class="col-sm-1 col-md-1 col-lg-1">
                                    <label for="group_id">Time in </label>
                                </div>
                                 <div class="col-sm-3 col-md- col-lg-3">
                                   <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="timepicker form-control" placeholder="Please choose a time...">
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-sm-1 col-md-1 col-lg-1">
                                    <label for="group_id">Time out </label>
                                </div>
                                <div class="col-sm-3 col-md- col-lg-3">
                                    <input autocomplete="none" type="text" name="time_out"  class="form-control datepicker" placeholder="Time Out"  >
                                </div>

                            </div>                          
                           {{-- <table class="table table-striped">
                                <thead>
                                    <tr class="bg-orange">
                                        <th>#</th>
                                        <th>Sheduler Type</th>
										<th>File Name</th>
                                        <th>Status</th>
                                        <th>Biometric id not present</th>
                                        <th>Start Time</th>
                                        <th>Finish Time</th>
                                        <th></th>
                                      </tr>
                                </thead>
                                <tbody>
								
							     
                                 
                                </tbody>
                            </table> --}}
                            
                        </div>
                      
            </div>
                
        </div>
       
    </div> 
@endsection
@section('jquery')
<script src="plugins/bootstrap-material-datetimepicker"></script>

<script type="text/javascript">
   $(document).ready(function(){
       $('.datepicker').datepicker().on('changeDate', function (ev) {
		    $('.dropdown-menu').hide();
		});
   });
</script>
@endsection