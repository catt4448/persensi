<h1>Dashboard Admin</h1>

<p>Selamat datang di halaman dashboard admin.</p>
<p>Di sini Anda dapat mengelola berbagai aspek aplikasi.</p>
<p>Gunakan menu navigasi untuk mengakses fitur-fitur yang tersedia.</p>
<p>Pastikan untuk selalu memperbarui informasi dan data sesuai kebutuhan.</p>
<p>Jika Anda memerlukan bantuan, silakan hubungi tim dukungan teknis.</p>
<p>Terima kasih telah menggunakan aplikasi kami!</p>

<hr>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>
