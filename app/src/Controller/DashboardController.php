<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard_index")
     */
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', []);
    }
}