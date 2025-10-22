@extends('admin.layouts.admin') 
@section('title','Thêm phòng')

@section('content')
<div class="container">
  <h2>Thêm phòng</h2>
  
  {{-- Flash Messages --}}
  @if(session('error'))
    @push('scripts')
    <script>window.addEventListener('DOMContentLoaded',()=>{(window.showToast||alert)(@json(strip_tags(session('error'))),'error')});</script>
    @endpush
    <noscript>
      <div class="alert alert-danger">{{ session('error') }}</div>
    </noscript>
  @endif

  @if(session('status'))
    @push('scripts')
    <script>window.addEventListener('DOMContentLoaded',()=>{(window.showToast||alert)(@json(session('status')),'success')});</script>
    @endpush
    <noscript>
      <div class="alert alert-success">{{ session('status') }}</div>
    </noscript>
  @endif

  <form action="{{ route('phong.store') }}" method="POST" enctype="multipart/form-data">
    @include('phong._form')
  </form>
</div>
@endsection
