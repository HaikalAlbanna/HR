<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        return view('dashboard.karyawan.index', [
            'karyawan' => Karyawan::orderByDesc('id')->get(),
            'edit' => null,
        ]);
    }

    public function edit(Karyawan $karyawan)
    {
        return view('dashboard.karyawan.index', [
            'karyawan' => Karyawan::orderByDesc('id')->get(),
            'edit' => $karyawan,
        ]);
    }

    public function store(Request $request)
    {
        Karyawan::create($this->validatedData($request));

        return redirect()->route('dashboard.karyawan.index')->with('status', 'Data karyawan berhasil ditambahkan.');
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $karyawan->update($this->validatedData($request));

        return redirect()->route('dashboard.karyawan.index')->with('status', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();

        return redirect()->route('dashboard.karyawan.index')->with('status', 'Data karyawan berhasil dihapus.');
    }

    public function report()
    {
        $rows = Karyawan::orderBy('nama')->get();
        $lines = ['REPORT DATA KARYAWAN', 'Dicetak: ' . now()->format('Y-m-d H:i:s'), ''];
        $lines[] = str_pad('ID', 6) . str_pad('Nama', 28) . str_pad('Tgl Lahir', 16) . 'Gaji';

        foreach ($rows as $row) {
            $lines[] = str_pad((string) $row->id, 6)
                . str_pad(substr($row->nama, 0, 24), 28)
                . str_pad((string) $row->tgl_lahir, 16)
                . number_format((float) $row->gaji, 2, ',', '.');
        }

        return response($this->makePdf($lines), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="report-karyawan.pdf"',
        ]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tgl_lahir' => ['nullable', 'date'],
            'gaji' => ['required', 'numeric', 'min:0'],
        ]);
    }

    private function makePdf(array $lines): string
    {
        $content = "BT\n/F1 11 Tf\n50 790 Td\n14 TL\n";

        foreach ($lines as $line) {
            $content .= '(' . $this->pdfEscape($line) . ") Tj\nT*\n";
        }

        $content .= "ET";

        $objects = [
            "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n",
            "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n",
            "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n",
            "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>\nendobj\n",
            "5 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream\nendobj\n",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        return $pdf . "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";
    }

    private function pdfEscape(string $value): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }
}
