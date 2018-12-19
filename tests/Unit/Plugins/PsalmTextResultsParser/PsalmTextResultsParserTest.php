<?php

declare(strict_types=1);

namespace DaveLiddament\StaticAnalysisResultsBaseliner\Tests\Unit\Plugins\PsalmResultsParser;

use DaveLiddament\StaticAnalysisResultsBaseliner\Domain\Common\FileName;
use DaveLiddament\StaticAnalysisResultsBaseliner\Domain\Common\LineNumber;
use DaveLiddament\StaticAnalysisResultsBaseliner\Domain\Common\Location;
use DaveLiddament\StaticAnalysisResultsBaseliner\Domain\Common\ProjectRoot;
use DaveLiddament\StaticAnalysisResultsBaseliner\Domain\Common\Type;
use DaveLiddament\StaticAnalysisResultsBaseliner\Domain\Utils\FqcnRemover;
use DaveLiddament\StaticAnalysisResultsBaseliner\Plugins\PsalmTextResultsParser\PsalmTextResultsParser;
use DaveLiddament\StaticAnalysisResultsBaseliner\Tests\Helpers\ResourceLoaderTrait;
use PHPUnit\Framework\TestCase;

class PsalmTextResultsParserTest extends TestCase
{
    use ResourceLoaderTrait;

    /**
     * @var ProjectRoot
     */
    private $projectRoot;

    /**
     * @var PsalmTextResultsParser
     */
    private $psalmTextResultsParser;

    /**
     * @var string
     */
    private $fileContents;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->projectRoot = new ProjectRoot('/vagrant/static-analysis-baseliner', '/home');
        $this->psalmTextResultsParser = new PsalmTextResultsParser(new FqcnRemover());
        $this->fileContents = $this->getResource('psalm/psalm.txt');
    }

    public function testConversionFromString(): void
    {
        $analysisResults = $this->psalmTextResultsParser->convertFromString($this->fileContents, $this->projectRoot);

        $this->assertCount(3, $analysisResults->getAnalysisResults());

        $result1 = $analysisResults->getAnalysisResults()[0];
        $result2 = $analysisResults->getAnalysisResults()[1];
        $result3 = $analysisResults->getAnalysisResults()[2];

        $this->assertTrue($result1->isMatch(
            new Location(
                new FileName('src/Plugins/PsalmTextResultsParser/PsalmTextResultsParser.php'),
                new LineNumber(51)
            ),
            new Type('Cannot assign $analysisResult to a mixed type')
        ));

        $this->assertTrue($result2->isMatch(
            new Location(
                new FileName('src/Plugins/PsalmTextResultsParser/PsalmTextResultsParser.php'),
                new LineNumber(51)
            ),
            new Type('Method does not exist')
        ));

        $this->assertTrue($result3->isMatch(
            new Location(
                new FileName('src/Plugins/PsalmTextResultsParser/PsalmTextResultsParser.php'),
                new LineNumber(52)
            ),
            new Type('Argument 1 of cannot be mixed, expecting')
        ));
    }

    public function testConvertToString(): void
    {
        $analysisResults = $this->psalmTextResultsParser->convertFromString($this->fileContents, $this->projectRoot);
        $asString = $this->psalmTextResultsParser->convertToString($analysisResults);

        $trimmedExpected = trim($this->fileContents);
        $trimmedActual = trim($asString);

        $this->assertEquals($trimmedExpected, $trimmedActual);
    }
}