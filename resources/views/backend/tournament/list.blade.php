@extends('backend.layout.master')

<?php
$title = 'Danh sách giải đấu';
?>

@section('css')
@endsection

@section('js')
    <script>
        var table = $('#datatable').DataTable({
            "aoColumnDefs": [
                {'bSortable': false, 'aTargets': [3]}
            ]
        });
    </script>
@endsection

@section('content')
    <div class="box post-list">
        <div class="box-header">
            <a href="{{route('card.create')}}" class="btn btn-success pull-right"><i class="fa fa-plus-circle"></i>
                Thêm loại thẻ</a>
        </div>
        <div class="box-body">
            <table id="datatable" class="collaptable table table-bordered table-hover">
                <thead>
                <tr>
                    <th style="text-align: left; vertical-align: middle;">Loại thẻ</th>
                    <th style="text-align: center; vertical-align: middle;">Điểm tối thiểu</th>
                    <th style="text-align: center; vertical-align: middle;">Trạng thái thẻ</th>
                    <th style="text-align: center; vertical-align: middle;">...</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tournament as $key=>$value)
                    <tr>
                        <td style="text-align: left; vertical-align: middle;">
                            {{$value->name}}
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            {{$value->point}}
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            {!! getStatus($value->status,'Đang mở','Đã khóa') !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <div class="btn-group btn-group-{{$value->id}}">
                                <a type="button" href="card/{{$value->id}}/edit" title="Cập nhật"
                                   data-toggle="tooltip" data-placement="top"
                                   class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>
                                <button type="button"
                                        onclick="deleteObj('.btn-group-{{$value->id}}','{{asset("card/".$value->id)}}','{{$value->name}}')"
                                        title="Xóa" data-toggle="tooltip" data-placement="top"
                                        class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th style="text-align: left; vertical-align: middle;">Loại thẻ</th>
                    <th style="text-align: center; vertical-align: middle;">Điểm tối thiểu</th>
                    <th style="text-align: center; vertical-align: middle;">Trạng thái thẻ</th>
                    <th style="text-align: center; vertical-align: middle;">...</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection