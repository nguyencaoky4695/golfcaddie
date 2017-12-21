<!DOCTYPE html>
<html>
@include('backend.partial.head')
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    @include('backend.partial.header')

    @include('backend.partial.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Dashboard
                <small>Control panel</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Dashboard</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            @hasSection('content')
                @yield('content')
            @else
                Có lỗi trong quá trình đọc nội dung...
            @endif
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 1.1.0
        </div>
        <strong>Copyright &copy; 2017-2018 <a href="https://hadesker.net">Hadesker HD</a>.</strong> All rights
        reserved.
    </footer>
</div>
<!-- ./wrapper -->

@include('backend.partial.js')

</body>
</html>
