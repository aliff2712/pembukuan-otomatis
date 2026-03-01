<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\MikhmonImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MikhmonImportController extends Controller
{
    // MikhmonImportController.php

    public function importForm()  // ganti dari index()
    {
        return view('voucher-sales.import');
    }

    public function import(Request $request)  // ganti dari store()
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $path = $request->file('csv_file')->store('mikhmon_temp');
        $fullPath = storage_path("app/{$path}");

        $service = new MikhmonImportService();

        try {
            $service->importCsv($fullPath);
            $service->transform();
            $service->aggregateDaily();
            $service->journalize();
        } catch (\Throwable $e) {
            return back()
                ->with('error', 'Pipeline gagal: ' . $e->getMessage())
                ->with('log', $service->log);
        }

        Storage::delete($path);

        return back()
            ->with('success', 'Pipeline berhasil dijalankan!')
            ->with('log', $service->log);
        }
}   