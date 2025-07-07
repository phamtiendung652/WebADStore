@extends('layouts.app_master_frontend')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/cart.min.css') }}">
    <style>
        /* CSS cho Modal tùy chỉnh */
        .custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            /* Nền mờ */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
            /* Đảm bảo nó nằm trên các nội dung khác */
            visibility: hidden;
            /* Mặc định ẩn */
            opacity: 0;
            /* Mặc định trong suốt */
            transition: visibility 0s, opacity 0.3s ease;
        }

        .custom-modal-overlay.show {
            visibility: visible;
            opacity: 1;
        }

        .custom-modal-content {
            background-color: #fff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 450px;
            /* Chiều rộng tối đa */
            transform: translateY(-20px);
            /* Hiệu ứng di chuyển nhẹ khi xuất hiện */
            transition: transform 0.3s ease;
            position: relative;
            box-sizing: border-box;
            /* Đảm bảo padding không làm tăng kích thước */
        }

        .custom-modal-overlay.show .custom-modal-content {
            transform: translateY(0);
        }

        .custom-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .custom-modal-header h5 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }

        .custom-modal-close {
            background: none;
            border: none;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
            color: #aaa;
            padding: 0;
        }

        .custom-modal-close:hover {
            color: #666;
        }

        .custom-modal-body {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
            margin-bottom: 20px;
        }

        .custom-modal-body #paymentMethodText {
            margin-top: 10px;
            font-weight: bold;
            color: #007bff;
            /* Màu xanh nổi bật */
        }

        .custom-modal-footer {
            border-top: 1px solid #eee;
            padding-top: 20px;
            text-align: right;
        }

        .custom-modal-footer button {
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            margin-left: 10px;
        }

        .custom-modal-footer .btn-secondary {
            background-color: #6c757d;
            color: #fff;
            border: 1px solid #6c757d;
        }

        .custom-modal-footer .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .custom-modal-footer .btn-primary {
            background-color: #007bff;
            color: #fff;
            border: 1px solid #007bff;
        }

        .custom-modal-footer .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
