import { test, expect } from '@playwright/test';

test.describe('Portal & Dashboard Siswa', () => {
  test('login siswa dan memilih sekolah SMK dengan jurusan', async ({ page }) => {
    await page.goto('/siswa?simulated_date=2026-06-16');

    await expect(page.locator('body')).toContainText('Login Calon Siswa');

    // Login siswa Budi Pratama
    await page.fill('input[name="nisn"]', '1234567890');
    await page.fill('input[name="password"]', 'password');
    await page.getByRole('button', { name: /Masuk/i }).click();

    // Verifikasi berhasil login ke dashboard
    await expect(page.locator('body')).toContainText('Selamat Datang, Budi Pratama');
    await expect(page.locator('body')).toContainText('terverifikasi');

    // Buka form pemilihan sekolah
    const btnPilih = page.getByRole('button', { name: /Ubah Pilihan|Pilih Sekolah Tujuan/i }).first();
    await btnPilih.click();

    await expect(page.locator('body')).toContainText('Pendaftaran & Pemilihan Sekolah Tujuan');

    // Pilih Pilihan 1: SMK Negeri 2 Surakarta
    const selectSekolah1 = page.locator('select[wire\\:model\\.live="pilihan_sekolah_1_id"]');
    const smkOption = selectSekolah1.locator('option', { hasText: 'SMK Negeri 2 Surakarta' });
    const smkValue = await smkOption.getAttribute('value');
    await selectSekolah1.selectOption(smkValue);

    // Verifikasi dropdown jurusan SMK otomatis muncul
    const selectJurusan1 = page.locator('select[wire\\:model="pilihan_jurusan_1_id"]');
    await expect(selectJurusan1).toBeVisible();

    // Pilih jurusan RPL
    const rplOption = selectJurusan1.locator('option', { hasText: 'Rekayasa Perangkat Lunak' });
    const rplValue = await rplOption.getAttribute('value');
    await selectJurusan1.selectOption(rplValue);
    await page.waitForTimeout(300);

    // Simpan pilihan
    await page.locator('button[type="submit"]').click();

    // Verifikasi notifikasi sukses dan kartu pilihan ter-update
    await expect(page.locator('body')).toContainText('Pilihan sekolah berhasil disimpan');
    await expect(page.locator('body')).toContainText('SMK Negeri 2 Surakarta');
    await expect(page.locator('body')).toContainText('Rekayasa Perangkat Lunak');

    // Logout
    await page.getByRole('button', { name: 'Logout' }).click();
    await expect(page.locator('body')).toContainText('Login Calon Siswa');
  });

  test('pilihan 2 wajib memiliki jenis sekolah yang sama dengan pilihan 1', async ({ page }) => {
    await page.goto('/siswa?simulated_date=2026-06-16');

    // Login siswa Budi Pratama
    await page.fill('input[name="nisn"]', '1234567890');
    await page.fill('input[name="password"]', 'password');
    await page.getByRole('button', { name: /Masuk/i }).click();

    // Buka form pemilihan sekolah
    const btnPilih = page.getByRole('button', { name: /Ubah Pilihan|Pilih Sekolah Tujuan/i }).first();
    await btnPilih.click();

    const selectSekolah1 = page.locator('select[wire\\:model\\.live="pilihan_sekolah_1_id"]');
    const selectSekolah2 = page.locator('select[wire\\:model\\.live="pilihan_sekolah_2_id"]');

    // 1. Pilih Pilihan 1: SMA Negeri 1 Surakarta
    const sma1Option = selectSekolah1.locator('option', { hasText: 'SMA Negeri 1 Surakarta' });
    await selectSekolah1.selectOption(await sma1Option.getAttribute('value'));

    // Pilihan 2 harus menampilkan sekolah SMA dan TIDAK menampilkan sekolah SMK
    await expect(page.locator('body')).toContainText('Pilihan 2 wajib memiliki jenis sekolah yang sama dengan Pilihan 1 (SMA)');
    const optionsTextSma = await selectSekolah2.innerText();
    expect(optionsTextSma).toContain('SMA Negeri 2 Surakarta');
    expect(optionsTextSma).not.toContain('SMK Negeri 2 Surakarta');

    // 2. Ubah Pilihan 1 ke SMK Negeri 2 Surakarta
    const smkOption = selectSekolah1.locator('option', { hasText: 'SMK Negeri 2 Surakarta' });
    await selectSekolah1.selectOption(await smkOption.getAttribute('value'));

    // Pilihan 2 harus terfilter untuk SMK dan TIDAK menampilkan sekolah SMA
    await expect(page.locator('body')).toContainText('Pilihan 2 wajib memiliki jenis sekolah yang sama dengan Pilihan 1 (SMK)');
    const optionsTextSmk = await selectSekolah2.innerText();
    expect(optionsTextSmk).not.toContain('SMA Negeri 1 Surakarta');
    expect(optionsTextSmk).not.toContain('SMA Negeri 2 Surakarta');
  });

  test('tombol pemilihan sekolah terkunci sebelum 15 Juni 2026', async ({ page }) => {
    await page.goto('/siswa?simulated_date=2026-06-05');

    // Login siswa Budi Pratama
    await page.fill('input[name="nisn"]', '1234567890');
    await page.fill('input[name="password"]', 'password');
    await page.getByRole('button', { name: /Masuk/i }).click();

    // Verifikasi badge bahwa jadwal belum dibuka
    await expect(page.locator('body')).toContainText('Belum Dibuka (Mulai 15 Juni 2026)');
    await expect(page.locator('body')).toContainText('Dibuka 15 Juni');
  });

  test('pemilihan sekolah terkunci dan ditutup mulai 19 Juni 2026', async ({ page }) => {
    await page.goto('/siswa?simulated_date=2026-06-19');

    // Login siswa Budi Pratama
    await page.fill('input[name="nisn"]', '1234567890');
    await page.fill('input[name="password"]', 'password');
    await page.getByRole('button', { name: /Masuk/i }).click();

    // Verifikasi badge bahwa jadwal telah ditutup
    await expect(page.locator('body')).toContainText('Telah Ditutup (Selesai 18 Juni 2026)');
    await expect(page.locator('body')).toContainText('Pendaftaran Ditutup');
    await expect(page.getByRole('button', { name: /Ubah Pilihan/i })).not.toBeVisible();
  });
});
