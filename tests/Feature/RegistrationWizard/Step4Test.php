<?php

use App\Enums\StatusKepengurusanOrganisasi;
use App\Enums\StatusPrestasi;
use App\Livewire\RegistrationWizard;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('local');
});

function fakePdf(string $name = 'dokumen.pdf', int $kilobytes = 500): UploadedFile
{
    return UploadedFile::fake()->create($name, $kilobytes, 'application/pdf');
}

function step4Wizard(
    string $statusPrestasi = StatusPrestasi::TidakMemiliki->value,
    string $statusOrganisasi = StatusKepengurusanOrganisasi::TidakPernah->value
): Testable {
    return Livewire::test(RegistrationWizard::class)
        ->set('currentStep', 4)
        ->set('currentSubStep', 1)
        ->set('akademikPrestasi.statusPrestasi', $statusPrestasi)
        ->set('akademikPrestasi.statusKepengurusanOrganisasi', $statusOrganisasi);
}

it('renders mandatory file uploaders and hides conditional ones by default', function () {
    step4Wizard()
        ->assertSee('Surat Pernyataan Kebenaran')
        ->assertSee('Kartu Keluarga')
        ->assertSee('Surat Keterangan Nilai Rapor Semester 1 s.d 5')
        ->assertDontSee('Piagam Prestasi / Kejuaraan')
        ->assertDontSee('Surat Keterangan Pengurus Organisasi');
});

it('renders piagam prestasi uploader only when applicant has achievements', function () {
    step4Wizard(statusPrestasi: StatusPrestasi::Memiliki->value)
        ->assertSee('Piagam Prestasi / Kejuaraan');
});

it('renders sk organisasi uploader only when applicant was an organization leader', function () {
    step4Wizard(statusOrganisasi: StatusKepengurusanOrganisasi::PernahMengurus->value)
        ->assertSee('Surat Keterangan Pengurus Organisasi');
});

it('validates mandatory files are required', function () {
    step4Wizard()
        ->call('submitBerkas')
        ->assertHasErrors([
            'berkas.suratPernyataanKebenaran' => 'required',
            'berkas.kartuKeluarga' => 'required',
            'berkas.suratKeteranganNilaiRapor' => 'required',
        ])
        ->assertSet('currentStep', 4);
});

it('validates uploaded files must be pdf format', function (string $filename, string $mime) {
    $nonPdfFile = UploadedFile::fake()->create($filename, 500, $mime);

    step4Wizard()
        ->set('berkas.suratPernyataanKebenaran', $nonPdfFile)
        ->call('submitBerkas')
        ->assertHasErrors(['berkas.suratPernyataanKebenaran' => 'mimes'])
        ->assertSet('currentStep', 4);
})->with([
    'png image'  => ['foto.png', 'image/png'],
    'jpg image'  => ['foto.jpg', 'image/jpeg'],
    'word docx'  => ['dokumen.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
]);

it('validates uploaded files cannot exceed 1mb (1024 kb)', function () {
    $oversizedFile = UploadedFile::fake()->create('besar.pdf', 2048, 'application/pdf'); // 2 MB

    step4Wizard()
        ->set('berkas.suratPernyataanKebenaran', $oversizedFile)
        ->call('submitBerkas')
        ->assertHasErrors(['berkas.suratPernyataanKebenaran' => 'max'])
        ->assertSet('currentStep', 4);
});

it('requires piagam prestasi when applicant has achievements', function () {
    step4Wizard(statusPrestasi: StatusPrestasi::Memiliki->value)
        ->set('berkas.suratPernyataanKebenaran', fakePdf('pernyataan.pdf'))
        ->set('berkas.kartuKeluarga', fakePdf('kk.pdf'))
        ->set('berkas.suratKeteranganNilaiRapor', fakePdf('rapor.pdf'))
        ->call('submitBerkas')
        ->assertHasErrors(['berkas.piagamPrestasi' => 'required'])
        ->assertSet('currentStep', 4);
});

it('requires sk organisasi when applicant was an organization leader', function () {
    step4Wizard(statusOrganisasi: StatusKepengurusanOrganisasi::PernahMengurus->value)
        ->set('berkas.suratPernyataanKebenaran', fakePdf('pernyataan.pdf'))
        ->set('berkas.kartuKeluarga', fakePdf('kk.pdf'))
        ->set('berkas.suratKeteranganNilaiRapor', fakePdf('rapor.pdf'))
        ->call('submitBerkas')
        ->assertHasErrors(['berkas.skOrganisasi' => 'required'])
        ->assertSet('currentStep', 4);
});


it('advances to step 5 when regular applicant uploads all mandatory files', function () {
    step4Wizard()
        ->set('berkas.suratPernyataanKebenaran', fakePdf('pernyataan.pdf'))
        ->set('berkas.kartuKeluarga', fakePdf('kk.pdf'))
        ->set('berkas.suratKeteranganNilaiRapor', fakePdf('rapor.pdf'))
        ->call('submitBerkas')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 5)
        ->assertSet('currentSubStep', 1);
});

it('advances to step 5 when applicant with achievements and organization uploads all required files', function () {
    step4Wizard(
        statusPrestasi: StatusPrestasi::Memiliki->value,
        statusOrganisasi: StatusKepengurusanOrganisasi::PernahMengurus->value
    )
        ->set('berkas.suratPernyataanKebenaran', fakePdf('pernyataan.pdf'))
        ->set('berkas.kartuKeluarga', fakePdf('kk.pdf'))
        ->set('berkas.suratKeteranganNilaiRapor', fakePdf('rapor.pdf'))
        ->set('berkas.piagamPrestasi', fakePdf('piagam.pdf'))
        ->set('berkas.skOrganisasi', fakePdf('sk_organisasi.pdf'))
        ->call('submitBerkas')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 5)
        ->assertSet('currentSubStep', 1);
});
