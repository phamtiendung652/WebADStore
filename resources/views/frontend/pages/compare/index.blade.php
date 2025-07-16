@extends('layouts.app_master_frontend')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/cart.min.css') }}">
    <style>
        /* CSS riêng cho trang so sánh */
        .compare-page {
            padding: 20px 0;
        }

        .compare-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .compare-table th,
        .compare-table td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            vertical-align: top;
        }

        .compare-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            width: 20%;
            /* Cột đặc điểm */
        }

        .compare-table td {
            width: calc(80% / {{ $products->count() > 0 ? $products->count() : 1 }});
            /* Chia đều cho số sản phẩm */
        }

        .compare-product-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .compare-product-item img {
            max-width: 150px;
            height: auto;
            margin-bottom: 10px;
        }

        .compare-product-item h3 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .compare-product-item .price {
            font-size: 16px;
            color: #e91e63;
            font-weight: bold;
        }

        .no-products-message {
            text-align: center;
            padding: 50px;
            font-size: 18px;
            color: #888;
        }

        .remove-compare-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .remove-compare-btn:hover {
            background-color: #d32f2f;
        }
    </style>
@stop

@section('content')
    <div class="container">
        <h1>So Sánh Sản Phẩm</h2>

            @if ($products->isEmpty())
                <div class="no-products-message">
                    <p>Không có sản phẩm nào để so sánh. Vui lòng chọn sản phẩm từ danh sách.</p>
                    <p><a href="{{ route('get.product.list') }}" class="btn btn-primary">Quay lại trang sản phẩm</a></p>
                </div>
            @else
                <table class="compare-table">
                    <thead>
                        <tr>
                            <th>Đặc điểm</th>
                            @foreach ($products as $product)
                                <th>
                                    <div class="compare-product-item">
                                        <img src="{{ pare_url_file($product->pro_avatar) }}" alt="{{ $product->pro_name }}">
                                        <h3>{{ $product->pro_name }}</h3>
                                        @if ($product->pro_sale)
                                            @php
                                                $price = ((100 - $product->pro_sale) * $product->pro_price) / 100;
                                            @endphp
                                            <p class="price">{{ number_format($price, 0, ',', '.') }} đ</p>
                                            <p class="price-sale" style="text-decoration: line-through; color: #999;">
                                                {{ number_format($product->pro_price, 0, ',', '.') }} đ</p>
                                        @else
                                            <p class="price">{{ number_format($product->pro_price, 0, ',', '.') }} đ</p>
                                        @endif
                                        <button class="remove-compare-btn" data-id="{{ $product->id }}">Xóa</button>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CPU</td>
                            @foreach ($products as $product)
                                <td>{{ $product->specification->sp_cpu ?? 'N/A' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>GPU</td>
                            @foreach ($products as $product)
                                <td>{{ $product->specification->sp_gpu ?? 'N/A' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>RAM</td>
                            @foreach ($products as $product)
                                <td>{{ $product->specification->sp_ram ?? 'N/A' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Storage</td>
                            @foreach ($products as $product)
                                <td>{{ $product->specification->sp_storage ?? 'N/A' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Display</td>
                            @foreach ($products as $product)
                                <td>{{ $product->specification->sp_display ?? 'N/A' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Lượt xem</td>
                            @foreach ($products as $product)
                                <td>{{ number_format($product->pro_view) }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Đánh giá</td>
                            @foreach ($products as $product)
                                <td>
                                    @php
                                        $iactive = 0;
                                        if ($product->pro_review_total) {
                                            $iactive = round($product->pro_review_star / $product->pro_review_total, 2);
                                        }
                                    @endphp
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="la la-star {{ $i <= $iactive ? 'active' : '' }}"></i>
                                    @endfor
                                    ({{ $product->pro_review_total }} đánh giá)
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Mô tả</td>
                            @foreach ($products as $product)
                                <td>{{ Str::limit($product->pro_description, 150) ?? 'Không có mô tả' }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            @endif
    </div>
@stop

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const removeButtons = document.querySelectorAll('.remove-compare-btn');
            const compareListKey = 'productCompareList';

            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productIdToRemove = parseInt(this.dataset.id);
                    let currentList = JSON.parse(localStorage.getItem(compareListKey) || '[]');

                    currentList = currentList.filter(id => id !== productIdToRemove);
                    localStorage.setItem(compareListKey, JSON.stringify(currentList));

                    alert('Sản phẩm đã được xóa khỏi danh sách so sánh.');
                    // Tải lại trang để cập nhật bảng so sánh
                    window.location.reload();
                });
            });
        });
    </script>
@stop
