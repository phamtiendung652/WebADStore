@extends('layouts.app_master_user')
@section('css')
    <style>
        <?php $style = file_get_contents('css/user.min.css');
        echo $style; ?> .css-tooltip:hover:after {
            content: attr(data-tooltip);
            position: absolute;
            z-index: 1;
            bottom: 100%;
            right: 0;
            width: 100%;
            background-color: rgba(251, 88, 88, 0.86);
            font-size: 11px;
            line-height: 1.6em;
            font-weight: 400;
            text-decoration: none;
            text-transform: none;
            text-align: center;
            color: #fff;
            padding: 5px;
            border-radius: 5px;
        }
    </style>
@stop

@section('content')
    <section>
        <div class="title">Danh sách đơn hàng</div>
        <form class="form-inline">
            <div class="form-group " style="margin-right: 10px;">
                <input type="text" class="form-control" value="{{ Request::get('id') }}" name="id" placeholder="ID">
            </div>
            <div class="form-group" style="margin-right: 10px;">
                <select name="status" class="form-control">
                    <option value="">Trạng thái</option>
                    <option value="1" {{ Request::get('status') == 1 ? "selected='selected'" : '' }}>Tiếp nhận</option>
                    <option value="2" {{ Request::get('status') == 2 ? "selected='selected'" : '' }}>Đang vận chuyển
                    </option>
                    <option value="3" {{ Request::get('status') == 3 ? "selected='selected'" : '' }}>Đã bàn giao
                    </option>
                    <option value="-1" {{ Request::get('status') == -1 ? "selected='selected'" : '' }}>Huỷ bỏ</option>
                </select>
            </div>
            <div class="form-group" style="margin-right: 10px;">
                <button type="submit" class="btn btn-pink btn-sm">Tìm kiếm</button>
            </div>
        </form>

        {{-- Display flash messages --}}
        @if (Session::has('success'))
            <div class="alert alert-success mt-3">
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::has('error'))
            <div class="alert alert-danger mt-3">
                {{ Session::get('error') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Mã đơn</th>
                        <th scope="col">Tên</th>
                        <th scope="col">Giá</th>
                        <th scope="col">Thời Gian</th>
                        <th scope="col">Trạng Thái</th>
                        <th scope="col" style="text-align: center">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td scope="row" style="text-align: center;position:relative;"
                                data-tooltip='Click để xem chi tiết' class="css-tooltip">
                                <a href="{{ route('get.user.order', $transaction->id) }}">DH{{ $transaction->id }}</a>
                            </td>
                            <td style="text-align: center">{{ $transaction->tst_name }}</td>
                            <td style="text-align: center">{{ number_format($transaction->tst_total_money, 0, ',', '.') }}
                                đ
                            </td>
                            <td style="text-align: center">{{ $transaction->created_at }}</td>
                            <td style="text-align: center">
                                <span class="label label-{{ $transaction->getStatus($transaction->tst_status)['class'] }}">
                                    {{ $transaction->getStatus($transaction->tst_status)['name'] }}
                                </span>
                            </td>
                            <td style="text-align: center">
                                @if ($transaction->can_cancel)
                                    <a href="{{ route('get.user.transaction.cancel', $transaction->id) }}"
                                        class="btn-xs label-danger" style="color: white"
                                        onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                        <i class="fa fa-times"></i> Hủy đơn
                                    </a>
                                @else
                                    @if ($transaction->tst_type == 2)
                                        <span></span>
                                    @elseif ($transaction->tst_status == -1)
                                        <span></span>
                                    @else
                                        <span></span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="display: block;">
            {!! $transactions->appends($query ?? [])->links() !!}
        </div>
    </section>
@stop

@section('script')
    <div id="popup-transaction" class="modal text-center">
        {{-- <div class="header">Hoá đơn mua hang</div> --}}
        <div class="content">

        </div>
        <div class="footer">
            <a href="#" rel="modal:close" class="btn btn-pink ">Đóng</a>
            <a href="" class="btn btn-purple js-export-pdf"> Export PDF</a>
        </div>
    </div>
@stop

{{-- @section('content')
    <section>
        <div class="title">Danh sách đơn hàng</div>
        <form class="form-inline">
            <div class="form-group " style="margin-right: 10px;">
                <input type="text" class="form-control" value="{{ Request::get('id') }}" name="id" placeholder="ID">
            </div>
            <div class="form-group" style="margin-right: 10px;">
                <select name="status" class="form-control">
                    <option value="">Trạng thái</option>
                    <option value="1" {{ Request::get('status') == 1 ? "selected='selected'" : '' }}>Tiếp nhận</option>
                    <option value="2" {{ Request::get('status') == 2 ? "selected='selected'" : '' }}>Đang vận chuyển
                    </option>
                    <option value="3" {{ Request::get('status') == 3 ? "selected='selected'" : '' }}>Đã bàn giao
                    </option>
                    <option value="-1" {{ Request::get('status') == -1 ? "selected='selected'" : '' }}>Huỷ bỏ</option>
                </select>
            </div>
            <div class="form-group" style="margin-right: 10px;">
                <button type="submit" class="btn btn-pink btn-sm">Tìm kiếm</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Mã đơn</th>
                        <th scope="col">Tên</th>
                        <th scope="col">Giá</th>
                        <th scope="col">Thời Gian</th>
                        <th scope="col">Trạng Thái</th>
                        <th scope="col" style="text-align: center">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td scope="row" style="text-align: center;position:relative;"
                                data-tooltip='Click để xem chi tiết' class="css-tooltip">
                                <a href="{{ route('get.user.order', $transaction->id) }}">DH{{ $transaction->id }}</a>
                            </td>
                            <td style="text-align: center">{{ $transaction->tst_name }}</td>
                            <td style="text-align: center">{{ number_format($transaction->tst_total_money, 0, ',', '.') }} đ
                            </td>
                            <td style="text-align: center">{{ $transaction->created_at }}</td>
                            <td style="text-align: center">
                                <span class="label label-{{ $transaction->getStatus($transaction->tst_status)['class'] }}">
                                    {{ $transaction->getStatus($transaction->tst_status)['name'] }}
                                </span>
                            </td>
                            <td style="text-align: center">
                                @if (!in_array($transaction->tst_status, [-1, 2, 3]))
                                    <a href="{{ route('get.user.transaction.cancel', $transaction->id) }}"
                                        class="btn-xs label-danger" style="color: white"><i class="fa fa-save"></i> Huỷ
                                        đơn</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="display: block;">
            {!! $transactions->appends($query ?? [])->links() !!}
        </div>
    </section>
@stop --}}
