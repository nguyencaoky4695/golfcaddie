<!DOCTYPE html>
<html lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đặt lại mật khẩu</title>

    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .error {
            color: red;
        }
    </style>
</head>

<body>

<div class="container" style="margin-top:20px">
    <div class="text-center">
        <img src="{{asset('images/logo.png')}}" class="img-responsive" style="display: block;margin: auto;max-width: 145px;" alt="NGV247">
    </div>
    <div class="container" style="margin-top:20px">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center text-bold">Đặt Lại Mật Khẩu</h3>
            </div>
            <div class="panel-body">
                <form action="change-password" id="pform" method="POST">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="email" value="{{$email}}">
                    <input type="hidden" name="token" value="{{$token}}">
                    <div class="form-group">
                        <label for="password">Mật khẩu mới</label>
                        <input type="password" class="form-control" autofocus name="new_password" required min="6" id="new_password" placeholder="">
                        <label id="new_password_error" class="error" for="password"></label>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Nhập lại mật khẩu mới</label>
                        <input type="password" class="form-control" id="re_new_password" required name="re_new_password">
                        <label id="re_new_password_error" class="error" for="password"></label>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#pform').submit(function (e) {

            var new_password = $('#new_password').val();
            var re_new_password = $('#re_new_password').val();

            $('#new_password_error').html('');
            $('#re_new_password_error').html('');

            if(new_password.length < 6){
                $('#new_password_error').html('Mật khẩu phải chứa ít nhất 6 kí tự');
                e.preventDefault();
            }

            if(new_password && re_new_password && new_password != re_new_password){
                $('#re_new_password_error').html('Mật khẩu nhập lại không khớp với mật khẩu mới');
                e.preventDefault();
            }
        });
    });
</script>
</body>

</html>