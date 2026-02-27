<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขพนักงาน</title>
    <link rel="stylesheet" href="{{ asset('css/employee.css') }}">
</head>
<body>
<div class="container">
    <div class="header">
        <button class="back-btn" onclick="window.history.back()">&#8249;</button>
        <h1>แก้ไขข้อมูลพนักงาน</h1>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('employees.update', $employee->employeeid) }}" method="POST" class="form-card">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">ID พนักงาน</label>
            {{-- ไม่ให้แก้ไข employeeid --}}
            <input type="text" class="form-input" value="{{ $employee->employeeid }}" disabled>
        </div>

        <div class="form-group">
            <label class="form-label">ชื่อ-นามสกุล <span class="required">*</span></label>
            <input type="text" class="form-input" name="empname"
                   value="{{ old('empname', $employee->empname) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">ตำแหน่ง</label>
            <input type="text" class="form-input" name="position"
                   value="{{ old('position', $employee->position) }}">
        </div>

        <div class="form-group">
            <label class="form-label">เบอร์โทร</label>
            <input type="tel" class="form-input" name="tel"
                   value="{{ old('tel', $employee->tel) }}">
        </div>

        <div class="form-actions">
            <button type="submit" class="save-btn">บันทึกการแก้ไข</button>
            <a href="{{ route('employees.index') }}" class="cancel-btn">ยกเลิก</a>
        </div>
    </form>
</div>
</body>
</html>