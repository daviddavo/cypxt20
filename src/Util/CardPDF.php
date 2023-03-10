<?php

namespace App\Util;

use TCPDF;
use App\Entity\OnlineCall;

// Tengo 38.5 de fiebre, no me responsabilizo de la calidad de este código
class CardPDF extends TCPDF 
{
    /**
     * @param OnlineCall[] $cards
     */
    public function __construct(
        protected array $cards,
        protected $title = 'Hello World!',
        protected $height = 105,
        protected $width = 150,
        protected $drawLines = False,
        protected $drawBoxes = False, // for debugging
        protected $lineHeight = 6,
        protected $firstLineHeight = 14,
    ) {
        // landscape and mm always
        parent::__construct('L', 'mm', [$height, $width]);
        $this->setTopMargin($firstLineHeight);
    }

    private function drawLinesInPage() {
        $w = $this->getPageWidth();
        $this->Line(0, $this->firstLineHeight, $w, $this->firstLineHeight, [
            'color' => array(255, 0, 0),
            'width' => 0.5,
        ]);

        $y = $this->firstLineHeight + $this->lineHeight;
        while ($y + $this->lineHeight < $this->getPageHeight()) {
            $this->Line(0, $y, $w, $y, [
                'color' => array(0,0,0),
                'width' => 0.25,
            ]);

            $y += $this->lineHeight;
        }
    }

    private function getLineY($line) {
        return $this->firstLineHeight + $this->lineHeight * $line;
    }

    private function setLabelFont() {
        $this->setFont('times', '', 12);
    }

    private function setBigFont($size = 24) {
        // TODO: Use other font (Roboto Condensed)
        $this->setFont('helvetica', '', $size);
    }

    public static function prettyNumber(string $number): string {
        $number = str_replace(' ', '', $number);
        $number = chunk_split($number, 3, ' ');
        return $number;
    }

    public static function sanitizeComments(?string $comments): string {
        $comments = str_replace('\r\n', '', $comments);
        $comments = str_replace(' .', '.', $comments);
        $comments = str_replace(' ,', ',', $comments);
        return $comments;
    }
    
    public function Label(string $text, $w=0, $ln=1, $stretch=0) {
        $this->setLabelFont();
        $this->Cell($w, $this->lineHeight, $text, border: $this->drawBoxes, ln: $ln, stretch: $stretch);
    }

    public function Datum(string $text, $w=0, $ln=1, $align='L', $stretch=0) {
        $this->setBigFont();
        $this->Cell($w, 2*$this->lineHeight, $text, $this->drawBoxes, $ln, $align);
    }

    public function PossiblyLongDatum(string $text, $w=0, $ln=1) {
        $this->setBigFont();
        if ($w == 0) {
            $w = $this->x - $this->pageWidth - $this->marginRight;
        }

        if ($this->GetStringWidth($text) < $w) {
            $this->Cell($w, 2*$this->lineHeight, $text, $this->drawBoxes, ln: $ln);
        } else {
            $this->setFontSize(12);
            $this->MultiCell($w, 2*$this->lineHeight, $text, $this->drawBoxes, ln: $ln, maxh: 2*$this->lineHeight, align: 'L', stretch: 1);
            $this->setFontSize(24);
        }
    }

    private function drawTextInPage(OnlineCall $card) {
        // TODO: Use condensed, bold fonts like in the tex template (Roboto Condensed)

        $this->y = $this->getLineY(0);
        $this->Label("Para:");

        // El nombre y la edad. Siguiente línea, dos líneas de alto
        $this->setBigFont();
        $this->y = $this->getLineY(1);
        $this->Datum(sprintf('%s, %d', mb_strtoupper($card->getName()), $card->getAge()));

        $this->y = $this->getLineY(4);
        $lw = $this->getPageWidth() - $this->lMargin - $this->rMargin;
        $width_left = $lw * 0.60;
        $width_right = $lw - $width_left;

        // The two labels
        $this->Label('De:', $width_left, 0);
        $this->Label('Tlf:', $width_right, 1, stretch: 1);

        // The two fields
        $this->y = $this->getLineY(5);

        // Try to fit into one line, or do two if impossible
        $fromName = mb_strtoupper($card->getFromName());
        $this->PossiblyLongDatum($fromName, $width_left, ln: 0);
        $this->Datum($this->prettyNumber($card->getNumber()), $width_right, align: 'R', stretch: 1);

        // Empty cell?
        $this->y = $this->getLineY(8);
        $this->Label('Comentarios:');

        $this->setFont('courier', '', 10);
        $this->y = $this->getLineY(9);
        $this->MultiCell(0, 0, $this->sanitizeComments($card->getComment()), $this->drawBoxes, 1);
    }

    private function drawPage($card) {
        $this->AddPage();
        if ($this->drawLines) {
            $this->drawLinesInPage();
        }

        $this->drawTextInPage($card);
    }

    public function drawAll() {
        $this->setTitle($this->title);
        $this->setAuthor('Montando el Local');
        $this->setCreator('Cuentos y Poemas por Teléfono');

        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->setAutoPageBreak(false);

        foreach ($this->cards as $c) {
            $this->drawPage($c);
        }
    }
}