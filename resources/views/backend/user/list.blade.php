@extends('backend.layouts.master')

<?php
$title = 'Người dùng';
?>

@section('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css">
@endsection

@section('js')
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script>
        var table = $('#datatable').DataTable({
            "aoColumnDefs": [
                { 'bSortable': false, 'aTargets': [ 6 ] }
            ]
        });
    </script>
    <script>
        function xoanguoidung(id, hoten) {
            $.confirm({
                title: 'Xác nhận xóa',
                animation: 'scaleY',
                closeAnimation: 'scaleX',
                content: "Bạn có chắc muốn xóa <b>" + hoten + "</b> khỏi danh sách người dùng ?",
                buttons: {
                    danger: {
                        btnClass: 'btn-danger',
                        text:'<i class="glyphicon glyphicon-trash"></i> XÓA',
                        action: function(){
                            $('.se-pre-con').css('display', 'block');
                            $.ajax({
                                dataType: "json",
                                url: 'admin/user/' + id,
                                type: 'DELETE',
                                success: function (re) {
                                    if (re.status == 1) {
                                        var tableRow = $('#id' + id);
                                        table.row(tableRow).remove();
                                        tableRow.find('td').fadeOut(1000);
                                        $.alert(re.stt);
                                    }
                                    else {
                                        $.alert(re.stt);
                                    }
                                },
                                error: function (e) {
                                    $.alert('Có lỗi xảy ra trong quá trình xóa, lỗi: ' + e.statusText, 'danger');
                                },
                                complete:function () {
                                    //$('.se-pre-con').css('display', 'none');
                                }
                            });
                        }
                    },
                    default: {
                        btnClass: 'btn-default',
                        text:'ĐÓNG'
                    },
                }
            });
        }
    </script>
@endsection

@section('content')
    <div class="box post-list">
        <div class="box-header">
            <div class="row">
                <div class="col-md-12">
                    <a href="{{route('backend.user.create')}}" class="btn btn-success pull-right"><i class="fa fa-plus-circle"></i> Thêm người dùng</a>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div >
                <table id="datatable" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th style="text-align: center; vertical-align: middle;">#</th>
                        <th style="text-align: left; vertical-align: middle;">Username</th>
                        <th style="text-align: left; vertical-align: middle;">Họ tên</th>
                        <th style="text-align: left; vertical-align: middle;">Email</th>
                        <th style="text-align: center; vertical-align: middle;">Chức vụ</th>
                        <th style="text-align: center; vertical-align: middle;">Trạng thái</th>
                        <th style="text-align: center; vertical-align: middle;">...</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $key=>$value)
                        <tr id="id{{$value->id}}">
                            <td style="text-align: center; vertical-align: middle;">
                                {{($key+1)}}
                            </td>
                            <td style="text-align: left; vertical-align: middle;">
                                {{$value->username}}
                            </td>
                            <td style="text-align: left; vertical-align: middle;">
                                {{$value->fullname}}
                            </td>
                            <td style="text-align: left; vertical-align: middle;">
                                {{$value->email}}
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                               {!! $value->role == 'admin' ? '<b>Quản trị</b>' : 'Nhân viên' !!}
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                @if($value->status)
                                    <span class="label label-success">Hoạt động</span>
                                @else
                                    <span class="label label-danger">Bị khóa</span>
                                @endif
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                @if($value->role != 'admin')
                                <div class="btn-group" role="group">
                                    <a type="button" href="admin/user/{{$value->id}}/edit"
                                       class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>
                                    <a type="button" onclick="xoanguoidung('{{$value->id}}','{{$value->fullname}}')"
                                       class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>
                                    </a>
                                </div>
                                @else
                                    <i class="fa fa-ban"></i>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th style="text-align: center; vertical-align: middle;">#</th>
                        <th style="text-align: left; vertical-align: middle;">Username</th>
                        <th style="text-align: left; vertical-align: middle;">Họ tên</th>
                        <th style="text-align: left; vertical-align: middle;">Email</th>
                        <th style="text-align: center; vertical-align: middle;">Chức vụ</th>
                        <th style="text-align: center; vertical-align: middle;">Trạng thái</th>
                        <th style="text-align: center; vertical-align: middle;">...</th>
                    </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
@endsection