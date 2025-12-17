<!DOCTYPE html>
<html lang="en">

<head>
    <title>E-Procurement & Payment</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
   <!--===============================================================================================-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::to('newicon.ico') }}" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ URL::to('static/login/vendor/bootstrap/css/bootstrap.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ URL::to('static/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ URL::to('static/login/fonts/Linearicons-Free-v1.0.0/icon-font.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ URL::to('static/login/vendor/animate/animate.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ URL::to('static/login/vendor/css-hamburgers/hamburgers.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ URL::to('static/login/vendor/animsition/css/animsition.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ URL::to('static/login/vendor/select2/select2.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ URL::to('static/login/vendor/daterangepicker/daterangepicker.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ URL::to('static/login/css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('static/login/css/main.css') }}">
    <!--===============================================================================================-->
</head>

<body>

    <div class="limiter">
        <div class="container-login100"
            style="background-image: url('{{ URL::to('static/login/images/01.jpg') }}');">
            <div class="wrap-login100 p-t-30 p-b-50">
                <span class="login100-form-title p-b-41">
                    E-Procurement & Payment
                </span>
                @if (Session::has('error'))
                    <div class="alert alert-danger" style="background: #F7D3D3  " role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <strong>Fail!</strong> {{ Session::get('error') }}
                    </div>
                @endif
                <form class="login100-form validate-form p-b-33 p-t-5" method="POST" action="{{ route('signin') }}" autocomplete="off">
                    {{ csrf_field() }}

                    <div class="wrap-input100 validate-input" data-validate="Enter username">
                        <input class="input100" type="text" name="email" placeholder="User name">
                        <span class="focus-input100" data-placeholder="&#xe82a;"></span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="Enter password">
                        <input class="input100" type="password" name="password" placeholder="Password" autocomplete="off">
                        <span class="focus-input100" data-placeholder="&#xe80f;"></span>
                    </div>

                    <div class="container-login100-form-btn m-t-32">
                        <button class="login100-form-btn" id="login_sub">
                            Login
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>


    <div id="dropDownSelect1"></div>

    <!--===============================================================================================-->
    <script src="{{ URL::to('static/login/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ URL::to('static/login/vendor/animsition/js/animsition.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ URL::to('static/login/vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ URL::to('static/login/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ URL::to('static/login/vendor/select2/select2.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ URL::to('static/login/vendor/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ URL::to('static/login/vendor/daterangepicker/daterangepicker.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ URL::to('static/login/vendor/countdowntime/countdowntime.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ URL::to('static/login/js/main.js') }}"></script>
    <script>
        $("#login_sub").click(function(){
            localStorage.removeItem('first_login');
            localStorage.setItem('first_login', 1);
        });

    </script>
</body>

</html>
