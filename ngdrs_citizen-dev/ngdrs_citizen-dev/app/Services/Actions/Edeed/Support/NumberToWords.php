<?php

namespace App\Services\Actions\EDeed\Support;

/**
 * Best-effort reimplementation of the CakePHP `num_to_words` model's
 * in_words(), based on the convertNumberToWords() helper in the legacy
 * codebase. If you have the real NumToWords model source, swap this out
 * for an exact port — some templates depend on exact wording.
 */
class NumberToWords
{
    private const HYPHEN = '-';
    private const CONJUNCTION = ' and ';
    private const SEPARATOR = ', ';
    private const NEGATIVE = 'negative ';
    private const DECIMAL = ' point ';

    private const DICTIONARY = [
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        100000 => 'lakh',
        10000000 => 'crore',
    ];

    /** Matches the exact method name EDeedTemplate.php calls. */
    public function in_words($number)
    {
        return $this->inWords($number);
    }

    public function inWords($number)
    {
        if (!is_numeric($number)) {
            return false;
        }

        if ($number < 0) {
            return self::NEGATIVE . $this->inWords(abs($number));
        }

        $fraction = null;

        if (strpos((string) $number, '.') !== false) {
            [$number, $fraction] = explode('.', (string) $number);
        }

        $number = (int) $number;

        if ($number < 21) {
            $string = self::DICTIONARY[$number];

        } elseif ($number < 100) {
            $tens = intdiv($number, 10) * 10;
            $units = $number % 10;

            $string = self::DICTIONARY[$tens];

            if ($units) {
                $string .= self::HYPHEN . self::DICTIONARY[$units];
            }

        } elseif ($number < 1000) {
            $hundreds = intdiv($number, 100);
            $remainder = $number % 100;

            $string = self::DICTIONARY[$hundreds] . ' ' . self::DICTIONARY[100];

            if ($remainder) {
                $string .= self::CONJUNCTION . $this->inWords($remainder);
            }

        } elseif ($number < 100000) {
            // Thousand
            $thousands = intdiv($number, 1000);
            $remainder = $number % 1000;

            $string = $this->inWords($thousands) . ' ' . self::DICTIONARY[1000];

            if ($remainder) {
                $string .= self::SEPARATOR . $this->inWords($remainder);
            }

        } elseif ($number < 10000000) {
            // Lakh
            $lakhs = intdiv($number, 100000);
            $remainder = $number % 100000;

            $string = $this->inWords($lakhs) . ' ' . self::DICTIONARY[100000];

            if ($remainder) {
                $string .= self::SEPARATOR . $this->inWords($remainder);
            }

        } else {
            // Crore
            $crores = intdiv($number, 10000000);
            $remainder = $number % 10000000;

            $string = $this->inWords($crores) . ' ' . self::DICTIONARY[10000000];

            if ($remainder) {
                $string .= self::SEPARATOR . $this->inWords($remainder);
            }
        }

        if ($fraction !== null && is_numeric($fraction)) {
            $string .= self::DECIMAL;

            $words = array_map(
                fn($digit) => self::DICTIONARY[(int) $digit],
                str_split((string) $fraction)
            );

            $string .= implode(' ', $words);
        }

        return $string;
    }
}