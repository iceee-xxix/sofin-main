@extends('layouts.delivery')

@section('title', 'พรีวิวแพ็กเกจ')

@section('content')
<?php

use App\Models\Config;

$config = Config::first();
?>
<style>
    .course-detail-card {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        border-radius: 5px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .course-image {
        width: 100%;
        object-fit: cover;
    }

    .course-body {
        padding: 2rem;
    }

    .course-title {
        font-size: 28px;
        font-weight: bold;
        color: #333;
        margin-bottom: 1rem;
    }

    .course-description {
        font-size: 16px;
        color: #555;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }

    .course-info {
        font-size: 14px;
        color: #777;
        margin-bottom: 1rem;
    }

    .btn-enroll {
        background-color: #28a745;
        color: white;
        font-weight: bold;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 16px;
        transition: background-color 0.3s ease;
        text-decoration: none;
    }

    .btn-enroll:hover {
        background-color: #218838;
    }

    .course-info ul li a {
        font-weight: 500;
        font-size: 15px;
    }
</style>
<div class="course-detail-card mt-4 mb-5">
    <img src="{{ $preview->image ? url('storage/' . $preview->image) : asset('foods/default-photo.png') }}" class="course-image" alt="{{ $preview->name }}">

    <div class="course-body">
        <div class="course-title">{{ $preview->name }}</div>
        <div class="course-description">
            <?= $preview->detail ?>
        </div>
        @if($preview->link_attachments)
        <div class="course-info" style="text-align: left;">
            <strong>ลิงค์ไฟล์แนบ : </strong>
            <a href="{{$preview->link_attachments}}" target="_blank" class="btn btn-sm btn-outline-info">
                click
            </a>
        </div>
        @endif
        @if($preview['files'])
        <div class="course-info" style="text-align: left;">
            <strong>ไฟล์แนบ:</strong>
            @foreach($preview['files'] as $file)
            <a href="{{ url('storage/' . $file->file) }}" target="_blank" class="btn btn-sm btn-outline-info">
                ดาวน์โหลดไฟล์แนบ
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection