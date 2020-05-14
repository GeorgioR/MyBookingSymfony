<?php

namespace App\Controller;


use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     * @param Ad $ad
     * @return Response 
     */
    public function book(Ad $ad, Request $request,EntityManagerInterface $manager)
    {
        $booking= new Booking();
        $form= $this->createForm(BookingType::class,$booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user= $this->getUser();

            $booking->setBooker($user)
                    ->setAd($ad);

            //si les dates ne sont pas disponibles
            if (!$booking->isBookabledays()) {
                $this->addFlash("warning","Les dates choisies ne sont pas disponibles.");
            }
            else {
               
                $manager->persist($booking);
                $manager->flush(); 
    
                return $this->redirectToRoute("booking_show",['id'=>$booking->getId(),'alert'=>true]);
            }

        }
        return $this->render('booking/book.html.twig', [
            'ad'=>$ad,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Affiche une réservation
     * @Route("/booking/{id}", name="booking_show") 
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking, Request $request, EntityManagerInterface $manager){

        $comment= new Comment();
        $form=$this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())  {   
            
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser())
                    ;

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash("success", "Votre commentaire a bien été enregistré");
        }

        return $this->render('booking/show.html.twig',[
            'booking'=>$booking, 'form'=>$form->createView()
        ]);
    }
}
