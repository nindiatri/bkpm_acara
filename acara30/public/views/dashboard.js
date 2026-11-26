// Alamat dasar API otomatis: mendukung akses dengan /public/views maupun langsung /views
const BASE_URL = window.location.origin + window.location.pathname.replace(/\/views(\/[^/]*)?$/, '');

const token = localStorage.getItem('token');
const role = localStorage.getItem('role');
const nama = localStorage.getItem('nama');
let editId = null;

// Langkah 2 & Tugas Mandiri 1 — Fungsi Escaping Output (XSS)
function escapeHTML(teks) {
    if (teks === null || teks === undefined) return '';
    const div = document.createElement('div');
    div.innerText = teks;
    return div.innerHTML;
}

// Tugas Praktik Mandiri 2 — Mekanisme Retry Otomatis 1 Kali
async function fetchWithRetry(url, options = {}, retries = 1) {
    try {
        return await fetch(url, options);
    } catch (err) {
        if (retries > 0) {
            console.warn('[Network Alert] Permintaan gagal karena gangguan jaringan sementara. Mencoba ulang (retry 1x)...');
            return await fetchWithRetry(url, options, retries - 1);
        }
        throw err;
    }
}

// Pembungkus fetch dengan otentikasi Bearer dan auto-retry
async function apiFetch(path, options = {}) {
    const headers = { 'Authorization': `Bearer ${token}` };
    if (!(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
    }

    const response = await fetchWithRetry(`${BASE_URL}${path}`, {
        ...options,
        headers,
    });
    const hasil = await response.json();

    if (response.status === 401) {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        localStorage.removeItem('nama');
        alert('Sesi Anda telah berakhir, silakan login kembali.');
        window.location.href = 'login.html';
    }
    return { status: response.status, hasil };
}

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
    if (teks.toLowerCase() === 'ubah') {
        btn.className = 'btn-ubah';
    } else if (teks.toLowerCase() === 'hapus') {
        btn.className = 'btn-hapus';
    }
    btn.addEventListener('click', aksi);
    return btn;
}

// Render data ke tabel dengan proteksi XSS pada semua field
function renderTabelMahasiswa(data) {
    const tbody = document.querySelector('#tabel-mahasiswa tbody');
    const totalEl = document.getElementById('total-mahasiswa');
    if (totalEl) totalEl.textContent = data ? data.length : 0;

    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Belum ada data mahasiswa</td></tr>';
        return;
    }

    tbody.innerHTML = '';
    data.forEach((mhs) => {
        const tr = document.createElement('tr');

        // Kolom Foto
        const tdFoto = document.createElement('td');
        if (mhs.foto) {
            const img = document.createElement('img');
            img.src = `${BASE_URL}/uploads/${encodeURIComponent(mhs.foto)}`;
            img.alt = `Foto ${escapeHTML(mhs.nama)}`;
            img.width = 50;
            tdFoto.appendChild(img);
        } else {
            tdFoto.textContent = '-';
        }
        tr.appendChild(tdFoto);

        // Kolom NIM, Nama, Prodi (Dengan perlindungan XSS / escapeHTML)
        const tdNim = document.createElement('td');
        tdNim.innerHTML = escapeHTML(mhs.nim);
        tr.appendChild(tdNim);

        const tdNama = document.createElement('td');
        tdNama.innerHTML = escapeHTML(mhs.nama);
        tr.appendChild(tdNama);

        const tdProdi = document.createElement('td');
        tdProdi.innerHTML = escapeHTML(mhs.prodi);
        tr.appendChild(tdProdi);

        // Kolom Aksi
        const aksi = document.createElement('td');
        aksi.className = 'aksi-admin';
        aksi.style.textAlign = 'center';
        aksi.append(
            buatTombol('Ubah', () => { window.location.href = `form_edit.html?id=${mhs.id}`; }),
            ' ',
            buatTombol('Hapus', () => hapusMahasiswa(mhs.id, mhs.nama))
        );
        tr.appendChild(aksi);
        tbody.appendChild(tr);
    });

    terapkanRole();
}

// Langkah 1: Indikator Loading & Error Feedback
async function muatDataMahasiswa() {
    const status = document.getElementById('status');
    const tbody = document.querySelector('#tabel-mahasiswa tbody');

    if (status) status.textContent = '';
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Memuat data...</td></tr>';

    try {
        const { hasil, status: httpStatus } = await apiFetch('/mahasiswa');
        if (httpStatus >= 400) {
            throw new Error(`Server merespons dengan status ${httpStatus}`);
        }
        renderTabelMahasiswa(hasil.data || []);
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:#ef4444;">Gagal memuat data: ${escapeHTML(error.message)}</td></tr>`;
        console.error('Gagal memuat data:', error);
    }
}

// Langkah 3: Pola try-catch-finally & Umpan Balik Hapus Mahasiswa
async function hapusMahasiswa(id, namaMhs) {
    if (!confirm(`Hapus data mahasiswa ${namaMhs || ''}?`)) return;

    try {
        const { status, hasil } = await apiFetch(`/mahasiswa?id=${id}`, { method: 'DELETE' });
        tampilNotif(formatPesan(status, hasil), hasil.success ? 'sukses' : 'error');
        if (hasil.success) {
            muatDataMahasiswa();
        }
    } catch (err) {
        tampilNotif(`Gagal menghapus data: ${escapeHTML(err.message)}`, 'error');
    }
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    localStorage.removeItem('nama');
    window.location.href = 'login.html';
}

document.addEventListener('DOMContentLoaded', () => {
    if (!token) return;

    // Tugas Mandiri 1: Escaping pada info user
    const infoUserEl = document.getElementById('info-user');
    if (infoUserEl) {
        infoUserEl.innerHTML = `Login sebagai: <strong>${escapeHTML(nama || '-')}</strong> (role: ${escapeHTML(role)})`;
    }

    const btnTambah = document.getElementById('btn-tambah');
    if (btnTambah) {
        btnTambah.addEventListener('click', () => {
            window.location.href = 'form_tambah.html';
        });
    }

    const btnLogout = document.getElementById('btn-logout');
    if (btnLogout) {
        btnLogout.addEventListener('click', logout);
    }

    terapkanRole();
    tampilNotifTersimpan();
    muatDataMahasiswa();
});
