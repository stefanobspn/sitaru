import { test, expect } from '@playwright/test';

test.describe('Portal Operator Sekolah', () => {
  test('login operator dan melakukan verifikasi berkas pendaftaran', async ({ page }) => {
    await page.goto('/operator');

    await expect(page.locator('body')).toContainText('Login Operator');

    // Login operator
    await page.fill('input[name="nip"]', '198501012010011001');
    await page.fill('input[name="password"]', 'password');
    await page.locator('button[type="submit"]').click();

    // Verifikasi masuk dashboard operator
    await expect(page.locator('body')).toContainText('Operator SMA Negeri 1 Surakarta');

    // Pindah ke menu manajemen
    await page.getByLabel('Teams').click();
    await expect(page.locator('body')).toContainText('Manajemen SPMB');

    // Buka form verifikasi berkas fisik
    await page.locator('a[wire\\:click="openVerificationForm"]').click();
    await expect(page.locator('body')).toContainText('Verifikasi Pendaftaran Siswa');

    // Cari siswa dengan NISN
    await page.fill('input[name="searchNisn"]', '1234567890');
    await page.getByRole('button', { name: 'Cari Data' }).click();

    // Verifikasi data siswa tampil di operator
    await expect(page.locator('body')).toContainText('Budi Pratama');
    await expect(page.locator('body')).toContainText('1234567890');

    // Buka menu Riwayat Verifikasi dan Reset Password
    await page.getByLabel('Teams').click();
    await page.locator('a[wire\\:click="openRiwayatVerifikasi"]').click();
    await expect(page.locator('body')).toContainText('Riwayat Verifikasi dan Akun Calon Siswa');
    await expect(page.locator('table')).toContainText('Budi Pratama');
    await expect(page.locator('table')).toContainText('1234567890');
    await expect(page.locator('table')).toContainText('Akun Aktif');

    // Lakukan reset password untuk akun Budi Pratama
    await page.locator('table tr', { hasText: 'Budi Pratama' }).getByRole('button', { name: 'Reset Password' }).click();
    await expect(page.locator('body')).toContainText('Konfirmasi Reset Password Siswa');

    // Isi password baru
    await page.fill('input[wire\\:model="newPassword"]', 'budiReset123');
    await page.getByRole('button', { name: 'Simpan & Reset Password' }).click();

    // Verifikasi pesan sukses muncul
    await expect(page.locator('body')).toContainText('Reset Password Berhasil!');
    await expect(page.locator('body')).toContainText('budiReset123');

    // Logout operator
    await page.getByLabel('Logout').click();
    await expect(page.locator('body')).toContainText('Login Operator');

    // Buka portal siswa untuk menguji login dengan password baru
    await page.goto('/siswa');
    await expect(page.locator('body')).toContainText('Lupa Password Akun?');
    await expect(page.locator('body')).toContainText('Operator Sekolah yang memverifikasi berkas pendaftaran Anda');

    // Login siswa menggunakan password baru
    await page.fill('input[name="nisn"]', '1234567890');
    await page.fill('input[name="password"]', 'budiReset123');
    await page.locator('button[type="submit"]').click();

    // Berhasil masuk dashboard siswa
    await expect(page.locator('body')).toContainText('Budi Pratama');
    await expect(page.locator('body')).toContainText('Portal Siswa');
  });
});

