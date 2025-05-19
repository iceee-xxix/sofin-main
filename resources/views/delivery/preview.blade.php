@extends('layouts.delivery')

@section('title', 'พรีวิวคอร์สเรียน')

@section('content')
<?php

use App\Models\Config;

$config = Config::first();
?>
<style>
    .title-food {
        font-size: 30px;
        font-weight: bold;
        color: <?= ($config->color_font != '')  ? $config->color_font :  '#ffffff' ?>;
    }

    .card-food {
        background-color: var(--bg-card-food);
        border-radius: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        padding: 4px;
    }


    .card-food img {
        width: 120px;
        height: 90px;
        border-radius: 20px;
    }

    .card-title {
        font-size: 21px;
        font-weight: bold;
    }

    .btn-gray-left {
        background-color: #d3d3d3;
        color: #333;
        border: none;
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
        padding: 0px 14px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .btn-gray-right {
        background-color: #d3d3d3;
        color: #333;
        border: none;
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
        padding: 0px 14px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .btn-gray-left:hover {
        background-color: #c0c0c0;
        transform: scale(1.05);
    }

    .btn-gray-right:hover {
        background-color: #c0c0c0;
        transform: scale(1.05);
    }

    .count {
        background-color: #e0e0e0;
        padding: 1.5px 0px;
    }

    .card-custom {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-custom:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .card-custom img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .card-custom .card-body {
        padding: 1rem;
        background-color: #ffffff;
    }

    .card-custom .card-title {
        font-size: 20px;
        font-weight: 600;
        color: #333333;
        margin-bottom: 0.75rem;
        text-align: center;
    }

    .btn-view {
        display: block;
        width: 100%;
        background-color: #007bff;
        color: white;
        font-weight: bold;
        border: none;
        padding: 8px 0;
        border-radius: 10px;
        transition: background-color 0.3s ease;
        text-align: center;
        text-decoration: none;
    }

    .btn-view:hover {
        background-color: #0056b3;
    }
</style>

<div class="title-food mb-3">
    รายการพรีวิว
</div>

<div class="row g-3">
    @foreach($preview as $rs)
    <div class="col-md-6">
        <div class="card card-custom">
            <img src="{{ $rs->image ? url('storage/'.$rs->image) : asset('foods/default-photo.png') }}" alt="{{ $rs->name }}">
            <div class="card-body">
                <div class="card-title">{{ $rs->name }}</div>
                <a href="{{route('users.preview_detail',$rs->id)}}" class="btn-view">รายละเอียด</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection