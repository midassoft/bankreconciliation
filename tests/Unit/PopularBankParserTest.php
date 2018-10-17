<?php

namespace Tests\Unit;

use MidasSoft\DominicanBankParser\Cache\ArrayCacheDriver;
use MidasSoft\DominicanBankParser\Files\CSV;
use MidasSoft\DominicanBankParser\Parsers\PopularBankParser;
use PHPUnit\Framework\TestCase;

class PopularBankParserTest extends TestCase
{
    private $parser;
    private $file;

    public function setUp()
    {
        $this->parser = new PopularBankParser();
        $this->parser->setCacheManager(new ArrayCacheDriver());
        $this->file = new CSV(file_get_contents(__DIR__ . '/../resources/popular_bank_file.csv'));
        parent::setUp();
    }

    /** @test */
    public function it_can_parse_popular_bank()
    {
        $parsedData = $this->parser->parse($this->file);

        $this->assertInstanceOf('Illuminate\Support\Collection', $parsedData);
        $this->assertCount(105, $parsedData);
    }

    /**
     * @test
     * @expectedException MidasSoft\DominicanBankParser\Exceptions\EmptyFileException
     * @expectedExceptionMessage You're trying to parse an empty file.
     */
    public function popular_bank_parser_throws_an_exception_when_you_try_to_parse_an_empty_file()
    {
        $parsedData = $this->parser->parse(new CSV(''));
    }

    /** @test */
    public function popular_bank_parser_can_parse_file_from_cache()
    {
        $parsedData = $this->parser->parse($this->file);
        $parsedFromCache = $this->parser->getCacheManager()->get(date('Ymd'));

        $this->assertInstanceOf('Illuminate\Support\Collection', $parsedData);
        $this->assertInstanceOf('Illuminate\Support\Collection', $parsedFromCache);
        $this->assertCount(105, $parsedData);
        $this->assertCount(105, $parsedFromCache);
    }
}
