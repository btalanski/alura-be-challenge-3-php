<?php
namespace App\Controller;

use App\Entity\TransactionsReport;
use App\Repository\TransactionsReportRepository;
use App\Form\Type\TransactionsReportType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard_index")
     */
    public function index(
        Request $request, 
        ManagerRegistry $doctrine,
        TransactionsReportRepository $reportsReporitory,
        SluggerInterface $slugger
    ): Response
    {
        $report = new TransactionsReport();
        $reports = $reportsReporitory->findAll();
        $form = $this->createForm(TransactionsReportType::class, $report);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reportFile = $form->get('file')->getData();

            
            if($reportFile){
                $originalFilename = pathinfo($reportFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$reportFile->guessExtension();

                try {
                    $reportFile->move(
                        $this->getParameter('reports_directory'),
                        $newFilename
                    );

                    $reportFileSize = filesize($this->getParameter('reports_directory') . DIRECTORY_SEPARATOR . $newFilename);

                    $entityManager = $doctrine->getManager();

                    $report
                        ->setFileName($newFilename)
                        ->setFileSize($reportFileSize)
                        ->setReportDate(new \DateTime())
                        ->setCreatedAt(new \DateTime());

                    $entityManager->persist($report);
                    $entityManager->flush();

                    return $this->redirectToRoute('dashboard_index');

                } catch (FileException $e) {
                    // To do: Reporte de erros
                }
            }
        }
        return $this->renderForm('dashboard/index.html.twig', [
            'form' => $form,
            'reports' => $reports,
        ]);
        
    }
}