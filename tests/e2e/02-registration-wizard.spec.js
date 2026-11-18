import { test, expect } from '@playwright/test';
import path from 'path';

test.describe('Wizard Pendaftaran Calon Siswa (Step 1 s.d. 6)', () => {
  test('alur pendaftaran siswa lengkap berhasil hingga cetak bukti', async ({ page }) => {
    await page.goto('/daftar?simulated_date=2026-06-05');

    // ── STEP 1: Biodata Siswa ───────────────────────────────────────────────
    await expect(page.locator('body')).toContainText('Biodata');
    await page.fill('input[name="nisn"]', '0099887766');
    await page.fill('input[name="nik"]', '3372010101090099');
    await page.selectOption('select[name="tahun_lulus"]', '2026');
    await page.selectOption('select[name="wilayah_sekolah_asal"]', 'Dalam Provinsi');

    await page.getByRole('button', { name: 'Cari' }).click();

    // Verifikasi data ditemukan di Dapodik simulation
    await expect(page.locator('body')).toContainText('Rian Hidayat');
    await expect(page.locator('body')).toContainText('SMP Negeri 1 Surakarta');
    await page.getByRole('button', { name: 'Konfirmasi' }).click();

    // ── STEP 2: Kontak & Domisili ───────────────────────────────────────────
    await expect(page.locator('body')).toContainText('Kontak & Domisili');
    await page.fill('input[name="no_whatsapp"]', '081234567899');
    await page.fill('input[name="email"]', 'rian.hidayat@example.test');
    await page.selectOption('select[name="status_domisili"]', 'Sesuai KK');

    await page.fill('input[name="tgl_cetak_kk"]', '2024-01-10');
    await page.getByRole('button', { name: 'Lanjut' }).click();

    // Pinpoint koordinat
    await expect(page.locator('body')).toContainText('Pinpoint Koordinat Rumah');
    await page.fill('input[name="koordinat"]', '-7.5561, 110.8317');
    await page.getByRole('button', { name: 'Cek' }).click();

    // Tombol konfirmasi muncul setelah peta di-render
    const btnKonfirmasi = page.getByRole('button', { name: 'Konfirmasi' });
    await expect(btnKonfirmasi).toBeVisible();
    await btnKonfirmasi.click();

    // ── STEP 3: Akademik & Nilai Rapor ──────────────────────────────────────
    await expect(page.locator('body')).toContainText('Akademik & Prestasi');
    await expect(page.locator('body')).toContainText('Nilai Rata-Rata Rapor');

    // Isi nilai rapor untuk semua input yang ada
    const inputsNilai = page.locator('table input[inputmode="decimal"]');
    const countInputs = await inputsNilai.count();
    for (let i = 0; i < countInputs; i++) {
      await inputsNilai.nth(i).fill('88');
    }

    await page.getByRole('button', { name: 'Lanjutkan' }).click();

    // Substep 2: Prestasi & Organisasi
    await expect(page.locator('body')).toContainText('Status Prestasi');
    await page.selectOption('select[name="status_prestasi"]', 'Tidak Memiliki Kejuaraan / Prestasi');
    await page.selectOption('select[name="status_khusus"]', 'Tidak Ada Status Khusus');
    await page.selectOption('select[name="status_kepengurusan_organisasi"]', 'Tidak Pernah');

    await page.getByRole('button', { name: 'Lanjutkan' }).click();

    // ── STEP 4: Unggah Berkas ───────────────────────────────────────────────
    await expect(page.locator('body')).toContainText('Unggah Berkas Persyaratan');

    const dummyPdfPath = path.resolve('tests/e2e/fixtures/dummy.pdf');

    // Input berkas wajib
    await page.locator('input[type="file"]').first().setInputFiles(dummyPdfPath);
    await page.locator('input[type="file"]').nth(1).setInputFiles(dummyPdfPath);
    await page.locator('input[type="file"]').nth(2).setInputFiles(dummyPdfPath);

    // Tunggu Livewire selesai upload (loading indicator hilang)
    await page.waitForTimeout(1000);
    await page.getByRole('button', { name: 'Lanjutkan' }).click();

    // ── STEP 5: Konfirmasi Ringkasan ────────────────────────────────────────
    await expect(page.locator('body')).toContainText('Konfirmasi Data');
    await expect(page.locator('body')).toContainText('Rian Hidayat');

    // Centang persetujuan
    await page.locator('input[type="checkbox"]').check();
    await page.getByRole('button', { name: 'Daftar Sekarang' }).click();

    // ── STEP 6: Bukti Pendaftaran & Sukses ───────────────────────────────────
    await expect(page.locator('body')).toContainText('Sukses!');
    await expect(page.locator('body')).toContainText('Nomor Pendaftaran:');
    await expect(page.locator('body')).toContainText('REG-2026-');
    await expect(page.getByRole('button', { name: 'Cetak Bukti' })).toBeVisible();
  });

  test('nisn yang sudah pernah didaftarkan tidak dapat didaftarkan kembali', async ({ page }) => {
    await page.goto('/daftar?simulated_date=2026-06-05');

    await expect(page.locator('body')).toContainText('Biodata');

    // Siswa Budi Pratama (NISN 1234567890) sudah terdaftar di database
    await page.fill('input[name="nisn"]', '1234567890');
    await page.fill('input[name="nik"]', '3372010101080001');
    await page.selectOption('select[name="tahun_lulus"]', '2026');
    await page.selectOption('select[name="wilayah_sekolah_asal"]', 'Dalam Provinsi');

    await page.getByRole('button', { name: 'Cari' }).click();

    // Sistem harus menolak dan menampilkan pesan bahwa siswa dengan NISN ini sudah terdaftar
    await expect(page.locator('body')).toContainText('Siswa dengan NISN ini sudah pernah didaftarkan');

    // Pastikan tetap berada di Step 1 dan tidak lanjut ke substep konfirmasi
    await expect(page.locator('body')).toContainText('Biodata');
    await expect(page.getByRole('button', { name: 'Cari' })).toBeVisible();
  });

  test('pengajuan akun ditutup pada 15 Juni 2026', async ({ page }) => {
    await page.goto('/daftar?simulated_date=2026-06-15');

    await expect(page.locator('body')).toContainText('Tahap Pengajuan Akun Telah Ditutup');
    await expect(page.locator('body')).toContainText('telah ditutup pada tanggal 12 Juni 2026');
  });
});

