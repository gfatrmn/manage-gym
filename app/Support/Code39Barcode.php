<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use InvalidArgumentException;

class Code39Barcode
{
    /**
     * Code 39 patterns with 9 elements per symbol, starting with a bar.
     */
    private const PATTERNS = [
        '0' => 'nnnwwnwnn',
        '1' => 'wnnwnnnnw',
        '2' => 'nnwwnnnnw',
        '3' => 'wnwwnnnnn',
        '4' => 'nnnwwnnnw',
        '5' => 'wnnwwnnnn',
        '6' => 'nnwwwnnnn',
        '7' => 'nnnwnnwnw',
        '8' => 'wnnwnnwnn',
        '9' => 'nnwwnnwnn',
        'A' => 'wnnnnwnnw',
        'B' => 'nnwnnwnnw',
        'C' => 'wnwnnwnnn',
        'D' => 'nnnnwwnnw',
        'E' => 'wnnnwwnnn',
        'F' => 'nnwnwwnnn',
        'G' => 'nnnnnwwnw',
        'H' => 'wnnnnwwnn',
        'I' => 'nnwnnwwnn',
        'J' => 'nnnnwwwnn',
        'K' => 'wnnnnnnww',
        'L' => 'nnwnnnnww',
        'M' => 'wnwnnnnwn',
        'N' => 'nnnnwnnww',
        'O' => 'wnnnwnnwn',
        'P' => 'nnwnwnnwn',
        'Q' => 'nnnnnnwww',
        'R' => 'wnnnnnwwn',
        'S' => 'nnwnnnwwn',
        'T' => 'nnnnwnwwn',
        'U' => 'wwnnnnnnw',
        'V' => 'nwwnnnnnw',
        'W' => 'wwwnnnnnn',
        'X' => 'nwnnwnnnw',
        'Y' => 'wwnnwnnnn',
        'Z' => 'nwwnwnnnn',
        '-' => 'nwnnnnwnw',
        '.' => 'wwnnnnwnn',
        ' ' => 'nwwnnnwnn',
        '$' => 'nwnwnwnnn',
        '/' => 'nwnwnnnwn',
        '+' => 'nwnnnwnwn',
        '%' => 'nnnwnwnwn',
        '*' => 'nwnnwnwnn',
    ];

    public static function svg(?string $value, int $barHeight = 64, int $narrowWidth = 2, int $wideWidth = 5): HtmlString
    {
        $normalized = strtoupper(trim((string) $value));

        if ($normalized === '') {
            return new HtmlString('');
        }

        $payload = '*'.$normalized.'*';
        $x = 12;
        $parts = [];

        foreach (str_split($payload) as $character) {
            $pattern = self::PATTERNS[$character] ?? null;

            if (! $pattern) {
                throw new InvalidArgumentException("Unsupported Code 39 character [{$character}].");
            }

            foreach (str_split($pattern) as $index => $widthType) {
                $width = $widthType === 'w' ? $wideWidth : $narrowWidth;
                $isBar = $index % 2 === 0;

                if ($isBar) {
                    $parts[] = '<rect x="'.$x.'" y="0" width="'.$width.'" height="'.$barHeight.'" fill="#111827" />';
                }

                $x += $width;
            }

            $x += $narrowWidth;
        }

        $svgWidth = $x + 12;
        $labelY = $barHeight + 22;
        $svgHeight = $barHeight + 32;

        return new HtmlString(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$svgWidth.' '.$svgHeight.'" role="img" aria-label="Barcode '.$normalized.'">'.
            '<rect width="'.$svgWidth.'" height="'.$svgHeight.'" rx="12" fill="#ffffff" />'.
            implode('', $parts).
            '<text x="'.($svgWidth / 2).'" y="'.$labelY.'" text-anchor="middle" font-size="14" font-family="monospace" letter-spacing="1.5" fill="#111827">'.$normalized.'</text>'.
            '</svg>'
        );
    }
}
