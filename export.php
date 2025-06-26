<?php
session_start();
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
include("include/connect.php");
date_default_timezone_set('Asia/Kolkata');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

$sql = "WITH ranked AS (
    SELECT 
        lcn.genre,
        lcn.lcn,
        cm.channelName,
        br.broadcaster,
        ROW_NUMBER() OVER (
            PARTITION BY lcn.genre 
            ORDER BY lcn.lcn
        ) AS raw_rank
    FROM 
        channel_mapping_tb AS cmap
    INNER JOIN sid_tb AS sid ON cmap.sid_id = sid.sid_id
    INNER JOIN lcn_tb AS lcn ON cmap.lcn_id = lcn.lcn_id
    LEFT JOIN channel_master_tb AS cm ON cmap.channel_id = cm.channel_id
    LEFT JOIN broadcaster_tb AS br ON cm.broadcaster_id = br.broadcaster_id
    WHERE 
        sid.city_id = 1
        AND br.broadcaster <> 'LOCAL'
),
full_data AS (
    SELECT 
        lcn.genre,
        lcn.lcn,
        cm.channelName,
        br.broadcaster
    FROM 
        channel_mapping_tb AS cmap
    INNER JOIN sid_tb AS sid ON cmap.sid_id = sid.sid_id
    INNER JOIN lcn_tb AS lcn ON cmap.lcn_id = lcn.lcn_id
    LEFT JOIN channel_master_tb AS cm ON cmap.channel_id = cm.channel_id
    LEFT JOIN broadcaster_tb AS br ON cm.broadcaster_id = br.broadcaster_id
    WHERE sid.city_id = 1
)
SELECT 
    f.genre,
    f.lcn,
    f.channelName,
    f.broadcaster,
    COALESCE(r.raw_rank, 0) AS genre_rank
FROM full_data f
LEFT JOIN ranked r ON 
    f.lcn = r.lcn AND 
    f.channelName = r.channelName AND
    f.broadcaster = r.broadcaster
ORDER BY f.lcn, genre_rank";

$result = mysqli_query($auth,$sql);
if (!$result) { // add this check.
    die('Invalid query: ' . mysqli_error());
}
$rowcount=2;

$spreadsheet = new Spreadsheet();

$spreadsheet->getProperties()->setCreator("Meghbela Digital")
							 ->setLastModifiedBy("Meghbela")
							 ->setTitle("MeghbelaLCN")
							 ->setSubject("Meghbela LCN")
							 ->setDescription("Meghbela LCN")
							 ->setKeywords("LCN Excel")
							 ->setCategory("LCN");
// Add Heading
$spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'GENRE')
            ->setCellValue('D1', 'LCN')
			->setCellValue('B1', 'CHANNEL NAME')
			->setCellValue('C1', 'BROADCASTER')
			->setCellValue('E1', 'RANK');
		
while($row = mysqli_fetch_array($result)){

	$spreadsheet->getActiveSheet()->SetCellValue('A'.$rowcount, $row['genre']);
  $spreadsheet->getActiveSheet()->SetCellValue('D'.$rowcount, $row['lcn']);
  $spreadsheet->getActiveSheet()->SetCellValue('B'.$rowcount, $row['channelName']);
  $spreadsheet->getActiveSheet()->SetCellValue('C'.$rowcount, $row['broadcaster']);
  $spreadsheet->getActiveSheet()->SetCellValue('E'.$rowcount, $row['genre_rank']);
  $rowcount++;
}

// Redirect output to a client’s web browser (Xlsx)
$file_name="Kolkata_LCN_".Date('Y-m-d').".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$file_name.'');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');


?>
