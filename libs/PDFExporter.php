```php
<?php
require_once __DIR__ . 'App.php';
require_once __DIR__ . '../vendor-pdf/vendor/autoload.php';

use \TCPDF as PDF;

class PDFExporter
{
    private $app;
    private $themeColor = array(45, 137, 164); // RGB values
    private $accentColor = array(70, 150, 180);
    private $successColor = array(40, 167, 69);
    private $warningColor = array(220, 53, 69);

    public function __construct()
    {
        $this->app = new App;
    }

    public function exportCCIMembers($startDate, $endDate, $regionId = null)
    {
        try {
            ob_clean();
            ob_start();

            // Create new PDF document
            $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Set document properties
            $pdf->SetCreator('Makueni System');
            $pdf->SetAuthor('Makueni System');
            $pdf->SetTitle('CCI Members Report');

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(true);

            // Set margins
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetHeaderMargin(5);
            $pdf->SetFooterMargin(10);

            // Set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, 15);

            // Add first page
            $pdf->AddPage();

            // Add logo and title
            $pdf->Image('http://localhost/makueni/assets/images/logo.png', 15, 10, 30);
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->SetTextColor($this->themeColor[0], $this->themeColor[1], $this->themeColor[2]);
            $pdf->Cell(0, 30, 'CCI Members Report', 0, 1, 'C');

            // Add report details
            $pdf->SetFont('helvetica', '', 11);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');

            if ($regionId) {
                $region_query = "SELECT RegionName FROM region WHERE RegionID = $regionId";
                $region = $this->app->select_one($region_query);
                $pdf->Cell(0, 6, 'Region: ' . $region->RegionName, 0, 1, 'C');
            }

            $pdf->Cell(0, 6, 'Period: ' . date('F d, Y', strtotime($startDate)) . ' - ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
            $pdf->Ln(10);

            // Get churches data
            $query = "SELECT c.ChurchID, c.ChurchName, r.RegionName,
                            (SELECT COUNT(*) FROM delegate WHERE ChurchID = c.ChurchID) as total_delegates,
                            (SELECT COUNT(*) FROM delegate WHERE ChurchID = c.ChurchID AND AttendanceStatus = 'Attended') as attended_delegates
                     FROM church c
                     JOIN region r ON c.RegionID = r.RegionID
                     WHERE " . ($regionId ? "r.RegionID = $regionId" : "1=1") . "
                     ORDER BY r.RegionName, c.ChurchName";

            $churches = $this->app->select_all($query);

            foreach ($churches as $church) {
                // Add church header with statistics
                $pdf->SetFillColor(245, 247, 250);
                $pdf->SetDrawColor($this->themeColor[0], $this->themeColor[1], $this->themeColor[2]);
                $pdf->Rect(15, $pdf->GetY(), $pdf->GetPageWidth() - 30, 20, 'DF');

                $pdf->SetFont('helvetica', 'B', 14);
                $pdf->SetTextColor($this->themeColor[0], $this->themeColor[1], $this->themeColor[2]);
                $pdf->Cell(0, 20, $church->ChurchName, 0, 1, 'L');

                $pdf->SetFont('helvetica', '', 10);
                $pdf->SetTextColor(100, 100, 100);
                $pdf->Cell(0, 6, 'Region: ' . $church->RegionName . ' | Total Delegates: ' . $church->total_delegates .
                    ' | Attended: ' . $church->attended_delegates, 0, 1, 'L');
                $pdf->Ln(5);

                // Get delegates for this church
                $delegates_query = "SELECT 
                    CONCAT(d.FirstName, ' ', d.LastName) as FullName,
                    d.PhoneNumber,
                    d.RegistrationCode,
                    d.AttendanceStatus,
                    des.DesignationName
                FROM delegate d
                JOIN designation des ON d.DesignationID = des.DesignationID
                WHERE d.ChurchID = {$church->ChurchID}
                ORDER BY des.DesignationID, d.FirstName";

                $delegates = $this->app->select_all($delegates_query);

                if (!empty($delegates)) {
                    $headers = array('Name', 'Phone', 'Registration Code', 'Designation', 'Status');
                    $this->generateStyledTable($pdf, $headers, $delegates);
                } else {
                    $pdf->SetFont('helvetica', 'I', 10);
                    $pdf->Cell(0, 10, 'No delegates found', 0, 1, 'L');
                }

                if ($pdf->GetY() > $pdf->GetPageHeight() - 40) {
                    $pdf->AddPage();
                } else {
                    $pdf->Ln(15);
                }
            }

            // Output PDF
            ob_end_clean();
            return $pdf->Output('CCI_Members_Report.pdf', 'D');
        } catch (Exception $e) {
            ob_end_clean();
            throw new Exception('Error generating PDF: ' . $e->getMessage());
        }
    }

    public function exportNonCCIMembers($startDate, $endDate)
    {
        try {
            ob_clean();
            ob_start();

            $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Similar setup as CCI members...
            $pdf->SetCreator('Makueni System');
            $pdf->SetAuthor('Makueni System');
            $pdf->SetTitle('Non-CCI Members Report');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(true);
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(TRUE, 15);

            $pdf->AddPage();

            // Add logo and title
            $pdf->Image('http://localhost/makueni/assets/images/logo.png', 15, 10, 30);
            $pdf->SetFont('helvetica', 'B', 20);
            $pdf->SetTextColor($this->themeColor[0], $this->themeColor[1], $this->themeColor[2]);
            $pdf->Cell(0, 30, 'Non-CCI Members Report', 0, 1, 'C');

            // Add report details
            $pdf->SetFont('helvetica', '', 11);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'C');
            $pdf->Cell(0, 6, 'Period: ' . date('F d, Y', strtotime($startDate)) . ' - ' . date('F d, Y', strtotime($endDate)), 0, 1, 'C');
            $pdf->Ln(10);

            // Get Non-CCI churches with statistics
            $query = "SELECT 
                        nc.NonCCIChurchID, 
                        nc.ChurchName,
                        COUNT(d.DelegateID) as total_delegates,
                        SUM(CASE WHEN d.AttendanceStatus = 'Attended' THEN 1 ELSE 0 END) as attended_delegates
                     FROM non_cci_church nc
                     JOIN delegate d ON nc.NonCCIChurchID = d.NonCCIChurchID
                     WHERE d.DelegateTypeID = 2
                     GROUP BY nc.NonCCIChurchID, nc.ChurchName
                     ORDER BY nc.ChurchName";

            $churches = $this->app->select_all($query);

            foreach ($churches as $church) {
                // Add church header with statistics
                $pdf->SetFillColor(245, 247, 250);
                $pdf->SetDrawColor($this->themeColor[0], $this->themeColor[1], $this->themeColor[2]);
                $pdf->Rect(15, $pdf->GetY(), $pdf->GetPageWidth() - 30, 20, 'DF');

                $pdf->SetFont('helvetica', 'B', 14);
                $pdf->SetTextColor($this->themeColor[0], $this->themeColor[1], $this->themeColor[2]);
                $pdf->Cell(0, 20, $church->ChurchName, 0, 1, 'L');

                $pdf->SetFont('helvetica', '', 10);
                $pdf->SetTextColor(100, 100, 100);
                $pdf->Cell(0, 6, 'Total Delegates: ' . $church->total_delegates .
                    ' | Attended: ' . $church->attended_delegates, 0, 1, 'L');
                $pdf->Ln(5);

                // Get delegates
                $delegates_query = "SELECT 
                    CONCAT(d.FirstName, ' ', d.LastName) as FullName,
                    d.PhoneNumber,
                    d.RegistrationCode,
                    d.AttendanceStatus,
                    des.DesignationName
                FROM delegate d
                JOIN designation des ON d.DesignationID = des.DesignationID
                WHERE d.NonCCIChurchID = {$church->NonCCIChurchID}
                ORDER BY des.DesignationID, d.FirstName";

                $delegates = $this->app->select_all($delegates_query);

                if (!empty($delegates)) {
                    $headers = array('Name', 'Phone', 'Registration Code', 'Attendance Status', 'Designation');
                    $this->generateStyledTable($pdf, $headers, $delegates);
                } else {
                    $pdf->SetFont('helvetica', 'I', 10);
                    $pdf->Cell(0, 10, 'No delegates found', 0, 1, 'L');
                }

                if ($pdf->GetY() > $pdf->GetPageHeight() - 40) {
                    $pdf->AddPage();
                } else {
                    $pdf->Ln(15);
                }
            }

            ob_end_clean();
            return $pdf->Output('Non_CCI_Members_Report.pdf', 'D');
        } catch (Exception $e) {
            ob_end_clean();
            throw new Exception('Error generating PDF: ' . $e->getMessage());
        }
    }

    private function generateStyledTable($pdf, $headers, $data)
    {
        // Calculate column widths
        $widths = array(
            0 => 50,  // Name
            1 => 35,  // Phone
            2 => 35,  // Registration Code
            3 => 40,  // Designation
            4 => 25   // Status
        );

        // Headers
        $pdf->SetFillColor($this->themeColor[0], $this->themeColor[1], $this->themeColor[2]);
        $pdf->SetTextColor(255);
        $pdf->SetFont('helvetica', 'B', 10);

        foreach ($headers as $index => $header) {
            $pdf->Cell($widths[$index], 8, $header, 1, 0, 'C', true);
        }
        $pdf->Ln();

        // Data rows
        $pdf->SetFont('helvetica', '', 9);
        $fill = false;

        foreach ($data as $row) {
            $row = (array)$row;

            // Alternate row colors
            $pdf->SetFillColor($fill ? 245 : 255, $fill ? 247 : 255, $fill ? 250 : 255);
            $pdf->SetTextColor(0);

            $i = 0;
            foreach ($row as $cell) {
                // Status color coding
                if ($i === 3 && is_string($cell)) { // Status column
                    $pdf->SetTextColor(
                        $cell === 'Attended' ? $this->successColor[0] : $this->warningColor[0],
                        $cell === 'Attended' ? $this->successColor[1] : $this->warningColor[1],
                        $cell === 'Attended' ? $this->successColor[2] : $this->warningColor[2]
                    );
                }

                $pdf->Cell($widths[$i], 7, $cell, 1, 0, 'L', $fill);
                $i++;
            }

            $pdf->Ln();
            $fill = !$fill;
        }
        $pdf->Ln(5);
    }
}
