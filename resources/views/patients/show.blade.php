@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
        <h2 class="mb-0">Detail Pasien</h2>
        <a href="{{ route('patients.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left-circle"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4 text-center">
                @if($patient->foto_pasien)
                    <img 
                        src="{{ asset('storage/patients/' . $patient->foto_pasien) }}" 
                        alt="Foto Pasien" 
                        class="border border-secondary"
                        style="width: 100%; max-width: 300px; height: 300px; object-fit: cover; border-radius: 0;"
                    >
                @else
                    <div class="text-muted fst-italic">Foto tidak tersedia</div>
                @endif
            </div>
            <div class="col-md-8">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th>Nama</th>
                            <td>{{ $patient->nama }}</td>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <td>{{ $patient->nik }}</td>
                        </tr>
                        <tr>
                            <th>Tempat/Tanggal Lahir</th>
                            <td>{{ $patient->tempat_lahir }}, {{ $patient->tanggal_lahir->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <td>{{ $patient->jenis_kelamin }}</td>
                        </tr>
                        <tr>
                            <th>Pekerjaan</th>
                            <td>{{ $patient->pekerjaan->nama_pekerjaan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $patient->desa }}, {{ $patient->kecamatan }}, {{ $patient->kota_kabupaten }}, {{ $patient->provinsi }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card border-primary mb-3">
                    <div class="card-header bg-primary text-white fw-bold">Riwayat Penyakit</div>
                    <ul class="list-group list-group-flush">
                        @forelse ($patient->riwayatPenyakits as $r)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $r->nama_penyakit }}
                                <span class="badge bg-primary rounded-pill">{{ $r->tahun }}</span>
                            </li>
                        @empty
                            <li class="list-group-item fst-italic text-muted">Tidak ada data</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-success mb-3">
                    <div class="card-header bg-success text-white fw-bold">Asuransi</div>
                    <ul class="list-group list-group-flush">
                        @forelse ($patient->asuransis as $a)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $a->jenis_asuransi }}
                                <span class="badge bg-success rounded-pill">{{ $a->nomor_asuransi }}</span>
                            </li>
                        @empty
                            <li class="list-group-item fst-italic text-muted">Tidak ada data</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
