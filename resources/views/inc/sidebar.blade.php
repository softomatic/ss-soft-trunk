a<section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <div class="user-info">
                <div class="image">
                    <img src="images/user.png" width="48" height="48" alt="User" />
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo Session::get('username'); ?></div>
                    <div class="email"><?php echo Session::get('useremail'); ?></div>
                    <div class="btn-group user-helper-dropdown">
                        <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="javascript:void(0);"><i class="material-icons">person</i>Profile</a></li>
                           
                            <li><a href="logout"><i class="material-icons">input</i>Sign Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- #User Info -->
            <!-- Menu -->
            <div class="menu">
                <ul class="list">
                    <li class="header">MAIN NAVIGATION</li>

            @if( session('role')=='admin')
                @if('master'== Request::path())
                    <li class="active ">
                @else
                    <li>
                 @endif
                        <a href="master">
                            <i class="material-icons">settings</i>
                            <span>Master Setting</span>
                        </a>
                    </li>

            @endif
               
                @if('dashboard'== Request::path())
                    <li class="active ">
                @else
                    <li>
                @endif
                    <!-- <li class="active"> -->
                        <a href="dashboard">
                            <i class="material-icons">dashboard</i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                
                @if('account'== Request::path())
                    <li class="active ">
                @else
                    <li>
                @endif
                        <a href="account">
                            <i class="material-icons">settings</i>
                            <span>Account</span>
                        </a>
                    </li>
                
                 @if('request_approvel'== Request::path())
                    <li class="active ">
                @else
                    <li>
                @endif
                        <a href="request_approvel">
                            <i class="material-icons">assignment_ind</i>
                            <span>Aproval/Request</span>
                        </a>
                    </li>
                
                  @if(session('role')=='hr' || session('role')=='hr admin' || session('role')=='admin')
                @if('zoneanalysis'== Request::path() || 'employeeanalysis'== Request::path() || 'brandanalysis' == Request::path())
                        <li class="active ">
                    @else
                        <li>
                    @endif
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">insert_chart</i>
                            <span>Analysis</span>
                        </a>
                        <ul class="ml-menu">

                        {{-- @if(session('role')!='sales' && session('role')!='employee') --}}

                            @if('zoneanalysis'== Request::path())
                                <li class="active ">
                            @else
                                <li>
                            @endif
                                <a href="zoneanalysis">
                                    <i class="material-icons">show_chart</i>
                                    <span>Zone Analysis</span>
                                </a>
                            </li>
                             @if('employeeanalysis'== Request::path())
                                <li class="active ">
                            @else
                                <li>
                            @endif
                                <a href="employeeanalysis">
                                    <i class="material-icons">show_chart</i>
                                    <span>Employee Analysis</span>
                                </a>
                            </li>
                        {{-- @endif --}}

                        @if('brandanalysis'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="brandanalysis">
                                    <i class="material-icons">show_chart</i>
                                    <span>Brand Wise Analysis</span>
                                </a>
                            </li>
                        </ul>
                    </li>
            @endif
               
                @if(session('role')=='admin')
                     @if('attendance_sheduler'== Request::path())
                    <li class="active ">
                @else
                    <li>
                @endif
                    <!-- <li class="active"> -->
                        <a href="attendance_sheduler">
                            <i class="material-icons">event</i>
                            <span>Attendance sheduler</span>
                        </a>
                    </li>
                   @endif   
                    
                  @if('attendance-search'== Request::path() || 'attendance-report'== Request::path() || 'monthly_attendance_report'== Request::path() || 'attendance-search'== Request::path() || 'edit_attendance'== Request::path())
                    <li class="active ">
                @else
                    <li>
                @endif                    
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">event</i>
                            <span>Attendance</span>
                        </a>
                        <ul class="ml-menu">

                        <!-- @if(session('role')=='hr' || session('role')=='hr admin' || session('role')=='admin')

                        @if('add-attendance'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="add-attendance">
                                    <i class="material-icons">layers</i>
                                    <span>Add Attendance</span>
                                </a>
                            </li>

                        @endif -->

                        @if('attendance-report'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="attendance-report">
                                    <i class="material-icons">layers</i>
                                    <span>Attendance Report</span>
                                </a>
                            </li>

                        @if(session('role')=='hr' || session('role')=='hr admin' || session('role')=='admin')
                            @if('attendance-search'== Request::path())
                                <li class="active ">
                            @else
                                <li>
                            @endif
                                    <a href="attendance-search">
                                        <i class="material-icons">layers</i>
                                        <span>Attendance Search</span>
                                    </a>
                                </li>

                            @if('monthly_attendance_report'== Request::path())
                                <li class="active ">
                            @else
                                <li>
                            @endif
                                    <a href="monthly_attendance_report">
                                        <i class="material-icons">layers</i>
                                        <span>Monthly Attendance Report</span>
                                    </a>
                                </li>
                              @if( session('role')=='admin')
                            @if('edit_attendance'== Request::path())
                                <li class="active ">
                            @else
                                <li>
                            @endif
                                    <a href="edit_attendance">
                                        <i class="material-icons">layers</i>
                                        <span>Edit Attendance</span>
                                    </a>
                                </li>
                            @endif
                        @endif

                        </ul>
                    </li>
               
            @if(session('role')=='admin')

                 @if('department'== Request::path() || 'designation'== Request::path() || 'branch'== Request::path() || 'division'== Request::path() || 'section'== Request::path() || 'sub-section'== Request::path() || 'working_hour'== Request::path() || 'bank'== Request::path())
                    <li class="active ">
                @else
                    <li>
                @endif                    
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">business</i>
                            <span>Company Master</span>
                        </a>
                        <ul class="ml-menu">

                        @if('branch'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="branch">
                                    <i class="material-icons">layers</i>
                                    <span>Branch</span>
                                </a>
                            </li>

                        @if('bank'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="bank">
                                    <i class="material-icons">layers</i>
                                    <span>Bank</span>
                                </a>
                            </li>

                        @if('department'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="department">
                                    <i class="material-icons">layers</i>
                                    <span>Department</span>
                                </a>
                            </li>

                        @if('designation'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="designation">
                                    <i class="material-icons">layers</i>
                                    <span>Designation</span>
                                </a>
                            </li>

                        @if('division'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="division">
                                    <i class="material-icons">layers</i>
                                    <span>Division</span>
                                </a>
                            </li>

                        @if('section'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="section">
                                    <i class="material-icons">layers</i>
                                    <span>Section</span>
                                </a>
                            </li>
                            
                        @if('sub-section'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="sub-section">
                                    <i class="material-icons">layers</i>
                                    <span>Sub Section</span>
                                </a>
                            </li>

                        @if('working_hour'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="working_hour">
                                    <i class="material-icons">layers</i>
                                    <span>Working Hour</span>
                                </a>
                            </li>

                        @if('holidays'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                 <a href="holidays">
                                    <i class="material-icons">flight</i>
                                    <span>Holidays</span>
                                </a>
                            </li>  
                    
                        </ul>
                    </li>
                 @endif
              @if('add-employee'== Request::path() || 'employee-list'== Request::path() || 'emp-search'== Request::path() || 'advance'== Request::path() || 'emp-doc'== Request::path() || 'other_addition_deduction'== Request::path() || 'add_tds'== Request::path() || 'employee_shift'== Request::path() || 'hold_release_salary'== Request::path() )
                    <li class="active ">
                @else
                    <li>
                @endif               
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">group</i>
                            <span>Employee</span>
                        </a>
                        <ul class="ml-menu">
                        @if('add-employee'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="add-employee">
                                    <i class="material-icons">group_add</i>
                                    <span>Add Employee</span>
                                </a>
                            </li>
                        @if('employee-list'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif  <a href="employee-list">
                                    <i class="material-icons">person</i>
                                    <span>Employee List</span>
                                </a>
                            </li>

                        @if('emp-doc'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif  <a href="emp-doc">
                                    <i class="material-icons">file_upload</i>
                                    <span>Upload Employee Doc</span>
                                </a>
                            </li>

                        @if('advance'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="advance">
                                    <i class="material-icons">layers</i>
                                    <span>Advance Master</span>
                                </a>
                            </li>
                        @if(session('role')=='admin')
                            @if('transfer_master'== Request::path())
                                <li class="active ">
                            @else
                                <li>
                            @endif
                           
                                    <a href="transfer_master">
                                        <i class="material-icons">layers</i>
                                        <span>Transfer Master</span>
                                    </a>
                            </li>
                        @endif
                    @if(session('role')=='admin')
                        @if('other_addition_deduction'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="other_addition_deduction">
                                    <i class="material-icons">layers</i>
                                    <span>Other Addition/Deduction</span>
                                </a>
                            </li>


                        @if('add_tds' == Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="add_tds">
                                    <i class="material-icons">layers</i>
                                    <span>Add TDS</span>
                                </a>
                            </li>
                    @endif

                        @if(session('role')=='admin')
                        @if('employee_shift' == Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="employee_shift">
                                    <i class="material-icons">layers</i>
                                    <span>Employee Shift</span>
                                </a>
                            </li>
                        

                        @if('hold_release_salary' == Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="hold_release_salary">
                                    <i class="material-icons">layers</i>
                                    <span>Hold/Release Salary</span>
                                </a>
                            </li>
                        @endif
                        </ul>
                    </li>
                      @if('feedback'== Request::path())
                    <li class="active ">
                @else
                    <li>
                @endif
                        <a href="feedback">
                            <i class="material-icons">feedback</i>
                            <span>Feedback</span>
                        </a>
                    </li>

                 @if(session('role')=='hr' || session('role')=='hr admin' || session('role')=='admin')

                 @if('history'== Request::path())
                    <li class="active ">
                @else
                    <li>
                @endif
                    <!-- <li class="active"> -->
                        <a href="history">
                            <i class="material-icons">history</i>
                            <span>History</span>
                        </a>
                    </li>
            
                 @if('leave'== Request::path())
                    <li class="active ">
                @else
                    <li>
                @endif
                        <a href="leave">
                            <i class="material-icons">flight</i>
                            <span>Leave</span>
                        </a>
                    </li>    

              @endif
                    
                @if('notification'== Request::path())
                    <li class="active">
                @else
                    <li>
                @endif
                    <!-- <li class="active"> -->
                        <a href="notification">
                            <i class="material-icons">notifications</i>
                            <span>Notification</span>
                        </a>
                    </li>
                     @if('notice-board'== Request::path())
                    <li class="active ">
                @else
                    <li>
                @endif
                        <a href="notice-board">
                            <i class="material-icons">notifications</i>
                            <span>Notice Board</span>
                        </a>
                    </li>
                
                     @if( session('role')=='admin')
                 @if('create-payslip'== Request::path() || 'payslip-list'== Request::path())
                        <li class="active ">
                    @else
                        <li>
                    @endif
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">layers</i>
                            <span>Payroll</span>
                        </a>
                        <ul class="ml-menu">

                        @if(session('role')!='sales' && session('role')!='employee')

                            @if('create-payslip'== Request::path())
                                <li class="active ">
                            @else
                                <li>
                            @endif
                                <a href="create-payslip">
                                    <i class="material-icons">layers</i>
                                    <span>Create Payslip</span>
                                </a>
                            </li>

                        @endif

                        @if('payslip-list'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="payslip-list">
                                    <i class="material-icons">layers</i>
                                    <span>Payslip List</span>
                                </a>
                            </li>
                            
                            @if('payslip-list'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="deletepayslip">
                                    <i class="material-icons">layers</i>
                                    <span>Delete Payslip</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                     @endif
                
                @if('personalinformation'== Request::path())
                    <li class="active ">
                @else
                    <li>
                @endif
                    <!-- <li class="active"> -->
                        <a href="personalinformation">
                            <i class="material-icons">account_box</i>
                            <span>Personal Information</span>
                        </a>
                    </li>
                    
                   @if(session('role')=='admin')

                     @if('report'== Request::path())
                    <li class="active ">
                     @else
                    <li>
                    
                        <a href="report">
                            <i class="material-icons"> view_list</i>
                            <span>Reports</span>
                        </a>
                    </li>   
                  
                        
                    @endif 
                @if(session('role')=='hr' || session('role')=='hr admin' || session('role')=='admin')

               @if('create-team'== Request::path() || 'target'== Request::path())
                    <li class="active ">
                @else
                    <li>
                @endif                    
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">business</i>
                            <span>Team Management</span>
                        </a>
                        <ul class="ml-menu">


                        @if('create-team'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="create-team">
                                    <i class="material-icons">layers</i>
                                    <span>Team</span>
                                </a>
                            </li>
                        
                     @if('target'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="target">
                                    <i class="material-icons">layers</i>
                                    <span>Target</span>
                                </a>
                            </li>
                      <!-- @if('asign-target'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="asign-target">
                                    <i class="material-icons">layers</i>
                                    <span>Asign Target</span>
                                </a>
                            </li>-->
                        </ul>
                    </li> 
                     @endif 


             @if(session('role')=='admin')

                @if('add-attendance'== Request::path() || 'upload-emp'== Request::path() || 'upload-sales'== Request::path() || 'file'==Request::path() || 'upload-tds'==Request::path() || 'upload-attendance'==Request::path() || 'upload-bonus'==Request::path() || 'upload-incentive'==Request::path() || 'upload-ex_gratia'==Request::path() || 'upload-arrear'==Request::path())
                    <li class="active ">
                @else
                    <li>
                 @endif
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">settings</i>
                            <span>Upload Reports</span>
                        </a>
                        <ul class="ml-menu">

                        @if('add-attendance'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="add-attendance">
                                    <i class="material-icons">layers</i>
                                    <span>Attendance</span>
                                </a>
                            </li>
                            
                        @if('upload-attendance'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="upload-attendance">
                                    <i class="material-icons">layers</i>
                                    <span>Monthly Attendance</span>
                                </a>
                            </li>

                        @if('upload-emp'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="upload-emp">
                                    <i class="material-icons">layers</i>
                                    <span>Employee</span>
                                </a>
                            </li>

                        @if('upload-sales'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="upload-sales">
                                    <i class="material-icons">layers</i>
                                    <span>Sales</span>
                                </a>
                            </li>

                        @if('file'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="file">
                                    <i class="material-icons">layers</i>
                                    <span>File</span>
                                </a>
                            </li>

                        <!--@if('upload-tds'== Request::path())-->
                        <!--    <li class="active ">-->
                        <!--@else-->
                        <!--    <li>-->
                        <!--@endif-->
                        <!--        <a href="upload-tds">-->
                        <!--            <i class="material-icons">layers</i>-->
                        <!--            <span>TDS</span>-->
                        <!--        </a>-->
                        <!--    </li>-->

                        @if('upload-bonus'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="upload-bonus">
                                    <i class="material-icons">layers</i>
                                    <span>Bonus</span>
                                </a>
                            </li>

                        @if('upload-incentive'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="upload-incentive">
                                    <i class="material-icons">layers</i>
                                    <span>Incentive</span>
                                </a>
                            </li>

                        @if('upload-ex_gratia'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="upload-ex_gratia">
                                    <i class="material-icons">layers</i>
                                    <span>Ex Gratia</span>
                                </a>
                            </li>

                        @if('upload-arrear'== Request::path())
                            <li class="active ">
                        @else
                            <li>
                        @endif
                                <a href="upload-arrear">
                                    <i class="material-icons">layers</i>
                                    <span>Arrear</span>
                                </a>
                            </li>

                        </ul>
                    </li>

                @endif
                
       

           
                
             


               <!--  @if(session('role')=='hr' || session('role')=='hr admin' || session('role')=='admin')
                     @if('upload-sales'== Request::path())
                        <li class="active ">
                    @else
                        <li>
                    @endif
                            <a href="upload-sales">
                                <i class="material-icons">add</i>
                                <span>Upload Sales</span>
                            </a>
                        </li>
                @endif -->
               
            @endif

            <!--@if(session('role')=='hr' || session('role')=='hr admin' || session('role')=='admin')-->

            <!--    @if('expense'== Request::path())-->
            <!--        <li class="active ">-->
            <!--    @else-->
            <!--        <li>-->
            <!--    @endif-->
            <!--            <a href="expense">-->
            <!--                <i class="material-icons"> view_list</i>-->
            <!--                <span>Expense</span>-->
            <!--            </a>-->
            <!--        </li>-->

              

            @endif

              

                
                   
                </ul>
            </div>
            <!-- #Menu -->
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                    &copy; 2018 - 2019 <a href="javascript:void(0);">Shree Shivam</a>
                </div>
                <!-- <div class="version">
                    <b>Version: </b> 1.0.5
                </div> -->
            </div>
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
        <!-- Right Sidebar -->
        @include('inc.right-sidebar')
        <!-- #END# Right Sidebar -->
    </section>