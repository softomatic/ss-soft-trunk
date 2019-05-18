
<nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand" href="dashboard"> <img src="images/shri_shivam_logo.jpg" height="50px"> </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <!-- Call Search -->
                    <li><a href="javascript:void(0);" class="js-search" data-close="true"><i class="material-icons">search</i></a></li>
                    <!-- #END# Call Search -->
                    <!-- Notifications -->
                    <li class="dropdown">
                        <!-- <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                                                        <i class="material-icons">notifications</i>
                            <span class="label-count">{{Session::get('pending_leaves')}}</span>
                        </a> -->
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                                                        <i class="material-icons">notifications</i>
                            <span class="label-count" id="notification">{{Session::get('notification')}}</span>
                        </a>
                        <ul class="dropdown-menu" >
                            <li class="header">NOTIFICATIONS</li>
                            <li class="body">
                                <ul class="menu" id="my_notification_list">
                                    <?php $i=0; ?>
                                    @foreach(Session::get('notification_list') as $notify)
                                    <?php $i++;?>
                                    <li>
                                        <a href="{{$notify->link}}">
                                            <div class="menu-info">
                                                <h4>{{$notify->notification}} :{{$notify->notification_status}}</h4>
                                                <p>
                                                    <i class="material-icons">access_time</i> {{$notify->created_at}}
                                                </p>
                                            </div>
                                        </a>
                                    </li>
                                    <?php if($i==5) break; ?>
                                    @endforeach
                                   
                                </ul>
                            </li>
                            <li class="footer">
                                <a href="notification">View All Notifications</a>
                                <!-- <a href="javascript:void(0);">View All Notifications</a> -->
                            </li>
                        </ul>
                    </li>
                    <!-- #END# Notifications -->
                    <!-- Tasks -->
                     <!-- Tasks -->
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                          <?php   $count= Session::get('task_notification');
                        //   echo var_dump($count);
                                                                                                                 
                          ?>
                            <!--@if(!empty($count))-->

                            <!-- <?php // $count= json_decode($count); ?>-->

                            <!--@endif-->

                            <i class="material-icons">flag</i>
                            @if(!empty($count))
                            <span class="label-count">{{count($count)}}</span>                          
                            @else
                            <span class="label-count">0</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu" style=" width: 400px; height: 500px; padding:unset;">
                            <li class="header" style="     font-weight: 200;font-size: 18px; padding-top: 15px; background-color: #673AB7;; border-bottom: #673AB7; border-bottom-style: solid; color: white; padding-bottom: 10px;">TASKS</li>
                            <li class="body" style="    height: 430px;">
                              
                                <ul id="menu4" class="menu tasks" >
                                  
                                @if(!empty($count))
                                    @foreach ($count as $row )
                                      <?php  
                                        $date=date('d-m-Y',strtotime($row->created_at));
                                        $time= date('H:i:s',strtotime($row->created_at));
                                        $days =$row->eta_days;
                                        $date_j= date('Y-m-d', strtotime($date. ' + '.$days.'days'));
                                        $current_date= date('d-m-Y H:i:s');
                                       $hourdiff = round((strtotime($current_date) - strtotime($row->created_at))/3600, 1);
                                       
                                       
                                        // $difference=(strtotime($current_date) - strtotime($date));
                                        // echo round($difference / (60 * 60 * 24));
                                         
                                        //  $date_j=date('M-d-Y',strtotime($row->created_at));

                                        //   echo  date_diff($adding_eta,$date);
                                        // echo $datework =new Carbon($date);
                                         
                                       
                                        //   echo  date('d-m-Y',strtotime($difference))
                                        // $datetime1 = date_create('2009-10-11');
                                        // $datetime2 = date_create('2009-10-13');
                                        // $interval = date_diff(strtotime($date), strtotime($adding_eta));
                                        // echo $interval->format('%R%a days');

                                     ?>
                                    <li>
                                       
                                        <input type="hidden" class="time" value="{{$time}}">
                                        <a href="javascript:void(0);">
                                            <h4 style="margin-left: 7px;">
                                               {{ucwords($row->request_type)}} <span style="float:right;">Raised On</span>
                                                {{-- <small>{{$row->id}}%</small> --}}
                                                 <br><span style="font-weight:normal !important;" >For : {{$row->first_name.' '.$row->middle_name.' '.$row->last_name}}</span> <span  style="    font-weight: normal !important;float:right;">{{date('d-m-Y H:i:s',strtotime($row->created_at))}} </span>
                                            </h4>
                                            
                                         
                                          <img src="images/1.svg" alt="Status"><span class="label bg-green"  style="font-size: 11px;"><span class="date_j"  value="{{$date_j.' '.$time}}"></span> </span>
                                          <span  style="float:right;margin-top: 6px; font-size: 13px;" class="label bg-red">{{strtoupper($row->status)}}</span>
                                   
                                    </li>
                                    @endforeach
                                  @endif 
                                    
                                </ul>
                            </li>
                   
                            <li class="footer">
                                <a href="request_approvel">View All Tasks</a>
                            </li>
                        </ul>
                    </li>
                    <!-- #END# Tasks -->
                    <li class="pull-right"><a href="javascript:void(0);" class="js-right-sidebar" data-close="true"><i class="material-icons">more_vert</i></a></li>
                </ul>
            </div>
        </div>
    </nav>
    <script
  src="http://code.jquery.com/jquery-2.2.4.min.js"
  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
  crossorigin="anonymous"></script>
<script src="https://cdn.rawgit.com/hilios/jQuery.countdown/2.1.0/dist/jquery.countdown.min.js"></script>
<script>
    $('.date_j').each(function(){
        $(this).countdown($(this).attr('value'), function(event) {
            $(this).text(
                event.strftime('%D days %H:%M:%S')
                
            );
        });
    });
   
</script>