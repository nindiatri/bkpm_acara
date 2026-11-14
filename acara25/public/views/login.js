// Alamat dasar API dihitung otomatis dari lokasi halaman: .../public/views/xxx.html -> .../public
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, '');

document.querySelector('#form-login').addEventListener('submit', async (e) => {
    e.preventDefault();

    try {
        const res = await fetch(`${BASE_URL}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            }),
        });
        const hasil = await res.json();

        if (hasil.success) {
            localStorage.setItem('token', hasil.data.token);
            localStorage.setItem('role', hasil.data.role);
            localStorage.setItem('nama', hasil.data.user.nama);
            window.location.href = 'dashboard.html';
        } else {
            alert(hasil.message);
        }
    } catch (error) {
        console.error('Gagal login:', error);
        alert('Tidak dapat terhubung ke server.');
    }
});
