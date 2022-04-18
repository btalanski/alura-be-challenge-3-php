<?php
namespace App\Controller;

use App\Entity\TransactionsReport;
use App\Repository\TransactionsReportRepository;
use App\Form\Type\TransactionsReportType;
use App\Service\ReportFileReader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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
        ReportFileReader $reportReader,
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
                $reportFileSize = $reportFile->getSize();

                $originalFilename = pathinfo($reportFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$reportFile->guessExtension();
                $reportFileTargetLocation = $this->getParameter('reports_directory') . DIRECTORY_SEPARATOR . $newFilename;
                
                // Moves the file to a new location
                try {
                    $reportFile->move(
                        $this->getParameter('reports_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // To do: handle upload error
                }                

                // Parse report file and get transactions
                $reportReader->readReportFile($reportFileTargetLocation);
                $reportTransactions = $reportReader->getReportTransactions();
                $currentReportDate = $reportTransactions[0]->getTransactionDatetime();
                
                // Report validation
                $transactionDateExistsInDb = $reportsReporitory->checkIfReportDateExists($currentReportDate);
                
                if(!$transactionDateExistsInDb)
                {
                    $entityManager = $doctrine->getManager();

                    // Persist report to DB
                    $report
                        ->setFileName($newFilename)
                        ->setFileSize($reportFileSize)
                        ->setReportDate($currentReportDate)
                        ->setCreatedAt(new \DateTime());

                    $entityManager->persist($report);

                    // Batch insert Transactions from report to DB
                    // To do: Can be improved
                    $batchSize = 20;
                    for ($i = 1; $i < count($reportTransactions); ++$i) {
                        $entityManager->persist($reportTransactions[$i]);
                        if (($i % $batchSize) === 0) {
                            $entityManager->flush();
                            $entityManager->clear();
                        }
                    }

                    $entityManager->flush();

                    return $this->redirectToRoute('dashboard_index'); 
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