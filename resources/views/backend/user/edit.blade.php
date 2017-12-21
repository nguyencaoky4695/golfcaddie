@extends('backend.layouts.master')

<?php
$title = 'Cập nhật người dùng';
$isEdit = true;
?>

@section('content')
    <div class="user-edit">
        @include('backend.user._form')
    </div>
@endsection