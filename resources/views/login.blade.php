<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>เข้าสู่ระบบ | Furniture POS</title>

<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Prompt',sans-serif;
}

body{
    min-height:100vh;
    background:linear-gradient(135deg,#f8f5f0,#e8dfd2);
    display:flex;
    align-items:center;
    justify-content:center;
}

.container{
    width:100%;
    max-width:420px;
    padding:20px;
}

.card{
    background:#ffffffcc;
    backdrop-filter:blur(10px);
    border-radius:20px;
    padding:40px;
    box-shadow:0 20px 40px rgba(0,0,0,0.08);
    border:1px solid #f0ebe5;
    animation:fadeIn .6s ease;
}

@keyframes fadeIn{
    from{opacity:0;transform:translateY(10px);}
    to{opacity:1;transform:translateY(0);}
}

.logo{
    text-align:center;
    font-size:22px;
    font-weight:600;
    color:#5a4a3f;
    margin-bottom:10px;
}

.subtitle{
    text-align:center;
    font-size:14px;
    color:#8c7b6a;
    margin-bottom:30px;
}

.input-group{
    margin-bottom:18px;
}

label{
    font-size:13px;
    color:#6b5b4f;
    display:block;
    margin-bottom:6px;
}

input{
    width:100%;
    padding:12px 14px;
    border-radius:12px;
    border:1px solid #e5ddd5;
    font-size:14px;
    background:#faf8f5;
    transition:.2s;
}

input:focus{
    outline:none;
    border-color:#a67c52;
    box-shadow:0 0 0 3px rgba(166,124,82,.15);
    background:#fff;
}

button{
    width:100%;
    padding:12px;
    border-radius:14px;
    border:none;
    background:#7b5e3c;
    color:#fff;
    font-size:15px;
    font-weight:500;
    cursor:pointer;
    transition:.2s;
}

button:hover{
    background:#6b4f32;
}

.error{
    background:#fdecea;
    color:#8a3b2f;
    padding:10px 14px;
    border-radius:12px;
    font-size:13px;
    margin-bottom:15px;
}

.footer{
    margin-top:20px;
    text-align:center;
    font-size:12px;
    color:#a08f80;
}
</style>
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

<a href="{{ route('password.forgot') }}">ลืมรหัสผ่าน?</a>

</form>

<div class="footer">
    © {{ date('Y') }} Furniture Store System
</div>

</div>
</div>

</body>
</html>
