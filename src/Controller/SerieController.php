<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/series', name: 'serie_')]
class SerieController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(SerieRepository $serieRepository): Response
    {
        //$series = $serieRepository->findBy([], ["popularity" => "DESC"], 50);
        $series = $serieRepository->findBestSeries();
        return $this->render('serie/list.html.twig', [
            'series' => $series
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ["id" => "\d+"])]
    public function show(int $id, SerieRepository $serieRepository): Response
    {
       $serie = $serieRepository->find($id);

       if (!$serie){
           throw  $this->createNotFoundException("The serie is not found...");
       }


        return $this->render('serie/show.html.twig', [
            'serie' => $serie
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(): Response
    {
        // TODO renvoyer un formulaire pour ajouter une nouvelle serie en bd


        return $this->render('serie/add.html.twig');
    }


}
