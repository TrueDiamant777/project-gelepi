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
        return $this->render('tripleblockbase.html.twig', [
            'controller_name' => 'GelepiBaseController',
        ]);
    }
}