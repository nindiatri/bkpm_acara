// Alamat dasar API dihitung otomatis dari lokasi halaman: .../public/views/xxx.html -> .../public
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, '');

const token = localStorage.getItem('token');
const role = localStorage.getItem('role');
const nama = localStorage.getItem('nama');
let editId = null;

// Pembungkus fetch: otomatis mengirim token Bearer (Langkah 6 membutuhkan ini)
async function apiFetch(path, options = {}) {
    // Acara 26: untuk FormData, Content-Type diisi otomatis oleh browser (beserta boundary)
    const headers = { 'Authorization': `Bearer ${token}` };
    if (!(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
    }

    const response = await fetch(`${BASE_URL}${path}`, {
        ...options,
        headers,
    });
    const hasil = await response.json();

    // Token tidak valid atau kedaluwarsa (berlaku 5 menit): paksa login ulang
    if (response.status === 401) {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        localStorage.removeItem('nama');
        alert('Data mahasiswa berhasil diperbaharui');
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

            // Acara 26: kolom foto (file di public/uploads/)
            const tdFoto = document.createElement('td');
            if (mhs.foto) {
                const img = document.createElement('img');
                img.src = `${BASE_URL}/uploads/${encodeURIComponent(mhs.foto)}`;
                img.alt = `Foto ${mhs.nama}`;
                img.width = 60;
                tdFoto.appendChild(img);
            } else {
                tdFoto.textContent = '-';
            }
            tr.appendChild(tdFoto);

            [mhs.nim, mhs.nama, mhs.prodi].forEach((nilai) => {
                const td = document.createElement('td');
                td.textContent = nilai;
                tr.appendChild(td);
            });

            const aksi = document.createElement('td');
            aksi.className = 'aksi-admin';
            aksi.append(
            buatTombol('Ubah', () => { window.location.href = `form_edit.html?id=${mhs.id}`; }),                ' ',
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
    document.getElementById('info-foto-lama').hidden = true;
    document.getElementById('form-mahasiswa').hidden = true;
}

function mulaiUbah(mhs) {
    editId = mhs.id;
    document.getElementById('nim').value = mhs.nim;
    document.getElementById('nama').value = mhs.nama;
    document.getElementById('prodi').value = mhs.prodi;
    document.getElementById('foto').value = '';
    document.getElementById('info-foto-lama').hidden = false;
    tampilkanForm('Ubah Mahasiswa');
}

// Pesan hasil aksi; kode status ikut ditampilkan agar 403 terlihat jelas
function laporkan(status, hasil) {
    tampilNotif(formatPesan(status, hasil), hasil.success ? 'sukses' : 'error');
}

async function simpanMahasiswa(e) {
    e.preventDefault();

    // Acara 26 Langkah 5: multipart tidak bisa dikirim sebagai JSON, gunakan FormData
    const formData = new FormData();
    formData.append('nim', document.getElementById('nim').value.trim());
    formData.append('nama', document.getElementById('nama').value.trim());
    formData.append('prodi', document.getElementById('prodi').value.trim());

    const inputFoto = document.getElementById('foto');
    if (inputFoto.files[0]) {
        formData.append('foto', inputFoto.files[0]);
    }

    // Update memakai POST + ?_method=PUT karena PHP tidak membaca multipart pada PUT
    const { status, hasil } = editId
        ? await apiFetch(`/mahasiswa?id=${editId}&_method=PUT`, { method: 'POST', body: formData })
        : await apiFetch('/mahasiswa', { method: 'POST', body: formData });

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
    window.location.href = 'form_tambah.html';
});
    document.getElementById('btn-batal').addEventListener('click', tutupForm);
    document.getElementById('form-mahasiswa').addEventListener('submit', simpanMahasiswa);
    document.getElementById('btn-logout').addEventListener('click', logout);

    terapkanRole();
    tampilNotifTersimpan();
    muatDataMahasiswa();
});
