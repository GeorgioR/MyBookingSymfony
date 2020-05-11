<?php

namespace App\DataFixtures;


use App\Entity\Ad;
use App\Entity\Booking;
use Faker\Factory;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
        
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker= Factory::create('FR-fr');

        //GESTION DES ROLES
        $adminRole= new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        //Création d'un utilisateur avec un role admin
        $adminUser=new User();
        $adminUser->setFirstName('Georgio')
                ->setLastname('Rahal')
                ->setEmail('georgiorahal@gmail.com')
                ->sethash($this->encoder->encodePassword($adminUser,'password'))
                ->setAvatar('https://randomuser.me/api/portraits/men/33.jpg')
                ->setIntroduction($faker->sentence())
                ->setDescription("<p>".join("</p><p>",$faker->paragraphs(5))."</p>")
                ->addUserRole($adminRole)
                ;

                $manager->persist($adminUser);


        //utilisateurs

        $users=[];
        $genres=['male','female'];


        for ($i=1; $i<= 10 ; $i++) {
            $user= new User();
            //avatar
            $genre=$faker->randomElement($genres);

            $avatar='https://randomuser.me/api/portraits/';
            $avatarId= $faker->numberBetween(1,99).'.jpg';
            $avatar.=($genre== 'male' ? 'men/': 'women/') .$avatarId;
            $hash = $this->encoder->encodePassword($user,'password');

            $description= "<p>".join("</p><p>",$faker->paragraphs(5))."</p>";
            $user->setDescription($description)
                ->setFirstname($faker->firstname)
                ->setLastname($faker->lastname)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence)
                ->setHash($hash)
                ->setAvatar($avatar)
                //->setUsername($faker->email)
                ;
            $manager->persist($user);
            $users[]=$user;
            
        }


        //ANNONCES

        for ($i=1; $i <=30 ; $i++) { 
        
            
            $ad = new Ad();

            $title = $faker->sentence(20);
            // $coverImage= $faker->imageUrl(1000,350);
            $coverImage='https://i.picsum.photos/id/';
            $coverImageId=$faker->numberBetween(100,999).'/640/480.jpg';
            $coverImage.=$coverImageId;
            $introduction = $faker->paragraph(2);
            $content = "<p>".join("</p><p>",$faker->paragraphs(5))."</p>";
            $user= $users[mt_rand(0,count($users)-1)];
            
        

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(30,250))
                ->setRooms(mt_rand(1,5))
                ->setAuthor($user)
                
                ;

            $manager ->persist($ad);

            for ($j=1; $j<=mt_rand(2,5); $j++) { 
             $image = new Image();

             $urlImage='https://i.picsum.photos/id/';
             $urlImageId=$faker->numberBetween(100,999).'/640/480.jpg';
             $urlImage.=$urlImageId;

             $image->setUrl($urlImage)
                    ->setCaption($faker->sentence())
                    ->setAd($ad)
                    ;

                $manager->persist($image);   
            }
        

            //RESERVATIONS
            for($k=1;$k <= mt_rand(0,5);$k++ ){
                $booking= new Booking();
                $createdAt= $faker->dateTimeBetween('-6 months');
                $startDate= $faker->dateTimeBetween('-3 months');
                $duration= mt_rand(3,10);
                //endDate is equal to startDate + the duration of the stay
                //clone to keep startDate and not to erase it 
                $endDate= (clone $startDate)->modify("+ $duration days");
                //the amount is the price times the number of days
                $amount= $ad->getPrice()* $duration;

                //find booker
                $booker = $users[mt_rand(0,count($users)-1)];
                $comment =$faker->paragraph();

                //config reservations
                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($comment)
                        ;
                        $manager->persist($booking);
            }

         }
        $manager->flush();
    }
}
