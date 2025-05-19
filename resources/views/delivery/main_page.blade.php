@extends('layouts.delivery')

@section('title', 'หน้าหลัก')

@section('content')
<?php

use App\Models\Config;

$config = Config::first();
?>
<style>
    .carousel-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 10px;
    }

    .icon-have {

        padding: 5px;
        background: linear-gradient(360deg, var(--primary-color), var(--sub-color));
        object-fit: cover;
        border-radius: 100%;
    }

    .icon-have img {
        width: 50px;
        height: 50px;
    }

    .title-food {
        font-size: 30px;
        font-weight: bold;
        color: <?= $config->color_font != '' ? $config->color_font : '#ffffff' ?>;
    }


    .food-box img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 0.5rem;
    }


    .food-label {
        font-size: 18px;
        color: <?= $config->color_category != '' ? $config->color_category : '#ffffff' ?>;
        font-weight: bold;
        text-align: center;
        word-wrap: break-word;
        overflow-wrap: break-word;
        width: 100%;
        line-height: 0.9;
    }

    .scroll-hidden {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        /* ทำให้เลื่อนลื่นบนมือถือ */
        scrollbar-width: none;
        /* Firefox */
    }

    .scroll-hidden::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari */
    }
</style>
@if (count($promotion) > 0)
<div id="carouselCaptions" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner" style="border-radius: 10px;">
        @foreach ($promotion as $key => $rs)
        <div class="carousel-item <?= $key == 0 ? 'active' : '' ?>">
            <img src="{{ url('storage/' . $rs->image) }}" class="d-block w-100" alt="slide">
        </div>
        @endforeach
    </div>
</div>
@endif
<div class="container mt-1">
    <div class="d-flex flex-column justify-content-center">
        <!-- <div class=" text-start fw-bold fs-5" style="color:<?= $config->color_font != '' ? $config->color_font : '#ffffff' ?>">
                ที่นี่เรามี...
            </div>
            <div class="overflow-x-auto d-flex justify-content-between gap-2 py-2"
                style="overflow-x: auto; white-space: nowrap;">
                <div class="d-flex flex-column justify-content-center align-items-center flex-shrink-0">
                    <div class="icon-have">
                        <img src="{{ asset('icon/icon-4.png') }}" alt="icon">
                    </div>
                    <div class="mt-1 fw-bold" style="font-size: 14px;color:<?= $config->color_font != '' ? $config->color_font : '#ffffff' ?>;">wifi ฟรี</div>
                </div>

                <div class="d-flex flex-column justify-content-center align-items-center flex-shrink-0">
                    <div class="icon-have">
                        <img src="{{ asset('icon/icon-1.png') }}" alt="icon">
                    </div>
                    <div class="mt-1 fw-bold" style="font-size: 14px;color:<?= $config->color_font != '' ? $config->color_font : '#ffffff' ?>;">ที่จอดรถ</div>
                </div>

                <div class="d-flex flex-column justify-content-center align-items-center flex-shrink-0">
                    <div class="icon-have">
                        <img src="{{ asset('icon/icon-2.png') }}" alt="icon" style="padding: 7px;">
                    </div>
                    <div class="mt-1 fw-bold" style="font-size: 14px;color:<?= $config->color_font != '' ? $config->color_font : '#ffffff' ?>;">จ่ายด้วย QR</div>
                </div>

                <div class="d-flex flex-column justify-content-center align-items-center flex-shrink-0">
                    <div class="icon-have">
                        <img src="{{ asset('icon/icon-3.png') }}" alt="icon">
                    </div>
                    <div class="mt-1 fw-bold" style="font-size: 13px;color:<?= $config->color_font != '' ? $config->color_font : '#ffffff' ?>;">ห้องน้ำสะอาด</div>
                </div>
            </div> -->
        <div class="title-food">
            คอร์สเรียน
        </div>
        <div class="row py-2">
            <div class="d-flex flex-nowrap px-2 scroll-hidden" style="gap: 5px;">
                @php
                $chunks = $category->chunk(2);
                @endphp

                @foreach ($chunks as $group)
                <div class="col-6 mb-2 d-flex flex-column justify-content-start">
                    @foreach ($group as $rs)
                    <div class="food-box mb-2">
                        <a href="{{ route('users.detail', $rs->id) }}"
                            class="d-flex flex-column justify-content-center align-items-center text-decoration-none">
                            @if ($rs->files)
                            <img src="{{ url('storage/' . $rs->files->file) }}" alt="icon">
                            @else
                            <img src="{{ asset('foods/default-photo.png') }}" alt="icon">
                            @endif
                            <div class="food-label mt-2">{{ $rs->name }}</div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        <div class="title-food">
            พรีวิว
        </div>
        <div class="row py-2">
            <div class="d-flex flex-nowrap px-2 scroll-hidden" style="gap: 5px;">
                @php
                $chunks = $category_preview->chunk(2);
                @endphp

                @foreach ($chunks as $group)
                <div class="col-6 mb-2 d-flex flex-column justify-content-start">
                    @foreach ($group as $rs)
                    <div class="food-box mb-2">
                        <a href="{{ route('users.preview', $rs->id) }}"
                            class="d-flex flex-column justify-content-center align-items-center text-decoration-none">
                            @if ($rs->files)
                            <img src="{{ url('storage/' . $rs->files->file) }}" alt="icon">
                            @else
                            <img src="{{ asset('foods/default-photo.png') }}" alt="icon">
                            @endif
                            <div class="food-label mt-2">{{ $rs->name }}</div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>


@endsection