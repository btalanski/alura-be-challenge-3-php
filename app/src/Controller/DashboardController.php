<?php
namespace App\Controller;

use App\Entity\TransactionsReport;
use App\Repository\TransactionsReportRepository;
use App\Form\Type\TransactionsReportType;
use App\Service\ReportFileReader;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard_index")
     */
    public function index(
        Request $request, 
        ManagerRegistry $doctrine,
        TransactionsReportRepository $reportsReporitory,
        ReportFileReader $reportReader,
        FileUploader $fileUploader
    ): Response
    {
        $report = new TransactionsReport();
        $reports = $reportsReporitory->findAll();
        
        $form = $this->createForm(TransactionsReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reportFile = $form->get('file')->getData();

            if($reportFile){
                $reportFileSize = $reportFile->getSize();
                $reportFileName = $fileUploader->upload($reportFile);       

                // Process report file
                $reportReader->readReportFile($fileUploader->getFileFullPath());
                $reportTransactions = $reportReader->getReportTransactions();
                $reportTransactionsCount = count($reportTransactions);
                $currentReportDate = $reportReader->getReportDate();
                
                // Report validation
                $reportHasTransactions = true === $reportTransactionsCount > 0;
                $transactionDateExistsInDb = $reportsReporitory->checkIfReportDateExists($reportReader->getReportDate());
                
                if($reportHasTransactions && !$transactionDateExistsInDb)
                {
                    $entityManager = $doctrine->getManager();

                    // Persist report to DB
                    $report
                        ->setFileName($reportFileName)
                        ->setFileSize($reportFileSize)
                        ->setReportDate($currentReportDate)
                        ->setCreatedAt(new \DateTime());

                    $entityManager->persist($report);

                    // Batch insert Transactions from report to DB
                    // To do: Can be improved
                    $batchSize = 20;
                    for ($i = 1; $i < $reportTransactionsCount; ++$i) {
                        $entityManager->persist($reportTransactions[$i]);
                        if (($i % $batchSize) === 0) {
                            $entityManager->flush();
                            $entityManager->clear();
                        }
                    }

                    $entityManager->flush();

                    return $this->redirectToRoute('dashboard_index', ['success' => 1]); 
                }
                else {
                    $this->addFlash(
                        "error",
                        "Transações com a data de \"{$currentReportDate->format("Y-m-d")}\" já foram importadas previamente."
                    );
                }
                               
            }
        }

        return $this->renderForm('dashboard/index.html.twig', [
            'form' => $form,
            'reports' => $reports,
        ]);
        
    }
}