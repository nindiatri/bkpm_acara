<h2>Edit Mahasiswa</h2>

<form
    method="POST"
    action="/BkpmWebServer/acara13/public/mahasiswa/<?= (int) $mahasiswa['id'] ?>"
>
    <div class="mb-3">
        <label for="nim" class="form-label">NIM</label>
        <input
            type="text"
            name="nim"
            id="nim"
            class="form-control"
            value="<?= htmlspecialchars($mahasiswa['nim']) ?>"
            required
        >
    </div>

    <div class="mb-3">
        <label for="nama" class="form-label">Nama</label>
        <input
            type="text"
            name="nama"
            id="nama"
            class="form-control"
            value="<?= htmlspecialchars($mahasiswa['nama']) ?>"
            required
        >
    </div>

    <div class="mb-3">
        <label for="prodi" class="form-label">Program Studi</label>
        <input
            type="text"
            name="prodi"
            id="prodi"
            class="form-control"
            value="<?= htmlspecialchars($mahasiswa['prodi']) ?>"
            required
        >
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select
            name="status"
            id="status"
            class="form-control"
            required
        >
            <option
                value="aktif"
                <?= $mahasiswa['status'] === 'aktif' ? 'selected' : '' ?>
            >
                Aktif
            </option>

            <option
                value="cuti"
                <?= $mahasiswa['status'] === 'cuti' ? 'selected' : '' ?>
            >
                Cuti
            </option>

            <option
                value="lulus"
                <?= $mahasiswa['status'] === 'lulus' ? 'selected' : '' ?>
            >
                Lulus
            </option>
        </select>
    </div>

    <div class="mb-3">
        <label for="dosen_id" class="form-label">ID Dosen (opsional)</label>
        <input
            type="number"
            name="dosen_id"
            id="dosen_id"
            class="form-control"
            value="<?= htmlspecialchars((string) ($mahasiswa['dosen_id'] ?? '')) ?>"
        >
    </div>

    <button type="submit" class="btn btn-primary">
        Update
    </button>

    <a
        href="/BkpmWebServer/acara13/public/mahasiswa"
        class="btn btn-secondary"
    >
        Kembali
    </a>
</form>