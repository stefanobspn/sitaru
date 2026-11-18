import { execSync } from 'child_process';

export default async function globalSetup() {
  console.log('🔄 Menyiapkan database test bersih (migrate:fresh --seed && db:seed DemoSiswaSeeder)...');
  execSync('php artisan migrate:fresh --seed && php artisan db:seed --class=DemoSiswaSeeder', { stdio: 'inherit' });
}
