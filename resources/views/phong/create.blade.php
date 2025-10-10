@extends('admin.layouts.admin') 
@section('title','Thêm phòng')

@section('content')
<div class="container">
  <h2>Thêm phòng</h2>
  <form action="{{ route('phong.store') }}" method="POST">
    @include('phong._form')
  </form>
</div>
@endsection
