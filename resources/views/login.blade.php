<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>เข้าสู่ระบบ</h2>

<form method="POST" action="{{ route('login.process') }}">
    @csrf

    <label>ชื่อพนักงาน</label><br>
    <input type="text" name="empname" required><br><br>

    <label>รหัสผ่าน</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">เข้าสู่ระบบ</button>
</form>

</body>
</html>
