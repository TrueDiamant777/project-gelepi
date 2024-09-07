<?php

namespace App\Service\CsvDataExplorer;

class CsvFilter
{
    private $csvManager;
    private $translations;

    public function __construct(CsvManager $csvManager, string $lang = 'fr')
    {
        $this->csvManager = $csvManager;

        // Définitions des chaînes de caractères en fonction de la langue choisie
        $this->translations = $this->getTranslations($lang);
    }

    // Méthode pour récupérer les traductions en fonction de la langue
    private function getTranslations(string $lang): array
    {
        $translations = [
            'fr' => [
                'multiprise' => 'MultiPriseM1',
                'toujours' => 'Toujours*',
                'foutu' => 'Foutu',
                'chaise_evite' => 'Chaise que tout le monde evite',
            ],
            'en' => [
                'multiprise' => 'PowerStripM1',
                'toujours' => 'Always*',
                'foutu' => 'Broken',
                'chaise_evite' => 'Chair everyone avoids',
            ],
        ];

        return $translations[$lang] ?? $translations['fr']; // Par défaut, le français
    }

    public function applyDemoLogic(): void
    {
        $data = $this->csvManager->getFileData();
        foreach ($data as &$row) {
            // Exemple de filtre en utilisant les traductions
            if ($row[0] == $this->translations['multiprise']) {
                $row[5] = $this->translations['toujours'];
            }
            if ($row[5] == $this->translations['foutu']) {
                $row[3] = $this->translations['chaise_evite'];
            }
        }

        $this->csvManager->setFileData($data);
    }

    public function placeholder(): void
    {
        $data = $this->csvManager->getFileData();
        foreach ($data as &$row) {
            if ($row[0] == $this->translations['multiprise']) {
                $row[5] = $this->translations['toujours'];
            }
        }

        $this->csvManager->setFileData($data);
    }
}
