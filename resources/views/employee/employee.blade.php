@extends('layouts.app')

@section('content')

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายชื่อพนักงาน</title>
    <link rel="stylesheet" href="{{ asset('css/employee.css') }}">
</head>

<body>
<div class="container">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="search-box">
        <input type="text" class="search-input" id="searchInput" placeholder="ค้นหาพนักงาน...">
        <span class="search-icon">🔍</span>
    </div>

    <div id="employeeList">
        @forelse($employees as $emp)
        <div class="employee-card"
             data-name="{{ strtolower($emp->empname) }}"
             data-empid="{{ strtolower($emp->employeeid) }}"
             data-position="{{ strtolower($emp->position) }}">
            <div class="avatar"><span class="avatar-icon">👤</span></div>
            <div class="employee-info">
                <div class="info-item"><span class="info-label">ชื่อ:</span> {{ $emp->empname }}</div>
                <div class="info-item"><span class="info-label">ID:</span> {{ $emp->employeeid }}</div>
                <div class="info-item"><span class="info-label">ตำแหน่ง:</span> {{ $emp->position }}</div>
                <div class="info-item"><span class="info-label">Tel:</span> {{ $emp->tel }}</div>
                <div class="card-actions">
                    <a href="{{ route('employees.edit', $emp->employeeid) }}" class="edit-btn">แก้ไข</a>
                    <form action="{{ route('employees.destroy', $emp->employeeid) }}" method="POST"
                        onsubmit="return confirm('ต้องการลบ {{ $emp->empname }} ใช่หรือไม่?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-btn">ลบ</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
            <div class="no-selection-message">ไม่พบพนักงาน</div>
        @endforelse
    </div>

    <div class="action-buttons">
        <a href="{{ route('employees.create') }}" class="btn btn-add">+ เพิ่มพนักงาน</a>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('input', function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.employee-card').forEach(card => {
            const match = card.dataset.name.includes(term)
                       || card.dataset.empid.includes(term)
                       || card.dataset.position.includes(term);
            card.style.display = match ? '' : 'none';
        });
    });
</script>
</body>
</html>

@endsection