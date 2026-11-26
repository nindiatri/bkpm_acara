// ===== Tugas Mandiri 2: validasi sisi View (lapisan tambahan di luar validasi Model) =====

// Mengembalikan daftar pesan error; kosong berarti data siap dikirim
function validasiMahasiswa(nimVal, namaVal, prodiVal, file) {
    const errors = [];

    if (nimVal === '') errors.push('NIM wajib diisi');
    else if (!/^\d+$/.test(nimVal)) errors.push('NIM harus berupa angka');

    if (namaVal === '') errors.push('Nama wajib diisi');
    if (prodiVal === '') errors.push('Program studi wajib diisi');

    // Foto bersifat opsional, tetapi bila dipilih harus JPG/PNG dan maksimal 2 MB
    if (file) {
        const ekstensi = file.name.split('.').pop().toLowerCase();
        if (!['jpg', 'jpeg', 'png'].includes(ekstensi)) {
            errors.push('Foto harus berformat JPG atau PNG');
        }
        if (file.size > 2 * 1024 * 1024) {
            errors.push('Ukuran foto maksimal 2 MB');
        }
    }
    return errors;
}