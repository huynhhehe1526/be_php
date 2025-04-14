<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Đăng nhập</h1>
        <form action="#">
            <label for="username">Tên người dùng:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Đăng nhập</button>
            <a href="#">Quên mật khẩu?</a>
            <a href="#">Tạo tài khoản</a>
            <button>
                <a href="{{ route('login-google') }}">Login google</a>
            </button>
        </form>
    </div>
</body>

</html>