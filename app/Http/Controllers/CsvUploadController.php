<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CsvRow;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use App\Models\CsvUpload;

class CsvUploadController extends Controller
{
    public function index()
    {
        $rows = CsvRow::all();
        $uploads = \App\Models\CsvUpload::orderBy('created_at', 'desc')->get();
        return view('csv_upload', ['rows' => $rows, 'uploads' => $uploads]);
    }

    public function upload(Request $request)
    {
        \Log::info('Upload controller hit!');
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:10240', // max 10MB
        ]);
        $file = $request->file('csv_file');
        $fileName = $file->getClientOriginalName();
        $storedFile = $file->storeAs('csv_uploads', uniqid() . '_' . $fileName);
        $csvUpload = CsvUpload::create([
            'file_name' => $fileName,
            'file_path' => $storedFile,
            'status' => 'Processing',
        ]);

        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        \Log::info('Header:', $header);

        $rowCount = 0;
        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowCount++;
            \Log::info('Row:', $row);
            if (count($row) !== count($header)) {
                \Log::warning('Skipping malformed row', $row);
                $skipped++;
                continue;
            }
            CsvRow::create(['row_data' => json_encode(array_combine($header, $row))]);
            $imported++;
        }
        fclose($handle);
        \Log::info("Imported $imported rows. Skipped $skipped. Processed $rowCount rows.");

        // Always update status before returning
        $csvUpload->status = $imported > 0 ? 'Uploaded' : 'Failed';
        $csvUpload->save();

        if ($imported === 0) {
            return redirect()->route('csv.index')->with('error', 'No rows were imported. Please check your CSV format.');
        }
        $msg = "CSV imported. Rows: $imported" . ($skipped ? ", Skipped: $skipped" : "");
        return redirect()->route('csv.index')->with('success', $msg);
    }

    public function download($id)
    {
        $upload = CsvUpload::findOrFail($id);
        return Storage::download($upload->file_path, $upload->file_name);
    }

    public function delete($id)
    {
        $upload = CsvUpload::findOrFail($id);
        // Optionally also delete the file from storage
        Storage::delete($upload->file_path);
        $upload->delete();
        return redirect()->route('csv.index')->with('success', 'Upload deleted successfully.');
    }

    public function data()
    {
        $rows = CsvRow::all();
        return response()->json(['rows' => $rows]);
    }
}
