@extends('layouts/master')
@include('helpers.meta')

@section('css')
  <link rel="stylesheet" href="{{ asset('assets/inventory/inventory.css') }}">
@endsection

@section('header')
<!-- Banner Area Start -->
<div class="banner-area pb-90 pt-160 bg-2">
  <div class="container">
    <div class="row">
      <div class="banner-content text-center text-white">
        <h1>{{ $page->name }}</h1>
        <ul>
          <li><a href="{{ url('') }}">inicio</a> <span class="arrow_carrot-right "></span></li>
          <li>{{ $page->name }}</li>
        </ul> 
      </div>
    </div>
  </div>
</div>
<!-- Banner Area End -->
@endsection

@section('content')
<!-- checkout-area start -->
<div class="container solunes-inventory">
  
  @include('inventory::includes.sale')
  
</div>
<!-- checkout-area end -->  
@endsection

@section('script')
@endsection