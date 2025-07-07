<!DOCTYPE html>
<html>
<head>
    <title>Xác thực tài khoản</title>
</head>
<body>
    <h1>Xin Chào {{ $name }},</h1>
    <p>Bạn đã đăng ký tài khoản thành công. Vui lòng nhấn vào liên kết dưới đây để kích hoạt tài khoản của bạn:</p>
    <a href="{{ route('activate', $active_token) }}">Kích hoạt tài khoản</a>
    <p>Nếu bạn không tạo tài khoản này, vui lòng bỏ qua email này.</p>
    <p>Cám ơn {{$name}}</p>
</body>
</html>
