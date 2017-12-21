@extends('layout.master')

<?php
$title = 'Thêm loại thẻ';
$isEdit = false;
?>

@section('content')
    <div class="card-create">
        @include('card._form')
    </div>
@endsection