@extends('layouts.app')

@section('content')
    <style>
        .product-card {
            border-radius: 25px;
            background: #f9f5f0;
            overflow: hidden;
        }

        .product-header {
            background: linear-gradient(90deg, #5a3e2b, #8b6a4f);
            color: white;
            padding: 18px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 10;
        }

        .product-header a {
            position: relative;
            z-index: 20;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        }

        .product-table thead {
            background: #f1ece6;
        }

        .product-table thead th {
            font-weight: 700;
            padding: 16px;
            border-bottom: 1px solid #e3ddd6;
        }

        .product-table tbody tr {
            border-bottom: 1px solid #eee;
        }

        .product-table tbody tr:last-child {
            border-bottom: none;
        }

        .product-table td {
            padding: 16px;
            vertical-align: middle;
        }

        .product-table td:first-child {
            font-weight: 700;
            color: #3e2b1d;
        }

        .product-table td:nth-child(3),
        .product-table td:nth-child(4) {
            font-weight: 600;
        }

        .product-table td:last-child {
            width: 130px;
            text-align: center;
        }

        .product-table th:nth-child(4),
        .product-table td:nth-child(4) {
            text-align: center;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-right: 50px;
        }

        .btn-edit {
            background: #2e8b57;
            color: white;
            border-radius: 20px;
            padding: 6px 18px;
            border: none;
            text-decoration: none;
        }

        .btn-delete {
            background: #d64545;
            color: white;
            border-radius: 20px;
            padding: 6px 14px;
            border: none;
        }

        @media (max-width: 768px) {
            .product-table thead {
                display: none;
            }

            .product-table,
            .product-table tbody,
            .product-table tr {
                display: block;
                width: 100%;
            }

            .product-table tr {
                margin-bottom: 20px;
                padding: 18px;
                border-radius: 16px;
                background: #ffffff;
                box-shadow: -15px -10px 30px rgb(231, 199, 138, 0.2), inset -50px -50px 50px rgb(221, 178, 149, 0.2), inset 50px 50px 50px rgb(150, 163, 145, 0.2);
            }

            .product-table td {
                display: block;
                padding: 8px 0;
                border: none;
            }

            .product-table td::before {
                content: attr(label);
                font-weight: 600;
                color: #666;
                display: block;
                margin-bottom: 4px;
            }

            .action-buttons {
                margin-left: 3cm;
                padding: auto;
            }

            .action-buttons .btn-edit {
                margin-right: 25px;
                text-decoration: none;
            }
        }
    </style>


    <div class="container py-5">
        <div class="container">

            <div class="product-card">

                <div class="product-header">
                    <h5 class="mb-0 fw-bold">
                        📦 Product Management
                    </h5>

                    <a href="/products/create" class="btn"
                        style="background:white;
                                                                                                                color:#6b4f3a;
                                                                                                                font-weight:600;
                                                                                                                border-radius:12px;
                                                                                                                padding:6px 15px;">
                        + เพิ่มสินค้า
                    </a>
                </div>

                <div class="p-4">

                    <div class="table-responsive">
                        <table class="product-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product name</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td label="รหัสสินค้า">
                                            #{{ $product->productid }}
                                        </td>

                                        <td label="ชื่อสินค้า">
                                            {{ $product->productname }}
                                        </td>

                                        <td label="ราคา">
                                            {{ number_format($product->price, 2) }}
                                        </td>

                                        <td label="จำนวน">
                                            {{ $product->stock }}
                                        </td>

                                        <td>
                                            <div class="action-buttons">
                                                <a href="/products/edit/{{ $product->productid }}" class="btn-edit">
                                                    แก้ไข
                                                </a>

                                                <form action="/products/delete/{{ $product->productid }}" method="POST">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('ต้องการจะลบจริงหรือไม่?')"
                                                        class="btn-delete">
                                                        ลบ
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            ยังไม่มีข้อมูลสินค้า
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

        </div>

@endsection