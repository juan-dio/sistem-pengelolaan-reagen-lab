<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class DatabaseController extends Controller
{
    public function index()
    {
        $backupPath = storage_path('app/backups');
        $backups = collect(Storage::files('backups'))
            ->filter(fn($file) => pathinfo($file, PATHINFO_EXTENSION) === 'sql') // Hanya file .sql
            ->map(function ($file) use ($backupPath) {
                return [
                    'name' => basename($file),
                    'size' => round(filesize(storage_path('app/' . $file)) / 1024, 2) . ' KB',
                    'date' => date('Y-m-d H:i:s', filemtime(storage_path('app/' . $file))),
                ];
            })
            ->sortByDesc('date')
            ->values();

        return view('database.index', compact('backups'));
    }

    public function backup()
    {
        $filename = 'backup_' . date('Y_m_d_H_i_s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');
        $port = env('DB_PORT');

        $command = "mysqldump --host={$host} --port={$port} --user={$username} --password={$password} {$database} > {$path}";

        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            return redirect()->route('database.index')->with('success', 'Backup berhasil disimpan.');
        } else {
            return redirect()->route('database.index')->with('error', 'Gagal melakukan backup.');
        }
    }

    public function restore(Request $request)
    {
        $backupFile = $request->input('backup_file');

        if (!$backupFile || !Storage::exists('backups/' . $backupFile)) {
            return redirect()->route('database.index')->with('error', 'File backup tidak ditemukan.');
        }

        try {
            $fullPath = storage_path('app/backups/' . $backupFile);
            $database = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST');
            $port = env('DB_PORT');

            $command = "mysql --host={$host} --port={$port} --user={$username} --password={$password} {$database} < {$fullPath}";
            exec($command, $output, $resultCode);

            if ($resultCode === 0) {
                return redirect()->route('database.index')->with('success', 'Database berhasil direstore dari file ' . $backupFile);
            } else {
                return redirect()->route('database.index')->with('error', 'Gagal melakukan restore database. Periksa file backup.');
            }
        } catch (\Exception $e) {
            return redirect()->route('database.index')->with('error', 'Terjadi kesalahan saat restore database.');
        }
    }

    public function delete(Request $request)
{
    $backupFile = $request->input('backup_file');

    if (!$backupFile || !Storage::exists('backups/' . $backupFile)) {
        return redirect()->route('database.index')->with('error', 'File backup tidak ditemukan.');
    }

    try {
        Storage::delete('backups/' . $backupFile);
        return redirect()->route('database.index')->with('success', 'File backup berhasil dihapus.');
    } catch (\Exception $e) {
        // \Log::error('Delete Backup Error: ' . $e->getMessage());
        return redirect()->route('database.index')->with('error', 'Terjadi kesalahan saat menghapus file backup.');
    }
}
}
