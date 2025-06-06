<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\NamedRange;
use PHPUnit\Framework\Attributes\DataProvider;

class RowOnSpreadsheetTest extends AllSetupTeardown
{
    #[DataProvider('providerROWonSpreadsheet')]
    public function testRowOnSpreadsheet(mixed $expectedResult, string $cellReference = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $this->setArrayAsValue();
        $sheet = $this->getSheet();
        $sheet->setTitle('ThisSheet');
        $this->getSpreadsheet()->addNamedRange(new NamedRange('namedrangex', $sheet, '$E$2:$E$6'));
        $this->getSpreadsheet()->addNamedRange(new NamedRange('namedrangey', $sheet, '$F$2:$H$2'));
        $this->getSpreadsheet()->addNamedRange(new NamedRange('namedrange3', $sheet, '$F$4:$H$4'));
        $this->getSpreadsheet()->addNamedRange(new NamedRange('namedrange5', $sheet, '$F$5:$H$5', true));

        $sheet1 = $this->getSpreadsheet()->createSheet();
        $sheet1->setTitle('OtherSheet');
        $this->getSpreadsheet()->addNamedRange(new NamedRange('localname', $sheet1, '$F$6:$H$6', true));

        if ($cellReference === 'omitted') {
            $sheet->getCell('B3')->setValue('=ROW()');
        } else {
            $sheet->getCell('B3')->setValue("=ROW($cellReference)");
        }

        $result = $sheet->getCell('B3')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerROWOnSpreadsheet(): array
    {
        return require 'tests/data/Calculation/LookupRef/ROWonSpreadsheet.php';
    }

    public function testINDIRECTLocalDefinedName(): void
    {
        $sheet = $this->getSheet();
        $this->setArrayAsValue();

        $sheet1 = $this->getSpreadsheet()->createSheet();
        $sheet1->setTitle('OtherSheet');
        $this->getSpreadsheet()->addNamedRange(new NamedRange('newnr', $sheet1, '$F$5:$H$5', true)); // defined locally, only usable on sheet1

        $sheet1->getCell('B3')->setValue('=ROW(newnr)');
        $result = $sheet1->getCell('B3')->getCalculatedValue();
        self::assertSame(5, $result);

        $sheet->getCell('B3')->setValue('=ROW(newnr)');
        $result = $sheet->getCell('B3')->getCalculatedValue();
        self::assertSame('#NAME?', $result);
    }
}
