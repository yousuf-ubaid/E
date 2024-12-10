<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class NumberToWords
{
    /**
     * Convert to number
     *
     * @param float $number
     * @throws InvalidArgumentException
     * @return string
     */
    function convert_number(float $number): string
    {
        if (!is_numeric($number)) {
            throw new InvalidArgumentException("Input must be a numeric value.");
        }

        // Handle negative numbers
        if ($number < 0) {
            return "negative " . $this->convert_number(abs($number));
        }

        // Handle zero
        if ($number == 0) {
            return "zero";
        }

        // Convert to whole number
        $wholePart = floor($number);

        // Arrays for conversion
        $ones = [
            "", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine",
            "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen",
            "Seventeen", "Eighteen", "Nineteen"
        ];

        $tens = [
            "", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"
        ];

        $thousands = ["", "Thousand", "Million", "Billion"];

        $words = [];
        $scale = 0;

        // Convert whole part to words
        while ($wholePart > 0) {
            $part = $wholePart % 1000;
            if ($part > 0) {
                $word = "";
                if ($part >= 100) {
                    $word .= $ones[floor($part / 100)] . " Hundred ";
                    $part %= 100;
                }
                if ($part >= 20) {
                    $word .= $tens[floor($part / 10)] . " ";
                    $part %= 10;
                }
                if ($part > 0) {
                    $word .= $ones[$part] . " ";
                }
                $words[] = trim($word) . " " . $thousands[$scale];
            }
            $wholePart = floor($wholePart / 1000);
            $scale++;
        }

        return trim(implode(" ", array_reverse($words))); // Combine and return the words
    }
}