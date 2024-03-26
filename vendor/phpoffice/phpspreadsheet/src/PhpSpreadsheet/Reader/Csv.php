<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Csv extends BaseReader
{
    const UTF8_BOM = "\xEF\xBB\xBF";
    const UTF8_BOM_LEN = 3;
    const UTF16BE_BOM = "\xfe\xff";
    const UTF16BE_BOM_LEN = 2;
    const UTF16BE_LF = "\x00\x0a";
    const UTF16LE_BOM = "\xff\xfe";
    const UTF16LE_BOM_LEN = 2;
    const UTF16LE_LF = "\x0a\x00";
    const UTF32BE_BOM = "\x00\x00\xfe\xff";
    const UTF32BE_BOM_LEN = 4;
    const UTF32BE_LF = "\x00\x00\x00\x0a";
    const UTF32LE_BOM = "\xff\xfe\x00\x00";
    const UTF32LE_BOM_LEN = 4;
    const UTF32LE_LF = "\x0a\x00\x00\x00";

    
    private $inputEncoding = 'UTF-8';

    
    private $delimiter;

    
    private $enclosure = '"';

    
    private $sheetIndex = 0;

    
    private $contiguous = false;

    
    private $escapeCharacter = '\\';

    
    public function __construct()
    {
        parent::__construct();
    }

    
    public function setInputEncoding($pValue)
    {
        $this->inputEncoding = $pValue;

        return $this;
    }

    
    public function getInputEncoding()
    {
        return $this->inputEncoding;
    }

    
    protected function skipBOM(): void
    {
        rewind($this->fileHandle);

        if (fgets($this->fileHandle, self::UTF8_BOM_LEN + 1) !== self::UTF8_BOM) {
            rewind($this->fileHandle);
        }
    }

    
    protected function checkSeparator(): void
    {
        $line = fgets($this->fileHandle);
        if ($line === false) {
            return;
        }

        if ((strlen(trim($line, "\r\n")) == 5) && (stripos($line, 'sep=') === 0)) {
            $this->delimiter = substr($line, 4, 1);

            return;
        }

        $this->skipBOM();
    }

    
    protected function inferSeparator(): void
    {
        if ($this->delimiter !== null) {
            return;
        }

        $potentialDelimiters = [',', ';', "\t", '|', ':', ' ', '~'];
        $counts = [];
        foreach ($potentialDelimiters as $delimiter) {
            $counts[$delimiter] = [];
        }

        
        $numberLines = 0;
        while (($line = $this->getNextLine()) !== false && (++$numberLines < 1000)) {
            $countLine = [];
            for ($i = strlen($line) - 1; $i >= 0; --$i) {
                $char = $line[$i];
                if (isset($counts[$char])) {
                    if (!isset($countLine[$char])) {
                        $countLine[$char] = 0;
                    }
                    ++$countLine[$char];
                }
            }
            foreach ($potentialDelimiters as $delimiter) {
                $counts[$delimiter][] = $countLine[$delimiter]
                    ?? 0;
            }
        }

        
        if ($numberLines === 0) {
            $this->delimiter = reset($potentialDelimiters);
            $this->skipBOM();

            return;
        }

        
        $meanSquareDeviations = [];
        $middleIdx = floor(($numberLines - 1) / 2);

        foreach ($potentialDelimiters as $delimiter) {
            $series = $counts[$delimiter];
            sort($series);

            $median = ($numberLines % 2)
                ? $series[$middleIdx]
                : ($series[$middleIdx] + $series[$middleIdx + 1]) / 2;

            if ($median === 0) {
                continue;
            }

            $meanSquareDeviations[$delimiter] = array_reduce(
                $series,
                function ($sum, $value) use ($median) {
                    return $sum + ($value - $median) ** 2;
                }
            ) / count($series);
        }

        
        $min = INF;
        foreach ($potentialDelimiters as $delimiter) {
            if (!isset($meanSquareDeviations[$delimiter])) {
                continue;
            }

            if ($meanSquareDeviations[$delimiter] < $min) {
                $min = $meanSquareDeviations[$delimiter];
                $this->delimiter = $delimiter;
            }
        }

        
        if ($this->delimiter === null) {
            $this->delimiter = reset($potentialDelimiters);
        }

        $this->skipBOM();
    }

    
    private function getNextLine()
    {
        $line = '';
        $enclosure = ($this->escapeCharacter === '' ? ''
            : ('(?<!' . preg_quote($this->escapeCharacter, '/') . ')'))
            . preg_quote($this->enclosure, '/');

        do {
            
            $newLine = fgets($this->fileHandle);

            
            if ($newLine === false) {
                return false;
            }

            
            $line = $line . $newLine;

            
            $line = preg_replace('/(' . $enclosure . '.*' . $enclosure . ')/Us', '', $line);

            
            
        } while (preg_match('/(' . $enclosure . ')/', $line) > 0);

        return $line;
    }

    
    public function listWorksheetInfo($pFilename)
    {
        
        $this->openFileOrMemory($pFilename);
        $fileHandle = $this->fileHandle;

        
        $this->skipBOM();
        $this->checkSeparator();
        $this->inferSeparator();

        $worksheetInfo = [];
        $worksheetInfo[0]['worksheetName'] = 'Worksheet';
        $worksheetInfo[0]['lastColumnLetter'] = 'A';
        $worksheetInfo[0]['lastColumnIndex'] = 0;
        $worksheetInfo[0]['totalRows'] = 0;
        $worksheetInfo[0]['totalColumns'] = 0;

        
        while (($rowData = fgetcsv($fileHandle, 0, $this->delimiter, $this->enclosure, $this->escapeCharacter)) !== false) {
            ++$worksheetInfo[0]['totalRows'];
            $worksheetInfo[0]['lastColumnIndex'] = max($worksheetInfo[0]['lastColumnIndex'], count($rowData) - 1);
        }

        $worksheetInfo[0]['lastColumnLetter'] = Coordinate::stringFromColumnIndex($worksheetInfo[0]['lastColumnIndex'] + 1);
        $worksheetInfo[0]['totalColumns'] = $worksheetInfo[0]['lastColumnIndex'] + 1;

        
        fclose($fileHandle);

        return $worksheetInfo;
    }

    
    public function load($pFilename)
    {
        
        $spreadsheet = new Spreadsheet();

        
        return $this->loadIntoExisting($pFilename, $spreadsheet);
    }

    private function openFileOrMemory($pFilename): void
    {
        
        $fhandle = $this->canRead($pFilename);
        if (!$fhandle) {
            throw new Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }
        $this->openFile($pFilename);
        if ($this->inputEncoding !== 'UTF-8') {
            fclose($this->fileHandle);
            $entireFile = file_get_contents($pFilename);
            $this->fileHandle = fopen('php:
            $data = StringHelper::convertEncoding($entireFile, 'UTF-8', $this->inputEncoding);
            fwrite($this->fileHandle, $data);
            $this->skipBOM();
        }
    }

    
    public function loadIntoExisting($pFilename, Spreadsheet $spreadsheet)
    {
        $lineEnding = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', true);

        
        $this->openFileOrMemory($pFilename);
        $fileHandle = $this->fileHandle;

        
        $this->skipBOM();
        $this->checkSeparator();
        $this->inferSeparator();

        
        while ($spreadsheet->getSheetCount() <= $this->sheetIndex) {
            $spreadsheet->createSheet();
        }
        $sheet = $spreadsheet->setActiveSheetIndex($this->sheetIndex);

        
        $currentRow = 1;
        $outRow = 0;

        
        while (($rowData = fgetcsv($fileHandle, 0, $this->delimiter, $this->enclosure, $this->escapeCharacter)) !== false) {
            $noOutputYet = true;
            $columnLetter = 'A';
            foreach ($rowData as $rowDatum) {
                if ($rowDatum != '' && $this->readFilter->readCell($columnLetter, $currentRow)) {
                    if ($this->contiguous) {
                        if ($noOutputYet) {
                            $noOutputYet = false;
                            ++$outRow;
                        }
                    } else {
                        $outRow = $currentRow;
                    }
                    
                    $sheet->getCell($columnLetter . $outRow)->setValue($rowDatum);
                }
                ++$columnLetter;
            }
            ++$currentRow;
        }

        
        fclose($fileHandle);

        ini_set('auto_detect_line_endings', $lineEnding);

        
        return $spreadsheet;
    }

    
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    
    public function setEnclosure($enclosure)
    {
        if ($enclosure == '') {
            $enclosure = '"';
        }
        $this->enclosure = $enclosure;

        return $this;
    }

    
    public function getSheetIndex()
    {
        return $this->sheetIndex;
    }

    
    public function setSheetIndex($pValue)
    {
        $this->sheetIndex = $pValue;

        return $this;
    }

    
    public function setContiguous($contiguous)
    {
        $this->contiguous = (bool) $contiguous;

        return $this;
    }

    
    public function getContiguous()
    {
        return $this->contiguous;
    }

    
    public function setEscapeCharacter($escapeCharacter)
    {
        $this->escapeCharacter = $escapeCharacter;

        return $this;
    }

    
    public function getEscapeCharacter()
    {
        return $this->escapeCharacter;
    }

    
    public function canRead($pFilename)
    {
        
        try {
            $this->openFile($pFilename);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        fclose($this->fileHandle);

        
        $extension = strtolower(pathinfo($pFilename, PATHINFO_EXTENSION));
        if (in_array($extension, ['csv', 'tsv'])) {
            return true;
        }

        
        $type = mime_content_type($pFilename);
        $supportedTypes = [
            'application/csv',
            'text/csv',
            'text/plain',
            'inode/x-empty',
        ];

        return in_array($type, $supportedTypes, true);
    }

    private static function guessEncodingTestNoBom(string &$encoding, string &$contents, string $compare, string $setEncoding): void
    {
        if ($encoding === '') {
            $pos = strpos($contents, $compare);
            if ($pos !== false && $pos % strlen($compare) === 0) {
                $encoding = $setEncoding;
            }
        }
    }

    private static function guessEncodingNoBom(string $filename): string
    {
        $encoding = '';
        $contents = file_get_contents($filename);
        self::guessEncodingTestNoBom($encoding, $contents, self::UTF32BE_LF, 'UTF-32BE');
        self::guessEncodingTestNoBom($encoding, $contents, self::UTF32LE_LF, 'UTF-32LE');
        self::guessEncodingTestNoBom($encoding, $contents, self::UTF16BE_LF, 'UTF-16BE');
        self::guessEncodingTestNoBom($encoding, $contents, self::UTF16LE_LF, 'UTF-16LE');
        if ($encoding === '' && preg_match('
            $encoding = 'UTF-8';
        }

        return $encoding;
    }

    private static function guessEncodingTestBom(string &$encoding, string $first4, string $compare, string $setEncoding): void
    {
        if ($encoding === '') {
            if ($compare === substr($first4, 0, strlen($compare))) {
                $encoding = $setEncoding;
            }
        }
    }

    private static function guessEncodingBom(string $filename): string
    {
        $encoding = '';
        $first4 = file_get_contents($filename, false, null, 0, 4);
        if ($first4 !== false) {
            self::guessEncodingTestBom($encoding, $first4, self::UTF8_BOM, 'UTF-8');
            self::guessEncodingTestBom($encoding, $first4, self::UTF16BE_BOM, 'UTF-16BE');
            self::guessEncodingTestBom($encoding, $first4, self::UTF32BE_BOM, 'UTF-32BE');
            self::guessEncodingTestBom($encoding, $first4, self::UTF32LE_BOM, 'UTF-32LE');
            self::guessEncodingTestBom($encoding, $first4, self::UTF16LE_BOM, 'UTF-16LE');
        }

        return $encoding;
    }

    public static function guessEncoding(string $filename, string $dflt = 'CP1252'): string
    {
        $encoding = self::guessEncodingBom($filename);
        if ($encoding === '') {
            $encoding = self::guessEncodingNoBom($filename);
        }

        return ($encoding === '') ? $dflt : $encoding;
    }
}
