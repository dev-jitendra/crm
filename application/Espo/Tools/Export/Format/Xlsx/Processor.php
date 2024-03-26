<?php


namespace Espo\Tools\Export\Format\Xlsx;

use Espo\Core\Exceptions\Error;
use Espo\Tools\Export\Collection;
use Espo\Tools\Export\Processor as ProcessorInterface;
use Espo\Tools\Export\Processor\Params;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use Psr\Http\Message\StreamInterface;

class Processor implements ProcessorInterface
{
    private const PARAM_LITE = 'lite';

    public function __construct(
        private PhpSpreadsheetProcessor $phpSpreadsheetProcessor,
        private OpenSpoutProcessor $openSpoutProcessor,
    ) {}

    
    public function process(Params $params, Collection $collection): StreamInterface
    {
        return $params->getParam(self::PARAM_LITE) ?
            $this->processOpenSpout($params, $collection) :
            $this->processPhpSpreadsheet($params, $collection);
    }

    
    private function processPhpSpreadsheet(Params $params, Collection $collection): StreamInterface
    {
        try {
            return $this->phpSpreadsheetProcessor->process($params, $collection);
        }
        catch (SpreadsheetException|WriterException $e) {
            throw new Error($e->getMessage());
        }
    }

    
    private function processOpenSpout(Params $params, Collection $collection): StreamInterface
    {
        try {
            return $this->openSpoutProcessor->process($params, $collection);
        }
        catch (\Throwable $e) {
            throw new Error($e->getMessage());
        }
    }
}
