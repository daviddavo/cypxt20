<?php

namespace App\Util;

use Fpdf\Fpdf;

// Tengo 38.5 de fiebre, no me responsabilizo de la calidad de este código
class CardPDF extends Fpdf 
{
    public function __construct(
        $cards,
        $title,
        $size = [100, 150], // mm
        $drawLines = False,
        $drawBoxes = False, // for debugging
    ) {
        // landscape and mm always
        parent::__construct('L', 'mm', $size);
        $this->cards = $cards;
        $this->documentTitle = $title;
        $this->drawLines = $drawLines;
        $this->drawBoxes = $drawBoxes;
    }

    private function drawLinesInPage() {
        // TODO: Make a function that draws the lines
    }

    private function drawTextInPage() {
        // TODO: Draw id
        // TODO: Draw "from"
        // TODO: Draw comment
        // TODO: Draw name
        // TODO: Draw number
    }

    private function drawPage() {
        // TODO: Make a function that draws
        if ($this->drawLines) {
            $this->drawLinesInPage();
        }

        $this->drawTextInPage();
    }

    public function drawAll() {
        $this->setTitle($this->documentTitle);
        $this->AddPage();
        $this->SetFont('Arial', 'B', '16');
        $this->Cell(40,0,'Hello World!', 1);
    }
}