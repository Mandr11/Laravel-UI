<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Pekerjaan;
use App\Models\RiwayatPenyakit;
use App\Models\Asuransi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::with(['pekerjaan', 'riwayatPenyakits', 'asuransis'])->paginate(10);
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        $pekerjaans = Pekerjaan::all();
        $jenisAsuransi = ['BPJS Kesehatan', 'Asuransi Swasta', 'Taspen', 'Askes', 'Jamsostek'];
        
        return view('patients.create', compact('pekerjaans', 'jenisAsuransi'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|unique:patients,nik|digits:16',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'required|date',
            'pekerjaan_id' => 'required|exists:pekerjaans,id',
            'provinsi' => 'required|string|max:255',
            'kota_kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'foto_pasien' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'riwayat_penyakit.*.nama_penyakit' => 'required|string|max:255',
            'riwayat_penyakit.*.tahun' => 'required|integer|min:1900|max:' . date('Y'),
            'asuransi.*.jenis_asuransi' => 'required|string|max:255',
            'asuransi.*.nomor_asuransi' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $patientData = $request->except(['riwayat_penyakit', 'asuransi', 'foto_pasien']);
        
        // Handle file upload
        if ($request->hasFile('foto_pasien')) {
            $file = $request->file('foto_pasien');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('patients', $filename, 'public');
            $patientData['foto_pasien'] = $filename;
        }

        $patient = Patient::create($patientData);

        // Save riwayat penyakit
        if ($request->has('riwayat_penyakit')) {
            foreach ($request->riwayat_penyakit as $riwayat) {
                if (!empty($riwayat['nama_penyakit']) && !empty($riwayat['tahun'])) {
                    RiwayatPenyakit::create([
                        'patient_id' => $patient->id,
                        'nama_penyakit' => $riwayat['nama_penyakit'],
                        'tahun' => $riwayat['tahun']
                    ]);
                }
            }
        }

        // Save asuransi
        if ($request->has('asuransi')) {
            foreach ($request->asuransi as $asuransi) {
                if (!empty($asuransi['jenis_asuransi']) && !empty($asuransi['nomor_asuransi'])) {
                    Asuransi::create([
                        'patient_id' => $patient->id,
                        'jenis_asuransi' => $asuransi['jenis_asuransi'],
                        'nomor_asuransi' => $asuransi['nomor_asuransi']
                    ]);
                }
            }
        }

        return redirect()->route('patients.index')->with('success', 'Data pasien berhasil ditambahkan!');

    }

    public function show(Patient $patient)
    {
        $patient->load(['pekerjaan', 'riwayatPenyakits', 'asuransis']);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        $patient->load(['riwayatPenyakits', 'asuransis']);
        $pekerjaans = Pekerjaan::all();
        $jenisAsuransi = ['BPJS Kesehatan', 'Asuransi Swasta', 'Taspen', 'Askes', 'Jamsostek'];
        
        return view('patients.edit', compact('patient', 'pekerjaans', 'jenisAsuransi'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|digits:16|unique:patients,nik,' . $patient->id,
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'required|date',
            'pekerjaan_id' => 'required|exists:pekerjaans,id',
            'provinsi' => 'required|string|max:255',
            'kota_kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'foto_pasien' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $patientData = $request->except(['riwayat_penyakit', 'asuransi', 'foto_pasien']);
        
        // Handle file upload
        if ($request->hasFile('foto_pasien')) {
            // Delete old file
            if ($patient->foto_pasien) {
                Storage::delete('public/patients/' . $patient->foto_pasien);
            }
            
            $file = $request->file('foto_pasien');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('patients', $filename, 'public');
            $patientData['foto_pasien'] = $filename;
        }

        $patient->update($patientData);

        // Update riwayat penyakit
        $patient->riwayatPenyakits()->delete();
        if ($request->has('riwayat_penyakit')) {
            foreach ($request->riwayat_penyakit as $riwayat) {
                if (!empty($riwayat['nama_penyakit']) && !empty($riwayat['tahun'])) {
                    RiwayatPenyakit::create([
                        'patient_id' => $patient->id,
                        'nama_penyakit' => $riwayat['nama_penyakit'],
                        'tahun' => $riwayat['tahun']
                    ]);
                }
            }
        }

        // Update asuransi
        $patient->asuransis()->delete();
        if ($request->has('asuransi')) {
            foreach ($request->asuransi as $asuransi) {
                if (!empty($asuransi['jenis_asuransi']) && !empty($asuransi['nomor_asuransi'])) {
                    Asuransi::create([
                        'patient_id' => $patient->id,
                        'jenis_asuransi' => $asuransi['jenis_asuransi'],
                        'nomor_asuransi' => $asuransi['nomor_asuransi']
                    ]);
                }
            }
        }

        return redirect()->route('patients.index')->with('success', 'Data pasien berhasil diperbarui!');
    }

    public function destroy(Patient $patient)
    {
        // Delete photo file
        if ($patient->foto_pasien) {
            Storage::delete('public/patients/' . $patient->foto_pasien);
        }
        
        $patient->delete();
        
        return redirect()->route('patients.index')->with('success', 'Data pasien berhasil dihapus!');
    }

    // API for address dropdown
    public function getKota($provinsi)
    {
        // Dummy data - in real project, use proper API or database
        $kota = [
            'Kalimantan Barat' => ['Pontianak', 'Singkawang', 'Ketapang', 'Sambas', 'Bengkayang', 'Landak', 'Mempawah', 'Sanggau', 'Sekadau', 'Melawi', 'Kapuas Hulu', 'Kayong Utara', 'Kubu Raya'],
            'DKI Jakarta' => ['Jakarta Pusat', 'Jakarta Utara', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Timur'],
            'Jawa Barat' => ['Bandung', 'Bekasi', 'Bogor', 'Depok', 'Cimahi'],
            'Jawa Tengah' => ['Semarang', 'Solo', 'Yogyakarta', 'Magelang', 'Pekalongan']
        ];
        
        return response()->json($kota[$provinsi] ?? []);
    }

    public function getKecamatan($kota)
    {
        // Dummy data
        $kecamatan = [
            'Pontianak' => ['Pontianak Selatan', 'Pontianak Timur', 'Pontianak Barat', 'Pontianak Kota', 'Pontianak Utara'],
            'Singkawang' => ['Singkawang Selatan', 'Singkawang Timur', 'Singkawang Barat', 'Singkawang Utara', 'Singkawang Tengah'],
            'Ketapang' => ['Delta Pawan', 'Matan Hilir Selatan', 'Matan Hilir Utara', 'Nanga Tayap', 'Tumbang Titi'],
            'Sambas' => ['Paloh', 'Sebawi', 'Selakau', 'Sajad', 'Tebas'],
            'Bengkayang' => ['Bengkayang', 'Capkala', 'Ledo', 'Lumar', 'Samalantan'],
            'Landak' => ['Air Besar', 'Mandor', 'Mempawah Hulu', 'Ngabang', 'Sengah Temila'],
            'Mempawah' => ['Mempawah Hilir', 'Mempawah Timur', 'Siantan', 'Segedong', 'Toho'],
            'Sanggau' => ['Balai', 'Bonti', 'Jangkang', 'Kapuas', 'Meliau'],
            'Sekadau' => ['Belitang Hilir', 'Belitang Hulu', 'Nanga Mahap', 'Sekadau Hilir', 'Sekadau Hulu'],
            'Melawi' => ['Belimbing', 'Elab', 'Kayan Hulu', 'Menukung', 'Sayan'],
            'Kapuas Hulu' => ['Bika', 'Boyan Tanjung', 'Bunut Hulu', 'Embaloh Hilir', 'Putussibau Selatan'],
            'Kayong Utara' => ['Seponti', 'Simpang Hilir', 'Sungai Laur', 'Sungai Melayu Rayak', 'Teluk Batang'],
            'Kubu Raya' => ['Kubu', 'Kubu Hulu', 'Rasau Jaya', 'Sungai Ambawang', 'Sungai Kakap']
        ];
        
        return response()->json($kecamatan[$kota] ?? []);
    }

    public function getDesa($kecamatan)
    {
        // Dummy data
        $desa = [
            'Pontianak Selatan' => ['Benua Melayu Darat', 'Benua Melayu Laut', 'Benua Melayu Raya'],
            'Pontianak Timur' => ['Batu Layang', 'Darat Sekip', 'Melayu'],
            'Pontianak Barat' => ['Batu Layang', 'Daratan Hulu', 'Melayu'],
            'Pontianak Kota' => ['Benua Melayu Darat', 'Benua Melayu Laut', 'Benua Melayu Raya'],
            'Pontianak Utara' => ['Batu Layang', 'Daratan Hulu', 'Melayu'],
            'Singkawang Selatan' => ['Pasiran', 'Sungai Pinyuh', 'Tebas'],
            'Singkawang Timur' => ['Pasiran', 'Sungai Pinyuh', 'Tebas'],
            'Singkawang Barat' => ['Pasiran', 'Sungai Pinyuh', 'Tebas'],
            'Singkawang Utara' => ['Pasiran', 'Sungai Pinyuh', 'Tebas'],
            'Singkawang Tengah' => ['Pasiran', 'Sungai Pinyuh', 'Tebas'],
            'Delta Pawan' => ['Sungai Melayu Rayak', 'Sungai Laur', 'Seponti'],
            'Matan Hilir Selatan' => ['Simpang Hilir', 'Simpang Hulu', 'Sungai Ambawang'],
            'Matan Hilir Utara' => ['Simpang Hilir', 'Simpang Hulu', 'Sungai Ambawang'],
            'Nanga Tayap' => ['Simpang Hilir', 'Simpang Hulu', 'Sungai Ambawang'],
            'Tumbang Titi' => ['Simpang Hilir', 'Simpang Hulu', 'Sungai Ambawang'],
            'Paloh' => ['Sebawi', 'Selakau', 'Sajad'],
            'Sebawi' => ['Paloh', 'Selakau', 'Sajad'],
            'Selakau' => ['Paloh', 'Sebawi', 'Sajad'],
            'Sajad' => ['Paloh', 'Sebawi', 'Selakau'],
            'Tebas' => ['Paloh', 'Sebawi', 'Selakau'],
            'Bengkayang' => ['Capkala', 'Ledo', 'Lumar'],
            'Capkala' => ['Bengkayang', 'Ledo', 'Lumar'],
            'Ledo' => ['Bengkayang', 'Capkala', 'Lumar'],
            'Lumar' => ['Bengkayang', 'Capkala', 'Ledo'],
            'Samalantan' => ['Bengkayang', 'Capkala', 'Ledo'],
            'Air Besar' => ['Mandor', 'Mempawah Hulu', 'Ngabang'],
            'Mandor' => ['Air Besar', 'Mempawah Hulu', 'Ngabang'],
            'Mempawah Hulu' => ['Air Besar', 'Mandor', 'Ngabang'],
            'Ngabang' => ['Air Besar', 'Mandor', 'Mempawah Hulu'],
            'Sengah Temila' => ['Air Besar', 'Mandor', 'Mempawah Hulu'],
            'Mempawah Hilir' => ['Mempawah Timur', 'Siantan', 'Segedong'],
            'Mempawah Timur' => ['Mempawah Hilir', 'Siantan', 'Segedong'],
            'Siantan' => ['Mempawah Hilir', 'Mempawah Timur', 'Segedong'],
            'Segedong' => ['Mempawah Hilir', 'Mempawah Timur', 'Siantan'],
            'Balai' => ['Bonti', 'Jangkang', 'Kapuas'],
            'Bonti' => ['Balai', 'Jangkang', 'Kapuas'],
            'Jangkang' => ['Balai', 'Bonti', 'Kapuas'],
            'Kapuas' => ['Balai', 'Bonti', 'Jangkang'],
            'Meliau' => ['Balai', 'Bonti', 'Jangkang'],
            'Belitang Hilir' => ['Belitang Hulu', 'Nanga Mahap', 'Sekadau Hilir'],
            'Belitang Hulu' => ['Belitang Hilir', 'Nanga Mahap', 'Sekadau Hilir'],
            'Nanga Mahap' => ['Belitang Hilir', 'Belitang Hulu', 'Sekadau Hilir'],
            'Sekadau Hilir' => ['Belitang Hilir', 'Belitang Hulu', 'Nanga Mahap'],
            'Sekadau Hulu' => ['Belitang Hilir', 'Belitang Hulu', 'Nanga Mahap'],
            'Belimbing' => ['Elab', 'Kayan Hulu', 'Menukung'],
            'Elab' => ['Belimbing', 'Kayan Hulu', 'Menukung'],
            'Kayan Hulu' => ['Belimbing', 'Elab', 'Menukung'],
            'Menukung' => ['Belimbing', 'Elab', 'Kayan Hulu'],
            'Sayan' => ['Belimbing', 'Elab', 'Kayan Hulu'],
            'Bika' => ['Boyan Tanjung', 'Bunut Hulu', 'Embaloh Hilir'],
            'Boyan Tanjung' => ['Bika', 'Bunut Hulu', 'Embaloh Hilir'],
            'Bunut Hulu' => ['Bika', 'Boyan Tanjung', 'Embaloh Hilir'],
            'Embaloh Hilir' => ['Bika', 'Boyan Tanjung', 'Bunut Hulu'],
            'Putussibau Selatan' => ['Bika', 'Boyan Tanjung', 'Bunut Hulu'],
            'Seponti' => ['Simpang Hilir', 'Sungai Laur', 'Sungai Melayu Rayak'],
            'Simpang Hilir' => ['Seponti', 'Sungai Laur', 'Sungai Melayu Rayak'],
            'Sungai Laur' => ['Seponti', 'Simpang Hilir', 'Sungai Melayu Rayak'],
            'Sungai Melayu Rayak' => ['Seponti', 'Simpang Hilir', 'Sungai Laur'],
            'Teluk Batang' => ['Seponti', 'Simpang Hilir', 'Sungai Laur'],
            'Kubu' => ['Kubu Hulu', 'Rasau Jaya', 'Sungai Ambawang'],
            'Kubu Hulu' => ['Kubu', 'Rasau Jaya', 'Sungai Ambawang'],
            'Rasau Jaya' => ['Kubu', 'Kubu Hulu', 'Sungai Ambawang'],
            'Sungai Ambawang' => ['Kubu', 'Kubu Hulu', 'Rasau Jaya'],
            'Sungai Kakap' => ['Kubu', 'Kubu Hulu', 'Rasau Jaya']
        ];
        
        return response()->json($desa[$kecamatan] ?? []);
    }
}
