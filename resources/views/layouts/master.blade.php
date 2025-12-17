<!DOCTYPE html>
<html lang="en">
<!-- Mirrored from colorlib.com//polygon/adminty/default/sample-page.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 08 Jan 2019 06:26:35 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<!-- /Added by HTTrack -->
<head>
    <title>E-Procurement Request</title>
    <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 10]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="#">
    <meta name="keywords" content="flat ui, admin Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="#">
    <!-- Favicon icon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{URL::to('newicon.ico')}}" />
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/bower_components/bootstrap/css/bootstrap.min.css')}}">
    <!-- jquery file upload Frame work -->
    <link href="{{URL::to('static/files/assets/pages/jquery.filer/css/jquery.filer.css')}}" type="text/css" rel="stylesheet" />
    <link href="{{URL::to('static/files/assets/pages/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css')}}" type="text/css" rel="stylesheet" />
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/assets/icon/themify-icons/themify-icons.css')}}">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/assets/icon/icofont/css/icofont.css')}}">
    <!-- feather Awesome -->
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/assets/icon/feather/css/feather.css')}}">
    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{URL::to('static/files/bower_components/select2/css/select2.min.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/bower_components/sweetalert/css/sweetalert.css')}}">
    <!-- animation nifty modal window effects css -->
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/assets/css/component.css')}}">
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/assets/icon/font-awesome/css/font-awesome.min.css')}}">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/assets/icon/icofont/css/icofont.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/assets/pages/data-table/css/buttons.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}">
    <!-- Style.css -->
    <link rel="stylesheet" href="{{URL::to('static/files/assets/pages/chart/radial/css/radial.css')}}" type="text/css" media="all">
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/assets/css/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{URL::to('static/files/assets/css/jquery.mCustomScrollbar.css')}}">
    <style>
        #mydiv {
            position: fixed;
            top: 50%;
            left: 50%;
            width: 18em;
            height: 7em;
            margin-top: -5em;
            /*set to a negative number 1/2 of your height*/
            margin-left: -5em;
            /*set to a negative number 1/2 of your width*/

            background: url("{{URL::to('src/flashscr/loading.gif')}}");
            background-repeat: no-repeat;
            background-size: 80px 80px;

            /* background-color: #f3f3f3; */
        }

        #first {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            top: 0;
            opacity: 0.8;
            background-color: #000;
            color: #fff;
            z-index: 9999;
        }

        @keyframes blinking {
            0% {
                background-color: #FB6868;
                /* border: 5px solid #871924; */
            }

            /* YOU CAN ADD MORE COLORS IN THE KEYFRAMES IF YOU WANT
        50%{
          background-color: #55d66b;
          border: 5px solid #126620;
        }
        */
            100% {
                background-color: #696969;
                /* border: 5px solid #6565f2; */
            }
        }

        .demo_blink {
            /* width: 300px;
        height: 300px; */
            /* NAME | TIME | ITERATION */
            animation: blinking 1s infinite;
        }

        .overlay {
            display: none;
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 99999999;
            background: rgba(255, 255, 255, 0.8) url("/examples/images/loader.gif") center no-repeat;
        }

        /* Turn off scrollbar when body element has the loading class */
        body.loading {
            overflow: hidden;
        }

        /* Make spinner image visible when body element has the loading class */
        body.loading .overlay {
            display: block;
        }
        .fix-col{
            border: none;
            outline: none;
            resize:none;
            width: 100%;
        }
    </style>
    @yield('style')
</head>

