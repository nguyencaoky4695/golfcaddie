@extends('backend.layouts.master')

<?php
$title = 'Thêm người dùng mới';
$isEdit = false;
?>

@section('content')
    <div class="user-create">
        @include('backend.user._form')
    </div>
@endsection