@extends('layout.master')

<?php
$title = 'Cập nhật loại thẻ thành viên';
$isEdit = true;
?>

@section('content')
    <div class="card-edit">
        @include('card._form')
    </div>
@endsection