import { test, expect } from '@playwright/test';

test.describe('Admin Manajemen Sekolah & Jurnal Seleksi Publik', () => {
  test('admin dapat menambah sekolah SMK dengan jurusan dinamis dan memfinalisasi seleksi', async ({ page }) => {
    // Tangani konfirmasi dialog otomatis
    page.on('dialog', dialog => dialog.accept());

    await page.goto('/admin');

    await expect(page.locator('body')).toContainText('Login Admin');

    // Login admin
    await page.fill('input[name="email"]', 'admin@sitaru.test');
    await page.fill('input[name="password"]', 'password');
    await page.locator('button[type="submit"]').click();

    // Verifikasi dashboard admin
    await expect(page.locator('body')).toContainText('Beranda');
    await expect(page.locator('body')).toContainText('Total Sekolah');

    // Pindah ke menu manajemen
    await page.locator('a[aria-label="Teams"]').click();
    await expect(page.locator('body')).toContainText('Manajemen SPMB');

    // Buka form tambah sekolah
    await page.locator('button[wire\\:click="openCreateSchoolForm"]').click();
    await expect(page.locator('body')).toContainText('Tambah Sekolah Baru');

    // Isi data sekolah SMK baru
    await page.fill('input[name="npsn_sekolah"]', '20327995');
    await page.fill('input[name="nama_sekolah"]', 'SMK Negeri 9 Surakarta');
    await page.selectOption('select[name="jenis_sekolah"]', 'SMK');

    // Form jurusan dinamis muncul untuk SMK
    await expect(page.locator('body')).toContainText('Daftar Jurusan SMK');
    await page.getByRole('button', { name: '+ Tambah Jurusan' }).click();

    // Isi 3 jurusan
    await page.fill('input[name="jurusans_0"]', 'Broadcasting dan Perfilman');
    await page.fill('input[name="jurusans_1"]', 'Animasi Digital');
    await page.fill('input[name="jurusans_2"]', 'Desain Komunikasi Visual');

    // Operator dan kuota
    await page.fill('input[name="nip_operator"]', '199101012022011009');
    await page.fill('input[name="kuota_awal"]', '75');

    // Submit form tambah sekolah
    await page.locator('form button[type="submit"]').click();

    // Verifikasi sekolah dan jurusannya tampil di tabel
    await expect(page.locator('body')).toContainText('SMK Negeri 9 Surakarta');
    await expect(page.locator('body')).toContainText('Broadcasting dan Perfilman');
    await expect(page.locator('body')).toContainText('Animasi Digital');

    // Pindah kembali ke menu Beranda untuk memfinalisasi seleksi
    await page.locator('a[aria-label="General"]').click();

    // Klik tombol umumkan hasil seleksi
    const btnFinalisasi = page.getByRole('button', { name: /Umumkan & Kunci Hasil Seleksi/i });
    if (await btnFinalisasi.isVisible()) {
      await btnFinalisasi.click();
      await expect(page.locator('body')).toContainText('Hasil seleksi berhasil difinalisasi');
    }

    // ── UJI JURNAL PUBLIK SELEKSI (/seleksi) ────────────────────────────────
    await page.goto('/seleksi');
    await expect(page.locator('h1')).toContainText('Jurnal Hasil Seleksi SPMB');

    // Ganti pilihan sekolah ke SMK Negeri 2 Surakarta
    const selectSekolahPublik = page.locator('select[wire\\:model\\.live="selectedSekolahId"]');
    const smkOption = selectSekolahPublik.locator('option', { hasText: 'SMK Negeri 2 Surakarta' });
    const smkVal = await smkOption.getAttribute('value');
    await selectSekolahPublik.selectOption(smkVal);
    await page.waitForTimeout(500);

    // Kolom jurusan harus muncul di tabel SMK
    await expect(page.locator('table thead')).toContainText('Jurusan');

    // Filter jurusan dropdown harus muncul untuk SMK
    const selectJurusanFilter = page.locator('select[wire\\:model\\.live="selectedJurusanId"]');
    await expect(selectJurusanFilter).toBeVisible();

    // 1. Filter jurusan ke Rekayasa Perangkat Lunak (jurusan Pandu Wicaksono)
    const rplOption = selectJurusanFilter.locator('option', { hasText: 'Rekayasa Perangkat Lunak' });
    await selectJurusanFilter.selectOption(await rplOption.getAttribute('value'));
    await page.waitForTimeout(500);

    // Pandu tampil di tabel, Qori tidak
    await expect(page.locator('table')).toContainText('Pandu Wicaksono');
    await expect(page.locator('table')).toContainText('Rekayasa Perangkat Lunak');
    await expect(page.locator('table')).not.toContainText('Qori Amanda');

    // 2. Filter jurusan ke Teknik Komputer dan Jaringan (jurusan Qori Amanda)
    const tkjOption = selectJurusanFilter.locator('option', { hasText: 'Teknik Komputer dan Jaringan' });
    await selectJurusanFilter.selectOption(await tkjOption.getAttribute('value'));
    await page.waitForTimeout(500);

    await expect(page.locator('table')).toContainText('Qori Amanda');
    await expect(page.locator('table')).toContainText('Teknik Komputer dan Jaringan');
    await expect(page.locator('table')).not.toContainText('Pandu Wicaksono');

    // 3. Reset filter ke Semua Jurusan
    await selectJurusanFilter.selectOption('');
    await page.waitForTimeout(500);
    await expect(page.locator('table')).toContainText('Pandu Wicaksono');
    await expect(page.locator('table')).toContainText('Qori Amanda');

    // Cek fitur live search jurnal
    await page.fill('input[placeholder*="Nama"]', 'Pandu');
    await page.waitForTimeout(500);
    await expect(page.locator('table')).toContainText('Pandu Wicaksono');
    await expect(page.locator('table')).not.toContainText('Qori Amanda');
  });
});

