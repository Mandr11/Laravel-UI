<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_creation_with_image_upload()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('patient.jpg');

        $response = $this->post(route('patients.store'), [
            'nama' => 'Test Patient',
            'nik' => '1234567890123456',
            'tanggal_lahir' => '2000-01-01',
            'pekerjaan_id' => 1,
            'provinsi' => 'Test Province',
            'kota_kabupaten' => 'Test City',
            'kecamatan' => 'Test District',
            'desa' => 'Test Village',
            'jenis_kelamin' => 'Laki-laki',
            'foto_pasien' => $file,
            'riwayat_penyakit' => [
                ['nama_penyakit' => 'Flu', 'tahun' => 2020]
            ],
            'asuransi' => [
                ['jenis_asuransi' => 'BPJS Kesehatan', 'nomor_asuransi' => '12345']
            ],
        ]);

        $response->assertRedirect(route('patients.index'));

        $patient = Patient::first();

        Storage::disk('public')->assertExists('patients/' . $patient->foto_pasien);
    }

    public function test_patient_update_with_image_upload()
    {
        Storage::fake('public');

        $patient = Patient::factory()->create();

        $file = UploadedFile::fake()->image('new_patient.jpg');

        $response = $this->put(route('patients.update', $patient), [
            'nama' => $patient->nama,
            'nik' => $patient->nik,
            'tanggal_lahir' => $patient->tanggal_lahir->format('Y-m-d'),
            'pekerjaan_id' => $patient->pekerjaan_id,
            'provinsi' => $patient->provinsi,
            'kota_kabupaten' => $patient->kota_kabupaten,
            'kecamatan' => $patient->kecamatan,
            'desa' => $patient->desa,
            'jenis_kelamin' => $patient->jenis_kelamin,
            'foto_pasien' => $file,
        ]);

        $response->assertRedirect(route('patients.index'));

        $patient->refresh();

        Storage::disk('public')->assertExists('patients/' . $patient->foto_pasien);
    }

    public function test_patient_index_page_shows_image_url()
    {
        $patient = Patient::factory()->create([
            'foto_pasien' => 'test_image.jpg',
        ]);

        $response = $this->get(route('patients.index'));

        $response->assertStatus(200);
        $response->assertSee('storage/patients/test_image.jpg');
    }

    public function test_patient_show_page_shows_image_url()
    {
        $patient = Patient::factory()->create([
            'foto_pasien' => 'test_image.jpg',
        ]);

        $response = $this->get(route('patients.show', $patient));

        $response->assertStatus(200);
        $response->assertSee('storage/patients/test_image.jpg');
    }
}
