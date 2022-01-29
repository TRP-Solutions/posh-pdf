<?php
define('K_PATH_IMAGES',__DIR__);

require_once __DIR__."/../tcpdf/tcpdf.php";
require_once __DIR__.'/../lib/PoshPDF.php';

$pdf = new PoshPDF('P','mm','A4');

$pdf->SetCreator('Research and development');
$pdf->SetAuthor('TRP Solutions ApS');
$pdf->SetTitle('PoshPDF - Sample');

$pdf->CreateStyle('title',[
	'font_size'=>20,
	'font_style'=>'B',
	'font_name'=>'times',
	'fill'=>'#87CEEB',
	'align'=>'C',
	'color'=>"#00008B",
]);

$pdf->CreateStyle('header',[
	'font_size'=>20,
	'font_style'=>'B',
	'line_height'=>15,
	'align'=>'R',
	'color'=>'#979797',
]);

$pdf->CreateStyle('footer',[
	'font_size'=>7,
	'font_style'=>'B',
	'align'=>'C',
],'header');

$pdf->CreateStyle('tbody',[
	'font_size'=>8,
	'font_name'=>'dejavusanscondensed',
]);

$pdf->CreateStyle('thead',[
	'font_style'=>'B',
],'tbody');

$pdf->CreateStyle('thead_r',['align'=>'R',],'thead');
$pdf->CreateStyle('tbody_r',['align'=>'R',],'tbody');

$header = function($pdf) {
	$pdf->SetY(10);
	$pdf->WriteParagraph('PoshPDF - Sample','header');
	$pdf->ImageSVG('sample.svg', 15, 10, null, 15);
};

$pdf->SetHeaderFunc($header,30);

$footer = function($pdf) {
	$pdf->SetY(-15);
	$pdf->WriteParagraph('TCPDF Extension','footer');
	$pdf->WriteParagraph('https://github.com/TRP-Solutions/posh-pdf','footer');
};

$pdf->SetFooterFunc($footer,20);

$pdf->AddPage();
$pdf->WriteParagraph('Paragraph','title');

$pdf->setY($pdf->getY() + 2);

$pdf->WriteParagraph('Denne grundlov gælder for alle dele af Danmarks Rige.');
$pdf->WriteParagraph('Regeringsformen er indskrænket-monarkisk. Kongemagten nedarves til mænd og kvinder efter de i tronfølgeloven af 27. marts 1953 fastsatte regler.');
$pdf->WriteParagraph('Den lovgivende magt er hos kongen og folketinget i forening. Den udøvende magt er hos kongen. Den dømmende magt er hos domstolene.');

$pdf->AddPage();
$pdf->WriteParagraph('Table','title');

$pdf->setY($pdf->getY() + 2);

$table = $pdf->Table();
$table->TableWidth(30,null,20,20,20);
$table->TableBorder(['B' => true]);
$table->TableStyle('thead','thead','thead_r','thead_r','thead_r');
$table->TableIndex('Number','Description','Amount','Price','Total');

$table->TableStyle('tbody','tbody','tbody_r','tbody_r','tbody_r');

$count = 0;
while($count++<100) {
	$price = rand(100,350);
	$table->TableRow(
		$count,
		'Test test test test test',
		$count.' pcs.',
		number_format($price,2,',','.'),
		number_format($count*$price,2,',','.'),
	);
}

header('Content-type: application/pdf');
echo (string) $pdf;
