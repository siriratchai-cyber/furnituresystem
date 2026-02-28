@extends('layouts.app')

@section('content')

    @php
        $isEdit = isset($product);
    @endphp

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
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-section {
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control,
        .form-select {
            width: 100%;
            border-radius: 12px;
            padding: 10px 12px;
            border: 1px solid #ddd;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn-save {
            background: #84934A;
            color: white;
            border-radius: 20px;
            padding: 10px 25px;
            border: none;
            width: 100%;
        }

        .btn-cancel {
            background: #D3504A;
            color: white;
            border-radius: 20px;
            padding: 10px 25px;
            border: none;
            text-align: center;
            width: 100%;
            text-decoration-line: none;
        }

        @media (max-width: 768px) {
            .form-section {
                padding: 20px;
            }

            .product-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>

    <div class="container py-5">
        <div class="product-card">

            <div class="product-header">
                <h5 class="mb-0 fw-bold">
                    {{ $isEdit ? '✏️ แก้ไขสินค้า' : '➕ เพิ่มสินค้าใหม่' }}
                </h5>

                <a href="/products" class="btn"
                    style="background:white;color:#6b4f3a;font-weight:600;border-radius:12px;padding:6px 15px;">
                    กลับ
                </a>
            </div>

            <div class="p-4">
                <div class="form-section">

                    <form action="{{ $isEdit ? '/products/update/' . $product->productid : '/products/store' }}"
                        method="POST">
                        @csrf

                        <div class="form-group">
                            <label>ชื่อสินค้า</label>
                            <input type="text" name="productname" class="form-control"
                                value="{{ old('productname', $product->productname ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label>ประเภทสินค้า</label>
                            <select name="producttype" class="form-select" required>
                                <option value="">-- เลือกประเภท --</option>
                                <option value="ชุด" {{ old('producttype', $product->producttype ?? '') == 'ชุด' ? 'selected' : '' }}>
                                    ชุด
                                </option>
                                <option value="ชิ้นเดียว" {{ old('producttype', $product->producttype ?? '') == 'ชิ้นเดียว' ? 'selected' : '' }}>
                                    ชิ้นเดียว
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>ราคา (ซื้อ)</label>
                            <input type="number" step="0.01" name="cost" class="form-control"
                                value="{{ old('cost', $product->cost ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label>ราคา (ขาย)</label>
                            <input type="number" step="0.01" name="price" class="form-control"
                                value="{{ old('price', $product->price ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>หมวดหมู่</label>
                            <select name="categories" class="form-select" required>
                                <option value="">-- เลือกหมวดหมู่ --</option>

                                @foreach($categories as $cat)
                                    <option value="{{ $cat->categories }}" {{ old('categories', $product->categories ?? '') == $cat->categories ? 'selected' : '' }}>
                                        {{ $cat->categories }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="form-group">
                            <label>ประเภทไม้</label>
                            <select name="woodtype" class="form-select" required>
                                <option value="">-- เลือกประเภทไม้ --</option>

                                @foreach($woodtypes as $wood)
                                    <option value="{{ $wood->woodtype }}" {{ old('woodtype', $product->woodtype ?? '') == $wood->woodtype ? 'selected' : '' }}>
                                        {{ $wood->woodtype }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="form-group">
                            <label>จำนวนคงเหลือ</label>
                            <input type="number" name="stock" class="form-control"
                                value="{{ old('stock', $product->stock ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Supplier</label>
                            <select name="supplierid" class="form-select" required>
                                <option value="">-- เลือก Supplier --</option>

                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->supplierid}}" {{ old('supplierid', $product->supplierid ?? '') == $supplier->supplierid ? 'selected' : '' }}>
                                        {{ $supplier->suppliername }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn-save">
                                {{ $isEdit ? 'อัปเดตสินค้า' : 'บันทึกสินค้า' }}
                            </button>

                            <a href="/products" class="btn-cancel">
                                ยกเลิก
                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
    </div>

@endsection