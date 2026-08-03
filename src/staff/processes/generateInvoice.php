<?php
// processes/generateInvoice.php

include(__DIR__ . '/../../config/dbConnect.php');

// 1. Require Composer's autoloader
require_once(__DIR__ . '/../../../vendor/autoload.php');

global $conn;

// 2. Validate Sale ID parameter
if (!isset($_GET['saleID']) || empty($_GET['saleID'])) {
    die("Invalid request: Sale ID is required.");
}

$saleID = intval($_GET['saleID']);

// 3. Fetch Sale Details with Joins
$sql = "SELECT 
            s.SaleID, s.TotalPrice, s.DateSold,
            u.RealName AS ClientName, u.Email AS ClientEmail,
            p.BlockID, p.SellingPrice,
            b.OrchardID,
            c.CompanyName
        FROM sale s
        LEFT JOIN client cl ON s.ClientID = cl.UserID
        LEFT JOIN user u ON cl.UserID = u.UserID
        LEFT JOIN purchase p ON s.SaleID = p.SaleID
        LEFT JOIN block b ON p.BlockID = b.BlockID
        LEFT JOIN company c ON b.OrchardID = c.OrchardID
        WHERE s.SaleID = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $saleID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$sale = mysqli_fetch_assoc($result);

if (!$sale) {
    die("Error: Invoice or Sale record not found.");
}

// 4. Extend FPDF to create a Custom Layout with Logo
class PDF_Invoice extends FPDF {
    function Header() {
        // Absolute Document Root path (Option 4)
        $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/tree/assets/images/TREE.png';

        // Render logo if file exists (X: 10, Y: 8, Width: 16mm)
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 10, 8, 16);
            $this->SetX(30); // Offset text right to account for logo width
        }

        // Company Branding Header
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(16, 185, 129); // Green Accent
        $this->Cell(80, 8, 'PacificTree', 0, 0, 'L');
        
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(51, 51, 51);
        $this->Cell(80, 8, 'OFFICIAL RECEIPT', 0, 1, 'R');

        if (file_exists($logoPath)) {
            $this->SetX(30);
        }
        
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(80, 5, 'Commercial Operations & Client Portal', 0, 0, 'L');
        $this->Ln(12);
        
        // Horizontal Line
        $this->SetDrawColor(220, 220, 220);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(8);
    }

    function Footer() {
        $this->SetY(-25);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 5, 'Thank you for your business with PacificTree.', 0, 1, 'C');
        $this->Cell(0, 5, 'Page ' . $this->PageNo() . ' | Generated automatically on ' . date('Y-m-d H:i:s'), 0, 0, 'C');
    }
}

// 5. Build PDF Output
$pdf = new PDF_Invoice();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Meta Information Block
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(100, 6, 'Billed To:', 0, 0);
$pdf->Cell(90, 6, 'Receipt Details:', 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(51, 51, 51);

// Line 1
$pdf->Cell(100, 6, 'Name: ' . ($sale['ClientName'] ?? 'N/A'), 0, 0);
$pdf->Cell(90, 6, 'Receipt No: #REC-' . str_pad($sale['SaleID'], 6, '0', STR_PAD_LEFT), 0, 1);

// Line 2
$pdf->Cell(100, 6, 'Email: ' . ($sale['ClientEmail'] ?? 'N/A'), 0, 0);
$pdf->Cell(90, 6, 'Date Issued: ' . date('d M Y, h:i A', strtotime($sale['DateSold'])), 0, 1);

// Line 3
$pdf->Cell(100, 6, 'Payment Status: COMPLETED', 0, 0);
$pdf->Cell(90, 6, '', 0, 1);

$pdf->Ln(10);

// Table Header
$pdf->SetFillColor(243, 244, 246);
$pdf->SetTextColor(51, 51, 51);
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(30, 8, 'Block ID', 1, 0, 'C', true);
$pdf->Cell(70, 8, 'Company / Orchard', 1, 0, 'L', true);
$pdf->Cell(45, 8, 'Orchard ID', 1, 0, 'C', true);
$pdf->Cell(45, 8, 'Amount (MYR)', 1, 1, 'R', true);

// Table Content Body
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(30, 8, '#' . ($sale['BlockID'] ?? 'N/A'), 1, 0, 'C');
$pdf->Cell(70, 8, ($sale['CompanyName'] ?? 'PacificTree Orchard'), 1, 0, 'L');
$pdf->Cell(45, 8, ($sale['OrchardID'] ?? 'N/A'), 1, 0, 'C');
$pdf->Cell(45, 8, 'RM ' . number_format($sale['SellingPrice'] ?? $sale['TotalPrice'], 2), 1, 1, 'R');

// Total Summary Row
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(145, 8, 'Total Amount Paid: ', 0, 0, 'R');
$pdf->Cell(45, 8, 'RM ' . number_format($sale['TotalPrice'], 2), 1, 1, 'R');

// Output PDF directly in Browser Stream
$pdf->Output('I', 'Receipt_Sale_' . $sale['SaleID'] . '.pdf');
exit();
?>