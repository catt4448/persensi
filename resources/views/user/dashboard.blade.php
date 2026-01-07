<h1>Dashboard User</h1>
<p>Selamat datang di halaman dashboard user.</p>
<p>Di sini Anda dapat melihat informasi terkait akun Anda.</p>
<p>Gunakan menu navigasi untuk mengakses fitur-fitur yang tersedia.</p>
<p>Pastikan untuk selalu memperbarui profil dan preferensi Anda.</p>
<p>Jika Anda memerlukan bantuan, silakan hubungi tim dukungan pelanggan.</p>
<p>Terima kasih telah menjadi bagian dari komunitas kami!</p>

<hr>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>