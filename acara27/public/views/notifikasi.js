// ===== Tugas Mandiri 1: notifikasi visual (menggantikan alert) =====

// Gaya notifikasi disisipkan dari sini, tidak perlu file CSS terpisah
const gayaNotif = document.createElement('style');
gayaNotif.textContent = `
    .notif { padding: 10px 14px; margin: 10px 0; border-radius: 4px; border: 1px solid; }
    .notif-sukses { background: #d4edda; color: #155724; border-color: #c3e6cb; }
    .notif-error  { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }
`;
document.head.appendChild(gayaNotif);

// Menampilkan notifikasi di bagian atas halaman, hilang otomatis setelah 5 detik
function tampilNotif(pesan, tipe = 'sukses') {
    let el = document.getElementById('notif');
    if (!el) {
        el = document.createElement('div');
        el.id = 'notif';
        el.setAttribute('role', 'alert');
        document.body.prepend(el);
    }
    el.textContent = pesan;
    el.className = `notif notif-${tipe}`;
    el.hidden = false;

    clearTimeout(el.timer);
    el.timer = setTimeout(() => { el.hidden = true; }, 5000);
}

// Merangkai pesan dari response API: [status] pesan + detail error per field
function formatPesan(status, hasil) {
    let pesan = `[${status}] ${hasil.message || ''}`;
    if (hasil.errors && Object.keys(hasil.errors).length > 0) {
        pesan += ' - ' + Object.values(hasil.errors).join(', ');
    }
    return pesan;
}

// Notifikasi yang harus tampil di halaman berikutnya (misalnya setelah pindah ke dashboard)
function simpanNotifBerikutnya(pesan, tipe) {
    sessionStorage.setItem('notif', JSON.stringify({ pesan, tipe }));
}

function tampilNotifTersimpan() {
    const tersimpan = sessionStorage.getItem('notif');
    if (!tersimpan) return;
    sessionStorage.removeItem('notif');
    const n = JSON.parse(tersimpan);
    tampilNotif(n.pesan, n.tipe);
}