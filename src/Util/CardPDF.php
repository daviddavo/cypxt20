<?php

namespace App\Util;

use TCPDF;
use TCPDF_FONTS;
use App\Entity\OnlineCall;

// Tengo 38.5 de fiebre, no me responsabilizo de la calidad de este código
class CardPDF extends TCPDF 
{
    protected string $fontsPath;

    /**
     * @param OnlineCall[] $cards
     */
    public function __construct(
        protected array $cards,
        protected $title = 'Título',
        protected $height = 105,
        protected $width = 150,
        protected $drawLines = False,
        protected $drawBoxes = False, // for debugging
        protected $lineHeight = 6,
        protected $firstLineHeight = 14,
        protected $lineMargin = 1,
        protected $compact = 0,
    ) {
        // landscape and mm always
        parent::__construct('L', 'mm', [$height, $width]);
        $this->setTopMargin($firstLineHeight);
        $this->fontsPath = './assets/fonts/';
    }

    public function setFontsPath(string $fontsPath) {
        $this->fontsPath = $fontsPath;
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
        $this->setFont('times', '', 13);
    }

    private function setBigFont($size = 34) {
        $fontName = TCPDF_FONTS::addTTFFont($this->fontsPath . 'RobotoCondensed-Bold.ttf');
        $this->setFont($fontName, '', $size);
    }

    private function setMonoFont($size = 12) {
        $fontName = 'courier';
        $this->setFont($fontName, '', $size);
    }

    public static function prettyNumber(string $number): string {
        $number = str_replace(' ', '', $number);
        $number = str_replace('+34', '', $number);
        $number = chunk_split($number, 3, ' ');
        return $number;
    }

    public static function sanitizeComments(?string $comments): string {
        if (is_null($comments)) return "";

        $comments = preg_replace('/\s\s+/m', ' ', $comments);
        $comments = str_replace('\r\n', '', $comments);
        $comments = str_replace(' .', '.', $comments);
        $comments = str_replace(' ,', ',', $comments);
        return $comments;
    }
    
    public function Label(int $line, string $text, $w=0, $ln=1, $stretch=0) {
        $this->setLabelFont();
        $this->y = $this->getLineY($line+1) - $this->lineMargin;
        $this->Cell($w, $this->lineHeight, $text, border: $this->drawBoxes, ln: $ln, stretch: $stretch, calign: 'L', valign: 'B');
    }

    public function CompactDatum(int $line, string $text, $w=0, $ln=1, $align='L', $stretch=0) {
        $this->setBigFont(12);
        $this->setFontSize(12);
        $this->y = $this->getLineY($line+1) - $this->lineMargin;
        $this->Cell($w, 2*$this->lineHeight, $text, $this->drawBoxes, ln: $ln, calign: 'L', stretch: $stretch);
    }

    public function Datum(int $line, string $text, $w=0, $ln=1, $align='L', $stretch=0) {
        if ($this->compact) {
            $this->CompactDatum($line, $text, $w, $ln, $align, $stretch);
        } else {
            $this->setBigFont();

            $this->y = $this->getLineY($line+2);
            $this->Cell($w, 2*$this->lineHeight, $text, $this->drawBoxes, $ln, $align, calign: 'L', valign: 'B', stretch: $stretch);
        }
    }

    public function PossiblyLongDatum(int $line, string $text, $w=0, $ln=1, $stretch=1) {
        $this->setBigFont();
        if ($w == 0) {
            $w = $this->getPageWidth() - $this->lMargin - $this->rMargin;
        }

        if ($this->compact) {
            $this->CompactDatum($line, $text, $w, $ln, stretch: $stretch);
        } else if ($this->GetStringWidth($text) < $w*1.15 - $this->cell_padding['L'] - $this->cell_padding['R']) {
            // Allow 15% stretching before splitting
            $this->y = $this->getLineY($line+2);
            $this->Cell($w, 2*$this->lineHeight, $text, $this->drawBoxes, ln: $ln, calign: 'L', stretch: $stretch);
        } else {
            $this->setFontSize(12);
            $this->y = $this->getLineY($line); // + $this->FontDescent; // - $this->FontDescent;
            $this->setCellHeightRatio($this->lineHeight / $this->getFontSize());
            $this->MultiCell($w, 2*$this->lineHeight, $text, $this->drawBoxes, ln: $ln, maxh: 2*$this->lineHeight, align: 'L', stretch: $stretch);
            $this->setFontSize(24);
        }
    }

    public function Comments(int $line, string $text) {
        $fontName = TCPDF_FONTS::addTTFFont($this->fontsPath . 'RobotoCondensed-Regular.ttf');
        $this->setFont($fontName, '', $this->compact?8:10);
        $this->y = $this->getLineY($line);
        $this->setCellHeightRatio($this->lineHeight / $this->getFontSize());
        $maxh = $this->getPageHeight() - $this->y;
        $this->MultiCell(0, 0, $this->sanitizeComments($text), $this->drawBoxes, align: 'L', fitcell: 1, maxh: $maxh);
    }

    private function drawTextInPage(OnlineCall $card) {
        $lw = $this->getPageWidth() - $this->lMargin - $this->rMargin;
        $width_left = $lw * 0.80;
        $line = 0;

        $this->y = $this->firstLineHeight;
        $this->setMonoFont();
        $this->Cell($width_left, $this->lineHeight, $this->title, border: $this->drawBoxes, ln: 0, align: 'L', calign: 'B', valign: 'C');
        $this->Cell($line, $this->lineHeight, "ID: " . $card->getId(), border: $this->drawBoxes, ln: 1, align: 'R', calign: 'B', valign: 'C');

        $this->Label($line, "Para:");

        // El nombre y la edad. Siguiente línea, dos líneas de alto
        $line++;
        $this->PossiblyLongDatum($line, sprintf('%s, %d', mb_strtoupper($card->getName()), $card->getAge()));

        $width_left = $this->compact?($lw * 0.65):($lw * 0.50);
        $width_right = $lw - $width_left;

        // The two labels
        $line += $this->compact?1:3;
        $this->Label($line, 'De:', $width_left, 0);
        $this->Label($line, 'Tlf:', $width_right, 1, stretch: 1);

        // The two fields
        // Try to fit into one line, or do two if impossible
        $line++;
        $fromName = mb_strtoupper($card->getFromName());
        $this->PossiblyLongDatum($line, $fromName, $width_left, ln: 0);
        $this->Datum($line, $this->prettyNumber($card->getNumber()), $width_right, align: 'R', stretch: 1);

        // Empty cell?
        $line += $this->compact?1:3;
        $this->Label($line, 'Comentarios:');
        $this->Comments($line+1, $this->sanitizeComments($card->getComment()));

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