<body>
    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

            <nav class="navbar header-navbar pcoded-header" header-theme="theme6">
                <div class="navbar-wrapper">

                    <div class="navbar-logo">
                        <a class="mobile-menu" id="mobile-collapse" href="#!">
                            <i class="feather icon-menu"></i>
                        </a>
                        <a href="{{route('/')}}" id="logo_prince">
                            <img class="img-fluid" src="{{URL::to('static/logo/Prince.png')}}" alt="Theme-Logo" />
                        </a>
                        <a class="mobile-options">
                            <i class="feather icon-more-horizontal"></i>
                        </a>
                    </div>

                    <div class="navbar-container container-fluid">
                        <ul class="nav-left">
                            <li class="header-search">
                                <div class="main-search morphsearch-search">

                                </div>
                            </li>
                            <li>
                                <a href="#!" onclick="javascript:toggleFullScreen()">
                                    <i class="feather icon-maximize full-screen"></i>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav-right">
                            <li class="user-profile header-notification">
                                <div class="dropdown-primary dropdown">
                                    <div class="dropdown-toggle" data-toggle="dropdown">
                                        <img src="{{URL::to('static/logo/avatar-17.png')}}" class="img-radius" alt="User-Profile-Image" >
                                        <span>{{Auth::user()->firstname}} {{Auth::user()->lastname}}</span>
                                        <i class="feather icon-chevron-down"></i>
                                    </div>
                                    <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">

                                        <li>
                                            <a href="#" data-toggle="modal" data-target="#profile-Modal">
                                                <i class="feather icon-user"></i> Profile
                                            </a>
                                        </li>

                                        <li>
                                            <a href="{{route('logout')}}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                                <i class="feather icon-log-out"></i> Logout
                                            </a>
                                            <form id="logout-form" action="{{route('logout')}}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </li>
                                    </ul>

                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    @yield('menu')
                    @if(Session::get('is_treasury') == '1' or Session::get('is_accounting_team') == '1' or Session::get('is_finance_team') == '1')
                    <li class="{{ request()->is('reports/payment-tracking-request') || request()->is('reports/filter-payment-tracking-request') || request()->is('reports/advance-tracking-request') || request()->is('reports/filter-advance_tracking-request') || request()->is('reports/clear-advance-tracking-request') || request()->is('reports/filter-clear-advance-tracking-request')  ? 'pcoded-hasmenu active  pcoded-trigger' : 'pcoded-hasmenu' }}">
                        <a href="javascript:void(0)">
                            <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
                            <span class="pcoded-mtext">DE Upload</span>
                        </a>
                        <ul class="pcoded-submenu">
                            <li class="{{ request()->is('lists/payment-record') ? 'active' : ''}}">
                                <a href="{{ route('DE-uploads') }}">
                                    <span class="pcoded-mtext">Bank Payment Voucher</span>
                                </a>
                            </li>
                        </ul>
                        <ul class="pcoded-submenu">
                            <li class="{{ request()->is('lists/payment-record') ? 'active' : ''}}">
                                <a href="{{ route('DE-uploads-bank-receipt') }}">
                                    <span class="pcoded-mtext">Bank Receipt Voucher</span>
                                </a>
                            </li>
                        </ul>
                        <ul class="pcoded-submenu">
                            <li class="{{ request()->is('lists/payment-record') ? 'active' : ''}}">
                                <a href="{{ route('DE-uploads-journal') }}">
                                    <span class="pcoded-mtext">Journal Voucher</span>
                                </a>
                            </li>
                        </ul>
                        <ul class="pcoded-submenu">
                            <li class="{{ request()->is('lists/payment-record') ? 'active' : ''}}">
                                <a href="{{ route('DE-uploads-cash-payment') }}">
                                    <span class="pcoded-mtext">Cash Payment Voucher</span>
                                </a>
                            </li>
                        </ul>
                        <ul class="pcoded-submenu">
                            <li class="{{ request()->is('lists/payment-record') ? 'active' : ''}}">
                                <a href="{{ route('DE-uploads-cash-receipt') }}">
                                    <span class="pcoded-mtext">Cash Receipt Voucher</span>
                                </a>
                            </li>
                            @if (Session::get('is_treasury') == '1')
                            <li class="{{ request()->is('reports/payment-tracking-request') || request()->is('reports/filter-payment-tracking-request') || request()->is('reports/advance-tracking-request') || request()->is('reports/filter-advance_tracking-request') || request()->is('reports/clear-advance-tracking-request') || request()->is('reports/filter-clear-advance-tracking-request')  ? 'pcoded-hasmenu active  pcoded-trigger' : 'pcoded-hasmenu' }}">
                                <a href="javascript:void(0)">
                                    <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
                                    <span class="pcoded-mtext">Treasury Voucher</span>
                                </a>
                                <ul class="pcoded-submenu">
                                    <li class=" ">
                                        <a href="{{ route('DE-uploads-bank') }}">
                                            <span class="pcoded-mtext">Treasury Voucher</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (Session::get('is_treasury') == '1')
                    <li class="">
                        <a href="{{ route('reports/bank-voucher-tracking-request-search') }}">
                            <span class="pcoded-micon"><i class="feather icon-edit-1"></i></span>
                            <span class="pcoded-mtext"> Treasury Voucher Report</span>
                        </a>
                    </li>
                    @endif
                    @if(Session::get('is_allow_to_view_payment_record') == '1')
                    <li class="{{ request()->is('lists/payment-record') ? 'active' : '' }}">
                        <a href="{{ route('lists/payment-record') }}">
                            <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
                            <span class="pcoded-mtext">Payment Record</span>
                            @if ($total_payment_record > 0)
                            <span class="pcoded-badge label label-warning">{{ $total_payment_record}}</span>
                            @endif
                        </a>
                    </li>
                    @endif
                    @if(Session::get('is_allow_to_view_advance_record') == '1')
                    <li class="{{ request()->is('reports/advance-records') ? 'active' : '' }}">
                        <a href="{{ route('reports/advance-records') }}">
                            <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
                            <span class="pcoded-mtext">Advance Record</span>
                            @if ($total_advance_record > 0)
                            <span class="pcoded-badge label label-warning">{{ $total_advance_record}}</span>
                            @endif
                        </a>
                    </li>
                    @endif
                    @if (Session::get('is_admin') == '1')
                    <li class="{{ request()->is('requestlog/listing') || request()->is('requestlog/filter-listing') ? 'active' : '' }}">
                        <a href="{{ route('requestlog/listing') }}">
                            <span class="pcoded-micon"><i class="fa fa-files-o"></i></span>
                            <span class="pcoded-mtext">Request Log</span>
                            @if (count($count_record) > 0)
                            <span class="pcoded-badge label label-warning">{{ count($count_record) }}</span>
                            @endif
                        </a>
                    </li>
                    @endif

                    <li class="{{ request()->is('tasklist') ? 'active' : '' }}" id="demo_blink_load">
                        <a href="{{route('tasklist')}}">
                            <span class="pcoded-micon"><i class="feather icon-check-circle"></i><b>A</b></span>
                            <span class="pcoded-mtext">Task</span>
                            @if(count($data)>0)
                            <span class="pcoded-badge label label-warning">{{count($data)}}</span>
                            @endif
                        </a>
                    </li>
                    </ul>
                </div>
                </nav>

                <div class="pcoded-content">
                    <div class="pcoded-inner-content">
                        <!-- Main-body start -->
                        <div class="main-body">
                            <div class="page-wrapper">
                                <!-- Page-header start -->
                                @yield('breadcrumb')
                                <!-- Page-header end -->
                                @if(Session::has('success'))
                                <div class="alert alert-success" style="background: #CAFACE " role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <strong>Success!</strong> {{Session::get('success')}}
                                </div>
                                @endif

                                @if(Session::has('error'))
                                <div class="alert alert-danger" style="background: #F7D3D3  " role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <strong>Fail!</strong> {{Session::get('error')}}
                                </div>
                                @endif
                                <div class="page-body">
                                    <div class="row">
                                        <div class="overlay">
                                            <div class="preloader3 loader-block" style="position: absolute;top: 50%;left: 50%;margin: -25px 0 0 -25px;">
                                                <div class="circ1"></div>
                                                <div class="circ2"></div>
                                                <div class="circ3"></div>
                                                <div class="circ4"></div>
                                            </div>
                                        </div>
                                        @yield('content')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="profile-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{strtoupper(Auth::user()->firstname)}} {{strtoupper(Auth::user()->lastname)}} profile</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <td style="text-align: right;">Full Name</td>
                                <td>{{Auth::user()->firstname}} {{Auth::user()->lastname}}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Branch/Department</td>
                                <td>{{Auth::user()->department}}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Position</td>
                                <td>{{Auth::user()->position}}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Mobile Number</td>
                                <td>{{Auth::user()->mobile}}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Status</td>
                                <td>Active</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Warning Section Starts -->
    <!-- Older IE warning message -->
    <!--[if lt IE 10]>
