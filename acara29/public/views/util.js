// ===== util.js (Acara 29): fungsi bantu bersama untuk seluruh View =====
// Dimuat lebih dulu dari file JS lain pada setiap halaman.

// --- Langkah 2 & Tugas Mandiri 1: escaping output --------------------------
// Mengubah karakter khusus HTML (< > & " ') menjadi teks biasa sebelum
// disisipkan ke innerHTML, sehingga <b>Coba</b> tampil apa adanya.
function escapeHTML(teks) {
    const div = document.createElement('div');
    div.textContent = (teks === null || teks === undefined) ? '' : String(teks);
    return div.innerHTML.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

// --- Tugas Mandiri 2: retry otomatis satu kali ------------------------------
// fetch() hanya GAGAL (reject) ketika terjadi gangguan jaringan. Jawaban error
// dari server (404, 422, 500, dst.) bukan kegagalan jaringan, jadi tidak diulang.
const JEDA_RETRY_MS = 800;

async function fetchDenganRetry(url, options = {}) {
    try {
        return await fetch(url, options);
    } catch (error) {
        console.warn('Permintaan gagal karena jaringan, mencoba ulang 1 kali...', error);
        await new Promise((selesai) => setTimeout(selesai, JEDA_RETRY_MS));
        return await fetch(url, options);
    }
}

// --- Langkah 3: bantu try-catch-finally -------------------------------------
// Membaca JSON dengan aman. Bila server membalas non-JSON (misalnya halaman
// error HTML), hasilnya tetap objek dengan pesan yang jelas, bukan error senyap.
async function bacaJson(response) {
    try {
        return await response.json();
    } catch (error) {
        return {
            success: false,
            message: `Respons server tidak valid (status ${response.status})`,
            errors: {},
        };
    }
}

// Menonaktifkan tombol selama proses berlangsung, mengembalikannya di finally.
function setTombolProses(tombol, sedangProses, teksProses = 'Memproses...') {
    if (!tombol) return;
    if (sedangProses) {
        tombol.dataset.teksAsli = tombol.textContent;
        tombol.textContent = teksProses;
        tombol.disabled = true;
    } else {
        tombol.textContent = tombol.dataset.teksAsli || tombol.textContent;
        tombol.disabled = false;
    }
}

// Pesan ramah untuk kegagalan jaringan (TypeError dari fetch) atau error lain.
function pesanKesalahan(error) {
    if (error && error.name === 'TypeError') {
        return 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
    }
    return `Terjadi kesalahan: ${error.message}`;
}
