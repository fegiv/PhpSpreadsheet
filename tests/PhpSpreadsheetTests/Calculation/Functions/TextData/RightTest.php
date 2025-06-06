<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\Attributes\DataProvider;

class RightTest extends AllSetupTeardown
{
    /**
     * @param mixed $str string from which to extract
     * @param mixed $cnt number of characters to extract
     */
    #[DataProvider('providerRIGHT')]
    public function testRIGHT(mixed $expectedResult, mixed $str = 'omitted', mixed $cnt = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($str === 'omitted') {
            $sheet->getCell('B1')->setValue('=RIGHT()');
        } elseif ($cnt === 'omitted') {
            $this->setCell('A1', $str);
            $sheet->getCell('B1')->setValue('=RIGHT(A1)');
        } else {
            $this->setCell('A1', $str);
            $this->setCell('A2', $cnt);
            $sheet->getCell('B1')->setValue('=RIGHT(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerRIGHT(): array
    {
        return require 'tests/data/Calculation/TextData/RIGHT.php';
    }

    #[DataProvider('providerLocaleRIGHT')]
    public function testLowerWithLocaleBoolean(string $expectedResult, string $locale, mixed $value, mixed $characters): void
    {
        $newLocale = Settings::setLocale($locale);
        if ($newLocale === false) {
            self::markTestSkipped('Unable to set locale for locale-specific test');
        }

        $sheet = $this->getSheet();
        $this->setCell('A1', $value);
        $this->setCell('A2', $characters);
        $sheet->getCell('B1')->setValue('=RIGHT(A1, A2)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerLocaleRIGHT(): array
    {
        return [
            ['RAI', 'fr_FR', true, 3],
            ['AAR', 'nl_NL', true, 3],
            ['OSI', 'fi', true, 3],
            ['ИНА', 'bg', true, 3],
            ['UX', 'fr_FR', false, 2],
            ['WAAR', 'nl_NL', false, 4],
            ['ÄTOSI', 'fi', false, 5],
            ['ЖЬ', 'bg', false, 2],
        ];
    }

    #[DataProvider('providerCalculationTypeRIGHTTrue')]
    public function testCalculationTypeTrue(string $type, string $resultB1, string $resultB2): void
    {
        Functions::setCompatibilityMode($type);
        $sheet = $this->getSheet();
        $this->setCell('A1', true);
        $this->setCell('A2', 'Hello');
        $this->setCell('B1', '=RIGHT(A1, 1)');
        $this->setCell('B2', '=RIGHT(A2, A1)');
        self::assertEquals($resultB1, $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($resultB2, $sheet->getCell('B2')->getCalculatedValue());
    }

    public static function providerCalculationTypeRIGHTTrue(): array
    {
        return [
            'Excel RIGHT(true, 1) AND RIGHT("hello", true)' => [
                Functions::COMPATIBILITY_EXCEL,
                'E',
                'o',
            ],
            'Gnumeric RIGHT(true, 1) AND RIGHT("hello", true)' => [
                Functions::COMPATIBILITY_GNUMERIC,
                'E',
                'o',
            ],
            'OpenOffice RIGHT(true, 1) AND RIGHT("hello", true)' => [
                Functions::COMPATIBILITY_OPENOFFICE,
                '1',
                '#VALUE!',
            ],
        ];
    }

    #[DataProvider('providerCalculationTypeRIGHTFalse')]
    public function testCalculationTypeFalse(string $type, string $resultB1, string $resultB2): void
    {
        Functions::setCompatibilityMode($type);
        $sheet = $this->getSheet();
        $this->setCell('A1', false);
        $this->setCell('A2', 'Hello');
        $this->setCell('B1', '=RIGHT(A1, 1)');
        $this->setCell('B2', '=RIGHT(A2, A1)');
        self::assertEquals($resultB1, $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($resultB2, $sheet->getCell('B2')->getCalculatedValue());
    }

    public static function providerCalculationTypeRIGHTFalse(): array
    {
        return [
            'Excel RIGHT(false, 1) AND RIGHT("hello", false)' => [
                Functions::COMPATIBILITY_EXCEL,
                'E',
                '',
            ],
            'Gnumeric RIGHT(false, 1) AND RIGHT("hello", false)' => [
                Functions::COMPATIBILITY_GNUMERIC,
                'E',
                '',
            ],
            'OpenOffice RIGHT(false, 1) AND RIGHT("hello", false)' => [
                Functions::COMPATIBILITY_OPENOFFICE,
                '0',
                '#VALUE!',
            ],
        ];
    }

    #[DataProvider('providerCalculationTypeRIGHTNull')]
    public function testCalculationTypeNull(string $type, string $resultB1, string $resultB2): void
    {
        Functions::setCompatibilityMode($type);
        $sheet = $this->getSheet();
        $this->setCell('A2', 'Hello');
        $this->setCell('B1', '=RIGHT(A1, 1)');
        $this->setCell('B2', '=RIGHT(A2, A1)');
        self::assertEquals($resultB1, $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($resultB2, $sheet->getCell('B2')->getCalculatedValue());
    }

    public static function providerCalculationTypeRIGHTNull(): array
    {
        return [
            'Excel RIGHT(null, 1) AND RIGHT("hello", null)' => [
                Functions::COMPATIBILITY_EXCEL,
                '',
                '',
            ],
            'Gnumeric RIGHT(null, 1) AND RIGHT("hello", null)' => [
                Functions::COMPATIBILITY_GNUMERIC,
                '',
                'o',
            ],
            'OpenOffice RIGHT(null, 1) AND RIGHT("hello", null)' => [
                Functions::COMPATIBILITY_OPENOFFICE,
                '',
                '',
            ],
        ];
    }

    /** @param mixed[] $expectedResult */
    #[DataProvider('providerRightArray')]
    public function testRightArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=RIGHT({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerRightArray(): array
    {
        return [
            'row vector #1' => [[['llo', 'rld', 'eet']], '{"Hello", "World", "PhpSpreadsheet"}', '3'],
            'column vector #1' => [[['llo'], ['rld'], ['eet']], '{"Hello"; "World"; "PhpSpreadsheet"}', '3'],
            'matrix #1' => [[['llo', 'rld'], ['eet', 'cel']], '{"Hello", "World"; "PhpSpreadsheet", "Excel"}', '3'],
            'column vector #2' => [[['eet'], ['sheet']], '"PhpSpreadsheet"', '{3; 5}'],
        ];
    }
}
