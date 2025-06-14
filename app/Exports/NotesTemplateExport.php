<?php

namespace App\Exports;

use App\Models\User;
use App\Models\UniteEnseignement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class NotesTemplateExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithCustomStartCell
{
    protected $ue;
    protected $etudiants;

    public function __construct(UniteEnseignement $ue)
    {
        $this->ue = $ue;
        
        // Get students from the same filiere as the UE
        $this->etudiants = User::where('role', 'etudiant')
            ->where('filiere_id', $ue->filiere_id)
            ->orderBy('name')
            ->get();
    }

    /**
     * Return collection of students data
     */
    public function collection()
    {
        return $this->etudiants->map(function ($etudiant) {
            return [
                'nom_etudiant' => $etudiant->name,
                'cne_etudiant' => $etudiant->matricule,
                'note' => '', // Empty for user to fill
                'statut_absence' => '' // Empty for user to fill
            ];
        });
    }

    /**
     * Define headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'Nom Etudiant',
            'CNE Etudiant', 
            'Note',
            'Statut Absence'
        ];
    }

    /**
     * Define the starting cell (to leave space for UE info)
     */
    public function startCell(): string
    {
        return 'A3'; // Start from row 3 to leave space for UE info
    }

    /**
     * Define the worksheet title
     */
    public function title(): string
    {
        return 'Notes ' . $this->ue->code;
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Add UE information in the first rows
        $sheet->setCellValue('A1', 'UE: ' . $this->ue->code . ' - ' . $this->ue->nom);
        $sheet->setCellValue('A2', 'Filière: ' . ($this->ue->filiere->nom ?? 'N/A') . ' | Semestre: ' . $this->ue->semestre);

        // Style the UE info rows
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '2E86AB']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '666666']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Style the headers (row 3)
        $sheet->getStyle('A3:D3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E86AB']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style the data rows
        $lastRow = $this->etudiants->count() + 3; // +3 because we start from row 3
        $sheet->getStyle('A4:D' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(30); // Nom Etudiant
        $sheet->getColumnDimension('B')->setWidth(15); // CNE Etudiant
        $sheet->getColumnDimension('C')->setWidth(10); // Note
        $sheet->getColumnDimension('D')->setWidth(15); // Statut Absence

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(25);

        // Add data validation for Note column (C4 to C+lastRow)
        $noteRange = 'C4:C' . $lastRow;
        $validation = $sheet->getCell('C4')->getDataValidation();
        $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_DECIMAL);
        $validation->setOperator(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::OPERATOR_BETWEEN);
        $validation->setFormula1('0');
        $validation->setFormula2('20');
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Note invalide');
        $validation->setError('La note doit être comprise entre 0 et 20');
        $validation->setPromptTitle('Saisie de note');
        $validation->setPrompt('Entrez une note entre 0 et 20 (ex: 15.5)');
        $validation->setShowInputMessage(true);

        // Apply validation to the entire note column
        $sheet->setDataValidation($noteRange, $validation);

        // Add data validation for Absence column (D4 to D+lastRow)
        $absenceRange = 'D4:D' . $lastRow;
        $absenceValidation = $sheet->getCell('D4')->getDataValidation();
        $absenceValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $absenceValidation->setFormula1('"absent,"'); // Empty or "absent"
        $absenceValidation->setShowErrorMessage(true);
        $absenceValidation->setErrorTitle('Statut invalide');
        $absenceValidation->setError('Laissez vide si présent, ou écrivez "absent" si absent');
        $absenceValidation->setPromptTitle('Statut de présence');
        $absenceValidation->setPrompt('Laissez vide si l\'étudiant est présent, ou écrivez "absent" s\'il est absent');
        $absenceValidation->setShowInputMessage(true);

        // Apply validation to the entire absence column
        $sheet->setDataValidation($absenceRange, $absenceValidation);

        // Add instructions at the bottom
        $instructionRow = $lastRow + 2;
        $sheet->setCellValue('A' . $instructionRow, 'INSTRUCTIONS:');
        $sheet->setCellValue('A' . ($instructionRow + 1), '• Ne modifiez pas les colonnes A et B (noms et CNE des étudiants)');
        $sheet->setCellValue('A' . ($instructionRow + 2), '• Colonne C: Saisissez les notes sur 20 (ex: 15.5)');
        $sheet->setCellValue('A' . ($instructionRow + 3), '• Colonne D: Écrivez "absent" pour les étudiants absents, laissez vide sinon');
        $sheet->setCellValue('A' . ($instructionRow + 4), '• Sauvegardez le fichier avant de l\'importer dans le système');

        // Style the instructions
        $sheet->getStyle('A' . $instructionRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'D32F2F']
            ]
        ]);

        $sheet->getStyle('A' . ($instructionRow + 1) . ':A' . ($instructionRow + 4))->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '666666']
            ]
        ]);

        // Merge cells for instructions to make them more readable
        $sheet->mergeCells('A' . ($instructionRow + 1) . ':D' . ($instructionRow + 1));
        $sheet->mergeCells('A' . ($instructionRow + 2) . ':D' . ($instructionRow + 2));
        $sheet->mergeCells('A' . ($instructionRow + 3) . ':D' . ($instructionRow + 3));
        $sheet->mergeCells('A' . ($instructionRow + 4) . ':D' . ($instructionRow + 4));

        return $sheet;
    }
}