<div class="ie-warning">
    <h1>Warning!!</h1>
    <p>You are using an outdated version of Internet Explorer, please upgrade <br/>to any of the following web browsers
        to access this website.</p>
    <div class="iew-container">
        <ul class="iew-download">
            <li>
                <a href="http://www.google.com/chrome/">
                    <img src="{{URL::to('static/')}}files/assets/images/browser/chrome.png" alt="Chrome">
                    <div>Chrome</div>
                </a>
            </li>
            <li>
                <a href="https://www.mozilla.org/en-US/firefox/new/">
                    <img src="{{URL::to('static/')}}files/assets/images/browser/firefox.png" alt="Firefox">
                    <div>Firefox</div>
                </a>
            </li>
            <li>
                <a href="http://www.opera.com">
                    <img src="{{URL::to('static/')}}files/assets/images/browser/opera.png" alt="Opera">
                    <div>Opera</div>
                </a>
            </li>
            <li>
                <a href="https://www.apple.com/safari/">
                    <img src="{{URL::to('static/')}}files/assets/images/browser/safari.png" alt="Safari">
                    <div>Safari</div>
                </a>
            </li>
            <li>
                <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                    <img src="{{URL::to('static/')}}files/assets/images/browser/ie.png" alt="">
                    <div>IE (9 & above)</div>
                </a>
            </li>
        </ul>
    </div>
    <p>Sorry for the inconvenience!</p>
