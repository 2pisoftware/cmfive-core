<?php

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    /**
     * Test formatCurrency() in functions.php.
     *
     * @return void
     */
    public function testFormatCurrency(): void
    {
        require_once("system/web.php");
        $w = new Web();

        $testCases = [
            ["value" => -1, "locale" => "en_AU", "currency" => "AUD", "want" => "-$1.00"],
            ["value" => -1.55, "locale" => "en_AU", "currency" => "AUD", "want" => "-$1.55"],
            ["value" => -1.555, "locale" => "en_AU", "currency" => "AUD", "want" => "-$1.56"],
            ["value" => 0, "locale" => "en_AU", "currency" => "AUD", "want" => "$0.00"],
            ["value" => 0.55, "locale" => "en_AU", "currency" => "AUD", "want" => "$0.55"],
            ["value" => 0.555, "locale" => "en_AU", "currency" => "AUD", "want" => "$0.56"],
            ["value" => 1, "locale" => "en_AU", "currency" => "AUD", "want" => "$1.00"],
            ["value" => 1.55, "locale" => "en_AU", "currency" => "AUD", "want" => "$1.55"],
            ["value" => 1.555, "locale" => "en_AU", "currency" => "AUD", "want" => "$1.56"],
        ];

        foreach ($testCases as $testCase) {
            $this->assertEquals($testCase["want"], formatCurrency($testCase["value"], $testCase["locale"], $testCase["currency"]));
        }
    }
}