@stop
@section('content')
    <div class="container cart">
        <div class="left">
            <div class="list">
                <div class="title">THÔNG TIN GIỎ HÀNG</div>
                <div class="list__content">
                    @if (count($shopping) > 0)
                        {{-- Hiển thị bảng sản phẩm khi giỏ hàng có sản phẩm --}}
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 100px;"></th>
                                    <th style="width: 30%">Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shopping as $key => $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('get.product.detail', \Str::slug($item->name) . '-' . $item->id) }}"
                                                title="{{ $item->name }}" class="avatar image contain">
                                                <img alt="" src="{{ pare_url_file($item->options->image) }}"
                                                    class="lazyload">
                                            </a>
                                        </td>
                                        <td>
                                            <a
                                                href="{{ route('get.product.detail', \Str::slug($item->name) . '-' . $item->id) }}"><strong>{{ $item->name }}</strong></a>
                                        </td>
                                        <td>
                                            <p><b>{{ number_format($item->price, 0, ',', '.') }} đ</b></p>
                                            <p>

                                                @if ($item->options->price_old)
                                                    <span
                                                        style="text-decoration: line-through;">{{ number_format(number_price($item->options->price_old), 0, ',', '.') }}
                                                        đ</span>
                                                    <span class="sale">- {{ $item->options->sale }} %</span>
                                                @endif
                                            </p>
                                        </td>
                                        <td>
                                            <div class="qty_number">
                                                <input type="number" min="1" class="input_quantity"
                                                    name="quantity_14692" value="{{ $item->qty }}" id="">
                                                <p data-price="{{ $item->price }}"
                                                    data-url="{{ route('ajax_get.shopping.update', $key) }}"
                                                    data-id-product="{{ $item->id }}">
                                                    <span class="js-increase">+</span>
                                                    <span class="js-reduction">-</span>
                                                </p>
                                                <a href="{{ route('get.shopping.delete', $key) }}"
                                                    class="js-delete-item btn-action-delete"><i class="la la-trash"></i></a>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="js-total-item">{{ number_format($item->price * $item->qty, 0, ',', '.') }}
                                                đ</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p style="float: right;margin-top: 20px;">Tổng tiền : <b id="sub-total">{{ \Cart::subtotal(0) }}
                                đ</b>
                        </p>
                    @else
                        {{-- Hiển thị thông báo và nút khi giỏ hàng rỗng --}}
                        <div style="text-align: center; padding: 50px 0;">
                            <p>Giỏ hàng của bạn đang trống.</p>
                            <a href="{{ url('/') }}" class="btn btn-primary" style="margin-top: 20px;">Quay về trang
                                chủ</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="right">
            <div class="customer">
                <div class="title">THÔNG TIN ĐẶT HÀNG</div>
                <div class="customer__content">
                    <form class="from_cart_register" action="{{ route('post.shopping.pay') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="name">Họ và tên <span class="cRed">(*)</span></label>
                            <input name="tst_name" id="name" required="" value="{{ get_data_user('web', 'name') }}"
                                type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="phone">Điện thoại <span class="cRed">(*)</span></label>
                            <input name="tst_phone" id="phone" required=""
                                value="{{ get_data_user('web', 'phone') }}" type="text" class="form-control">
                        </div>
                        {{-- PHẦN CHỌN ĐỊA CHỈ TỈNH/THÀNH PHỐ, QUẬN/HUYỆN, PHƯỜNG/XÃ --}}
                        <div class="form-group">
                            <label for="city">Tỉnh / Thành phố <span class="cRed">(*)</span></label>
                            <select id="city" class="form-control" required>
                                <option value="" selected="selected">Chọn tỉnh / TP</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="district">Quận / Huyện <span class="cRed">(*)</span></label>
                            <select id="district" class="form-control" required>
                                <option value="" selected="selected">Chọn quận huyện</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ward">Phường / Xã <span class="cRed">(*)</span></label>
                            <select id="ward" class="form-control" required>
                                <option value="" selected="selected">Chọn phường xã</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="detailed_address">Số nhà / Đường <span class="cRed">(*)</span></label>
                            <input name="detailed_address" id="detailed_address" required=""
                                value="{{ get_data_user('web', 'address') }}" type="text" class="form-control"
                                placeholder="Ví dụ: Số 123, Ngõ 456, Đường ABC">
                            {{-- Trường input ẩn này sẽ chứa địa chỉ đầy đủ đã được gộp --}}
                            <input type="hidden" id="fullAddressOutput" name="tst_address">
                        </div>
                        {{-- <div class="form-group">
                            <label for="address">Địa chỉ <span class="cRed">(*)</span></label>
                            <input name="tst_address" id="address" required=""
                                value="{{ get_data_user('web', 'address') }}" type="text" class="form-control">
                        </div> --}}
                        <div class="form-group">
                            <label for="email">Email <span class="cRed">(*)</span></label>
                            <input name="tst_email" id="email" required=""
                                value="{{ get_data_user('web', 'email') }}" type="text" value=""
                                class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="note">Ghi chú thêm</label>
                            <textarea name="tst_note" id="note" cols="3" style="min-height: 100px;" rows="2"
                                class="form-control"></textarea>
                        </div>
                        <div class="btn-buy">
                            <button
                                class="buy1 btn btn-purple {{ \Auth::id() ? '' : 'js-show-login' }} js-show-payment-confirm"
                                style="width: 100%" type="button" name="pay" value="online"
                                data-payment-method="Thanh toán khi nhận hàng">
                                Thanh toán khi nhận hàng
                            </button>
                            <button
                                class="buy1 btn btn-primary {{ \Auth::id() ? '' : 'js-show-login' }} js-show-payment-confirm"
                                style="width: 100%;margin-top: 20px" type="button" name="pay" value="transfer"
                                data-payment-method="Thanh toán Online">
                                Thanh toán Online
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL XÁC NHẬN THANH TOÁN --}}
    <div class="custom-modal-overlay" id="paymentConfirmModal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h5 id="paymentConfirmModalLabel">Xác nhận thanh toán</h5>
                <button type="button" class="custom-modal-close js-close-modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="custom-modal-body">
                Bạn có chắc chắn muốn hoàn tất thanh toán không?
                <p id="paymentMethodText" class="mt-2"></p>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn-secondary js-close-modal">Hủy</button>
                <button type="button" class="btn-primary" id="confirmPaymentBtn">Xác nhận</button>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script src="{{ asset('js/cart.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/address-selector.js') }}" type="text/javascript"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentButtons = document.querySelectorAll(
                '.js-show-payment-confirm'); // Chọn các nút bằng lớp mới
            const paymentConfirmModal = document.getElementById('paymentConfirmModal');
            const confirmPaymentBtn = document.getElementById('confirmPaymentBtn');
            const paymentMethodText = document.getElementById('paymentMethodText');
            const closeButtons = document.querySelectorAll('.js-close-modal'); // Chọn các nút đóng modal

            let currentPaymentButton = null; // Để lưu trữ nút nào đã kích hoạt modal

            // Hàm để hiển thị modal
            function showModal() {
                paymentConfirmModal.classList.add('show');
            }

            // Hàm để ẩn modal
            function hideModal() {
                paymentConfirmModal.classList.remove('show');
                // Optional: Clear the currentPaymentButton when the modal is hidden
                currentPaymentButton = null;
                paymentMethodText.textContent = ''; // Xóa văn bản
            }

            // Gán sự kiện click cho các nút thanh toán
            paymentButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Nếu người dùng chưa đăng nhập, hãy để js-show-login xử lý
                    if (this.classList.contains('js-show-login')) {
                        return; // Tránh mở modal xác nhận nếu người dùng chưa đăng nhập
                    }

                    currentPaymentButton = this;
                    const paymentMethod = this.getAttribute('data-payment-method');
                    paymentMethodText.textContent = `Phương thức: ${paymentMethod}`;
                    showModal(); // Hiển thị modal tùy chỉnh
                });
            });

            // Gán sự kiện click cho nút xác nhận trong modal
            confirmPaymentBtn.addEventListener('click', function() {
                if (currentPaymentButton) {
                    const form = currentPaymentButton.closest('form'); // Lấy form cha của nút
                    if (form) {
                        let hiddenInput = document.createElement('input');
                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', currentPaymentButton.name);
                        hiddenInput.setAttribute('value', currentPaymentButton.value);
                        form.appendChild(hiddenInput);
                        // GỌI HÀM GỘP ĐỊA CHỈ TRƯỚC KHI SUBMIT
                        // Đảm bảo hàm này được gọi và giá trị của #fullAddressOutput được cập nhật
                        if (typeof updateCombinedAddress === 'function') {
                            updateCombinedAddress();
                        }
                        form.submit(); // Gửi form
                    }
                }
                hideModal(); // Ẩn modal sau khi gửi hoặc nếu không tìm thấy form
            });

            // Gán sự kiện click cho các nút đóng modal
            closeButtons.forEach(button => {
                button.addEventListener('click', hideModal);
            });

            // Đóng modal khi click ra ngoài (trên lớp overlay)
            paymentConfirmModal.addEventListener('click', function(event) {
                if (event.target === paymentConfirmModal) { // Chỉ đóng nếu click trực tiếp vào overlay
                    hideModal();
                }
            });
        });
    </script>
