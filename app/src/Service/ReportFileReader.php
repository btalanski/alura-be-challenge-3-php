<?php

namespace App\Service;

use App\Entity\Transaction;

class ReportFileReader
{
    protected $filePath;
    protected $reportContent;
    protected $reportDate;
    private const EXPECTED_NUMBER_OF_FIELDS = 8;

    public function readReportFile(string $filePath){
        $this->filePath = $filePath;
        $this->reportContent = [];
        $this->parseCsvToArray();
    }

    /**
     * @return Transaction[]
    */
    public function getReportTransactions(): array {
        $firstTransactionDateTime = $this->reportContent[0]->getTransactionDatetime();

        $sameDayTransactions = [];

        foreach($this->reportContent as $transaction){
            $transactionDate = $transaction->getTransactionDatetime()->format("Y-m-d");
            
            if($transactionDate === $firstTransactionDateTime->format("Y-m-d")){
                $sameDayTransactions[] = $transaction;
            }
        }

        return $sameDayTransactions;
    }

    /**
     * @throws \Exception
     * @return \Datetime
     */
    public function getReportDate() {
        if (count($this->reportContent) > 0){
            return $this->reportContent[0]->getTransactionDatetime();
        }

        throw new \Exception("Report content is empty");
    }

    private function formatLineToTransaction(array $line): Transaction {
        list(
            $source_bank_name,
            $source_bank_branch,
            $source_bank_account,
            $destination_bank_name,
            $destination_bank_branch,
            $destination_bank_account,
            $transaction_amount,
            $transaction_datetime,
        ) = $line;
        
        $transaction = new Transaction();
        $transaction->setSourceBankName($source_bank_name);
        $transaction->setSourceBankBranch($source_bank_branch);
        $transaction->setSourceBankAccount($source_bank_account);
        $transaction->setDestinationBankName($destination_bank_name);
        $transaction->setDestinationBankBranch($destination_bank_branch);
        $transaction->setDestinationBankAccount($destination_bank_account);
        $transaction->setTransactionAmount($transaction_amount);
        $transaction->setTransactionDatetime(new \DateTime($transaction_datetime));

        return $transaction;
    }
    
    private function parseCsvToArray(): void {
        if (($file = fopen($this->filePath, "r")) !== FALSE) {

            while (($line = fgetcsv($file, 1000, ",")) !== FALSE) {
                // Get the number of fields in a line
                $numberOfFields = count($line);
                
                // Check for empty fields in a line
                $hasEmptyFields = FALSE;
                for($i = 0; $i < $numberOfFields; $i++){
                    if(empty($line[$i])){
                        $hasEmptyFields = TRUE;
                        break;
                    }
                }

                // Only parse lines with the expected number of fields and that contains no empty fields
                if(!$hasEmptyFields && $numberOfFields === self::EXPECTED_NUMBER_OF_FIELDS){
                    $this->reportContent[] = $this->formatLineToTransaction($line);                    
                }
            }

            fclose($file);
        }
    }
}