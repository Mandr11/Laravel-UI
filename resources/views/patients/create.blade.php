@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0">Tambah Data Pasien</h2>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('patients.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIK</label>
                    <input type="text" name="nik" class="form-control" value="{{ old('nik') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Jenis Kelamin</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="jenis_kelamin_laki" value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'checked' : '' }}>
                            <label class="form-check-label" for="jenis_kelamin_laki">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="jenis_kelamin_perempuan" value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'checked' : '' }}>
                            <label class="form-check-label" for="jenis_kelamin_perempuan">Perempuan</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pekerjaan</label>
                    <select name="pekerjaan_id" class="form-select">
                        @foreach ($pekerjaans as $pekerjaan)
                            <option value="{{ $pekerjaan->id }}" {{ old('pekerjaan_id') == $pekerjaan->id ? 'selected' : '' }}>{{ $pekerjaan->nama_pekerjaan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Provinsi</label>
                    <select name="provinsi" id="provinsi" class="form-select">
                        <option value="">Pilih Provinsi</option>
                        <option value="Kalimantan Barat" {{ old('provinsi') == 'Kalimantan Barat' ? 'selected' : '' }}>Kalimantan Barat</option>
                        <option value="DKI Jakarta" {{ old('provinsi') == 'DKI Jakarta' ? 'selected' : '' }}>DKI Jakarta</option>
                        <option value="Jawa Barat" {{ old('provinsi') == 'Jawa Barat' ? 'selected' : '' }}>Jawa Barat</option>
                        <option value="Jawa Tengah" {{ old('provinsi') == 'Jawa Tengah' ? 'selected' : '' }}>Jawa Tengah</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kota/Kabupaten</label>
                    <select name="kota_kabupaten" id="kota_kabupaten" class="form-select" disabled>
                        <option value="">Pilih Kota/Kabupaten</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Kecamatan</label>
                    <select name="kecamatan" id="kecamatan" class="form-select" disabled>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Desa</label>
                    <select name="desa" id="desa" class="form-select" disabled>
                        <option value="">Pilih Desa</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Foto Pasien</label>
                <input type="file" name="foto_pasien" class="form-control">
            </div>

            <h5>Riwayat Penyakit</h5>
            <div id="riwayat-container">
                <div class="riwayat-item mb-3">
                    <input type="text" name="riwayat_penyakit[0][nama_penyakit]" placeholder="Nama Penyakit" class="form-control mb-1">
                    <input type="text" name="riwayat_penyakit[0][tahun]" placeholder="Tahun" class="form-control">
                </div>
            </div>

            <h5>Asuransi</h5>
            <div id="asuransi-container">
                <div class="asuransi-item mb-3">
                    <select name="asuransi[0][jenis_asuransi]" class="form-select mb-1">
                        @foreach ($jenisAsuransi as $asuransi)
                            <option value="{{ $asuransi }}">{{ $asuransi }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="asuransi[0][nomor_asuransi]" placeholder="Nomor Asuransi" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const provinsiSelect = document.getElementById('provinsi');
    const kotaSelect = document.getElementById('kota_kabupaten');
    const kecamatanSelect = document.getElementById('kecamatan');
    const desaSelect = document.getElementById('desa');

    function clearSelect(selectElement) {
        selectElement.innerHTML = '<option value="">Pilih</option>';
        selectElement.disabled = true;
    }

    function fetchOptions(url, selectElement, oldValue = '') {
        fetch(url)
            .then(response => response.json())
            .then(data => {
                clearSelect(selectElement);
                if (data.length > 0) {
                    selectElement.disabled = false;
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item;
                        option.textContent = item;
                        if (item === oldValue) {
                            option.selected = true;
                        }
                        selectElement.appendChild(option);
                    });
                }
            });
    }

    // On page load, if old values exist, populate dependent dropdowns
    const oldProvinsi = provinsiSelect.value;
    const oldKota = "{{ old('kota_kabupaten') }}";
    const oldKecamatan = "{{ old('kecamatan') }}";
    const oldDesa = "{{ old('desa') }}";

    if (oldProvinsi) {
        fetchOptions('/api/kota/' + encodeURIComponent(oldProvinsi), kotaSelect, oldKota);
    }
    if (oldKota) {
        fetchOptions('/api/kecamatan/' + encodeURIComponent(oldKota), kecamatanSelect, oldKecamatan);
    }
    if (oldKecamatan) {
        fetchOptions('/api/desa/' + encodeURIComponent(oldKecamatan), desaSelect, oldDesa);
    }

    provinsiSelect.addEventListener('change', function () {
        clearSelect(kotaSelect);
        clearSelect(kecamatanSelect);
        clearSelect(desaSelect);
        if (this.value) {
            fetchOptions('/api/kota/' + encodeURIComponent(this.value), kotaSelect);
        }
    });

    kotaSelect.addEventListener('change', function () {
        clearSelect(kecamatanSelect);
        clearSelect(desaSelect);
        if (this.value) {
            fetchOptions('/api/kecamatan/' + encodeURIComponent(this.value), kecamatanSelect);
        }
    });

    kecamatanSelect.addEventListener('change', function () {
        clearSelect(desaSelect);
        if (this.value) {
            fetchOptions('/api/desa/' + encodeURIComponent(this.value), desaSelect);
        }
    });
});
</script>
@endsection
