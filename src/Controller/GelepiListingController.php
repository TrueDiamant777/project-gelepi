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
        $etatGarantiPath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MetaDataGaranti.csv'; // Nouveau fichier pour l'état de garantie
        $etatSantePath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MetaDataSante.csv';   // Nouveau fichier pour l'état de santé

        if (!$csvManager->loadMetaData($metaListPath)) {
            return new Response('[GLPI : Cannot Find Meta]');
        }

        if (!$csvManager->loadEtatGarantiData($etatGarantiPath)) { // Charger les données d'état de garantie
            return new Response('[GLPI : Cannot Find EtatGaranti Meta]');
        }

        if (!$csvManager->loadEtatSanteData($etatSantePath)) { // Charger les données d'état de santé
            return new Response('[GLPI : Cannot Find EtatSante Meta]');
        }

        if (!$csvManager->loadFileData($listPath)) {
            return new Response('[GLPI : Cannot Find History]');
        }

        // Appliquer la logique personnalisée
        $csvFilter->applyDemoLogic();

        return $this->render('gelepi_listing/ListeMateriel.html.twig', [
            'controller_name' => 'GelepiListingController',
            'csvData' => $csvManager->getFileData(),
        ]);
    }


    /* ================================================== */
    #[Route('/ajouter_element', name: 'app_gelepi_element')]
       public function ajouter_materiel(Request $request, CsvManager $csvManager, CsvFilter $csvFilter): Response
    {
        $form = $this->createForm(AddElementType::class);
        $form->handleRequest($request);
        
        $metaListPath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MaterielType.csv';
        $listPath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MaterielListe.csv';
        $etatGarantiPath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MetaDataGaranti.csv'; // Nouveau fichier pour l'état de garantie
        $etatSantePath = $this->getParameter('kernel.project_dir') . '/public/GLPISYS/MetaDataSante.csv';   // Nouveau fichier pour l'état de santé

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$csvManager->loadMetaData($metaListPath)) {
                return new Response('[GLPI : Cannot Find Meta]');
            }
            if (!$csvManager->loadEtatGarantiData($etatGarantiPath)) { // Charger les données d'état de garantie
                return new Response('[GLPI : Cannot Find EtatGaranti Meta]');
            }
            if (!$csvManager->loadEtatSanteData($etatSantePath)) { // Charger les données d'état de santé
                return new Response('[GLPI : Cannot Find EtatSante Meta]');
            }
            if (!$csvManager->loadFileData($listPath)) {
                return new Response('[GLPI : Cannot Find History]');
            }



            //result of submit
            $data = $form->getData();
            return new Response('Form submitted: ' . json_encode($data));
        }

        return $this->render('gelepi_listing/AjouterMateriel.html.twig', [
            'form' => $form->createView(), 
        ]);
    }
}