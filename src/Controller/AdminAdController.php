<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads", name="admin_ads_list")
     */
    public function index(AdRepository $repo)
    {
        return $this->render('admin/ad/index.html.twig', [
          'ads'=>$repo->findAll()
        ]);
    }


    /**
     * Edit annonces pour admin
     * @Route("admin/ads/{id}/edit", name="admin_ads_edit")
     * @param Ad $ad
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager){

        $form = $this->createForm(AnnonceType::class,$ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success',"Les informations ont bien été enregistées");
        }


        return $this->render('admin/ad/edit.html.twig', [
            'ad'=>$ad,
            'form'=>$form->createView()
        ]);

    }

    /**
     * Delete an Ad
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Ad $ad,EntityManagerInterface $manager){

        if(count($ad->getBookings()) > 0) {
            $this->addFlash("warning","Vous ne pouvez pas supprimer une annonce qui possède des réservations");
        }else{

            
            $manager->remove($ad);
            $manager->flush();
            $this->addFlash('success',"L'annonce: {$ad->getTitle()} a bien été suprimée.");
            
        }
        return $this->redirectToRoute("admin_ads_list");

    }
}
