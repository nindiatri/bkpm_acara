// Alamat dasar API dihitung otomatis dari lokasi halaman: .../public/views/xxx.html -> .../public
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, '');

async function muatDataMahasiswa() {
    const status = document.getElementById('status');
    const tbody = document.querySelector('#tabel-mahasiswa tbody');

    // Tugas mandiri 1: indikator pemuatan
    status.textContent = 'Memuat data...';
    tbody.innerHTML = '';

    try {
        const response = await fetch(`${BASE_URL}/mahasiswa`);
        const hasil = await response.json();
        const data = hasil.data || [];

        // Tugas mandiri 2: penanganan data kosong
        if (data.length === 0) {
            status.textContent = 'Belum ada data';
            return;
        }

        data.forEach((mhs) => {
            tbody.innerHTML +=
`<tr><td>${mhs.nim}</td><td>${mhs.nama}</td><td>${mhs.prodi}</td></tr>`;
        });
        status.textContent = '';
    } catch (error) {
        status.textContent = 'Gagal memuat data.';
        console.error('Gagal memuat data:', error);
    }
}

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
