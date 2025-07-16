<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Xác nhận đơn hàng thành công!</title>
    {{-- Bootstrap CSS --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    {{-- Font Awesome CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-P9Q3x5K9z6/y5F/k+3QyM/7B8jWl8x+N2n/F7R8vC5/jQzL9C8x8p6F7vE8A+5Z7C/A8P5F6x+Q8F5N9C8x8Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- KẾT HỢP CÁC STYLE KHÔNG THỂ CHUYỂN THÀNH CLASS BOOTSTRAP TRỰC TIẾP --}}
    <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
            display: flex;
            /* Để căn giữa nội dung theo chiều dọc */
            justify-content: center;
            /* Căn giữa ngang */
            align-items: center;
            /* Căn giữa dọc */
            min-height: 100vh;
            /* Chiếm toàn bộ chiều cao khung nhìn */
            margin: 0;
            padding: 20px;
            /* Padding tổng thể cho body */
            box-sizing: border-box;
            /* Tính padding vào kích thước tổng thể */
        }

        /* Các style còn lại sẽ được chuyển vào thuộc tính class hoặc inline style */
    </style>
</head>

<body>
    <div class="container order-success-container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8"> {{-- Sử dụng các class cột của Bootstrap để giới hạn chiều rộng và căn giữa --}}
                <div class="card shadow-lg rounded-lg"> {{-- shadow-lg, rounded-lg thay cho box-shadow và border-radius --}}
                    {{-- header dùng bg-light và text-success để gần giống màu xanh lá cây của bạn --}}
                    <div class="card-header text-center bg-light text-success border-bottom py-3 rounded-top">
                        <h2 class="mb-0 font-weight-bold">Xác nhận đơn hàng thành công!</h2>
                    </div>
                    <div class="card-body p-4"> {{-- p-4 thay cho padding: 30px --}}
                        @if (isset($transaction))
                            <div class="alert alert-success text-center d-flex align-items-center justify-content-center py-3 mb-4"
                                role="alert"> {{-- py-3, mb-4, d-flex, align-items-center, justify-content-center --}}
                                <i class="fas fa-check-circle mr-3" style="font-size: 2rem; color: #28a745;"></i>
                                {{-- mr-3 thay cho margin-right --}}
                                Cảm ơn bạn đã mua hàng! Đơn hàng của bạn đã được thanh toán thành công.
                            </div>

                            <h3 class="text-primary mt-4 mb-3 pb-2 border-bottom">Thông tin đơn hàng của bạn:</h3>
                            {{-- text-primary, mt-4, mb-3, pb-2, border-bottom --}}
                            <p class="mb-2"><strong>Mã giao dịch:</strong> {{ $transaction->id }}</p>
                            {{-- mb-2 --}}
                            <p class="mb-2"><strong>Tổng tiền:</strong>
                                {{ number_format($transaction->tst_total_money, 0, ',', '.') }} VND</p>
                            <p class="mb-2"><strong>Phương thức thanh toán:</strong>
                                {{ $transaction->tst_type == \App\Models\Transaction::TYPE_ONLINE ? 'Thanh toán Online (VNPAY)' : 'Thanh toán khi nhận hàng (COD)' }}
                            </p>
                            <p class="mb-2"><strong>Tên người nhận:</strong> {{ $transaction->tst_name }}</p>
                            <p class="mb-2"><strong>Số điện thoại:</strong> {{ $transaction->tst_phone }}</p>
                            <p class="mb-2"><strong>Địa chỉ:</strong> {{ $transaction->tst_address }}</p>
                            <p class="mb-2"><strong>Email:</strong> {{ $transaction->tst_email }}</p>
                            <p class="mb-2"><strong>Ghi chú:</strong> {{ $transaction->tst_note }}</p>
                            <p class="mb-2"><strong>Ngày đặt hàng:</strong>
                                {{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</p>

                            <h4 class="text-primary mt-4 mb-3 pb-2 border-bottom">Chi tiết sản phẩm:</h4>
                            {{-- text-primary, mt-4, mb-3, pb-2, border-bottom --}}
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mt-4"> {{-- mt-4 --}}
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-right">Giá</th>
                                            <th class="text-right">Tổng cộng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transaction->orders as $order)
                                            <tr>
                                                <td>{{ $order->product->pro_name ?? 'N/A' }}</td>
                                                <td class="text-center">{{ $order->od_qty }}</td>
                                                <td class="text-right">
                                                    {{ number_format($order->od_price, 0, ',', '.') }} VND</td>
                                                <td class="text-right">
                                                    {{ number_format($order->od_qty * $order->od_price, 0, ',', '.') }}
                                                    VND</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning text-center" role="alert">
                                Không tìm thấy thông tin đơn hàng. Vui lòng liên hệ hỗ trợ nếu có vấn đề.
                            </div>
                        @endif

                        <div class="text-center mt-4"> {{-- mt-4 --}}
                            <a href="{{ url('/') }}" class="btn btn-primary btn-lg">Quay về trang chủ</a>
                            {{-- btn-lg cho kích thước lớn hơn --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- jQuery (Cần cho Bootstrap) --}}
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    {{-- Popper.js (Cần cho Bootstrap) --}}
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous">
    </script>
    {{-- Bootstrap JS --}}
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57B+UyuR/B5sLwN3rL6tQ8Wj" crossorigin="anonymous"></script>
</body>

</html>
