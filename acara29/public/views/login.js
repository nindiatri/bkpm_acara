// Alamat dasar API dihitung otomatis dari lokasi halaman: .../public/views/xxx.html -> .../public
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, '');

document.querySelector('#form-login').addEventListener('submit', async (e) => {
    e.preventDefault();
    const tombol = e.currentTarget.querySelector('button[type="submit"]');

    // Langkah 3: try-catch-finally, tombol nonaktif selama permintaan berlangsung
    setTombolProses(tombol, true, 'Memproses...');
    try {
        const res = await fetchDenganRetry(`${BASE_URL}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            }),
        });
        const hasil = await bacaJson(res);

        if (hasil.success) {
            localStorage.setItem('token', hasil.data.token);
            localStorage.setItem('role', hasil.data.role);
            localStorage.setItem('nama', hasil.data.user.nama);
            window.location.href = 'dashboard.html';
        } else {
            tampilNotif(formatPesan(res.status, hasil), 'error');
        }
    } catch (error) {
        console.error('Gagal login:', error);
        tampilNotif(pesanKesalahan(error), 'error');
    } finally {
        setTombolProses(tombol, false);
    }
});

// Menampilkan pesan yang dititipkan halaman lain (misalnya "Sesi berakhir")
tampilNotifTersimpan();
