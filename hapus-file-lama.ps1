# Hapus file lama yang digantikan paket langkah 10. Jalankan dari folder project.
$hapus = @(
    'tests\Feature\BookingPageTest',
    'tests\Feature\Orderworkflowtest.php',
    'resources\views\dashboard.blade.php',
    '.phpunit.result.cache'
)
foreach ($f in $hapus) {
    if (Test-Path $f) { Remove-Item $f -Force; Write-Host "Dihapus: $f" } else { Write-Host "Tidak ada (dilewati): $f" }
}
