<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CsvDataExplorer\CsvManager;
use App\Service\CsvDataExplorer\CsvFilter;

class GelepiBaseController extends AbstractController
{
     #[Route('/', name: 'app_accueil')]
    public function accueil(): Response
    {
        return $this->render('accueil/nav.html.twig', [
            'controller_name' => 'GelepiBaseController',
        ]);
    }





    
   #[Route('/base', name: 'app_base')]
    public function details(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'GelepiBaseController',
        ]);
    }
}