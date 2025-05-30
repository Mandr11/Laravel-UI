@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0">Edit Data Pasien</h2>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('patients.update', $patient->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $patient->nama) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIK</label>
                    <input type="text" name="nik" class="form-control" value="{{ old('nik', $patient->nik) }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $patient->tempat_lahir) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $patient->tanggal_lahir->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Jenis Kelamin</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="jenis_kelamin_laki" value="Laki-laki" {{ old('jenis_kelamin', $patient->jenis_kelamin) == 'Laki-laki' ? 'checked' : '' }}>
                            <label class="form-check-label" for="jenis_kelamin_laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="jenis_kelamin_perempuan" value="Perempuan" {{ old('jenis_kelamin', $patient->jenis_kelamin) == 'Perempuan' ? 'checked' : '' }}>
                            <label class="form-check-label" for="jenis_kelamin_perempuan">Perempuan</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pekerjaan</label>
                    <select name="pekerjaan_id" class="form-select">
                        @foreach ($pekerjaans as $pekerjaan)
                            <option value="{{ $pekerjaan->id }}" {{ $patient->pekerjaan_id == $pekerjaan->id ? 'selected' : '' }}>
                                {{ $pekerjaan->nama_pekerjaan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Provinsi</label>
                    <input type="text" name="provinsi" class="form-control" value="{{ old('provinsi', $patient->provinsi) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota/Kabupaten</label>
                    <input type="text" name="kota_kabupaten" class="form-control" value="{{ old('kota_kabupaten', $patient->kota_kabupaten) }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Kecamatan</label>
                    <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan', $patient->kecamatan) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Desa</label>
                    <input type="text" name="desa" class="form-control" value="{{ old('desa', $patient->desa) }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Foto Pasien</label>
                <input type="file" name="foto_pasien" class="form-control">
@if($patient->foto_pasien)
    <p>Foto saat ini: <img src="{{ asset('storage/patients/' . $patient->foto_pasien) }}" style="width:150px; height:150px; object-fit: cover;"></p>
@endif
            </div>

            {{-- Bagian riwayat & asuransi bisa dibuat pakai JS dinamis (opsional) --}}

            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
</div>
@endsection
