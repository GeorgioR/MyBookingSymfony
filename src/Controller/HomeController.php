<?php

//namespace:chemin du controller
namespace App\Controller;


//Pour créer une page: - une fonction publique (classe)/ -une route/ -une réponse


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

    /**
     * Création de la route
     * @Route("/", name="homepage")
     */

    public function home(){
        $noms=['Durant'=>'visiteur', 'Francois'=>'Admin', 'Dupont'=>'contributeur'];
        return $this->render('home.html.twig', ['titre'=>'Site d\'annonces', 'acces'=>'admin', 'tableau'=>$noms]);
    }

}