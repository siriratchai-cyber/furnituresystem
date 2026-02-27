@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/luxury.css') }}">
<link rel="stylesheet" href="{{ asset('css/supplier.css') }}">

<div class="container">
    <div class="card" style="padding:28px;max-width:520px;margin:0 auto;">

        <h2 style="font-size:18px;font-weight:700;color:#5a3e2b;margin-bottom:24px;">
            ✏️ แก้ไขซัพพลายเออร์
        </h2>

        @if($errors->any())
            <div style="background:#fdecea;color:#8a3b2f;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:14px;">
                @foreach($errors->all() as $e)
                    <div>❌ {{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('suppliers.update', $supplier->supplierid) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">รหัสซัพพลายเออร์</label>
                <input type="text" class="form-input"
                    value="{{ $supplier->supplierid }}"
                    disabled
                    style="background:#f3ede7;color:#8c7b6a;">
            </div>

            <div class="form-group">
                <label class="form-label">ชื่อซัพพลายเออร์ <span style="color:red">*</span></label>
                <input type="text" name="suppliername" class="form-input"
                    value="{{ old('suppliername', $supplier->suppliername) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <input type="text" name="tel" class="form-input"
                    value="{{ old('tel', $supplier->tel) }}">
            </div>

            <div class="form-group">
                <label class="form-label">ที่อยู่</label>
                <input type="text" name="address" class="form-input"
                    value="{{ old('address', $supplier->address) }}">
            </div>

            <div class="form-group">
                <label class="form-label">ผู้ติดต่อ</label>
                <input type="text" name="contactperson" class="form-input"
                    value="{{ old('contactperson', $supplier->contactperson) }}">
            </div>

            <div class="modal-footer" style="margin-top:24px;">
                <a href="{{ route('suppliers.index') }}" class="btn-cancel">ยกเลิก</a>
                <button type="submit" class="btn-confirm">บันทึกการแก้ไข</button>
            </div>
        </form>

    </div>
</div>

@endsection