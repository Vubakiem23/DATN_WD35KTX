@extends('admin.layouts.admin')
@section('title','Sửa khu')

@section('content')
<div class="container">
  <h2>Sửa khu</h2>
  <form action="{{ route('khu.update', $khu) }}" method="POST">
    @method('PUT')
    @include('khu._form', ['khu' => $khu])
  </form>
</div>
@endsection