@stop

{{-- <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 100px;"></th>
                                <th style="width: 30%">Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shopping as $key => $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('get.product.detail', \Str::slug($item->name) . '-' . $item->id) }}"
                                            title="{{ $item->name }}" class="avatar image contain">
                                            <img alt="" src="{{ pare_url_file($item->options->image) }}"
                                                class="lazyload">
                                        </a>
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('get.product.detail', \Str::slug($item->name) . '-' . $item->id) }}"><strong>{{ $item->name }}</strong></a>
                                    </td>
                                    <td>
                                        <p><b>{{ number_format($item->price, 0, ',', '.') }} đ</b></p>
                                        <p>

                                            @if ($item->options->price_old)
                                                <span
                                                    style="text-decoration: line-through;">{{ number_format(number_price($item->options->price_old), 0, ',', '.') }}
                                                    đ</span>
                                                <span class="sale">- {{ $item->options->sale }} %</span>
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <div class="qty_number">
                                            <input type="number" min="1" class="input_quantity"
                                                name="quantity_14692" value="{{ $item->qty }}" id="">
                                            <p data-price="{{ $item->price }}"
                                                data-url="{{ route('ajax_get.shopping.update', $key) }}"
                                                data-id-product="{{ $item->id }}">
                                                <span class="js-increase">+</span>
                                                <span class="js-reduction">-</span>
                                            </p>
                                            <a href="{{ route('get.shopping.delete', $key) }}"
                                                class="js-delete-item btn-action-delete"><i class="la la-trash"></i></a>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="js-total-item">{{ number_format($item->price * $item->qty, 0, ',', '.') }}
                                            đ</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p style="float: right;margin-top: 20px;">Tổng tiền : <b id="sub-total">{{ \Cart::subtotal(0) }} đ</b>
                    </p> --}}
