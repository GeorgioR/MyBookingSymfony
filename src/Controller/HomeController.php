<?php

//namespace:chemin du controller
namespace App\Controller;


//Pour créer une page: - une fonction publique (classe)/ -une route/ -une réponse

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

    /**
     * Création de la route
     * @Route("/", name="homepage")
     */

    public function home(AdRepository $adRepo, UserRepository $userRepo){
        
        return $this->render('home.html.twig', 
                            ['ads'=>$adRepo->findBestAds(6),
                            'users'=>$userRepo->findBestUsers()
                            ]                        
    );
    }

}