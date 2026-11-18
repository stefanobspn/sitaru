import { test, expect } from '@playwright/test';

test.describe('Landing Page & Navigasi Publik', () => {
  test('dapat memuat halaman beranda dengan elemen utama', async ({ page }) => {
    await page.goto('/');

    // Brand logo
    await expect(page.locator('header')).toContainText('SITARU');

    // Hero section
    await expect(page.locator('h1')).toContainText('Sistem Penerimaan Murid Baru (SPMB)');
    await expect(page.getByRole('link', { name: 'Ajukan Akun' }).first()).toBeVisible();

    // Statistik cards
    await expect(page.locator('#statistik')).toBeVisible();
    await expect(page.locator('#statistik')).toContainText('Total Pendaftar');
    await expect(page.locator('#statistik')).toContainText('Total Kuota');

    // Sambutan Gubernur
    await expect(page.locator('#sambutan')).toContainText('Ahmad Luthfi');

    // Footer
    await expect(page.locator('footer')).toContainText('SITARU');
    await expect(page.locator('footer')).toContainText('Dinas Pendidikan dan Kebudayaan Provinsi Jawa Tengah');
  });

  test('navigasi header mengarahkan ke halaman yang benar', async ({ page }) => {
    await page.goto('/');

    // Klik link Jurnal Seleksi
    await page.getByRole('link', { name: 'Jurnal Seleksi' }).first().click();
    await expect(page).toHaveURL(/.*\/seleksi/);
    await expect(page.locator('h1')).toContainText('Jurnal Hasil Seleksi SPMB');

    // Kembali ke beranda
    await page.goto('/');

    // Klik Login Siswa
    await page.getByRole('link', { name: 'Login Siswa' }).first().click();
    await expect(page).toHaveURL(/.*\/siswa/);
    await expect(page.locator('body')).toContainText('Login Calon Siswa');
  });

  test('menampilkan aura highlight pada semua agenda yang berjalan bersamaan di 5 Juni', async ({ page }) => {
    await page.goto('/?simulated_date=2026-06-05');

    const auraCards = page.locator('#alur .aura');
    await expect(auraCards).toHaveCount(3);
    await expect(auraCards.nth(0)).toContainText('Pendaftaran Akun');
    await expect(auraCards.nth(1)).toContainText('Verifikasi Akun');
    await expect(auraCards.nth(2)).toContainText('Aktivasi Akun');
  });

  test('semua 6 kartu agenda memiliki lebar yang sama persis di desktop', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 });
    await page.goto('/?simulated_date=2026-06-05');

    const cards = page.locator('#alur > *');
    await expect(cards).toHaveCount(6);

    const widths = [];
    for (let i = 0; i < 6; i++) {
      const box = await cards.nth(i).boundingBox();
      widths.push(Math.round(box.width));
    }

    const firstWidth = widths[0];
    for (const width of widths) {
      expect(Math.abs(width - firstWidth)).toBeLessThanOrEqual(1);
    }
  });
});
