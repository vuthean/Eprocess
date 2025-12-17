<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from www.themeturka.com/fixed-plus/layouts-5/page-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Feb 2019 23:18:17 GMT -->
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LOGIN</title>
        <!-- Favicon icon -->
        <link rel="shortcut icon" type="image/x-icon" href="{{URL::to('newicon.ico')}}" />
        <!-- Common Plugins -->
        <link href="{{URL::to('src/assets/lib/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

        <!-- Custom Css-->
        <link href="{{URL::to('src/assets/scss/style.css')}}" rel="stylesheet">
                <link rel="stylesheet" href="{{URL::to('src/slot/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{URL::to('src/slot/font-awesome.min.css')}}">
        <link rel="stylesheet" href="{{URL::to('src/css/app.css')}}">
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">
            html,body{
                height: 100%;
            }
            #confetti{
                position: absolute;
                left: 0;
                top: 0;
                height: 100%;
                width: 100%;
        }
        </style>
    </head>
    <body class="bg-light" style="background-image: url({{URL::to('src/image/04.jpg')}});background-size: cover;height: 100%;overflow: auto;">
        <canvas id="confetti" width="1" height="1" ></canvas>
        <form method="POST" action="{{ route('sginin') }}">
            @csrf
        <div class="misc-wrapper">
            <div class="misc-content">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-10">
                            <div class="misc-header text-center">
                                <img alt="" src="{{URL::to('src/image/logo-icon.png')}}" class="toggle-none hidden-xs">
                            </div>



                            <div class="row text-center" style="padding:20px;overflow: auto">
                                <div class="col-sm-3" style="">
                                    <div class="col-md-12" style="border-radius: 15px;background: red;color: white;height: 50px;">
                                        <span style="font-weight: bold;font-size: 25px;line-height: 50px; vertical-align: middle;">
                                            1 Prize 
                                        </span>                                        
                                    </div>
                                </div>
                                <div class="col-sm-7" style="">
                                    <span style="font-weight: bold;font-size: 70px;line-height: 50px; vertical-align: middle;color: white">
                                        5.000.000
                                    </span> 
                                </div>
                                <div class="col-sm-2">
                                    <span style="font-weight: bold;font-size: 30px;line-height: 50px; vertical-align: middle;color: white">
                                        Riels.
                                    </span> 
                                </div>

                                <div class="col-md-12" style="padding-top: 80px">
                                    <div class="col-sm-10" >
                                        <div class="slotwrapper">
                                        <!-- <div class="border-number"> -->
                                            <ul id="one">                       
                                                <li>0</li>
                                                <li>1</li>
                                                <li>2</li>
                                                <li>3</li>
                                                <li>4</li>
                                                <li>5</li>
                                                <li>6</li>
                                                <li>7</li>
                                                <li>8</li>
                                                <li>9</li>
                                               
                                            </ul>
                                        </div>
                                        <div class="slotwrapper">
                                            <ul id="two">
                                                <li>0</li>
                                                <li>1</li>
                                                <li>2</li>
                                                <li>3</li>
                                                <li>4</li>
                                                <li>5</li>
                                                <li>6</li>
                                                <li>7</li>
                                                <li>8</li>
                                                <li>9</li>
                                                
                                            </ul>
                                        </div>
                                        <div class="slotwrapper">
                                            <ul id="three">
                                                <li>0</li>
                                                <li>1</li>
                                                <li>2</li>
                                                <li>3</li>
                                                <li>4</li>
                                                <li>5</li>
                                                <li>6</li>
                                                <li>7</li>
                                                <li>8</li>
                                                <li>9</li>
                                                
                                            </ul>
                                        </div>
                                        <div class="slotwrapper">
                                            <ul id="four">
                                                <li>0</li>
                                                <li>1</li>
                                                <li>2</li>
                                                <li>3</li>
                                                <li>4</li>
                                                <li>5</li>
                                                <li>6</li>
                                                <li>7</li>
                                                <li>8</li>
                                                <li>9</li>
                                                
                                            </ul>
                                        </div>
                                        <div class="slotwrapper">
                                            <ul id="five">
                                                <li>0</li>
                                                <li>1</li>
                                                <li>2</li>
                                                <li>3</li>
                                                <li>4</li>
                                                <li>5</li>
                                                <li>6</li>
                                                <li>7</li>
                                                <li>8</li>
                                                <li>9</li>
                                                
                                            </ul>
                                        </div>
                                        <div class="slotwrapper">
                                            <ul id="six">
                                                <li>0</li>
                                                <li>1</li>
                                                <li>2</li>
                                                <li>3</li>
                                                <li>4</li>
                                                <li>5</li>
                                                <li>6</li>
                                                <li>7</li>
                                                <li>8</li>
                                                <li>9</li>
                                               
                                            </ul>
                                        </div>
                                        <div class="slotwrapper">
                                            <ul id="seven">
                                                <li>0</li>
                                                <li>1</li>
                                                <li>2</li>
                                                <li>3</li>
                                                <li>4</li>
                                                <li>5</li>
                                                <li>6</li>
                                                <li>7</li>
                                                <li>8</li>
                                                <li>9</li>
                                                
                                            </ul>
                                        </div>
                                        <div class="slotwrapper">
                                            <ul id="eight">
                                                <li>0</li>
                                                <li>1</li>
                                                <li>2</li>
                                                <li>3</li>
                                                <li>4</li>
                                                <li>5</li>
                                                <li>6</li>
                                                <li>7</li>
                                                <li>8</li>
                                                <li>9</li>
                                                
                                            </ul>
                                        </div>
                                        <div class="slotwrapper">
                                            <ul id="nine">
                                                <li>0</li>
                                                <li>1</li>
                                                <li>2</li>
                                                <li>3</li>
                                                <li>4</li>
                                                <li>5</li>
                                                <li>6</li>
                                                <li>7</li>
                                                <li>8</li>
                                                <li>9</li>                        
                                            </ul>
                                        </div>
                                        <div>
                                            <input type="hidden" name="ac_no" id="ac_no">
                                            <input type="hidden" name="ac_desc" id="ac_desc">
                                            <input type="hidden" name="phone_number" id="phone_number">
                                            <input type="hidden" name="txt-1" id="txt-1">
                                            <input type="hidden" name="txt-2" id="txt-2">
                                            <input type="hidden" name="txt-3" id="txt-3">
                                            <input type="hidden" name="txt-4" id="txt-4">
                                            <input type="hidden" name="txt-5" id="txt-5">
                                            <input type="hidden" name="txt-6" id="txt-6">
                                            <input type="hidden" name="txt-7" id="txt-7">
                                            <input type="hidden" name="txt-8" id="txt-8">
                                            <input type="hidden" name="txt-9" id="txt-9">
                                            <input type="hidden" name="prize" id="prize" value="Iphone">
                                        </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn-start" id="btn-example10-start">Start</button>
                                            <button type="button" class="btn-stop" id="btn-example10-stop" disabled>Stop</button>
                                        </div>
                                    </div>
                                    <div class="col-md-12" style="padding-top: 80px;">
                                        <div class="col-sm-6 text-left">
                                            <span style="font-weight: bold;color: white;font-size:40px">
                                                Name
                                            </span>
                                            <div class="col-sm-12" style="background: red;height: 70px;border-radius: 15px">
                                                <span id="spn_winnername" style="font-weight: bold;color: white;font-size:40px"></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 text-left">
                                            <span style="font-weight: bold;color: white;font-size:40px">
                                                Phone
                                            </span>
                                            <div class="col-sm-12" style="background: red;height: 70px;border-radius: 15px">
                                                <span id="spn_winnerphone" style="font-weight: bold;color: white;font-size:40px"></span>
                                            </div>
                                        </div>
                                    </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <!-- Common Plugins -->
        <script src="{{URL::to('src/assets/lib/jquery/dist/jquery.min.js')}}"></script>
        <script src="{{URL::to('src/assets/lib/bootstrap/js/bootstrap.min.js')}}"></script>
        <script src="{{URL::to('src/assets/lib/pace/pace.min.js')}}"></script>
        <script src="{{URL::to('src/assets/lib/jasny-bootstrap/js/jasny-bootstrap.min.js')}}"></script>
        <script src="{{URL::to('src/assets/lib/slimscroll/jquery.slimscroll.min.js')}}"></script>
        <script src="{{URL::to('src/assets/lib/nano-scroll/jquery.nanoscroller.min.js')}}"></script>
        <script src="{{URL::to('src/assets/lib/metisMenu/metisMenu.min.js')}}"></script>
        <script src="{{URL::to('src/assets/js/custom.js')}}"></script>
        
        <script src="{{URL::to('src/slot/jquery.min.js')}}"></script>
        <script src="{{URL::to('src/slot/jquery.easing.min.js')}}"></script>
        <script src="{{URL::to('src/slot/bootstrap.min.js')}}"></script>
        <script src="{{URL::to('src/js/slotmachine.min.js')}}"></script>

        @include('spin.spinscript')
    </body>

<!-- Mirrored from www.themeturka.com/fixed-plus/layouts-5/page-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Feb 2019 23:18:17 GMT -->

   
</html>
