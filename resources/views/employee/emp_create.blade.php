<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มพนักงาน</title>
    <link rel="stylesheet" href="{{ asset('css/employee.css') }}">
</head>
<body>
<div class="container">
    <div class="header">
        <button class="back-btn" onclick="window.history.back()">&#8249;</button>
        <h1>เพิ่มพนักงาน</h1>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('employees.store') }}" method="POST" class="form-card">
        @csrf

        <div class="form-group">
            <label class="form-label">ชื่อ-นามสกุล <span class="required">*</span></label>
            <input type="text" class="form-input" name="empname"
                   value="{{ old('empname') }}" placeholder="ชื่อ นามสกุล" required>
        </div>

        <div class="form-group">
            <label class="form-label">ตำแหน่ง</label>
            <input type="text" class="form-input" name="position"
                   value="{{ old('position') }}" placeholder="เช่น พนักงาน">
        </div>

        <div class="form-group">
            <label class="form-label">เบอร์โทร</label>
            <input type="tel" class="form-input" name="tel"
                   value="{{ old('tel') }}" placeholder="xxx-xxx-xxxx">
        </div>

        <div class="form-actions">
            <button type="submit" class="save-btn">บันทึก</button>
            <a href="{{ route('employees.index') }}" class="cancel-btn">ยกเลิก</a>
        </div>
    </form>
</div>
</body>
</html>