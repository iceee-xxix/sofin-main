@extends('layouts.delivery')

@section('title', 'หน้ารายละเอียด')

@section('content')
<?php

use App\Models\Config;

$config = Config::first();

function DateTimeThai($strDate)
{
    $strYear = date("Y", strtotime($strDate)) + 543;
    $strMonth = date("n", strtotime($strDate));
    $strDay = date("j", strtotime($strDate));
    $strMonthCut = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
    $strMonthThai = $strMonthCut[$strMonth];
    return "$strDay $strMonthThai $strYear";
}
?>
<style>
    /* Base Styles */
    .title-buy {
        font-size: 28px;
        font-weight: bold;
        color: <?= $config->color_font != '' ? $config->color_font : '#ffffff' ?>;
        text-align: center;
        margin-bottom: 30px;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        background-color: #fff;
    }

    .card-header {
        background-color: #007bff;
        color: white;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        padding: 15px;
        font-weight: bold;
        font-size: 18px;
    }

    .list-group-item {
        padding: 15px;
        border-radius: 12px;
        background-color: #f8f9fa;
        border: none;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        background-color: #e9ecef;
        transform: translateY(-5px);
    }

    .btn-success,
    .btn-secondary,
    .btn-link {
        font-size: 14px;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    .btn-link {
        color: #007bff;
        text-decoration: none;
        font-weight: bold;
    }

    .btn-link:hover {
        text-decoration: underline;
    }

    .modal-header {
        background-color: #007bff;
        color: white;
    }

    .modal-footer {
        border-top: 1px solid #ddd;
    }

    .modal-body {
        font-size: 16px;
    }

    .text-muted {
        font-size: 14px;
        font-style: italic;
    }

    .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .col-md-8 {
        text-align: start;
    }

    .col-md-4 {
        text-align: end;
    }

    /* Responsive Layout */
    @media (max-width: 576px) {
        .title-buy {
            font-size: 24px;
        }

        .list-group-item {
            padding: 12px;
        }

        .btn-success,
        .btn-secondary {
            font-size: 12px;
            padding: 6px 12px;
        }
    }
</style>

<div class="container my-4">
    <div class="d-flex flex-column align-items-center">
        <h2 class="title-buy">รายงานผล</h2>

        <div class="card w-100">
            <div class="card-header">
                <strong>รายการทั้งหมด</strong>
            </div>
            <div class="card-body">
                @foreach($list as $rs)
                <div class="list-group">
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-md-8">
                                <span class="fw-bold">รายละเอียด:</span> {{$rs->remark}}
                            </div>
                            <div class="col-6 text-end">
                                <a href="{{$rs->link_url}}" target="_blank" class="btn btn-link">
                                    <i class="fas fa-link"></i> เปิดลิงค์
                                </a>
                            </div>
                            @if($rs['files'])
                            <div class="col-6 text-start">
                                <a href="{{url('storage/'.$rs['files']->file)}}" target="_blank" class="btn btn-link">
                                    <i class="fas fa-paperclip"></i> เปิดไฟล์แนบ
                                </a>
                            </div>
                        </div>
                        @endif
                        <div class="col-12 mt-2">
                            <small class="text-muted">วันที่ {{DateTimeThai($rs->created_at)}}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @if($list->isEmpty())
            <div class=" text-center text-muted my-4">
                                    ไม่มีการรายงานผล
                            </div>
                            @endif
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
            @endsection