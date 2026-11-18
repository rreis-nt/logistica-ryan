<?php
declare(strict_types=1);

/**
 * Gerador de PDF mínimo (somente texto) escrito em PHP puro, sem bibliotecas externas.
 * Usa as fontes padrão Helvetica / Helvetica-Bold com codificação WinAnsi (acentos do português).
 */
class SimplePDF
{
    private const W = 595.28, H = 841.89, MARGIN = 42.0;
    private array $pages = [];
    private string $cur = '';
    private float $y;

    public function __construct()
    {
        $this->newPage();
    }

    private function newPage(): void
    {
        if ($this->cur !== '') $this->pages[] = $this->cur;
        $this->cur = '';
        $this->y = self::H - self::MARGIN;
    }

    private static function enc(string $s): string
    {
        $c = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s);
        if ($c === false) $c = preg_replace('/[^\x20-\x7E]/', '?', $s) ?? '';
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $c);
    }

    public function line(string $text, float $size = 10, bool $bold = false, float $indent = 0, float $gap = 2): void
    {
        $maxChars = (int) floor((self::W - 2 * self::MARGIN - $indent) / ($size * 0.52));
        $parts = $text === '' ? [''] : explode("\n", wordwrap($text, $maxChars, "\n", true));
        foreach ($parts as $p) {
            $lh = $size + $gap;
            if ($this->y - $lh < self::MARGIN) $this->newPage();
            $this->y -= $lh;
            if ($p !== '') {
                $this->cur .= sprintf("BT /%s %.1F Tf %.2F %.2F Td (%s) Tj ET\n", $bold ? 'F2' : 'F1', $size, self::MARGIN + $indent, $this->y, self::enc($p));
            }
        }
    }

    public function space(float $h = 6): void
    {
        $this->y -= $h;
        if ($this->y < self::MARGIN) $this->newPage();
    }

    public function rule(): void
    {
        if ($this->y - 4 < self::MARGIN) $this->newPage();
        $this->y -= 4;
        $this->cur .= sprintf("0.7 w %.2F %.2F m %.2F %.2F l S\n", self::MARGIN, $this->y, self::W - self::MARGIN, $this->y);
        $this->y -= 4;
    }

    public function output(): string
    {
        $pages = $this->pages;
        $pages[] = $this->cur;
        $total = count($pages);
        $objs = [];
        $objs[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $kids = [];
        for ($i = 0; $i < $total; $i++) $kids[] = (5 + $i * 2) . ' 0 R';
        $objs[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . $total . ' >>';
        $objs[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objs[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        foreach ($pages as $i => $content) {
            $footer = sprintf("BT /F1 8 Tf %.2F 24 Td (%s) Tj ET\n", self::MARGIN, self::enc('Página ' . ($i + 1) . ' de ' . $total));
            $stream = $content . $footer;
            $pg = 5 + $i * 2;
            $objs[$pg] = sprintf('<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>', self::W, self::H, $pg + 1);
            $objs[$pg + 1] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . 'endstream';
        }
        $out = "%PDF-1.4\n";
        $offsets = [];
        ksort($objs);
        foreach ($objs as $n => $body) {
            $offsets[$n] = strlen($out);
            $out .= $n . " 0 obj\n" . $body . "\nendobj\n";
        }
        $xref = strlen($out);
        $count = max(array_keys($objs)) + 1;
        $out .= "xref\n0 $count\n0000000000 65535 f \n";
        for ($n = 1; $n < $count; $n++) $out .= sprintf("%010d 00000 n \n", $offsets[$n]);
        $out .= "trailer\n<< /Size $count /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";
        return $out;
    }
}
