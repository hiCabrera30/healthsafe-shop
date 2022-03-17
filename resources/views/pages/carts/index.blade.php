@extends('pages.products.layout')

@section('page_title', __("pages.title", ["title" => "Products"]))
@section('header')
    <h1 class="text-white">Products</h1>
@endsection

@section('content')
    <div class="container mt-5">
        
        <h4 class="text-muted mb-4">My cart</h4>
        <cart-list currency-sign="{{ $currencySign }}"></cart-list>
    </div>
@endsection
