@extends('layouts.app')

@section('content')

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายชื่อซัพพลายเออร์</title>
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
        <input type="text" class="search-input" id="searchInput" placeholder="ค้นหาซัพพลายเออร์...">
        <span class="search-icon">🔍</span>
    </div>

    <div id="supplierList">
        @forelse($suppliers as $sup)
        <div class="employee-card"
             data-name="{{ strtolower($sup->suppliername) }}"
             data-id="{{ strtolower($sup->supplierid) }}"
             data-tel="{{ strtolower($sup->tel) }}">
            <div class="avatar">
                <span class="avatar-icon">🏭</span>
            </div>
            <div class="employee-info">
                <div class="info-item"><span class="info-label">ชื่อ:</span> {{ $sup->suppliername }}</div>
                <div class="info-item"><span class="info-label">ID:</span> {{ $sup->supplierid }}</div>
                <div class="info-item"><span class="info-label">ติดต่อ:</span> {{ $sup->contactperson ?? '-' }}</div>
                <div class="info-item"><span class="info-label">Tel:</span> {{ $sup->tel ?? '-' }}</div>
                <div class="info-item"><span class="info-label">ที่อยู่:</span> {{ $sup->address ?? '-' }}</div>
                <div class="card-actions">
                    <a href="{{ route('suppliers.edit', $sup->supplierid) }}" class="edit-btn">แก้ไข</a>
                    <form action="{{ route('suppliers.destroy', $sup->supplierid) }}" method="POST"
                          onsubmit="return confirm('ต้องการลบ {{ $sup->suppliername }} ใช่หรือไม่?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-btn">ลบ</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
            <div class="no-selection-message">ไม่พบซัพพลายเออร์</div>
        @endforelse
    </div>

    <div class="action-buttons">
        <a href="{{ route('suppliers.create') }}" class="btn-add">+ เพิ่มซัพพลายเออร์</a>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('input', function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.employee-card').forEach(card => {
            const match = card.dataset.name.includes(term)
                       || card.dataset.id.includes(term)
                       || card.dataset.tel.includes(term);
            card.style.display = match ? '' : 'none';
        });
    });
</script>
</body>
</html>
@endsection