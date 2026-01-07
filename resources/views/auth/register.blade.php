<!DOCTYPE html>
<html>
<head>
    <title>Register Mahasiswa</title>
</head>
<body>
    <h2>Daftar Mahasiswa</h2>

    <form method="POST" action="/register">
        @csrf

        <input type="text" name="name" placeholder="Nama Lengkap" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" min=8 required><br><br>
        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" min=8 required><br><br>

        <button type="submit">Daftar</button>
    </form>

    <p>
        <a href="/login">Kembali ke Login</a>
    </p>
</body>
</html>
