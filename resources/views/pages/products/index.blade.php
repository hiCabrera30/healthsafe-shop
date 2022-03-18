@extends('pages.products.layout')

@section('page_title', __("pages.title", ["title" => "Products"]))
@section('header')
    <h1 class="text-white">Products</h1>
@endsection

@section('content')
<div class="container mt-5">
    <div class="row">
        @include('pages.products.includes.records-table', [
            "showCreateForm" => true
        ])
    </div>            
</div>

@endsection
