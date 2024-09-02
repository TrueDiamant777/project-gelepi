<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CsvDataExplorer\CsvManager;
use App\Service\CsvDataExplorer\CsvFilter;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AddElementType;

class GelepiListingController extends AbstractController
{


    /* ================================================== */

    #[Route('/lister_element', name: 'app_gelepi_listing')]
public function base(CsvManager $csvManager, CsvFilter $csvFilter): Response
{
    $metaListPath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MaterielType.csv';
    $listPath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MaterielListe.csv';
    $etatGarantiPath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MetaDataGaranti.csv';
    $etatSantePath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MetaDataSante.csv';

    if (!$csvManager->loadMetaData($metaListPath)) {
        return new Response('[GLPI : Cannot Find Meta]');
    }

    if (!$csvManager->loadEtatGarantiData($etatGarantiPath)) {
        return new Response('[GLPI : Cannot Find EtatGaranti Meta]');
    }

    if (!$csvManager->loadEtatSanteData($etatSantePath)) {
        return new Response('[GLPI : Cannot Find EtatSante Meta]');
    }

    if (!$csvManager->loadFileData($listPath)) {
        return new Response('[GLPI : Cannot Find History]');
    }

    $data = $csvManager->getFileData();
    $processedData = [];

    foreach ($data as $row) {
        // Skip rows where the first column is '0'
        if ($row[0] == '0') {
            continue;
        }

        // Skip empty rows
        if (empty(array_filter($row))) {
            continue;
        }

        // Fill missing columns with 'unknown'
        $processedRow = array_map(function($column) {
            return $column === '' ? 'unknown' : $column;
        }, $row);

        // Ensure all rows have at least 5 columns (or the number of header columns)
        while (count($processedRow) < count($data[0])) { // Assuming $data[0] is the header
            $processedRow[] = 'unknown';
        }

        $processedData[] = $processedRow;
    }
       return $this->render('tripleblockbase.html.twig', [
            'body_template' => 'gelepi_listing/ListeMateriel.html.twig',
            'csvData' => $csvManager->getFileData(),
        ]);
}


    /* ================================================== */
#[Route('/ajouter_element', name: 'app_gelepi_element')]
public function ajouter_materiel(Request $request, CsvManager $csvManager, CsvFilter $csvFilter): Response
{
    // Create the form
    $form = $this->createForm(AddElementType::class);
    $form->handleRequest($request);

    // Paths to your CSV files
    $metaListPath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MaterielType.csv';
    $listPath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MaterielListe.csv';
    $etatGarantiPath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MetaDataGaranti.csv';
    $etatSantePath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MetaDataSante.csv';

    // Load material types
    $materialTypes = [];
    $warrantyStates = [];
    $healthStates = [];

    if (($handle = fopen($metaListPath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            if (count($data) >= 2) {
                $materialTypes[] = ['id' => $data[0], 'name' => $data[3]];
            }
        }
        fclose($handle);
    }
    // MetaDataGaranti
    if (($handle = fopen($etatGarantiPath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            if (count($data) >= 2) {
                $warrantyStates[] = ['id' => $data[0], 'state' => $data[1]];
            }
        }
        fclose($handle);
    }
    // MetaDataSante
    if (($handle = fopen($etatSantePath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            if (count($data) >= 2) {
                $healthStates[] = ['id' => $data[0], 'state' => $data[1]];
            }
        }
        fclose($handle);
    }

if ($form->isSubmitted() && $form->isValid()) {
    $newId = null;

    if (file_exists($listPath)) {
        if (($handle = fopen($listPath, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                if (empty(array_filter($data))) {
                    continue;
                }
            $newId = $data[6];
            }
        fclose($handle);
        }
    }

    $data = $form->getData();
    
    $nextId = $newId + 1 ?? '0'; 
    $materialTypeId = $data['material_type'] ?? '0'; 

    $arriverTimestamp = $data['Arriver'] instanceof \DateTime ? $data['Arriver']->format('Y-m-d') : 'unknown';
    $garrantiTimeStamp = $data['DepartGaranti'] instanceof \DateTime ? $data['DepartGaranti']->format('Y-m-d') : 'unknown';
    $garrantiYearsRemaining = $data['DureeGaranti'] ?? '0'; 

    $warrantyStateId = $data['warranty_state'] ?? 'unknown'; 
    $healthStateId = $data['health_state'] ?? 'unknown'; 
    
    $additionalField = $data['AdditionalField'] ?? 'unknown'; 

    $newEntry = [
        $materialTypeId,
        $arriverTimestamp,
        $garrantiTimeStamp,
        $garrantiYearsRemaining,
        $warrantyStateId,
        $healthStateId,
        $nextId
    ];

    if (($handle = fopen($listPath, "a")) !== FALSE) {
        fputcsv($handle, $newEntry, ";");
        fclose($handle);
    }

    return $this->redirectToRoute('app_gelepi_element');
}



    
       return $this->render('tripleblockbase.html.twig', [
            'body_template' => 'gelepi_listing/AjouterMateriel.html.twig',
            'csvData' => $csvManager->getFileData(),
            'form' => $form->createView(),
            'materialTypes' => $materialTypes,
            'warrantyStates' => $warrantyStates,
            'healthStates' => $healthStates,
        ]);
}



    #[Route('/ajouter_variable', name: 'app_gelepi_variable')]
    public function addMaterialType(Request $request): Response
   {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MaterielType.csv';

        // Calculate the next ID
        $nextId = $this->getNextId($filePath);

        $form = $this->createForm(AddElementType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Prepare the new uplet (row) for the CSV
            $newEntry = [
                $nextId,                // Use the calculated next ID
                $data['category'],
                $data['section'],
                $data['name'],
            ];

            // Append the new entry to the CSV file
            if (($handle = fopen($filePath, 'a')) !== false) {
                fputcsv($handle, $newEntry, ';');
                fclose($handle);
            }

            // Redirect or return a response
            return $this->redirectToRoute('app_gelepi_variable'); // Redirect to the same form or another page
        }

        
    
       return $this->render('tripleblockbase.html.twig', [
            'body_template' => 'gelepi_listing/AjouterVariable.html.twig', 
            'form' => $form->createView()
        ]);
    }

    private function getNextId(string $filePath): int
    {
        $lastId = 0;

        if (file_exists($filePath)) {
            if (($handle = fopen($filePath, 'r')) !== false) {
                while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                    if (!empty($data) && is_numeric($data[0])) {
                        $lastId = max($lastId, (int)$data[0]);
                    }
                }
                fclose($handle);
            }
        }

        return $lastId + 1;
    }
}