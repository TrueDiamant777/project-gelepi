<?php

namespace App\Service\CsvDataExplorer;

class CsvFilter
{
    private $csvManager;

    public function __construct(CsvManager $csvManager)
    {
        $this->csvManager = $csvManager;
    }

    public function applyDemoLogic(): void
    {
        $data = $this->csvManager->getFileData();
        foreach ($data as &$row) {
            //exemple de filtre
            if ($row[0] == 'MultiPriseM1') {
                $row[5] = 'Toujours*';
            }
             if ($row[5] == 'Foutu') {
                $row[3] = 'Chaise Honte';
            }
        }

        $this->csvManager->setFileData($data);
    }
        public function placeholder(): void
    {
        $data = $this->csvManager->getFileData();
        foreach ($data as &$row) {
            if ($row[0] == 'MultiPriseM1') {
                $row[5] = 'Toujours*';
            }
        }

        $this->csvManager->setFileData($data);
    }
}
