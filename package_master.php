<?php
session_start();
include("include/connect.php");
include 'include/log.php';
if(!isset($_SESSION['select_db']) || !isset($_SESSION['city'])){
	$_SESSION['select_db'] = 'meghbela_lcn_db_kol';
	$_SESSION['city'] = 'Kolkata';
}
mysqli_select_db($con,$_SESSION['select_db']) or die("No database");

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Kolkata');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once 'Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Meghbela Digital")
							 ->setLastModifiedBy("Meghbela")
							 ->setTitle("MeghbelaPackage")
							 ->setSubject("Meghbela Package")
							 ->setDescription("Meghbela Package")
							 ->setKeywords("Package Excel")
							 ->setCategory("Package");
               // Add Heading
$objPHPExcel->setActiveSheetIndex(0)
                           ->setCellValue('A1', 'GENRE')
                           ->setCellValue('B1', 'LCN')
                           ->setCellValue('C1', 'CHANNEL NAME')
                           ->setCellValue('D1', 'BRONZE')
                           ->setCellValue('E1', 'SILVER')
                           ->setCellValue('F1', 'GOLD')
                           ->setCellValue('G1', 'PLATINUM')
                           ->setCellValue('H1', 'POWER')
                           ->setCellValue('I1', 'PRICE');

$objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray(
                           		array(
                           			'font'    => array(
                           				'bold'      => true
                           			),
                           			'alignment' => array(
                           				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                           			),
                           			'borders' => array(
                           				'top'     => array(
                            					'style' => PHPExcel_Style_Border::BORDER_THIN
                            				)
                           			),
                           			'fill' => array(
                           	 			'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                           	  			'rotation'   => 90,
                           	 			'startcolor' => array(
                           	 				'argb' => 'FFA0A0A0'
                           	 			),
                           	 			'endcolor'   => array(
                           	 				'argb' => 'FFFFFFFF'
                           	 			)
                           	 		)
                           		)
                           );
                           $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                           $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                           $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                           $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                           $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                           $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                           $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                           $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                           $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
                           $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                           $objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                           $objPHPExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                           $objPHPExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                           $objPHPExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                           $objPHPExcel->getActiveSheet()->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                           $objPHPExcel->getActiveSheet()->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                           $objPHPExcel->getActiveSheet()->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                           $objPHPExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$sql = "SELECT * FROM channel_tb,lcn_tb,package_tb WHERE channel_tb.lcn=lcn_tb.lcn AND channel_tb.sid=package_tb.sid ORDER BY lcn_tb.lcn";

$result = mysqli_query($con,$sql);
if (!$result) { // add this check.
    die('Invalid query: ' . mysqli_error());
}
$rowcount=2;
while($row = mysqli_fetch_array($result)){

	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowcount, $row['genre']);
  $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowcount, $row['lcn']);
  $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowcount, $row['channel']);
  $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowcount, $row['bronze']);
  $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowcount, $row['silver']);
  $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowcount, $row['gold']);
  $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowcount, $row['platinum']);
  $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowcount, $row['power']);
  $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowcount, $row['price']);
  $rowcount++;
}
$file_name=$_SESSION['city']."_Package_Master_".Date('Y-m-d').".xlsx";
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$file_name.'');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
 ?>
