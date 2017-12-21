<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>.:Đăng Nhập:.</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <base href="{{asset('')}}">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/iCheck/square/blue.css">
    <link rel="shortcut icon" href="favicon.png" type="image/vnd.microsoft.icon">

    <style type="text/css" media="screen">
        .se-pre-con {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url(images/Preloader_8.gif) center no-repeat rgba(255, 255, 255, 0.73);
        }
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="{{asset('')}}"><b>WLIN ADMIN</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Mời đăng nhập</p>
        <form action="admin/login" method="post" autocomplete="on">
            <input type="hidden" name="_token" value="{{csrf_token()}}"/>
            @include('backend.partial.alert')
            <div class="form-group has-feedback">
                    <input type="text" class="form-control" autofocus value="{{ session()->has('nhoemail') ? session('nhoemail') : old('email')}}" name="email"
                       placeholder="Email...">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" name="password" value="{{ session('nhopass')}}" placeholder="Mật khẩu...">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember"> Ghi nhớ
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Đăng nhập</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
        <!-- /.social-auth-links -->

        <a href="#thankyou" data-toggle="modal">Quên mật khẩu?</a>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<!--start dathangthanhcong-->
<div class="modal fade" id="thankyou">
    <div class="modal-dialog">
        <form action="{{asset('admin/forget-password')}}" id="form-forget" autocomplete="off" method="post" role="form">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <div class="modal-content" style="font-family: 'Lato', sans-serif;border:4px solid rgba(36, 36, 37, 0.5);">
                <div class="modal-header" style="background: rgb(230, 249, 236);">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Lấy lại mật khẩu</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for=""></label>
                        <input type="email" required autofocus class="form-control" name="email"
                               placeholder="Nhập địa chỉ email quản trị để lấy lại mật khẩu...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Gửi</button>
                </div>
            </div><!-- /.modal-content -->
        </form>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--end dathangthanhcong-->
<div class="se-pre-con" style="display:none"></div>
<!-- jQuery 2.2.0 -->
@include('backend.partial.js')
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
        $('#form-forget').submit(function () {
            $('.se-pre-con').css('display', 'block');
        });
    });
</script>
{{--marker--}}
<script>var language = '{{$language or 'vi'}}'</script>
<script src="js/marker.js"></script>
</body>
</html>
