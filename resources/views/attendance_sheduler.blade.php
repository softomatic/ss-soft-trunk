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
                <h2>Attendance Sheduler</h2>
               
            </div>
            <!-- Example -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <form method="post" action="testupload" >
                            {{ csrf_field() }}
                                    <button class="btn btn-success" type="submit" style="font-size: 15px;"> Start Sheduler <i class="material-icons">file_upload</i></button>
                            </form>
                            
                        </div>
                        <div class="body">                          
                           <table class="table table-striped">
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
								<?php 
								$j=1;
								?>
								@foreach($jobs as $row)
                                  
								    <tr>
									 {{ csrf_field() }}
									    <th scope="row">{{ $j++ }}</th>
                                        <td>{{$row->job_type}}</td>
										<td>{{$row->file_name}}</td>
                                        <td>{{$row->status}}</td>
                                        <td><?php  
                                            
                                        $bio= explode( ',',$row->biometric_id_not );
                                        for($i=0;count( $bio)> $i;$i++ )
                                        {?>
                                            {{$bio[$i]}}
                                      <?php  }
                                        ?>
                                           
                                          
                                        </td>
                                        <td>{{$row->start_time}}</td>
                                        <td>{{$row->finish_time}}</td>
                                         <td>@if($row->status=='Pending')
                                                <button styletype="button" style="height:30px;width:30px"class=" refresh btn btn-xs bg-red btn-circle-lg waves-effect waves-circle waves-float"><i class="material-icons" style="font-size:20px !important;top: 5px !important;">refresh</i></button>
                                            @endif
                                        </td>
								    </tr>
                                 @endforeach       
                                 
                                </tbody>
                            </table>
                            
                        </div>
                      
            </div>
                
        </div>
       
    </div> 
@endsection
@section('jquery')

<script type="text/javascript">
    $(document).ready(function(){
        $(".refresh").click(function(){
            location.reload(true);
        });
    });
</script>
@endsection