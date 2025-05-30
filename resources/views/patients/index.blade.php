@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Daftar Pasien</h2>
        <a href="{{ route('patients.create') }}" class="btn btn-warning btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah Pasien
        </a>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-striped table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>Pekerjaan</th>
                    <th>Jenis Kelamin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($patients as $patient)
                    <tr>
                        <td>
@if($patient->foto_pasien)
    <img src="{{ asset('storage/patients/' . $patient->foto_pasien) }}" width="60" height="60" alt="Foto" class="rounded-circle border border-secondary">
@else
    <span class="text-muted">Tidak Ada</span>
@endif
                        </td>
                        <td>{{ $patient->nama }}</td>
                        <td>{{ $patient->nik }}</td>
                        <td>{{ $patient->pekerjaan->nama_pekerjaan ?? '-' }}</td>
                        <td>{{ $patient->jenis_kelamin }}</td>
                        <td>
                            <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-info btn-sm me-1" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-warning btn-sm me-1" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $patients->links() }}
    </div>
</div>
@endsection
