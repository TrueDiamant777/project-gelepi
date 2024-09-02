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

    return $this->render('gelepi_listing/ListeMateriel.html.twig', [
        'controller_name' => 'GelepiListingController',
        'csvData' => $processedData,
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

    // Load material types for the dropdown
    $materialTypes = [];
    if (($handle = fopen($metaListPath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            if (count($data) >= 2) {
                $materialTypes[] = ['id' => $data[0], 'name' => $data[3]];
            }
        }
        fclose($handle);
    }

    // Load warranty states (MetaDataGaranti)
    $warrantyStates = [];
    if (($handle = fopen($etatGarantiPath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            if (count($data) >= 2) {
                $warrantyStates[] = ['id' => $data[0], 'state' => $data[1]];
            }
        }
        fclose($handle);
    }

    // Load health states (MetaDataSante)
    $healthStates = [];
    if (($handle = fopen($etatSantePath, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            if (count($data) >= 2) {
                $healthStates[] = ['id' => $data[0], 'state' => $data[1]];
            }
        }
        fclose($handle);
    }

    // Check if the form is submitted and valid
    if ($form->isSubmitted() && $form->isValid()) {
        // Get the form data
        $data = $form->getData();

        // For debugging, you might want to log or dump the data
        // dump($data); // Uncomment this if you want to see the form data in the Symfony profiler
        // die(); // Stop execution to check the data

        // Here you would validate the data further if necessary
        // Save or process the data
        $newEntry = [
            $data['Arriver'], 
            $data['DepartGaranti'], 
            $data['DureeGaranti'],
            // Add other fields as needed
        ];

        // Append to the CSV file or process the data as needed
        if (($handle = fopen($listPath, "a")) !== FALSE) {
            fputcsv($handle, $newEntry, ";");
            fclose($handle);
        }

        // Redirect or return a response
        return $this->redirectToRoute('app_gelepi_element'); // Redirect back to the form page or another page
    }

    // Render the form view
    return $this->render('gelepi_listing/AjouterMateriel.html.twig', [
        'form' => $form->createView(),
        'materialTypes' => $materialTypes,
        'warrantyStates' => $warrantyStates,
        'healthStates' => $healthStates,
    ]);
}


//function i would move in another place later

}