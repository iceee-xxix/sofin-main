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

    svg {
        width: 100%;
    }
</style>
<div class="container my-4">
    <div class="d-flex flex-column align-items-center">
        <h2 class="mb-4">ชำระเงิน</h2>
        <div class="card w-100 mb-3">
            <div class="card-header bg-primary text-white">
                <strong>แพ็กเกจของคุณ</strong>
            </div>
            <div class="card-body">
                @if($orderlist)
                <div class="list-group">
                    <div class="list-group-item list-group-item-action mb-3 p-4 rounded border bg-light">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6">
                                    <span class="mb-3 fw-bold">เลขออเดอร์ #{{$orderlist->id}}</span>
                                </div>
                                <div class="col-6 d-flex justify-content-end">
                                    <button data-id="{{$orderlist->id}}" type="button" class="btn btn-sm btn-success modalShow">
                                        ดูรายละเอียด
                                    </button>
                                </div>
                                <div class="col-12 mt-3 d-flex justify-content-end">
                                    <small class="text-muted">ราคา: {{ number_format($orderlist->total ?? 0, 2) }} บาท</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(!$orderlist)
                <div class="text-center text-muted my-4">
                    ไม่มีรายการแพ็กเกจที่สั่ง
                </div>
                @endif
            </div>
        </div>
        <div class="card w-100">
            <div class="card-header bg-primary text-white">
                ข้อมูลชำระเงิน
            </div>
            <div class="card-body">
                <form action="{{route('users.paySave')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if($config->promptpay != '')
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <?= $qr_code ?>
                        </div>
                    </div>
                    @elseif($config->image_qr != '')
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <?= $qr_code ?>
                        </div>
                    </div>
                    @endif
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label for="silp" class="form-label d-flex justify-content-start">แนบสลิป : </label>
                            <input type="file" class="form-control" id="silp" name="silp" required accept="image/jpeg, image/png">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-outline-primary" type="submit">บันทึก</button>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id" value="{{$id}}">
                </form>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).on('click', '.modalShow', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            type: "post",
            url: "{{ route('users.listOrderDetail') }}",
            data: {
                id: id
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#modal-detail').modal('show');
                $('#body-html').html(response);
            }
        });
    });
</script>
<div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" id="modal-detail">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายละเอียดออเดอร์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="body-html">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
@endsection