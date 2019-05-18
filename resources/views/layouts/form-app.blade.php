<?php date_default_timezone_set('Asia/Kolkata'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="mycsrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Welcome To | Shree Shivam</title>
    <!-- Favicon-->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />
    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />
    <!-- Sweet Alert Css -->
    <link href="plugins/sweetalert/sweetalert.css" rel="stylesheet" />

    <link href="plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
	<link href="plugins/DatePickerBootstrap/css/datepicker.css" rel="stylesheet" />


    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">
    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="css/themes/all-themes.css" rel="stylesheet" />
</head>

<body class="theme-pink">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Search Bar -->
    <div class="search-bar">
        <div class="search-icon">
            <i class="material-icons">search</i>
        </div>
        <input type="text" placeholder="START TYPING...">
        <div class="close-search">
            <i class="material-icons">close</i>
        </div>
    </div>
    <!-- #END# Search Bar -->
    <!-- Top Bar -->
    @include('inc.nav')
    <!-- #Top Bar -->
    @include('inc.sidebar')

    @yield('content')

    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>
    <!-- Select Plugin Js -->
    <!-- <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script> -->
    <!-- Slimscroll Plugin Js -->
    <script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <!-- Jquery Validation Plugin Css -->
    <!-- <script src="plugins/jquery-validation/jquery.validate.js"></script> -->
    <!-- JQuery Steps Plugin Js -->
<!--     <script src="plugins/jquery-steps/jquery.steps.js"></script> -->
    <!-- Sweet Alert Plugin Js -->
     <script src="plugins/sweetalert/sweetalert.min.js"></script> 
    <!-- Waves Effect Plugin Js -->
    <script src="plugins/bootstrap-notify/bootstrap-notify.js"></script>
    <script src="plugins/node-waves/waves.js"></script>
    <script src="js/pages/ui/dialogs.js"></script>
    <script src="plugins/DatePickerBootstrap/js/bootstrap-datepicker.js"></script>
    <!-- <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script> -->
	<link href="plugins/selectpicker/select2.css" rel="stylesheet">
    <script src="plugins/selectpicker/select2.js"></script>
    <!-- Custom Js -->
    <script src="js/admin.js"></script>
   <!--  <script src="js/pages/forms/form-wizard.js"></script> -->
    <!-- Demo Js -->
    <script src="js/demo.js"></script>
//     <script type="text/javascript">
//         $.ajaxSetup({
//                 headers: {
//                     'X-CSRF-TOKEN': $('meta[name="mycsrf-token"]').attr('content')
//                 }
//             });
//         $(document).ready(function(){
//           var autoLoad = setInterval(
//           function ()
//           {
//               $.ajax({
//                 type:'GET',
//                 url:'get_notification',
//                 data:{},
//                 success:function(data){ 
//                     var mydata = JSON.parse(data);
//                     var notification = mydata.notification;
//                     var list = mydata.notification_list;
//                     if(notification>$('#notification').html())
//                     { 
//                         var newdata = notification-$('#notification').html();
//                         var text = newdata + ' New Notification';
//                         showNotification('bg-blue', text, "top", "right", 'animated fadeInDown', 'animated fadeOutUp');
//                     }                   
//                         $('#notification').html(notification);
//                         $('#my_notification_list').html(list);
//                 }
//                 });
//           }, 3000000);
//       }); 

//         function showNotification(colorName, text, placementFrom, placementAlign, animateEnter, animateExit) {
//     if (colorName === null || colorName === '') { colorName = 'bg-black'; }
//     if (text === null || text === '') { text = 'Turning standard Bootstrap alerts'; }
//     if (animateEnter === null || animateEnter === '') { animateEnter = 'animated fadeInDown'; }
//     if (animateExit === null || animateExit === '') { animateExit = 'animated fadeOutUp'; }
//     var allowDismiss = true;

//     $.notify({
//         message: text
//     },
//         {
//             type: colorName,
//             allow_dismiss: allowDismiss,
//             newest_on_top: true,
//             timer: 1000,
//             placement: {
//                 from: placementFrom,
//                 align: placementAlign
//             },
//             animate: {
//                 enter: animateEnter,
//                 exit: animateExit
//             },
//             template: '<div data-notify="container" class="bootstrap-notify-container alert alert-dismissible {0} ' + (allowDismiss ? "p-r-35" : "") + '" role="alert">' +
//             '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
//             '<span data-notify="icon"></span> ' +
//             '<span data-notify="title">{1}</span> ' +
//             '<span data-notify="message">{2}</span>' +
//             '<div class="progress" data-notify="progressbar">' +
//             '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
//             '</div>' +
//             '<a href="{3}" target="{4}" data-notify="url"></a>' +
//             '</div>'
//         });
// }
//     </script>
<!--    <script type="text/javascript">-->
<!--        $.ajaxSetup({-->
<!--                headers: {-->
<!--                    'X-CSRF-TOKEN': $('meta[name="mycsrf-token"]').attr('content')-->
<!--                }-->
<!--            });-->
<!--        $(document).ready(function(){-->
         
<!--           var autoLoad_2 = setInterval(-->
             
<!--           function ()-->
<!--           {-->
<!--              $.ajax({-->
<!--                type:'GET',-->
<!--                url:'task_show',-->
<!--                data:{},-->
<!--                success:function(data){ -->
                    
<!--                  },-->
<!--                  error:function(er){-->
                    
<!--                  }-->
<!--                });-->
<!--           }, 3000);-->
<!--       }); -->
           
<!--</script>-->
    @yield('jquery')
</body>

</html>
