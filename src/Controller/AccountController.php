<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{ /**
    * Affichage de la page connexion
    * @Route("/login", name="account_login")
    * @return Reponse
    */
   public function login(AuthenticationUtils $utils)
   {

       // get the login error if there is one
       $error = $utils->getLastAuthenticationError();

       // last username entered by the user
       $username = $utils->getLastUsername();


       return $this->render('account/login.html.twig',[
           'username' => $username,
           'hasError' => $error !==null
       ]);
   }

   /**
     * Logout
     * @Route ("/logout", name="account_logout")
     * 
     * @return void
     */
    public function logout(){
        //security.yaml takes care of it

    }

     /**
     * Register page
     *@Route ("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager){

        $user= new User();

        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user,$user->getHash());

            //on modifie le mot de passe avec le setter
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success","Votre compte a bien été créé");

            return $this->redirectToRoute("account_login");
        }

        return $this->render("account/register.html.twig",[
            'form'=>$form->createView()
        ]);
    }

     /**
     * Modification du profil
     *@Route("/account/profile",name="account_profile")
     *@IsGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager){

        $user = $this->getUser();

        $form = $this->createForm(AccountType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success","Les informations de votre profil ont bien été modifiées.");

            
        }

        return $this->render('account/profile.html.twig',[
            'form'=>$form->createView()
        ]);
    }

        /**
     * Modification du mot de pass
     * @Route("/account/update-password", name="account_password")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager){

        $passwordUpdate = new PasswordUpdate();
        $user= $this->getUser();
        $form=$this->createForm(PasswordUpdateType::class,$passwordUpdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //mot de passe actuel n'est pas le bon
            if (!password_verify($passwordUpdate->getOldPassword(),$user->getHash())) {
                //message d'erreur
                //$this->addFlash("warning","Mot de passe actuel incorrect");

                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez entré n'est pas votre mot de passe actuel"));
            }else {
                $newPassword = $passwordUpdate->getNewPassword();

                //encode new password
                $hash = $encoder->encodePassword($user,$newPassword);

                //on modifie le nouveau mdp
                $user->setHash($hash);
                
                //on enregistre
                $manager->persist($user);

                $manager->flush();

                //on ajoute un message
                $this->addFlash("success","Votre nouveau mot de passe a bien été enregistré");

                //on redirige
                return $this->redirectToRoute("account_profile");
            }
        }
        return $this->render('account/password.html.twig',[
            'form'=>$form->createView()
        ]);
    }

     /**
     * Page mon compte
     *@Route("/account",name="account_home")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function myAccount(){
        return $this->render("user/index.html.twig",['user'=>$this->getUser()]);
    }
}
