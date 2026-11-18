import { test, expect } from '@playwright/test';

test.describe('Aktivasi Akun Siswa', () => {
  test('validasi kesalahan kode aktivasi dan berhasil aktivasi akun baru', async ({ page }) => {
    await page.goto('/aktivasi?simulated_date=2026-06-05');

    await expect(page.locator('h1')).toContainText('Aktivasi Akun Siswa');

    // Coba dengan kode aktivasi salah
    await page.fill('input[name="nisn"]', '0077889900');
    await page.fill('input[name="activation_code"]', 'ACT-WRONGCODE');
    await page.getByRole('button', { name: 'Verifikasi Kode & Lanjutkan' }).click();

    await expect(page.locator('body')).toContainText('Kode aktivasi tidak sesuai');

    // Masukkan kode aktivasi yang benar
    await page.fill('input[name="activation_code"]', 'ACT-DEWI2026');
    await page.getByRole('button', { name: 'Verifikasi Kode & Lanjutkan' }).click();

    // Berpindah ke Step 2: Buat Password
    await expect(page.locator('body')).toContainText('2. Buat Password');
    await expect(page.locator('body')).toContainText('Dewi Sartika');

    // Buat password baru
    await page.fill('input[name="password"]', 'password123');
    await page.fill('input[name="password_confirmation"]', 'password123');
    await page.getByRole('button', { name: 'Simpan & Aktifkan Akun' }).click();

    // Redirect otomatis ke dashboard siswa
    await expect(page).toHaveURL(/.*\/siswa/);
    await expect(page.locator('body')).toContainText('Dewi Sartika');
    await expect(page.locator('body')).toContainText('terverifikasi');
  });

  test('nisn yang sudah pernah diaktivasi tidak dapat diaktivasi kembali', async ({ page }) => {
    await page.goto('/aktivasi?simulated_date=2026-06-05');

    await expect(page.locator('h1')).toContainText('Aktivasi Akun Siswa');

    // Budi Pratama (NISN 1234567890) akunnya sudah aktif
    await page.fill('input[name="nisn"]', '1234567890');
    await page.fill('input[name="activation_code"]', 'ACT-BUDIPRT1');
    await page.getByRole('button', { name: 'Verifikasi Kode & Lanjutkan' }).click();

    // Sistem harus menolak dan menampilkan pesan bahwa akun sudah diaktivasi
    await expect(page.locator('body')).toContainText('Akun untuk NISN ini sudah pernah diaktivasi sebelumnya');

    // Tetap di Step 1
    await expect(page.locator('body')).toContainText('1. Verifikasi Kode');
    await expect(page.getByRole('button', { name: 'Verifikasi Kode & Lanjutkan' })).toBeVisible();
  });

  test('aktivasi akun ditutup pada 15 Juni 2026', async ({ page }) => {
    await page.goto('/aktivasi?simulated_date=2026-06-15');

    await expect(page.locator('body')).toContainText('Telah Ditutup (Selesai 13 Juni 2026)');
    await expect(page.locator('button[type="submit"]')).toBeDisabled();
  });
});

