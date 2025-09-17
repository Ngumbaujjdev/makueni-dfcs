<?php
require_once __DIR__ . 'App.php';
require_once __DIR__ . '../vendor-pdf/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;

class PaymentExporter
{
    private $app;
    private $phpWord;

    public function __construct()
    {
        $this->app = new App;
        $this->phpWord = new PhpWord();
    }

    public function exportWord($startDate, $endDate, $perPage, $churchId = null, $regionId = null)
    {
        try {
            // Set default template styles
            $this->phpWord->setDefaultFontName('Calibri');
            $this->phpWord->setDefaultFontSize(11);

            // Get payments data with additional details
            $query = "SELECT 
                t.TransactionCode,
                CONCAT(d.FirstName, ' ', d.LastName) as DelegateName,
                d.PhoneNumber as DelegatePhone,
                t.PayerPhoneNumber,
                p.created_at as PaymentDate,
                d.DelegateTypeID,
                CASE 
                    WHEN d.DelegateTypeID = 1 THEN 'CCI Member'
                    ELSE 'Non-CCI Member'
                END as MemberType,
                c.ChurchName,
                CASE
                    WHEN d.AttendanceStatus = 1 THEN 'Attended'
                    ELSE 'Not Attended'
                END as AttendanceStatus
            FROM payment p
            JOIN transaction t ON p.TransactionID = t.TransactionID
            JOIN delegate d ON p.DelegateID = d.DelegateID
            JOIN church c ON d.ChurchID = c.ChurchID
            WHERE DATE(p.created_at) BETWEEN '$startDate' AND '$endDate'";

            if (!empty($churchId)) {
                $query .= " AND d.ChurchID = '$churchId'";
            }

            $query .= " ORDER BY p.created_at DESC";

            $payments = $this->app->select_all($query);

            // Document settings
            $section = $this->phpWord->addSection([
                'marginLeft' => 600,
                'marginRight' => 600,
                'marginTop' => 600,
                'marginBottom' => 600
            ]);

            // Add header only on the first page
            $header = $section->addHeader();
            $header->addImage(
                'http://localhost/makueni/assets/images/logo.png',
                [
                    'width' => 100,
                    'height' => 50,
                    'alignment' => 'center',
                    'marginTop' => 50,
                    'wrappingStyle' => 'inline'
                ]
            );

            // Add report title
            $section->addText(
                'Payment Transactions Report',
                ['bold' => true, 'size' => 16, 'color' => '1F4E79'],
                ['alignment' => 'center', 'spaceAfter' => 200]
            );

            // Add church name if available
            if (!empty($churchId)) {
                $churchName = $this->getChurchName($churchId);
                $section->addText(
                    "Church: $churchName",
                    ['bold' => true, 'size' => 14, 'color' => '1F4E79'],
                    ['alignment' => 'center', 'spaceAfter' => 100]
                );
            }

            // Add date range
            $section->addText(
                "Report Period: " . date('F d, Y', strtotime($startDate)) . " to " . date('F d, Y', strtotime($endDate)),
                ['bold' => true, 'size' => 12],
                ['alignment' => 'center', 'spaceAfter' => 200]
            );

            // Create table style for main data
            $tableStyle = [
                'borderSize' => 6,
                'borderColor' => '1F4E79',
                'cellMargin' => 80
            ];

            // Add data tables with pagination
            $counter = 0;
            $pageCounter = 1;

            while ($counter < count($payments)) {
                if ($counter > 0) {
                    $section->addPageBreak();
                }

                // Create table
                $table = $section->addTable($tableStyle);

                // Add header row on each page
                $table->addRow(400, ['bgColor' => '1F4E79']);
                $headers = ['Transaction Code', 'Delegate Name', 'Delegate Phone', 'Payer Phone', 'Date', 'Member Type', 'Church', 'Attendance Status'];
                foreach ($headers as $header) {
                    $cell = $table->addCell(2000);
                    $cell->addText($header, ['bold' => true, 'color' => 'FFFFFF']);
                }

                // Add data rows for this page
                $pageRecords = array_slice($payments, $counter, $perPage);
                foreach ($pageRecords as $payment) {
                    $table->addRow(400);
                    $table->addCell(2000)->addText($payment->TransactionCode);
                    $table->addCell(2000)->addText($payment->DelegateName);
                    $table->addCell(2000)->addText($payment->DelegatePhone);
                    $table->addCell(2000)->addText($payment->PayerPhoneNumber);
                    $table->addCell(2000)->addText(date('Y-m-d', strtotime($payment->PaymentDate)));
                    $table->addCell(2000)->addText($payment->MemberType);
                    $table->addCell(2000)->addText($payment->ChurchName);
                    $table->addCell(2000)->addText($payment->AttendanceStatus);
                }

                $counter += $perPage;

                // Add page number
                $footer = $section->addFooter();
                $footer->addText(
                    'Page ' . $pageCounter . ' of ' . ceil(count($payments) / $perPage),
                    ['size' => 10, 'color' => '666666'],
                    ['alignment' => 'center']
                );
                $pageCounter++;
            }

            // Save file
            $fileName = 'Payment_Report_' . date('Y-m-d_His') . '.docx';
            $tempFile = tempnam(sys_get_temp_dir(), 'payment_report_');
            $this->phpWord->save($tempFile);

            // Send appropriate headers
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');

            // Output file
            readfile($tempFile);
            unlink($tempFile);
            exit;
        } catch (Exception $e) {
            throw new Exception('Error generating report: ' . $e->getMessage());
        }
    }

    private function getChurchName($churchId)
    {
        $query = "SELECT ChurchName FROM church WHERE ChurchID = '$churchId'";
        $result = $this->app->select_one($query);

        if ($result) {
            return $result->ChurchName;
        }

        return '';
    }
}