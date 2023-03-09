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
        protected $height = 100,
        protected $width = 150,
        protected $drawLines = False,
        protected $drawBoxes = False, // for debugging
        protected $lineHeight = 6,
    ) {
        // landscape and mm always
        parent::__construct('L', 'mm', [$height, $width]);
    }

    private function drawLinesInPage() {
        // TODO: Make a function that draws the lines
        // TODO: Take into account the first line
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

    private function drawTextInPage(OnlineCall $card) {
        // TODO: Check with drawLines that everything fits
        // TODO: Use condensed, bold fonts like in the tex template (Roboto Condensed)

        // Para: pequeña etiqueta en la primera línea
        $this->setLabelFont();
        $this->Cell(0,$this->lineHeight,'Para:', $this->drawBoxes, 1);

        // El nombre y la edad. Siguiente línea, dos líneas de alto
        $this->setBigFont();
        $this->Cell(0,2*$this->lineHeight,sprintf('%s, %d', mb_strtoupper($card->getName()), $card->getAge()), $this->drawBoxes, 1);
        $this->y += $this->lineHeight;

        $lw = $this->getPageWidth() - $this->lMargin - $this->rMargin;
        $width_left = $lw * 0.60;
        $width_right = $lw - $width_left;

        // The two labels
        $this->setLabelFont();
        $this->Cell($width_left, $this->lineHeight,'De:', $this->drawBoxes, 0);
        $this->Cell($width_right, $this->lineHeight,'Tlf:', $this->drawBoxes, 1, stretch: 1);

        // The two fields
        $h2 = 2*$this->lineHeight;
        $this->setBigFont();

        // Try to fit into one line, or do two if impossible
        $fromName = mb_strtoupper($card->getFromName());
        if ($this->GetStringWidth($fromName) < $width_left) {
            $this->Cell($width_left, $h2, $fromName, $this->drawBoxes, ln: 0);
        } else {
            $this->setFontSize(12);
            $this->MultiCell($width_left, $h2, $fromName, $this->drawBoxes, ln: 0, maxh: $h2, align: 'L', stretch: 1);
            $this->setFontSize(24);
        }
        $this->Cell($width_right, $h2, $this->prettyNumber($card->getNumber()), $this->drawBoxes, 1, align: 'R', stretch: 1);
        $this->y += $this->lineHeight;

        // Empty cell?
        $this->setLabelFont();
        $this->Cell(0, $this->lineHeight, 'Comentarios:', $this->drawBoxes, 1);
        $this->setFont('courier', '', 10);
        $this->MultiCell(0, 0, $this->sanitizeComments($card->getComment()), $this->drawBoxes, 1);
    }

    private function drawPage($card) {
        // TODO: Make a function that draws
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