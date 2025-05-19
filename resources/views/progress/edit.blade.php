@extends('admin.layout')
@section('style')
@endsection
@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 col-md-12 order-1">
                <div class="row d-flex justify-content-center">
                    <div class="col-8">
                        <form action="{{route('ProgressSave')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    รายงานผลการพัฒนา
                                    <hr>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-12">
                                            <label for="link_url" class="form-label">แนบลิงค์ (สำหรับเพิ่มรูปภาพหรือวิดีโอ) : </label>
                                            <input type="text" class="form-control" id="link_url" name="link_url" value="{{ old('link_url', $info->link_url) }}">
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-12">
                                            <label for="remark" class="form-label">รายละเอียด : </label>
                                            <textarea class="form-control" rows="6" name="remark" id="remark" required>{{ old('remark', $info->remark) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-12">
                                            <label for="date" class="form-label">วันที่ : </label>
                                            <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $info->date) }}" required>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-12">
                                            <label for="file" class="form-label">ไฟล์แนบ : </label>
                                            <input type="file" class="form-control" id="file" name="file">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-end">
                                    <button type="submit" class="btn btn-outline-primary">บันทึก</button>
                                </div>
                            </div>
                            <input type="hidden" name="id" value="{{ old('id', $info->id) }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection