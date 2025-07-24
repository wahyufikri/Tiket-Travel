<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AWR Travel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(to bottom, #a8323e, #f8d7da);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .logo {
            width: 80px;
            margin-bottom: 15px;
        }

        .title {
            font-size: 20px;
            margin-bottom: 30px;
            color: #a8323e;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #a8323e;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-login:hover {
            background-color: #8c2a34;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="logo">
        <div class="title">AWR TRAVEL</div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email <span style="color:red;">*</span></label>
                <input type="email" name="email" id="email" placeholder="Masukan Email" required>
            </div>
            <div class="form-group">
                <label for="password">Kata Sandi <span style="color:red;">*</span></label>
                <input type="password" name="password" id="password" placeholder="Masukan Kata Sandi" required>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>

</body>
</html>
