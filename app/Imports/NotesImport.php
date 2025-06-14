<?php

namespace App\Imports;

use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;

class NotesImport implements ToCollection, WithHeadingRow, WithValidation, WithStartRow
{
    protected $ueId;
    protected $uploadedBy;
    protected $sessionType;
    protected $importedCount = 0;
    protected $errors = [];

    public function __construct($ueId, $uploadedBy, $sessionType)
    {
        $this->ueId = $ueId;
        $this->uploadedBy = $uploadedBy;
        $this->sessionType = $sessionType;
    }

    /**
     * Start reading from row 4 (skip UE info rows and headers)
     */
    public function startRow(): int
    {
        return 4; // Skip UE info rows (1-2) and header row (3), start from data
    }

    /**
     * Validate Excel file structure and UE match
     */
    public function validateExcelFile($filePath)
    {
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Read UE info from first row
            $ueInfo = $sheet->getCell('A1')->getValue();

            if (empty($ueInfo)) {
                $this->errors[] = "Format Excel invalide: Information UE manquante en ligne 1";
                return false;
            }

            // Extract UE code from the format "UE: CODE - NAME"
            if (preg_match('/UE:\s*([A-Z0-9]+)/', $ueInfo, $matches)) {
                $excelUeCode = $matches[1];

                // Get the selected UE code
                $selectedUe = \App\Models\UniteEnseignement::find($this->ueId);
                if (!$selectedUe) {
                    $this->errors[] = "UE sélectionnée introuvable";
                    return false;
                }

                if ($excelUeCode !== $selectedUe->code) {
                    $this->errors[] = "Erreur: Le fichier Excel est pour l'UE '{$excelUeCode}' mais vous avez sélectionné l'UE '{$selectedUe->code}'";
                    return false;
                }
            } else {
                $this->errors[] = "Format Excel invalide: Code UE non trouvé en ligne 1";
                return false;
            }

            return true;

        } catch (\Exception $e) {
            $this->errors[] = "Erreur de lecture du fichier Excel: " . $e->getMessage();
            return false;
        }
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                // Calculate actual row number (starting from row 4 in Excel)
                $actualRowNumber = $index + 4;

                // Get row values directly by position (since we know the structure)
                $rowArray = is_array($row) ? $row : $row->toArray();

                // Skip empty rows
                if (empty($rowArray) || count(array_filter($rowArray)) === 0) {
                    continue;
                }

                // Extract values by position (A, B, C, D columns)
                $nomEtudiant = isset($rowArray[0]) ? trim($rowArray[0]) : '';
                $cneEtudiant = isset($rowArray[1]) ? trim($rowArray[1]) : '';
                $noteValue = isset($rowArray[2]) ? trim($rowArray[2]) : '';
                $statutAbsence = isset($rowArray[3]) ? trim($rowArray[3]) : '';

                // Skip rows with no student data
                if (empty($nomEtudiant) && empty($cneEtudiant)) {
                    continue;
                }

                // Find student by CNE (matricule) first, then by name
                $etudiant = null;

                if (!empty($cneEtudiant)) {
                    $etudiant = User::where('matricule', $cneEtudiant)
                        ->where('role', 'etudiant')
                        ->first();
                }

                if (!$etudiant && !empty($nomEtudiant)) {
                    // Try exact match first
                    $etudiant = User::where('name', $nomEtudiant)
                        ->where('role', 'etudiant')
                        ->first();

                    // If not found, try partial match
                    if (!$etudiant) {
                        $etudiant = User::where('name', 'LIKE', '%' . $nomEtudiant . '%')
                            ->where('role', 'etudiant')
                            ->first();
                    }
                }

                if (!$etudiant) {
                    $this->errors[] = "Ligne {$actualRowNumber}: Étudiant non trouvé - {$nomEtudiant} ({$cneEtudiant})";
                    continue;
                }

                // Process absence status
                $isAbsent = false;
                if (!empty($statutAbsence)) {
                    $absenceStatus = strtolower($statutAbsence);
                    $isAbsent = in_array($absenceStatus, [
                        'absent', 'abs', 'a', 'absentee', 'absente',
                        '1', 'true', 'oui', 'yes', 'x'
                    ]);
                }

                // Process note value
                $note = null;
                if (!$isAbsent && !empty($noteValue)) {
                    // Clean and normalize note value
                    $cleanNote = str_replace(',', '.', $noteValue);

                    if (is_numeric($cleanNote)) {
                        $note = (float) $cleanNote;

                        // Validate note range
                        if ($note < 0 || $note > 20) {
                            $this->errors[] = "Ligne {$actualRowNumber}: Note invalide (doit être entre 0 et 20) - {$note}";
                            continue;
                        }
                    } else {
                        $this->errors[] = "Ligne {$actualRowNumber}: Format de note invalide - '{$noteValue}'";
                        continue;
                    }
                }

                // Create or update note in database
                Note::updateOrCreate(
                    [
                        'ue_id' => $this->ueId,
                        'etudiant_id' => $etudiant->id,
                        'session_type' => $this->sessionType
                    ],
                    [
                        'note' => $isAbsent ? null : $note,
                        'is_absent' => $isAbsent,
                        'uploaded_by' => $this->uploadedBy
                    ]
                );

                $this->importedCount++;

            } catch (\Exception $e) {
                $actualRowNumber = $index + 4;
                $this->errors[] = "Ligne {$actualRowNumber}: Erreur - " . $e->getMessage();
            }
        }
    }

    public function rules(): array
    {
        return [
            'nom_etudiant' => 'nullable|string',
            'cne_etudiant' => 'nullable|string',
            'note' => 'nullable|numeric|min:0|max:20',
            'statut_absence' => 'nullable|string'
        ];
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }
}
