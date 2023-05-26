<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Form\SerieType;
use App\Repository\SeasonRepository;
use App\Repository\SerieRepository;
use App\Tools\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;

#[Route('/season', name: 'season_')]
class SeasonController extends AbstractController
{
    #[Route('/add/{id}', name: 'add', requirements: ["id" => "\d+"])]
    public function add(SerieRepository $serieRepository, SeasonRepository $seasonRepository, Request $request, EntityManagerInterface $entityManager, int $id, Uploader $uploader): Response
    {
        // récupération de l'instance de la série
        $serie = $serieRepository->find($id);
        $season = new Season();

        //association de l'instance de season avec la bonne serie
        $season->setSerie($serie);
        $seasonForm = $this->createForm(SeasonType::class, $season);

        $seasonForm->handleRequest($request);

        if($seasonForm->isSubmitted() && $seasonForm->isValid()){

            /**
             * @var UploadedFile $file
             */
            $file = $seasonForm->get('poster')->getData();

            if ($file){
                $newFileName = $uploader->save($file, $season->getSerie()->getName() . '-' . $season->getNumber(), $this->getParameter('upload_season_poster'));
                $season->setPoster($newFileName);
            }

            $seasonRepository->save($season, true);

/*           autre methode
            $entityManager->persist($season);
            $entityManager->flush();*/

            $this->addFlash('success', 'Season added on '. $season->getSerie()->getName());
            return $this->redirectToRoute('serie_show', ['id' => $season->getSerie()->getId()]);
        }


        return $this->render('season/add.html.twig', [
            'seasonForm'=> $seasonForm->createView()
        ]);
    }
}
