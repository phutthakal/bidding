<?php
require '../vendor/autoload.php';
require_once __DIR__ . '/../config/connect.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// รับช่วงวันที่จาก GET
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

if (!$start_date || !$end_date) die('❌ กรุณาระบุช่วงวันที่');

$stmt = $pdo->prepare("
    SELECT items.id,
    items.title, 
    items.description,
    items.image_url,
    items.price,
    items.update_price,
    items.quantity,
    items.unit,
    items.bidding_start,
    items.bidding_end,
    CONCAT(buyer.first_name, ' ', buyer.last_name) AS buyer_name,
    buyer.email AS buyer_email,
    buyer.role AS buyer_role,
    companies_buyer.name AS company_buyer,
    winner_id,
    CONCAT(winner.first_name,' ',winner.last_name) AS winner_name,
    winner.email AS winner_email,
    winner.role AS winner_role,
    companies_winner.name AS company_winner
    FROM items
    LEFT JOIN users AS buyer ON items.seller_id = buyer.id
    LEFT JOIN companies AS companies_buyer ON buyer.company_id = companies_buyer.id
    LEFT JOIN users AS winner ON items.winner_id = winner.id
    LEFT JOIN companies AS companies_winner ON winner.company_id = companies_winner.id
    WHERE items.status='closed' 
        AND DATE(items.bidding_end) BETWEEN :start AND :end
    ORDER BY items.bidding_end DESC
");
$stmt->execute(['start' => $start_date, 'end' => $end_date]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('รายงานผลผู้ชนะ');
$sheet->fromArray([
    'สินค้า',
    'รายละเอียดสินค้า',
    'ราคาเริ่มต้น',
    'ราคาสุดท้าย',
    'ผู้ชนะ',
    'อีเมล',
    'บริษัท',
    'วันเปิดประมูล',
    'วันปิดประมูล'
    // 'รูป'
], NULL, 'A1');

$row = 2;
foreach ($results as $r) {
    $sheet->setCellValue("A$row", $r['title']);
    $sheet->setCellValue("b$row", $r['description']);
    $sheet->setCellValue("c$row", number_format($r['price']));
    $sheet->setCellValue("d$row", number_format($r['update_price']));
    $sheet->setCellValue("e$row", $r['winner_name'] ?: 'ไม่มีผู้ชนะ');
    $sheet->setCellValue("f$row", $r['winner_email'] ?: 'ไม่มีอีเมล');
    $sheet->setCellValue("g$row", $r['company_winner'] ?: 'ไม่มีบริษัท');
    $sheet->setCellValue("h$row", date('d-m-Y h:m', strtotime($r['bidding_start'])));
    $sheet->setCellValue("i$row", date('d-m-Y h:m', strtotime($r['bidding_end'])));
    // $sheet->setCellValue("j$row", $r['image_url'] ?: 'ไม่มีรูป');
    // if (file_exists($r['image_url'])) {
    //     $drawing = new Drawing();
    //     $drawing->setPath($r['image_url']); // ไฟล์รูป
    //     $drawing->setHeight(50); // สูง 50px
    //     $drawing->setCoordinates("j$row"); // ช่องใส่รูป
    //     $drawing->setWorksheet($sheet);
    // }

    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="winners_report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
