<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ExportService
{
    /**
     * Export data to CSV format
     */
    public function exportToCsv($data, $filename, $headers)
    {
        $callback = function() use ($data, $headers) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM to fix Hungarian characters
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, $headers, ';');
            
            // Add data rows
            foreach ($data as $row) {
                $rowData = [];
                foreach ($headers as $key => $header) {
                    $rowData[] = $row[$key] ?? '';
                }
                fputcsv($file, $rowData, ';');
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ]);
    }

    /**
     * Export data to PDF format with header, footer, and logo
     */
    public function exportToPdf($data, $filename, $title, $headers, $logoPath = null)
    {
        // Prepare table data
        $tableData = [];
        foreach ($data as $row) {
            $rowData = [];
            foreach (array_keys($headers) as $key) {
                $rowData[] = $row[$key] ?? '';
            }
            $tableData[] = $rowData;
        }

        // Generate PDF
        $pdf = Pdf::loadView('exports.pdf-template', [
            'title' => $title,
            'headers' => array_values($headers),
            'data' => $tableData,
            'logoPath' => $logoPath,
            'exportDate' => now()->format('Y-m-d H:i:s'),
        ]);

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Get column configuration for different entity types
     */
    public function getColumnConfig($type)
    {
        $configs = [
            'authors' => [
                'id' => 'ID',
                'name' => 'Név',
                'nationality' => 'Nemzetiség',
                'age' => 'Életkor',
                'gender' => 'Nem',
            ],
            'books' => [
                'id' => 'ID',
                'title' => 'Cím',
                'author_name' => 'Szerző',
                'year' => 'ISBN',
                'category_name' => 'Kategória',
                'price' => 'Ár',
                'publication_date' => 'Kiadás éve',
                'edition' => 'Kiadás',
            ],
            'categories' => [
                'id' => 'ID',
                'name' => 'Név',
            ],
        ];

        return $configs[$type] ?? [];
    }

    /**
     * Get title for different entity types
     */
    public function getTitle($type)
    {
        $titles = [
            'authors' => 'Szerzők listája',
            'books' => 'Könyvek listája',
            'categories' => 'Kategóriák listája',
        ];

        return $titles[$type] ?? 'Lista';
    }
}
