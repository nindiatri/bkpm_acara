// Alamat dasar API dihitung otomatis dari lokasi halaman: .../public/views/xxx.html -> .../public
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, '');

const token = localStorage.getItem('token');
const role = localStorage.getItem('role');
const nama = localStorage.getItem('nama');
let editId = null;

// Pembungkus fetch: otomatis mengirim token Bearer (Langkah 6 membutuhkan ini)
async function apiFetch(path, options = {}) {
    const response = await fetch(`${BASE_URL}${path}`, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
    });
    const hasil = await response.json();

    // Token tidak valid atau kedaluwarsa (berlaku 5 menit): paksa login ulang
    if (response.status === 401) {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        localStorage.removeItem('nama');
        alert('Sesi berakhir, silakan login kembali.');
        window.location.href = 'login.html';
    }
    return { status: response.status, hasil };
}

// Tugas mandiri 2: sembunyikan elemen khusus admin bila role = user
function terapkanRole() {
    if (role === 'admin') return;
    document.querySelectorAll('.aksi-admin').forEach((el) => {
        el.hidden = true;
    });
}

function buatTombol(teks, aksi) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.textContent = teks;
    btn.addEventListener('click', aksi);
    return btn;
}

// Langkah 5: ambil data lewat GET /mahasiswa, tampilkan tabel dan jumlah total
async function muatDataMahasiswa() {
    const status = document.getElementById('status');
    const tbody = document.querySelector('#tabel-mahasiswa tbody');

    status.textContent = 'Memuat data...';
    tbody.innerHTML = '';

    try {
        const { hasil } = await apiFetch('/mahasiswa');
        const data = hasil.data || [];

        document.getElementById('total-mahasiswa').textContent = data.length;

        if (data.length === 0) {
            status.textContent = 'Belum ada data';
            return;
        }

        data.forEach((mhs) => {
            const tr = document.createElement('tr');
            [mhs.nim, mhs.nama, mhs.prodi].forEach((nilai) => {
                const td = document.createElement('td');
                td.textContent = nilai;
                tr.appendChild(td);
            });

            const aksi = document.createElement('td');
            aksi.className = 'aksi-admin';
            aksi.append(
                buatTombol('Ubah', () => mulaiUbah(mhs)),
                ' ',
                buatTombol('Hapus', () => hapusMahasiswa(mhs.id))
            );
            tr.appendChild(aksi);
            tbody.appendChild(tr);
        });

        status.textContent = '';
        terapkanRole();
    } catch (error) {
        status.textContent = 'Gagal memuat data.';
        console.error('Gagal memuat data:', error);
    }
}

function tampilkanForm(judul) {
    document.getElementById('judul-form').textContent = judul;
    document.getElementById('form-mahasiswa').hidden = false;
}

function tutupForm() {
    editId = null;
    document.getElementById('form-mahasiswa').reset();
    document.getElementById('form-mahasiswa').hidden = true;
}

function mulaiUbah(mhs) {
    editId = mhs.id;
    document.getElementById('nim').value = mhs.nim;
    document.getElementById('nama').value = mhs.nama;
    document.getElementById('prodi').value = mhs.prodi;
    tampilkanForm('Ubah Mahasiswa');
}

// Pesan hasil aksi; kode status ikut ditampilkan agar 403 terlihat jelas
function laporkan(status, hasil) {
    let pesan = `[${status}] ${hasil.message || ''}`;
    if (hasil.errors && Object.keys(hasil.errors).length > 0) {
        pesan += '\n' + Object.values(hasil.errors).join('\n');
    }
    alert(pesan);
}

async function simpanMahasiswa(e) {
    e.preventDefault();
    const body = JSON.stringify({
        nim: document.getElementById('nim').value.trim(),
        nama: document.getElementById('nama').value.trim(),
        prodi: document.getElementById('prodi').value.trim(),
    });

    const { status, hasil } = editId
        ? await apiFetch(`/mahasiswa?id=${editId}`, { method: 'PUT', body })
        : await apiFetch('/mahasiswa', { method: 'POST', body });

    laporkan(status, hasil);
    if (hasil.success) {
        tutupForm();
        muatDataMahasiswa();
    }
}

async function hapusMahasiswa(id) {
    if (!confirm('Hapus data mahasiswa ini?')) return;

    const { status, hasil } = await apiFetch(`/mahasiswa?id=${id}`, { method: 'DELETE' });
    laporkan(status, hasil);
    if (hasil.success) {
        muatDataMahasiswa();
    }
}

// Tugas mandiri 1: logout menghapus data login lalu kembali ke login.html
function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    localStorage.removeItem('nama');
    window.location.href = 'login.html';
}

document.addEventListener('DOMContentLoaded', () => {
    if (!token) return;

    document.getElementById('info-user').textContent =
        `Login sebagai: ${nama || '-'} (role: ${role})`;

    document.getElementById('btn-tambah').addEventListener('click', () => {
        editId = null;
        document.getElementById('form-mahasiswa').reset();
        tampilkanForm('Tambah Mahasiswa');
    });
    document.getElementById('btn-batal').addEventListener('click', tutupForm);
    document.getElementById('form-mahasiswa').addEventListener('submit', simpanMahasiswa);
    document.getElementById('btn-logout').addEventListener('click', logout);

    terapkanRole();
    muatDataMahasiswa();
});
