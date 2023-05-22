<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/series', name: 'serie_')]
class SerieController extends AbstractController
{
    #[Route('/{page}', name: 'list', requirements: ["page" => "\d+"])]
    public function list(int $page = 1, SerieRepository $serieRepository): Response
    {
        //$series = $serieRepository->findBy([], ["popularity" => "DESC"], 55);
        //$series = $serieRepository->findBestSeries();

        $nbSeries = $serieRepository->count([]);
        $maxPage = ceil($nbSeries / Serie::MAX_RESULT);

        // page inférieur à 1
        if ($page < 1) {
            return $this -> redirectToRoute('serie_list', ['page' => 1]);;
        // page superieur au max page
        } else if ($page > $maxPage){
            return $this->redirectToRoute('serie_list', ['page' => $maxPage]);
        }else{

            $series = $serieRepository->findSeriesWithPagination($page);

            return $this->render('serie/list.html.twig', [
                'series' => $series,
                'currentPage' => $page,
                'maxPage' => $maxPage
            ]);
        }
    }

    #[Route('/detail/{id}', name: 'show', requirements: ["id" => "\d+"])]
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
    public function add(Request $request, SerieRepository $serieRepository): Response
    {
        $serie = new Serie();
        // instanciation du formulaire en lui passant l'instance de série
        $serieForm = $this->createForm(SerieType::class, $serie);

        //permet d'extraire les données de la requete
        $serieForm->handleRequest($request);

        if ($serieForm->isSubmitted() && $serieForm->isValid()){

            //traitement de la donnée
            // récupération des champs non mapped
            $genres = $serieForm->get('genres')->getData();
            $serie->setGenres(implode('/', $genres));
            $serie -> setDateCreated(new \DateTime());

            $serieRepository->save($serie, true);

            // redirige vers la page de détail
            $this->addFlash('success', 'Serie added !');
            return $this->redirectToRoute('serie_show', ['id' => $serie->getId()]);
        }
        return $this->render('serie/add.html.twig', [
            "serieForm" => $serieForm->createView()
        ]);
    }

    #[Route('/update/{id}', name: 'update', requirements: ["id" => "\d+"])]
    public function update(int $id, SerieRepository $serieRepository){

        $serie = $serieRepository->find($id);
        $serieForm = $this->createForm(SerieType::class, $serie);

        return $this-> render('serie/update.html.twig', [
            'serieForm' => $serieForm->createView()
            ]);

    }
}
