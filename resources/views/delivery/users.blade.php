@extends('layouts.delivery')

@section('title', 'หน้ารายละเอียด')

@section('content')
<?php

use App\Models\Config;

$config = Config::first();
?>
<style>
    .title-buy {
        font-size: 30px;
        font-weight: bold;
        color: <?= $config->color_font != '' ? $config->color_font : '#ffffff' ?>;
    }

    .title-list-buy {
        font-size: 25px;
        font-weight: bold;
    }

    .btn-plus {
        background: none;
        border: none;
        color: rgb(0, 156, 0);
        cursor: pointer;
        padding: 0;
        font-size: 12px;
        text-decoration: none;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .btn-plus:hover {
        color: rgb(185, 185, 185);
    }

    .btn-delete {
        background: none;
        border: none;
        color: rgb(192, 0, 0);
        cursor: pointer;
        padding: 0;
        font-size: 12px;
        text-decoration: none;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .btn-delete:hover {
        color: rgb(185, 185, 185);
    }

    .btn-aprove {
        background: linear-gradient(360deg, var(--primary-color), var(--sub-color));
        border-radius: 20px;
        border: 0px solid #0d9700;
        padding: 5px 0px;
        font-weight: bold;
        text-decoration: none;
        color: rgb(255, 255, 255);
        transition: background 0.3s ease;
    }

    .btn-aprove:hover {
        background: linear-gradient(360deg, var(--sub-color), var(--primary-color));
        cursor: pointer;
    }
</style>
<div class="container">
    <div class="d-flex flex-column justify-content-center gap-2">
        <div class="card">
            <div class="card-header">ข้อมูลส่วนตัว</div>
            <div class="card-body">
                <form action="{{route('users.usersSave')}}" method="post">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label for="name" class="form-label d-flex justify-content-start">ชื่อ : </label>
                            <input type="text" class="form-control" id="name" name="name" value="{{Session::get('user')->name}}" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label for="email" class="form-label d-flex justify-content-start">อีเมล : </label>
                            <input type="text" class="form-control" id="email" name="email" value="{{Session::get('user')->email}}" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label for="tel" class="form-label d-flex justify-content-start">เบอร์โทรศัพท์ : </label>
                            <input type="text" class="form-control" id="tel" name="tel" value="{{Session::get('user')->tel}}" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-outline-primary" type="submit">บันทึก</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <a href="{{route('admin.logout')}}" class="btn btn-sm btn-danger" type="button">ออกจากระบบ</a>
    </div>
</div>
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if ($message = Session::get('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '{{ $message }}',
    })
</script>
@endif
@if($message = Session::get('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '{{ $message }}',
    })
</script>
@endif
@endsection