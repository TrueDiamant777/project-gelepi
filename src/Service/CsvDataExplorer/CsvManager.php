<?php

namespace App\Service\CsvDataExplorer;

class CsvManager
{
    private $metaData = [];
    private $fileData = [];
    private $header = [];
    private $etatGarantiData = []; // Nouvelle propriété pour les états de garantie
    private $etatSanteData = [];   // Nouvelle propriété pour les états de santé

    public function loadMetaData(string $metaListPath): bool
    {
        if (!file_exists($metaListPath)) {
            return false;
        }

        if (($handle = fopen($metaListPath, 'r')) !== false) {
            fgetcsv($handle, 1000, ';'); // Lire et ignorer l'en-tête
            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                $this->metaData[$data[0]] = $data[3]; // $data[0] = Id, $data[3] = Nom
            }
            fclose($handle);
            return true;
        }
        return false;
    }

    public function loadEtatGarantiData(string $etatGarantiPath): bool
    {
        if (!file_exists($etatGarantiPath)) {
            return false;
        }

        if (($handle = fopen($etatGarantiPath, 'r')) !== false) {
            fgetcsv($handle, 1000, ';'); // Lire et ignorer l'en-tête
            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                $this->etatGarantiData[$data[0]] = $data[1]; // $data[0] = Code, $data[1] = Description
            }
            fclose($handle);
            return true;
        }
        return false;
    }

    public function loadEtatSanteData(string $etatSantePath): bool
    {
        if (!file_exists($etatSantePath)) {
            return false;
        }

        if (($handle = fopen($etatSantePath, 'r')) !== false) {
            fgetcsv($handle, 1000, ';'); // Lire et ignorer l'en-tête
            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                $this->etatSanteData[$data[0]] = $data[1]; // $data[0] = Code, $data[1] = Description
            }
            fclose($handle);
            return true;
        }
        return false;
    }

    public function loadFileData(string $listPath): bool
    {
        if (!file_exists($listPath)) {
            return false;
        }

        if (($handleList = fopen($listPath, 'r')) !== false) {
            $this->header = fgetcsv($handleList, 1000, ';'); // Lire et stocker l'en-tête

            while (($data = fgetcsv($handleList, 1000, ';')) !== false) {
                if ($data[0] == '0') {
                    continue;
                }

                // Remplacement des ID avec les noms de matériels
                if (isset($this->metaData[$data[0]])) {
                    $data[0] = $this->metaData[$data[0]];
                }

                // Remplacement des codes d'état de garantie avec les descriptions
                if (isset($this->etatGarantiData[$data[4]])) {
                    $data[4] = $this->etatGarantiData[$data[4]];
                }

                // Remplacement des codes d'état de santé avec les descriptions
                if (isset($this->etatSanteData[$data[5]])) {
                    $data[5] = $this->etatSanteData[$data[5]];
                }

                $this->fileData[] = $data;
            }
            fclose($handleList);
            return true;
        }
        return false;
    }

    public function getFileData(): array
    {
        return [$this->header] + $this->fileData; // Ajouter l'en-tête aux données du fichier
    }

    public function setFileData(array $fileData): void
    {
        $this->fileData = $fileData;
    }

    public function getMetaChoices(): array
    {
        $choices = [];
        foreach ($this->metaData as $row) {
            $choices[$row[3]] = $row[0]; // Assuming $row[3] is the "Nom" and $row[0] is the "Id"
        }
        return $choices;
    }

    public function getEtatGarantiChoices(): array
    {
        $choices = [];
        foreach ($this->etatGarantiData as $row) {
            $choices[$row[1]] = $row[0]; // Hypothetical: $row[1] is the display value, $row[0] is the identifier
        }
        return $choices;
    }

    public function getEtatSanteChoices(): array
    {
        $choices = [];
        foreach ($this->etatSanteData as $row) {
            $choices[$row[1]] = $row[0];
        }
        return $choices;
    }
}
