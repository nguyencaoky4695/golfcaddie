@extends('backend.layout.master')

<?php
$title = 'Thông tin cá nhân';
$isEdit = true;
$profile = true;
?>

@section('content')
    <div class="user-profile">
        @include('backend.user._form')
    </div>
@endsection