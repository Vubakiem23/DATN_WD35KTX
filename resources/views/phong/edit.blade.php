@extends('admin.layouts.admin') 

@section('title','Sửa phòng')

@section('content')
<div class="container">
  <h3>Sửa phòng: {{ $phong->ten_phong }}</h3>
  <form action="{{ route('phong.update', $phong) }}" method="POST">
    @csrf
    @method('PUT')
    @include('phong._form')
  </form>
</div>
@endsection
