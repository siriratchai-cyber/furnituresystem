@extends('layouts.app')

@section('content')

<style>

/* ===== Profile Card ===== */
.profile-card {
    width: 100%;
    max-width: 420px;
    background: #ffffff;
    padding: 30px 25px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    text-align: center;
    margin: 60px auto;
}

.profile-header {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 30px;
}

.profile-img {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    background: #e9ecef;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 60px;
}

.profile-name {
    font-size: 22px;
    font-weight: bold;
}

.logout-btn {
    margin-top: 25px;
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: #2c3e50;
    color: white;
    cursor: pointer;
}

</style>


<div class="profile-card">

    <div class="profile-header">
        Profile
    </div>

    <div class="profile-img">👤</div>

    <div class="profile-name">
        {{ session('employee_name') }}
    </div>

    <div>Tel: {{ session('tel') ?? '-' }}</div>
    <div>ตำแหน่ง: {{ session('role') }}</div>

    <form action="/logout" method="GET">
        <button class="logout-btn">ออกจากระบบ</button>
    </form>

</div>

@endsection
