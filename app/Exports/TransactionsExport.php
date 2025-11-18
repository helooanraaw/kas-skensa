<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon; // <-- Kita butuh ini untuk format tanggal
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles; // <-- Baru: Untuk Desain (Bold, BG)
use Maatwebsite\Excel\Concerns\WithEvents; // <-- Baru: Untuk Baris Total (Summary)
use Maatwebsite\Excel\Concerns\WithTitle; // <-- Baru: Untuk memberi nama Sheet
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents, WithTitle
{
    protected $class_id;
    protected $saldoBerjalan = 0; // Untuk menghitung saldo di tiap baris
    protected $totalMasuk = 0;
    protected $totalKeluar = 0;
    protected $bulan;
    protected $tahun;

    public function __construct(int $class_id, $bulan = null, $tahun = null)
    {
        $this->class_id = $class_id;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Transaction::where('school_class_id', $this->class_id)
                            ->with('student')
                            ->orderBy('date', 'asc')
                            ->orderBy('created_at', 'asc');

        // Jika ada filter bulan/tahun, terapkan
        if ($this->bulan && $this->tahun) {
            $query->whereMonth('date', $this->bulan)
                ->whereYear('date', $this->tahun);
        }

        $transactions = $query->get();

        // (Sisa kode hitung total tetap sama...)
        $this->totalMasuk = $transactions->where('type', 'masuk')->sum('amount');
        $this->totalKeluar = $transactions->where('type', 'keluar')->sum('amount');

        return $transactions;
    }

    /**
    * @return string
    */
    // 1. Memberi Nama Sheet
    public function title(): string
    {
        return 'Laporan Kas';
    }

    /**
    * @return array
    */
    // 2. Buat Judul Kolom (Header) Profesional
    public function headings(): array
    {
        return [
            'No.',
            'Tanggal',
            'Keterangan',
            'Siswa',
            'Debet (Masuk)',  // <-- Baru
            'Kredit (Keluar)', // <-- Baru
            'Saldo',           // <-- Baru
        ];
    }

    /**
    * @param mixed $transaction
    * @return array
    */
    // 3. Memetakan Data ke Kolom (Logika Debet/Kredit & Saldo Berjalan)
    public function map($transaction): array
    {
        static $i = 0; // Untuk nomor urut
        $i++;

        $debet = null;
        $kredit = null;

        if ($transaction->type == 'masuk') {
            $debet = $transaction->amount;
            $this->saldoBerjalan += $transaction->amount;
        } else {
            $kredit = $transaction->amount;
            $this->saldoBerjalan -= $transaction->amount;
        }

        return [
            $i, // Kolom No.
            Carbon::parse($transaction->date)->format('d-m-Y'), // Format Tanggal
            $transaction->description,
            $transaction->student->name ?? '-', // Nama siswa (atau '-' jika pengeluaran)
            $debet,  // Kolom Debet
            $kredit, // Kolom Kredit
            $this->saldoBerjalan, // Saldo setelah transaksi ini
        ];
    }

    /**
    * @param Worksheet $sheet
    */
    // 4. Memberi Desain (Styling)
    public function styles(Worksheet $sheet)
    {
        // Buat Baris 1 (Header) jadi Bold dan diberi Latar Belakang
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFEEF2F7'); // Warna abu-abu muda

        // Format kolom E, F, G (Debet, Kredit, Saldo) sebagai Angka (Rupiah)
        $sheet->getStyle('E:G')->getNumberFormat()
              ->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"??_-;_-@_-');
    }

    /**
    * @return array
    */
    // 5. Membuat Baris Total (Summary) di Bawah
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Dapatkan baris terakhir (misal 30)
                $lastRow = $sheet->getHighestRow();

                // Tentukan baris untuk summary (kita beri jarak 2 baris)
                $summaryRowStart = $lastRow + 2;

                // Tulis Judul Summary
                $sheet->mergeCells("D{$summaryRowStart}:E{$summaryRowStart}");
                $sheet->setCellValue("D{$summaryRowStart}", 'REKAPITULASI');
                $sheet->getStyle("D{$summaryRowStart}")->getFont()->setBold(true);

                // Tulis Total Pemasukan
                $sheet->setCellValue("D".($summaryRowStart + 1), 'Total Pemasukan (Debet)');
                $sheet->setCellValue("E".($summaryRowStart + 1), $this->totalMasuk);
                $sheet->getStyle("E".($summaryRowStart + 1))->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"??_-;_-@_-');

                // Tulis Total Pengeluaran
                $sheet->setCellValue("D".($summaryRowStart + 2), 'Total Pengeluaran (Kredit)');
                $sheet->setCellValue("E".($summaryRowStart + 2), $this->totalKeluar);
                $sheet->getStyle("E".($summaryRowStart + 2))->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"??_-;_-@_-');

                // Tulis Saldo Akhir
                $sheet->setCellValue("D".($summaryRowStart + 3), 'SALDO AKHIR');
                $sheet->setCellValue("E".($summaryRowStart + 3), $this->totalMasuk - $this->totalKeluar);
                $sheet->getStyle("D".($summaryRowStart + 3))->getFont()->setBold(true);
                $sheet->getStyle("E".($summaryRowStart + 3))->getFont()->setBold(true);
                $sheet->getStyle("E".($summaryRowStart + 3))->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"??_-;_-@_-');
            },
        ];
    }
}