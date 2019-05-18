@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>DASHBOARD </h2>
                 <div class="flash-message" id="success-alert">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                    @endif
                    @endforeach
                </div> 
            </div>
            
            <!-- Widgets -->
            <div class="row clearfix">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-pink hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">NEW TASKS</div>
                            <div class="number count-to" data-from="0" data-to="125" data-speed="15" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-cyan hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">help</i>
                        </div>
                        <div class="content">
                            <div class="text">NEW TICKETS</div>
                            <div class="number count-to" data-from="0" data-to="257" data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-light-green hover-expand-effect">
                        <div class="icon">
                           <i class="material-icons">person_add </i>  
                        </div>
                        <div class="content">
                            <div class="text">Total Active Employee  </div>
                            <div class="number count-to" data-from="0" data-to="1225" data-speed="1000" data-fresh-interval="20">{{$emp}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-orange hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">person_add </i>   
                        </div>
                        <div class="content">
                            <div class="text">Total Inactive Employee  </div>
                            <div class="number count-to" data-from="0" data-to="1225" data-speed="1000" data-fresh-interval="20">{{$emp_inactive}}</div>
                        </div>
                    </div>
                </div>
            </div>
                
                <!-- #END# Answered Tickets -->
            </div>

            <div class="row clearfix">
                <!-- Task Info -->
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <div class="card">
                        <div class="header">
                            <h2>Noticeboard</h2>
                            
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-hover dashboard-task-infos">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Titile</th>
                                            <th>Notice</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @php 
                                            $i=1;
                                       @endphp
                                        @foreach($notice as $notices)
                                        <tr>
                                            <td>{{$i}}</td>
                                            <td>{{ $notices->title}}</td>
                                            <td>{{ $notices->notice}}</td>
                                            <td>{{ $notices->date}}</td>
                                            <td> @if($notices->date < now())
                                                <span class="label bg-green">Past Date</span>
                                               
                                                @else
                                                <span class="label bg-blue">Upcoming</span>
                                             @endif
                                            </td>
                                        </tr>
                                         @php 
                                            $i++;
                                       @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    </div>
                    @if(Session::get('role_id')==1)
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <div class="card">
                        <div class="header">
                            <h2>Generate reports</h2>
                            
                        </div>
                    <div class="body">
                        <form method="POST" action="">
                          {{ csrf_field() }}
                        <div class="row" >
                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                <a href="in_ou_report"  name="button" class="btn btn-success" >Generate report of in/out punch </a>
                            </div>
                        </div>
                        </form>
                    </div>
                    </div>
                </div>
                @endif
                <!-- #END# Task Info -->
               
            </div>
        </div>
    </section>
    @endsection
    @section('jquery')
    <script type="text/javascript">
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="mycsrf-token"]').attr('content')
                }
            });
        $(document).ready(function(){
            $("#one").click(function(){
            
              $.ajax({
                type:'GET',
                url:'update_attendance_tt',
                cache : false,
                processData: false,
                contentType: false,
                data:{},
                success:function(data){ 
                
                  },
                  error:function(er){
                     
                  }
            });         
       }); 
    });
    </script>
 @endsection