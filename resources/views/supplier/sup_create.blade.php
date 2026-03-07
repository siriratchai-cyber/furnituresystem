@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/luxury.css') }}">
<link rel="stylesheet" href="{{ asset('css/supplier.css') }}">

<div class="container">
    <div class="card" style="padding:28px;max-width:520px;margin:0 auto;">

        <h2 style="font-size:18px;font-weight:700;color:#5a3e2b;margin-bottom:24px;">
            ➕ เพิ่มซัพพลายเออร์
        </h2>

        @if($errors->any())
            <div style="background:#fdecea;color:#8a3b2f;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:14px;">
                @foreach($errors->all() as $e)
                    <div>❌ {{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('suppliers.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">ชื่อซัพพลายเออร์ <span style="color:red">*</span></label>
                <input type="text" name="suppliername" class="form-input"
                    placeholder="กรอกชื่อ..."
                    value="{{ old('suppliername') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <input type="text" name="tel" class="form-input"
                    placeholder="0XX-XXX-XXXX"
                    value="{{ old('tel') }}">
            </div>

            <div class="form-group">
                <label class="form-label">ที่อยู่</label>
                <input type="text" name="address" class="form-input"
                    placeholder="กรอกที่อยู่..."
                    value="{{ old('address') }}">
            </div>

            <div class="form-group">
                <label class="form-label">ช่องทางการติดต่อ</label>
                <input type="text" name="contactperson" class="form-input"
                    placeholder="ช่องทางการติดต่อ..."
                    value="{{ old('contactperson') }}">
            </div>

            <div class="modal-footer" style="margin-top:24px;">
                <a href="{{ route('suppliers.index') }}" class="btn-cancel">ยกเลิก</a>
                <button type="submit" class="btn-confirm">บันทึก</button>
            </div>
        </form>

    </div>
</div>

@endsection