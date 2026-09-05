<?php

namespace App\Controller;

use App\Service\ComptesMainEventClient;
use App\Service\DataReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'active' => 'home'
        ]);
    }

    #[Route('/inscription', name: 'inscription')]
    public function inscription(): Response
    {
        return $this->render('home/inscription.html.twig', [
            'active' => 'inscription'
        ]);
    }

    #[Route('/structure', name: 'structure')]
    public function structure(): Response
    {
        $structure = DataReader::getStructure(9, 30);
        return $this->render('home/structure.html.twig', [
            'active' => 'structure',
            'structure' => $structure
        ]);
    }

    #[Route('/dotation', name: 'dotation')]
    public function dotation(): Response
    {
        // $prizepool = [1300, 900, 700, 500, 350, 250, 200, 125, 125, 125, 100, 100, 100, 75, 75, 75, 75, 75, 75, 50, 50, 50, 50, 50, 50];
        $dotation = DataReader::getDotation();
        return $this->render('home/dotation.html.twig', [
            'active' => 'dotation',
            'dotation' => $dotation
        ]);
    }

    #[Route('/dotation150', name: 'dotation150')]
    public function dotation150(): Response
    {
        // $prizepool = [1300, 900, 700, 500, 350, 250, 200, 125, 125, 125, 100, 100, 100, 75, 75, 75, 75, 75, 75, 50, 50, 50, 50, 50, 50];
        $dotation = DataReader::getDotation150();
        return $this->render('home/dotation150.html.twig', [
            'active' => 'dotation',
            'dotation' => $dotation
        ]);
    }


    #[Route('/side-event', name: 'side')]
    public function side(): Response
    {
        return $this->render('home/side.html.twig', [
            'active' => 'side'
        ]);
    }

    #[Route('/infos-pratiques', name: 'infos')]
    public function infos(): Response
    {
        return $this->render('home/infos.html.twig', [
            'active' => 'infos'
        ]);
    }

    #[Route('/classement', name: 'classement')]
    public function classement(): Response
    {
        $players = DataReader::getRanking();
        return $this->render('home/classement.html.twig', [
            'active' => 'classement',
            'players' => $players
        ]);
    }

    #[Route('/liste', name: 'liste')]
    public function liste(ComptesMainEventClient $comptesMainEventClient): Response
    {
        $result = $comptesMainEventClient->fetchParticipants();
        return $this->render('home/liste.html.twig', [
            'active' => 'liste',
            'players' => $result['players'],
            'nbPlayers' => count($result['players']),
            'error' => $result['error'],
        ]);
    }
}
