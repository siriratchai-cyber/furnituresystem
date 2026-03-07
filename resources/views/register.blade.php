<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ลงทะเบียน | Furniture POS</title>

<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>

<body>

<div class="container">
<div class="card">

<div class="logo">Furniture POS</div>
<div class="subtitle">ตั้งรหัสผ่านสำหรับพนักงานใหม่</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="error">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

{{-- Session Error --}}
@if(session('error'))
    <div class="error">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ url('/register') }}">
@csrf

<div class="input-group">
    <label>ชื่อพนักงาน</label>
    <input type="text" name="empname" value="{{ old('empname') }}" required>
</div>

<div class="input-group">
    <label>เบอร์โทรศัพท์</label>
    <input type="text" name="tel" value="{{ old('tel') }}" required>
</div>

<div class="input-group">
    <label>สร้างรหัสผ่าน</label>
    <input type="password" name="password" required>
</div>

<div class="input-group">
    <label>ยืนยันรหัสผ่าน</label>
    <input type="password" name="password_confirmation" required>
</div>

<button type="submit">
    ลงทะเบียน
</button>

<div class="link-group">
    <a href="{{ route('login') }}">กลับหน้า Login</a>
</div>

</form>

<div class="footer">
    © {{ date('Y') }} Furniture Store System
</div>

</div>
</div>

</body>
</html>
