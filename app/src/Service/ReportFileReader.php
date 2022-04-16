<?php

namespace App\Service;

class ReportFileReader
{
    protected $filePath;
    private $data;

    public function setReportFile(string $filePath){
        $this->filePath = $filePath;
        $this->parseCsvToArray();
    }

    public function getReportContent(): array {
        return $this->data;
    }

    public function getReportContentByDate(): array {
        return $this->data;
    }
    
    private function parseReportLine(array $line) {
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
        
        // Formats a line
        $line = [];
        $line['source_bank_name'] = $source_bank_name;
        $line['source_bank_branch'] = $source_bank_branch;
        $line['source_bank_account'] = $source_bank_account;
        $line['destination_bank_name'] = $destination_bank_name;
        $line['destination_bank_branch'] = $destination_bank_branch;
        $line['destination_bank_account'] = $destination_bank_account;
        $line['transaction_amount'] = $transaction_amount;
        $line['transaction_datetime'] = $transaction_datetime;

        return $line;
    }
    
    private function parseCsvToArray() {
        $this->data = [];

        if (($file = fopen($this->filePath, "r")) !== FALSE) {
            $expectedNumberOfFields = 8;

            while (($line = fgetcsv($file, 1000, ",")) !== FALSE) {
                // Get the number of fields in a line
                $numberOfFields = count($line);

                // Only parse lines with the expected number of fields
                if($numberOfFields === $expectedNumberOfFields){
                    // Parse line
                    $this->data[] = $this->parseReportLine($line);                    
                }
            }

            fclose($file);
        }
    }
}