@extends('admin.layouts.admin')
@section('title','Thêm khu')

@section('content')
<div class="container">
  <h2>Thêm khu</h2>
  <form action="{{ route('khu.store') }}" method="POST">
    @include('khu._form')
  </form>
</div>
@endsection



