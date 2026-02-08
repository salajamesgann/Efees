<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    public function exportTable(array $columns, array $rows, string $format, string $filename, array $options = [])
    {
        $format = strtolower($format);
        $filename = $this->sanitizeFilename($filename);
        if ($format === 'csv') {
            return $this->generateCsv($columns, $rows, $filename);
        }
        if ($format === 'pdf') {
            return $this->generatePdf($columns, $rows, $filename, $options);
        }
        if ($format === 'xlsx') {
            return $this->generateXlsx($columns, $rows, $filename);
        }

        return response()->json(['error' => 'unsupported format'], 422);
    }

    protected function sanitizeFilename(string $name): string
    {
        $clean = preg_replace('/[^A-Za-z0-9_\\-]+/', '_', $name);

        return trim($clean ?: 'export', '_');
    }

    protected function generateCsv(array $columns, array $rows, string $filename): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($columns, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($rows as $r) {
                $line = [];
                foreach ($columns as $col) {
                    $line[] = $r[$col] ?? '';
                }
                fputcsv($out, $line);
            }
            fclose($out);
        });
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'.csv"');

        return $response;
    }

    protected function generatePdf(array $columns, array $rows, string $filename, array $options)
    {
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return response()->json(['error' => 'pdf library missing'], 500);
        }
        $view = $options['view'] ?? 'reports.pdf.table';
        $title = $options['title'] ?? 'Report';
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
        ])->setPaper($options['paper'] ?? 'a4', $options['orientation'] ?? 'portrait');

        return $pdf->download($filename.'.pdf');
    }

    protected function generateXlsx(array $columns, array $rows, string $filename)
    {
        if (! class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            return response()->json(['error' => 'excel library missing'], 500);
        }
        $export = new class($columns, $rows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings
        {
            protected array $columns;

            protected array $rows;

            public function __construct(array $columns, array $rows)
            {
                $this->columns = $columns;
                $this->rows = $rows;
            }

            public function array(): array
            {
                $data = [];
                foreach ($this->rows as $r) {
                    $line = [];
                    foreach ($this->columns as $col) {
                        $line[] = $r[$col] ?? '';
                    }
                    $data[] = $line;
                }

                return $data;
            }

            public function headings(): array
            {
                return $this->columns;
            }
        };

        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename.'.xlsx');
    }
}
