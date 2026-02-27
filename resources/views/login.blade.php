<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>เข้าสู่ระบบ | Furniture POS</title>

<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

<div class="container">
<div class="card">

<div class="logo">Furniture POS</div>
<div class="subtitle">ระบบจัดการร้านเฟอร์นิเจอร์</div>

@if(session('error'))
    <div class="error">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('login.process') }}">
@csrf

<div class="input-group">
    <label>ชื่อพนักงาน</label>
    <input type="text" name="empname" required autofocus>
</div>

<div class="input-group">
    <label>รหัสผ่าน</label>
    <input type="password" name="password" required>
</div>

<button type="submit">
    เข้าสู่ระบบ
</button>

<div class="link-group">
    <a href="{{ route('password.forgot') }}">ลืมรหัสผ่าน?</a>
    <a href="{{ route('register') }}">ลงทะเบียน</a>
</div>


<div class="footer">
    © {{ date('Y') }} Furniture Store System
</div>

</div>
</div>

</body>
</html>
