const BASE_URL = 'http://localhost/BkpmWebServer/acara24_dasar/public';

async function muatDataMahasiswa() {
    const status = document.getElementById('status');
    const tbody = document.querySelector('#tabel-mahasiswa tbody');

    status.textContent = 'Memuat data...';
    tbody.innerHTML = '';

    try {
        const response = await fetch(`${BASE_URL}/mahasiswa`);
        const hasil = await response.json();

        hasil.data.forEach((mhs) => {
            tbody.innerHTML +=`
            <tr>
                <td>${mhs.nim}</td>
                <td>${mhs.nama}</td>
                <td>${mhs.prodi}</td>
        </tr>`;
        });
        status.textContent = '';
    } catch (error) {
        status.textContent = 'Gagal memuat data.';
        console.error('Gagal memuat data:', error);
    }
}
document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
