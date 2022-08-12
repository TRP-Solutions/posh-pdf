<?php
require_once __DIR__.'/PoshPDFTable.php';

class PoshPDF extends TCPDF {
	private $style = [
		'default' => [
			'font_name' => PDF_FONT_NAME_MAIN,
			'font_size' => PDF_FONT_SIZE_MAIN,
			'font_style' => '',
			'margin' => PDF_MARGIN_LEFT,
			'line_height' => null,
			'align' => 'L',
			'vertical_align'=>'M',
			'fill' => 0,
			'color' => null,
		]
	];
	private $current_style = null;
	private $headerfunc = null;
	private $footerfunc = null;
	private $footerheight = 0;

	public function __construct(...$args){
		parent::__construct(...$args);
		$this->SetLeftMargin(PDF_MARGIN_LEFT);
		$this->SetRightMargin(PDF_MARGIN_RIGHT);
	}

	public function SetHeaderFunc($func,$height = PDF_MARGIN_TOP){
		$this->SetHeaderMargin(0);
		$this->SetMargins(PDF_MARGIN_LEFT, $height, PDF_MARGIN_RIGHT);
		$this->headerfunc = $func;
	}

	public function SetFooterFunc($func,$height = PDF_MARGIN_BOTTOM){
		$this->footerheight = $height;
		$this->SetFooterMargin(0);
		$this->SetAutoPageBreak(true, $this->footerheight);
		$this->footerfunc = $func;
	}

	public function CreateStyle($id,$input,$source = 'default'){
		$font_size = isset($input['font_size']) ? $input['font_size'] : $this->style[$source]['font_size'];
		$this->style[$id] = [
			'font_name' => isset($input['font_name']) ? $input['font_name'] : $this->style[$source]['font_name'],
			'font_size' => $font_size,
			'font_style' => isset($input['font_style']) ? $input['font_style'] : $this->style[$source]['font_style'],
			'margin' => isset($input['margin']) ? $input['margin'] : $this->style[$source]['margin'],
			'line_height' => isset($input['line_height']) ? $input['line_height'] : $font_size/2,
			'align' => isset($input['align']) ? $input['align'] : $this->style[$source]['align'],
			'vertical_align' => isset($input['vertical_align']) ? $input['vertical_align'] : $this->style[$source]['vertical_align'],
			'fill' => isset($input['fill']) ? $this->ParseColor($input['fill']) : $this->style[$source]['fill'],
			'color' => isset($input['color']) ? $this->ParseColor($input['color']) : $this->style[$source]['color'],
		];
	}

	public function SetStyle($style_id){
		$s = isset($this->style[$style_id]) ? $this->style[$style_id] : $this->style['default'];

		if($this->current_style !== $style_id){
			$this->SetTextColorArray($s['color'] ? $s['color'] : [0,0,0]);
			$this->SetFont($s['font_name'],$s['font_style'],$s['font_size']);
			if($s['fill']) $this->SetFillColorArray($s['fill']);
			$this->current_style = $style_id;
		}

		if(!isset($s['line_height'])){
			$s['line_height'] = $s['font_size'] / 2;
		}

		return $s;
	}

	public function GetStyle($style_id){
		$s = isset($this->style[$style_id]) ? $this->style[$style_id] : $this->style['default'];
		return $s;
	}

	public function WriteParagraph($text,$style_id = 'default'){
		$text .= PHP_EOL;
		$s = $this->SetStyle($style_id);
		$this->Write($s['line_height'],$text,null,$s['fill'],$s['align']);
	}

	public function Header(){
		$this->current_style = null;
		$func = $this->headerfunc;
		if($func){
			$func($this);
		}
	}

	public function Footer(){
		$func = $this->footerfunc;
		if($func){
			$this->SetAutoPageBreak(false);
			$func($this);
			$this->SetAutoPageBreak(true, $this->footerheight);
		}
	}

	private function ParseColor($input){
		if(strpos($input,'#')===0 && strlen($input)===7){
			return [
				hexdec(substr($input,1,2)),
				hexdec(substr($input,3,2)),
				hexdec(substr($input,5,2)),
			];
		}
		return null;
	}

	public function Table($table_width = null) {
		return new PoshPDFTable($this,$table_width);
	}

	public function __toString(){
		return $this->output(null,'S');
	}
}