</div>
<![endif]-->
    <!-- Warning Section Ends -->
    <!-- Required Jquery -->
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/jquery/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/jquery-ui/js/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/popper.js/js/popper.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/bootstrap/js/bootstrap.min.js')}}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js')}}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/modernizr/js/modernizr.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/modernizr/js/css-scrollbars.js')}}"></script>

    <!-- jquery file upload js -->
    <script src="{{URL::to('static/files/assets/pages/jquery.filer/js/jquery.filer.min.js')}}"></script>
    <script src="{{URL::to('static/files/assets/pages/filer/custom-filer.js')}}" type="text/javascript"></script>
    <script src="{{URL::to('static/files/assets/pages/filer/jquery.fileuploads.init.js')}}" type="text/javascript"></script>
    <script type="text/javascript" src="{{URL::to('static/files/assets/pages/edit-table/jquery.tabledit.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('static/files/assets/pages/edit-table/editable.js')}}"></script>

    <!-- Chart js -->
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/chart.js/js/Chart.js')}}"></script>
    <!-- amchart js -->
    <script src="{{URL::to('static/files/assets/pages/widget/amchart/amcharts.js')}}"></script>
    <script src="{{URL::to('static/files/assets/pages/widget/amchart/serial.js')}}"></script>
    <script src="{{URL::to('static/files/assets/pages/widget/amchart/light.js')}}"></script>
    <!-- sweet alert js -->
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/sweetalert/js/sweetalert.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('static/files/assets/js/modal.js')}}"></script>
    <!-- sweet alert modal.js intialize js -->
    <!-- modalEffects js nifty modal window effects -->
    <script type="text/javascript" src="{{URL::to('static/files/assets/js/modalEffects.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('static/files/assets/js/classie.js')}}"></script>

    <!-- data-table js -->
    <script src="{{URL::to('static/files/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::to('static/files/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{URL::to('static/files/assets/pages/data-table/js/jszip.min.js')}}"></script>
    <script src="{{URL::to('static/files/assets/pages/data-table/js/pdfmake.min.js')}}"></script>
    <script src="{{URL::to('static/files/assets/pages/data-table/js/vfs_fonts.js')}}"></script>
    <script src="{{URL::to('static/files/bower_components/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{URL::to('static/files/bower_components/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{URL::to('static/files/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{URL::to('static/files/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{URL::to('static/files/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{URL::to('static/files/assets/pages/data-table/js/data-table-custom.js')}}"></script>
    <!-- Masking js -->
    <script src="{{URL::to('static/files/assets/pages/form-masking/inputmask.js')}}"></script>
    <script src="{{URL::to('static/files/assets/pages/form-masking/jquery.inputmask.js')}}"></script>
    <script src="{{URL::to('static/files/assets/pages/form-masking/autoNumeric.js')}}"></script>
    <script src="{{URL::to('static/files/assets/pages/form-masking/form-mask.js')}}"></script>
    <!-- i18next.min.js -->
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/i18next/js/i18next.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/i18next-xhr-backend/js/i18nextXHRBackend.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/i18next-browser-languagedetector/js/i18nextBrowserLanguageDetector.min.js')}}"></script>
    <!-- Select 2 js -->
    <script type="text/javascript" src="{{URL::to('static/files/bower_components/select2/js/select2.full.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('static/files/assets/pages/advance-elements/select2-custom.js')}}"></script>
    <!-- Validation js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
    <script type="text/javascript" src="{{URL::to('static/files/assets/pages/form-validation/validate.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('static/files/assets/pages/form-validation/form-validation.js')}}"></script>


    <!-- Validation customize form js -->
    <script type="text/javascript" src="{{URL::to('static/files/assets/pages/form-validation/query.validate.js')}}"></script>

    <script type="text/javascript" src="{{URL::to('static/files/bower_components/jquery-i18next/js/jquery-i18next.min.js')}}"></script>
    <script src="{{URL::to('static/files/assets/js/pcoded.min.js')}}"></script>
    <script src="{{URL::to('static/files/assets/js/vartical-layout.min.js')}}"></script>
    <script src="{{URL::to('static/files/assets/js/jquery.mCustomScrollbar.concat.min.js')}}"></script>

    <!-- Custom js -->

    <script type="text/javascript" src="{{URL::to('static/files/assets/js/script.js')}}"></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script>
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 4000);
    </script>
    <script>
        $(document).ready(function($) {
            var coooblink = "{{count($data)}}";

            if (coooblink > 0) {
                $('#demo_blink_load').addClass('demo_blink');
            } else {
                $('#demo_blink_load').removeClass('demo_blink');
            }
        });
    </script>
    <script>
        $(document).ready(function() {
        
            var firstLogin = localStorage.getItem('first_login');
            if (firstLogin == 1) {
                console.log('firstLogin');
                var dt = $('#pcoded').attr("pcoded-device-type");
                if (dt == "phone") {
                    $('#pcoded').attr("vertical-nav-type", "expanded");
                }
                localStorage.setItem('first_login', 0);
            }

        });
    </script>
    <script>
        $("#dashboard").click(function(){
            localStorage.removeItem('first_login');
            localStorage.setItem('first_login', 1);
        });
        $("#logo_prince").click(function(){
            localStorage.removeItem('first_login');
            localStorage.setItem('first_login', 1);
        });

    </script>
    @yield('script')
</body>


<!-- Mirrored from colorlib.com//polygon/adminty/default/sample-page.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 08 Jan 2019 06:26:35 GMT -->

</html>