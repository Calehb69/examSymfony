<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\FilmType;
use App\Form\ImpressionType;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    #[Route('/film', name: 'film')]
    public function index(FilmRepository $films): Response
    {
        return $this->render('film/index.html.twig', [
            'films' => $films->findAll()
        ]);
    }

    /**
     * @Route("unfilm/{id}", name="unfilm")
     * @return Response
     */
    public function show(Film $film, Request $request, EntityManagerInterface $manager): Response
    {
        $impression = new Impression();
        $formulaire = $this->createForm(ImpressionType::class, $impression);

        $formulaire->handleRequest($request);

        if($formulaire->isSubmitted()){

            $impression->setFilm($film);
            $manager->persist($impression);
            $manager->flush();

        }

        return $this->renderForm('film/show.html.twig', [
            'film' => $film,
            'formulaire'=>$formulaire
        ]);
    }

    /**
     * @Route("/film/new", name="film_new")
     */
    public function new(Request $request, EntityManagerInterface $manager)
    {
        $film = new Film();

        $formulaire = $this->createForm(FilmType::class, $film);

        $formulaire->handleRequest($request);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {

            $film = $formulaire->getData();

            $manager->persist($film);
            $manager->flush();

            return $this->redirectToRoute('film');
        }

        return $this->renderForm('film/new.html.twig', ["formulaire" => $formulaire]);
    }

    /**
     * @Route("/filmsupprimer/{id}", name="filmsupprimer")
     *
     * @return Response
     */
    public function suppr(Film $film = null, entityManagerInterface $manager)
    {
        if ($film) {

            $manager->remove($film);
            $manager->flush();
        }

        return $this->redirectToRoute('film');
    }

    /**
     * @Route("/film/change/{id}", name="changefilm")
     * @param Film $film
     * @param Request
     * @return Response $manager
     */
    public function change(Film $film, Request $request, EntityManagerInterface $manager)
    {
        $formulaire = $this->createForm(FilmType::class, $film);

        $formulaire->handleRequest($request);
        if($formulaire->isSubmitted()){

        $manager->persist($film);
        $manager->flush();

        return $this->redirectToRoute('film');
        }

        return $this->renderForm("film/new.html.twig", ["formulaire"=>$formulaire]);
    }
}