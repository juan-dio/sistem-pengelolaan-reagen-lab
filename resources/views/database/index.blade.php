@extends('layouts.app')

@section('content')

<div class="section-header">
    <h1>Database Backup & Restore</h1>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <h3>Backup</h3>
        <form action="{{ route('database.backup') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-database"></i> Backup
            </button>
        </form>
    </div>
    <div class="col-md-6">
        <h3>Restore</h3>
        <form action="{{ route('database.restore') }}" method="POST">
            @csrf
            <div class="form-group">
                {{-- <label for="backup_file">Pilih File Backup</label> --}}
                <select name="backup_file" id="backup_file" class="form-control">
                    <option value="" disabled selected>Pilih file backup...</option>
                    @foreach ($backups as $backup)
                        <option value="{{ $backup['name'] }}">{{ $backup['name'] }} - {{ $backup['size'] }} - {{ $backup['date'] }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-undo"></i> Restore
            </button>
        </form>
    </div>
</div>

<div class="mt-5">
    <h3>Daftar File Backup</h3>
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Nama File</th>
                <th>Ukuran</th>
                <th>Tanggal Dibuat</th>
                <th>Opsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($backups as $index => $backup)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $backup['name'] }}</td>
                    <td>{{ $backup['size'] }}</td>
                    <td>{{ $backup['date'] }}</td>
                    <td>
                        <!-- Tombol Hapus -->
                        <form action="{{ route('database.delete') }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="backup_file" value="{{ $backup['name'] }}">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus file ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada file backup tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


@endsection