<?php

namespace App\Tests\Service\CsvDataExplorer;

use PHPUnit\Framework\TestCase;
use App\Service\CsvDataExplorer\CsvManager;

class CsvManagerTest extends TestCase
{
    private $mockedDataDir;

    protected function setUp(): void
    {
        $this->mockedDataDir = __DIR__ . '/mocked_data';

        if (!is_dir($this->mockedDataDir)) {
            mkdir($this->mockedDataDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob("$this->mockedDataDir/*.*"));
        rmdir($this->mockedDataDir);
    }

    public function testLoadMetaData(): void
    {
        $csvManager = new CsvManager();

        $metaListPath = $this->mockedDataDir . '/MaterielType.csv';

        // Créer un fichier CSV fictif pour les tests
        file_put_contents($metaListPath, "Id;Categorie;Section;Nom\n1;Informatique;Infrastructure;CableHDMI200\n");

        $result = $csvManager->loadMetaData($metaListPath);

        $this->assertTrue($result, 'La méthode loadMetaData doit retourner true pour un fichier valide.');
        $this->assertArrayHasKey(1, $csvManager->getMetaData(), 'La méthode loadMetaData doit charger les données correctement.');
        $this->assertEquals('CableHDMI200', $csvManager->getMetaData()[1], 'La donnée doit être correctement chargée dans le tableau metaData.');
    }

    public function testLoadEtatGarantiData(): void
    {
        $csvManager = new CsvManager();

        $etatGarantiPath = $this->mockedDataDir . '/MetaDataGaranti.csv';

        // Créer un fichier CSV fictif pour les tests
        file_put_contents($etatGarantiPath, "Code;Description\n1;Garantie 2 ans\n");

        $result = $csvManager->loadEtatGarantiData($etatGarantiPath);

        $this->assertTrue($result, 'La méthode loadEtatGarantiData doit retourner true pour un fichier valide.');
        $this->assertArrayHasKey(1, $csvManager->getEtatGarantiData(), 'La méthode loadEtatGarantiData doit charger les données correctement.');
        $this->assertEquals('Garantie 2 ans', $csvManager->getEtatGarantiData()[1], 'La donnée doit être correctement chargée dans le tableau etatGarantiData.');
    }

    public function testLoadEtatSanteData(): void
    {
        $csvManager = new CsvManager();

        $etatSantePath = $this->mockedDataDir . '/MetaDataSante.csv';

        // Créer un fichier CSV fictif pour les tests
        file_put_contents($etatSantePath, "Code;Description\n1;En bon état\n");

        $result = $csvManager->loadEtatSanteData($etatSantePath);

        $this->assertTrue($result, 'La méthode loadEtatSanteData doit retourner true pour un fichier valide.');
        $this->assertArrayHasKey(1, $csvManager->getEtatSanteData(), 'La méthode loadEtatSanteData doit charger les données correctement.');
        $this->assertEquals('En bon état', $csvManager->getEtatSanteData()[1], 'La donnée doit être correctement chargée dans le tableau etatSanteData.');
    }

    public function testLoadFileData(): void
    {
        $csvManager = new CsvManager();

        $filePath = $this->mockedDataDir . '/MaterielListe.csv';

        // Créer un fichier CSV fictif pour les tests
        file_put_contents($filePath, "Id;Categorie;Section;Nom;Garantie;EtatSante\n1;1;1;CableHDMI200;1;1\n");

        $result = $csvManager->loadFileData($filePath);

        $this->assertTrue($result, 'La méthode loadFileData doit retourner true pour un fichier valide.');
        $fileData = $csvManager->getFileData();
        $this->assertIsArray($fileData, 'La méthode loadFileData doit charger les données sous forme de tableau.');
        $this->assertCount(2, $fileData, 'La méthode getFileData doit retourner le nombre correct de lignes, y compris l\'en-tête.');
        $this->assertEquals('CableHDMI200', $fileData[1][3], 'La donnée doit être correctement chargée dans le tableau fileData.');
    }
}
