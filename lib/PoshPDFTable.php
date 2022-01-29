<?php
class PoshPDFTable {
	private $pdf = null;
	private $table_border = false;
	private $table_style = [];
	private $table_index = null;
	private $table_width = 0;
	private $table_cellwidth = [];

	public function __construct($pdf,$table_width){
		$this->pdf = $pdf;

		if($table_width===null){
			$table_width = $this->pdf->getPageWidth();
			$margins = $this->pdf->getMargins();

			$table_width -= $margins['left'];
			$table_width -= $margins['right'];
		}

		$this->table_width = $table_width;
	}

	public function TableWidth(...$cellwidth){
		$usedwidth = 0;
		$part = 0;
		foreach($cellwidth as $key => $width){
			if(empty($width)){
				$part++;
			}
			else {
				$usedwidth += $width;
			}
		}

		if($part){
			foreach($cellwidth as $key => $width){
				if(empty($width)){
					$cellwidth[$key] = ($this->table_width-$usedwidth)/$part;
				}
			}
		}

		$this->table_cellwidth = $cellwidth;
	}

	public function TableStyle(...$styles){
		if($styles===[]){
			$this->table_style = ['default'];
		}
		else {
			$this->table_style = $styles;
		}
	}

	public function TableIndex(...$text){
		if(empty($text)) $this->table_index = null;
		else {
			$this->table_index['text'] = $text;
			$this->table_index['style'] = $this->table_style;
			$this->TablePrintIndex();
		}
	}

	private function TablePrintIndex(){
		$style = $this->table_style;

		$this->table_style = $this->table_index['style'];
		$this->TableRow(...$this->table_index['text']);

		$this->table_style = $style;
	}

	public function TableBorder($border){
		$this->table_border = $border;
	}

	public function TableRow(...$cells){
		$cell_count = count($cells);
		$width_count = count($this->table_cellwidth);
		if(!isset($this->table_cellwidth) || $cell_count > $width_count) return;

		$cell_height = 0;
		for($i = 0; $i < $cell_count; $i++){
			if(isset($this->table_style[$i])){
				$style_id[$i] = $this->table_style[$i];
			}
			elseif(isset($this->table_style[0])){
				$style_id[$i] = $this->table_style[0];
			}
			else {
				$style_id[$i] = 'default';
			}
			$line_height = $this->pdf->GetStyle($style_id[$i])['line_height'];
			$cell_height = max($cell_height, $line_height*count(explode("\n",$cells[$i])));
		}

		if($this->pdf->getAutoPageBreak() && $this->pdf->getY() + $cell_height + $this->pdf->getBreakMargin() >= $this->pdf->getPageHeight()){
			$this->pdf->AddPage();
			if(isset($this->table_index)){
				$this->TablePrintIndex();
			}
		}

		for($i = 0; $i < $cell_count; $i++){
			$w = $this->table_cellwidth[$i];
			$s = $this->pdf->SetStyle($style_id[$i]);

			$this->pdf->MultiCell($w,$cell_height,$cells[$i],$this->table_border,$s['align'],(bool) $s['fill'],0,
				// default values to get to late parameter
				'', '', true, 0, false, true,
				// maxh: $cell_height, vertical align: Middle
				$cell_height,'M');
		}
		$this->pdf->Ln($cell_height);
	}
}
