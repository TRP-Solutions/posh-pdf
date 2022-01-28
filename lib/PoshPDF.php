<?php
class PoshPDF extends TCPDF {
	private $style = [];
	private $headerfunc = null;
	private $footerfunc = null;

	public function __construct(...$args) {
		if(!defined('K_PATH_IMAGES')) {
			define('K_PATH_IMAGES',__DIR__.'/../../');
		}
		parent::__construct(...$args);
	}

	public function SetHeaderFunc($func,$height = PDF_MARGIN_TOP) {
		$this->SetHeaderMargin(0);
		$this->SetMargins(PDF_MARGIN_LEFT, $height, PDF_MARGIN_RIGHT);
		$this->headerfunc = $func;
	}

	public function SetFooterFunc($func,$height = PDF_MARGIN_BOTTOM) {
		$this->SetFooterMargin(0);
		$this->SetAutoPageBreak(true, $height);
		$this->footerfunc = $func;
	}

	public function SetStyle($id,$input) {
		$this->style[$id] = [
			'font_name' => isset($input['font_name']) ? $input['font_name'] : PDF_FONT_NAME_MAIN,
			'font_size' => isset($input['font_size']) ? $input['font_size'] : PDF_FONT_SIZE_MAIN,
			'font_style' => isset($input['font_style']) ? $input['font_style'] : '',
			'margin' => isset($input['margin']) ? $input['margin'] : PDF_MARGIN_LEFT,
			'line_height' => isset($input['line_height']) ? $input['line_height'] : 0,
			'align' => isset($input['align']) ? $input['align'] : 'L',
			'fill' => isset($input['fill']) ? $this->ParseColor($input['fill']) : 0,
			'color' => isset($input['color']) ? $this->ParseColor($input['color']) : null,
		];
	}

	public function WriteParagraph($style_id,$text) {
		$s = $this->style[$style_id];

		$this->SetTextColorArray($s['color']);
		$this->SetFont($s['font_name'],$s['font_style'],$s['font_size']);

		if($s['fill']) $this->SetFillColorArray($s['fill']);
		$this->Write($s['line_height'],$text,null,$s['fill'],$s['align']);
	}

	public function Header() {
		$func = $this->headerfunc;
		if($func) {
			$func($this);
		}
	}

	public function Footer() {
		$func = $this->footerfunc;
		if($func) {
			$func($this);
		}
	}

	private function ParseColor($input) {
		if(strpos($input,'#')===0 && strlen($input)===7) {
			return [
				hexdec(substr($input,1,2)),
				hexdec(substr($input,3,2)),
				hexdec(substr($input,5,2)),
			];
		}
		return null;
	}

	public function __toString(){
		return $this->output(null,'S');
	}
}
