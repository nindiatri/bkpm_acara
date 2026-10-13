<h2>Tambah Mahasiswa</h2>

<form
    method="POST"
    action="/BkpmWebServer/acara14/public/mahasiswa"
>
    <div class="mb-3">
        <label for="nim" class="form-label">NIM</label>
        <input
            type="text"
            id="nim"
            name="nim"
            class="form-control"
            required
        >
    </div>

    <div class="mb-3">
        <label for="nama" class="form-label">Nama</label>
        <input
            type="text"
            id="nama"
            name="nama"
            class="form-control"
            required
        >
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-control"
            required
        >
    </div>

    <div class="mb-3">
        <label for="prodi" class="form-label">Prodi</label>
        <input
            type="text"
            id="prodi"
            name="prodi"
            class="form-control"
            required
        >
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select
            id="status"
            name="status"
            class="form-select"
        >
            <option value="aktif">Aktif</option>
            <option value="cuti">Cuti</option>
            <option value="lulus">Lulus</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="dosen_id" class="form-label">Dosen ID</label>
        <input
            type="number"
            id="dosen_id"
            name="dosen_id"
            class="form-control"
        >
    </div>

    <button type="submit" class="btn btn-primary">
        Simpan
    </button>

    <a
        href="/BkpmWebServer/acara14/public/mahasiswa"
        class="btn btn-secondary"
    >
        Kembali
    </a>
</form>