<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\ImpressionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImpressionController extends AbstractController
{
    #[Route("/impression/new/{id}", name:"impression_new")]
    public function new(Request $request, EntityManagerInterface $manager, Film $film): Response
    {
        if(!$film){
            return $this->redirectToRoute('film');
        }

        $impression = new Impression();
        $formulaire = $this->createForm(ImpressionType::class, $impression);

        $formulaire->handleRequest($request);

        if($formulaire->isSubmitted() && $formulaire->isValid()){

            $impression->setFilm($film);
            $manager->persist($impression);
            $manager->flush();
        }

        return $this->redirectToRoute('unfilm', ['id'=>$film->getId()]);
    }

    /**
     * @Route ("/impression/suppr/{id}", name="impression_suppr")
     */
    public function suppr(Impression $impression=null, EntityManagerInterface $manager): Response
    {
        if ($impression && $impression->getImpression() == $this->getImpression()) {
            $id = $impression->getFilm()->getId();
            $manager->remove($impression);
            $manager->flush();
            return $this->redirectToRoute('unfilm', ['id'=>$id]);
        }

        return  $this->redirectToRoute('film');
    }

    /**
     * @Route("/impression/change/{id}", name="impression_change")
     */
    public function change(Impression $impression, Impression $request, EntityManagerInterface $manager){
        if( $impression->getImpression() !== $this->getImpression()){
            return $this->redirectToRoute('unfilm');
        }

        $formulaire = $this->createForm(ImpressionType::class, $impression);
        $formulaire->handleRequest($request);

        if($formulaire->isSubmitted() && $formulaire->isValid()){

            $manager->persist($impression);
            $manager->flush();
            return $this->redirectToRoute('unfilm', ['id'=>$impression->getFilm()->getId()]);
        }
    }
}
