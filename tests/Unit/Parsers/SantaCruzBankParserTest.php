<?php

namespace Tests\Unit\Parsers;

use DateTime;
use DateTimeZone;
use MidasSoft\DominicanBankParser\Cache\ArrayCacheDriver;
use MidasSoft\DominicanBankParser\Files\CSV;
use MidasSoft\DominicanBankParser\Parsers\SantaCruzBankParser;
use PHPUnit\Framework\TestCase;

class SantaCruzBankParserTest extends TestCase
{
    private $parser;
    private $file;

    public function setUp()
    {
        $this->parser = new SantaCruzBankParser();
        $this->parser->setCacheManager(new ArrayCacheDriver());
        $this->file = new CSV(file_get_contents(__DIR__.'/../../resources/santa_cruz_bank_file.csv'));
        parent::setUp();
    }

    /** @test */
    public function it_can_parse_santa_cruz_bank()
    {
        $parsedData = $this->parser->parse($this->file);

        $this->assertInstanceOf('MidasSoft\DominicanBankParser\Collections\DepositCollection', $parsedData);
        $this->assertCount(6, $parsedData);
    }

    /**
     * @test
     * @expectedException MidasSoft\DominicanBankParser\Exceptions\EmptyFileException
     * @expectedExceptionMessage You're trying to parse an empty file.
     */
    public function santa_cruz_bank_parser_throws_an_exception_when_you_try_to_parse_an_empty_file()
    {
        $parsedData = $this->parser->parse(new CSV(''));
    }

    /** @test */
    public function santa_cruz_bank_parser_can_parse_file_from_cache()
    {
        $parsedData = $this->parser->parse($this->file);
        $parsedFromCache = $this->parser->getCacheManager()->get((new DateTime('now', new DateTimeZone('America/Santo_Domingo')))->format('Y-m-d H:i:s'));

        $this->assertInstanceOf('MidasSoft\DominicanBankParser\Collections\DepositCollection', $parsedData);
        $this->assertInstanceOf('MidasSoft\DominicanBankParser\Collections\DepositCollection', $parsedFromCache);
        $this->assertCount(6, $parsedData);
        $this->assertCount(6, $parsedFromCache);
    }
}
