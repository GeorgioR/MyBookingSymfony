<?php

namespace App\Controller;

use App\Service\Statistics;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(EntityManagerInterface $manager, Statistics $statsService)
    {
        $stats=$statsService->getStatistics();
        

        

        $bestAds= $statsService->getAdsStats('DESC');

        $worstAds= $statsService->getAdsStats('ASC');


        

        return $this->render('admin/dashboard/index.html.twig', [
            // 'users'=>$users,
            // 'ads'=>$ads,
            // 'bookings'=>$bookings,
            // 'comments'=>$comments
            'stats'=>$stats,
            'bestAds'=>$bestAds,
            'worstAds'=>$worstAds
        ]);
    }
}
