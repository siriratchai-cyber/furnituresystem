<!DOCTYPE html>
<html>
<head>
    <title>รีเซ็ตรหัสผ่าน</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ asset('css/forgot.css') }}">
</head>
<body>

<div class="card">

    <div class="title">🔐 รีเซ็ตรหัสผ่าน</div>
    <div class="subtitle">Furniture POS System</div>

    <form method="POST" action="{{ route('password.reset') }}" id="resetForm">
        @csrf

        <div class="input-group">
            <label>ชื่อพนักงาน</label>
            <input type="text" name="empname" value="{{ old('empname') }}" required>
            @error('empname')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="input-group">
            <label>เบอร์โทร</label>
            <input type="text" name="tel" id="tel" value="{{ old('tel') }}" required>
            <div class="error-text" id="telError"></div>
            @error('tel')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="input-group">
            <label>รหัสผ่านใหม่</label>
            <input type="password" name="password" id="password" required>
            <div class="error-text" id="passError"></div>
            @error('password')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="input-group">
            <label>ยืนยันรหัสผ่าน</label>
            <input type="password" name="password_confirmation" id="confirmPassword" required>
            <div class="error-text" id="matchError"></div>
        </div>

        <button type="submit" class="btn" id="submitBtn">
            รีเซ็ตรหัสผ่าน
        </button>
    </form>

    <div class="back">
        <a href="{{ route('login') }}">← กลับหน้า Login</a>
    </div>

</div>

<script>

/* ===============================
   Validation Frontend
================================ */

const form = document.getElementById("resetForm");
const telInput = document.getElementById("tel");
const passInput = document.getElementById("password");
const confirmInput = document.getElementById("confirmPassword");
const submitBtn = document.getElementById("submitBtn");

form.addEventListener("submit", function(e){

    let valid = true;

    // เบอร์โทรต้องเป็นตัวเลข 8-12 ตัว
    if(!/^[0-9]{8,12}$/.test(telInput.value)){
        document.getElementById("telError").innerText = "เบอร์โทรไม่ถูกต้อง";
        valid = false;
    }else{
        document.getElementById("telError").innerText = "";
    }

    // password ขั้นต่ำ 6 ตัว
    if(passInput.value.length < 6){
        document.getElementById("passError").innerText = "รหัสผ่านต้องอย่างน้อย 6 ตัวอักษร";
        valid = false;
    }else{
        document.getElementById("passError").innerText = "";
    }

    // ตรวจ match
    if(passInput.value !== confirmInput.value){
        document.getElementById("matchError").innerText = "รหัสผ่านไม่ตรงกัน";
        valid = false;
    }else{
        document.getElementById("matchError").innerText = "";
    }

    if(!valid){
        e.preventDefault();
    }else{
        submitBtn.disabled = true;
        submitBtn.innerText = "กำลังรีเซ็ต...";
    }
});


/* ===============================
   SweetAlert จาก Backend
================================ */

@if(session('error'))
Swal.fire({
    icon:"error",
    title:"ไม่สำเร็จ",
    text:"{{ session('error') }}"
});
@endif

@if(session('success'))
Swal.fire({
    icon:"success",
    title:"สำเร็จ",
    text:"{{ session('success') }}",
    timer:2000,
    showConfirmButton:false
}).then(()=>{
    window.location="{{ route('login') }}";
});
@endif

</script>

</body>
</html>
