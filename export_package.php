<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Kolkata');

require_once "include/package_return.php";
include 'include/log.php';


$rowcount=2;
$pack_var=$_GET['par'];
//setting price
if($pack_var=='bronze'){$pack_price="125";}
elseif($pack_var=='silver'){$pack_price="205";}
elseif($pack_var=='gold'){$pack_price="265";}
elseif($pack_var=='platinum'){$pack_price="330";};

$print_pack=printpackage($pack_var);
$pack_var=ucfirst($pack_var);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once 'Classes/PHPExcel.php';
require_once ('Classes/PHPExcel/IOFactory.php');
$rendererName = PHPExcel_Settings::PDF_RENDERER_TCPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
$rendererLibrary = 'tcPDF5.9';
//$rendererLibrary = 'mpdf60';
//$rendererLibrary = 'domPDF0.6.0beta3';
$rendererLibraryPath = './TCPDF-master';

if (!PHPExcel_Settings::setPdfRenderer(
		$rendererName,
		$rendererLibraryPath
	)) {
	die(
		'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
		EOL .
		'at the top of this script as appropriate for your directory structure'
	);
}
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Meghbela Digital")
							 ->setLastModifiedBy("Meghbela")
							 ->setTitle("MeghbelaPackage")
							 ->setSubject("Meghbela Package")
							 ->setDescription("Meghbela Package")
							 ->setKeywords("Package Excel")
							 ->setCategory("Package");
//SET header & Footer
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&G&C&HMeghbela Channel Package');
$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');

// Add a drawing to the header
$objDrawing = new PHPExcel_Worksheet_HeaderFooterDrawing();
$objDrawing->setName('Meghbela logo');
$objDrawing->setPath('images/android-icon-72x72.png');
$objDrawing->setHeight(36);
$objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);

$heading=strtoupper($pack_var)." DIGITAL @ Rs ".$pack_price." Without Tax";
// Add Heading
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', $heading);

$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray(
		array(
			'font'    => array(
				'bold'      => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS,
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
//SET DIFFERENT FORMATTING
for ($i=2; $i <45 ; $i++) {
	//set genre names format
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setName('Candara');
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setItalic(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$i++;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setName('Verdana');
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(false);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

}



//setting custom width of column
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth("100");
$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//Text wrap option here
$objPHPExcel->getActiveSheet()->getStyle('A1:A100')->getAlignment()->setWrapText(true);
//Set vertical alignment
$objPHPExcel->getActiveSheet()->getStyle('A1:A100')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);


foreach ($print_pack as $key_1 => $value_1) {
	foreach ($value_1 as $key_2 => $value_2) {

$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowcount, $key_2);
$rowcount++;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowcount, $value_2);
$rowcount++;
 }

}

$objPHPExcel->setActiveSheetIndex(0);
//ob_end_clean();

$file__name=$pack_var."_Package.pdf";
// Redirect output to a client’s web browser (Excel2007)
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header('Content-Type: application/pdf');
header('Content-Disposition: attachment;filename='.$file__name.'');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
//ob_end_clean();

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
$objWriter->save('php://output');

?>
