<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Engine\CyclicReferenceStack;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\Logger;
use PhpOffice\PhpSpreadsheet\Calculation\Token\Stack;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\Shared;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use ReflectionMethod;

class Calculation
{
    
    
    
    const CALCULATION_REGEXP_NUMBER = '[-+]?\d*\.?\d+(e[-+]?\d+)?';
    
    const CALCULATION_REGEXP_STRING = '"(?:[^"]|"")*"';
    
    const CALCULATION_REGEXP_OPENBRACE = '\(';
    
    const CALCULATION_REGEXP_FUNCTION = '@?(?:_xlfn\.)?([\p{L}][\p{L}\p{N}\.]*)[\s]*\(';
    
    const CALCULATION_REGEXP_CELLREF = '((([^\s,!&%^\/\*\+<>=-]*)|(\'[^\']*\')|(\"[^\"]*\"))!)?\$?\b([a-z]{1,3})\$?(\d{1,7})(?![\w.])';
    
    const CALCULATION_REGEXP_CELLREF_RELATIVE = '((([^\s\(,!&%^\/\*\+<>=-]*)|(\'[^\']*\')|(\"[^\"]*\"))!)?(\$?\b[a-z]{1,3})(\$?\d{1,7})(?![\w.])';
    
    const CALCULATION_REGEXP_COLUMNRANGE_RELATIVE = '(\$?[a-z]{1,3}):(\$?[a-z]{1,3})';
    const CALCULATION_REGEXP_ROWRANGE_RELATIVE = '(\$?\d{1,7}):(\$?\d{1,7})';
    
    const CALCULATION_REGEXP_DEFINEDNAME = '((([^\s,!&%^\/\*\+<>=-]*)|(\'[^\']*\')|(\"[^\"]*\"))!)?([_\p{L}][_\p{L}\p{N}\.]*)';
    
    const CALCULATION_REGEXP_ERROR = '\#[A-Z][A-Z0_\/]*[!\?]?';

    
    const RETURN_ARRAY_AS_ERROR = 'error';
    const RETURN_ARRAY_AS_VALUE = 'value';
    const RETURN_ARRAY_AS_ARRAY = 'array';

    const FORMULA_OPEN_FUNCTION_BRACE = '{';
    const FORMULA_CLOSE_FUNCTION_BRACE = '}';
    const FORMULA_STRING_QUOTE = '"';

    private static $returnArrayAsType = self::RETURN_ARRAY_AS_VALUE;

    
    private static $instance;

    
    private $spreadsheet;

    
    private $calculationCache = [];

    
    private $calculationCacheEnabled = true;

    
    private $branchStoreKeyCounter = 0;

    private $branchPruningEnabled = true;

    
    private static $operators = [
        '+' => true, '-' => true, '*' => true, '/' => true,
        '^' => true, '&' => true, '%' => false, '~' => false,
        '>' => true, '<' => true, '=' => true, '>=' => true,
        '<=' => true, '<>' => true, '|' => true, ':' => true,
    ];

    
    private static $binaryOperators = [
        '+' => true, '-' => true, '*' => true, '/' => true,
        '^' => true, '&' => true, '>' => true, '<' => true,
        '=' => true, '>=' => true, '<=' => true, '<>' => true,
        '|' => true, ':' => true,
    ];

    
    private $debugLog;

    
    public $suppressFormulaErrors = false;

    
    public $formulaError;

    
    private static $referenceHelper;

    
    private $cyclicReferenceStack;

    private $cellStack = [];

    
    private $cyclicFormulaCounter = 1;

    private $cyclicFormulaCell = '';

    
    public $cyclicFormulaCount = 1;

    
    private $delta = 0.1e-12;

    
    private static $localeLanguage = 'en_us'; 

    
    private static $validLocaleLanguages = [
        'en', 
    ];

    
    private static $localeArgumentSeparator = ',';

    private static $localeFunctions = [];

    
    public static $localeBoolean = [
        'TRUE' => 'TRUE',
        'FALSE' => 'FALSE',
        'NULL' => 'NULL',
    ];

    
    private static $excelConstants = [
        'TRUE' => true,
        'FALSE' => false,
        'NULL' => null,
    ];

    
    private static $phpSpreadsheetFunctions = [
        'ABS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'abs',
            'argumentCount' => '1',
        ],
        'ACCRINT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'ACCRINT'],
            'argumentCount' => '4-7',
        ],
        'ACCRINTM' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'ACCRINTM'],
            'argumentCount' => '3-5',
        ],
        'ACOS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'acos',
            'argumentCount' => '1',
        ],
        'ACOSH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'acosh',
            'argumentCount' => '1',
        ],
        'ACOT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'ACOT'],
            'argumentCount' => '1',
        ],
        'ACOTH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'ACOTH'],
            'argumentCount' => '1',
        ],
        'ADDRESS' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'cellAddress'],
            'argumentCount' => '2-5',
        ],
        'AGGREGATE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3+',
        ],
        'AMORDEGRC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'AMORDEGRC'],
            'argumentCount' => '6,7',
        ],
        'AMORLINC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'AMORLINC'],
            'argumentCount' => '6,7',
        ],
        'AND' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical::class, 'logicalAnd'],
            'argumentCount' => '1+',
        ],
        'ARABIC' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'ARABIC'],
            'argumentCount' => '1',
        ],
        'AREAS' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'ASC' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'ASIN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'asin',
            'argumentCount' => '1',
        ],
        'ASINH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'asinh',
            'argumentCount' => '1',
        ],
        'ATAN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'atan',
            'argumentCount' => '1',
        ],
        'ATAN2' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'ATAN2'],
            'argumentCount' => '2',
        ],
        'ATANH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'atanh',
            'argumentCount' => '1',
        ],
        'AVEDEV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'AVEDEV'],
            'argumentCount' => '1+',
        ],
        'AVERAGE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'AVERAGE'],
            'argumentCount' => '1+',
        ],
        'AVERAGEA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'AVERAGEA'],
            'argumentCount' => '1+',
        ],
        'AVERAGEIF' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'AVERAGEIF'],
            'argumentCount' => '2,3',
        ],
        'AVERAGEIFS' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3+',
        ],
        'BAHTTEXT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'BASE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'BASE'],
            'argumentCount' => '2,3',
        ],
        'BESSELI' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'BESSELI'],
            'argumentCount' => '2',
        ],
        'BESSELJ' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'BESSELJ'],
            'argumentCount' => '2',
        ],
        'BESSELK' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'BESSELK'],
            'argumentCount' => '2',
        ],
        'BESSELY' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'BESSELY'],
            'argumentCount' => '2',
        ],
        'BETADIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'BETADIST'],
            'argumentCount' => '3-5',
        ],
        'BETA.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '4-6',
        ],
        'BETAINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'BETAINV'],
            'argumentCount' => '3-5',
        ],
        'BETA.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'BETAINV'],
            'argumentCount' => '3-5',
        ],
        'BIN2DEC' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'BINTODEC'],
            'argumentCount' => '1',
        ],
        'BIN2HEX' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'BINTOHEX'],
            'argumentCount' => '1,2',
        ],
        'BIN2OCT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'BINTOOCT'],
            'argumentCount' => '1,2',
        ],
        'BINOMDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'BINOMDIST'],
            'argumentCount' => '4',
        ],
        'BINOM.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'BINOMDIST'],
            'argumentCount' => '4',
        ],
        'BINOM.DIST.RANGE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3,4',
        ],
        'BINOM.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'BITAND' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'BITAND'],
            'argumentCount' => '2',
        ],
        'BITOR' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'BITOR'],
            'argumentCount' => '2',
        ],
        'BITXOR' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'BITOR'],
            'argumentCount' => '2',
        ],
        'BITLSHIFT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'BITLSHIFT'],
            'argumentCount' => '2',
        ],
        'BITRSHIFT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'BITRSHIFT'],
            'argumentCount' => '2',
        ],
        'CEILING' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'CEILING'],
            'argumentCount' => '2',
        ],
        'CEILING.MATH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'CEILING.PRECISE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'CELL' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1,2',
        ],
        'CHAR' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'CHARACTER'],
            'argumentCount' => '1',
        ],
        'CHIDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'CHIDIST'],
            'argumentCount' => '2',
        ],
        'CHISQ.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'CHISQ.DIST.RT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'CHIDIST'],
            'argumentCount' => '2',
        ],
        'CHIINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'CHIINV'],
            'argumentCount' => '2',
        ],
        'CHISQ.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'CHISQ.INV.RT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'CHIINV'],
            'argumentCount' => '2',
        ],
        'CHITEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'CHISQ.TEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'CHOOSE' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'CHOOSE'],
            'argumentCount' => '2+',
        ],
        'CLEAN' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'TRIMNONPRINTABLE'],
            'argumentCount' => '1',
        ],
        'CODE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'ASCIICODE'],
            'argumentCount' => '1',
        ],
        'COLUMN' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'COLUMN'],
            'argumentCount' => '-1',
            'passByReference' => [true],
        ],
        'COLUMNS' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'COLUMNS'],
            'argumentCount' => '1',
        ],
        'COMBIN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'COMBIN'],
            'argumentCount' => '2',
        ],
        'COMBINA' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'COMPLEX' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'COMPLEX'],
            'argumentCount' => '2,3',
        ],
        'CONCAT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'CONCATENATE'],
            'argumentCount' => '1+',
        ],
        'CONCATENATE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'CONCATENATE'],
            'argumentCount' => '1+',
        ],
        'CONFIDENCE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'CONFIDENCE'],
            'argumentCount' => '3',
        ],
        'CONFIDENCE.NORM' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'CONFIDENCE'],
            'argumentCount' => '3',
        ],
        'CONFIDENCE.T' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'CONVERT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'CONVERTUOM'],
            'argumentCount' => '3',
        ],
        'CORREL' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'CORREL'],
            'argumentCount' => '2',
        ],
        'COS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'cos',
            'argumentCount' => '1',
        ],
        'COSH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'cosh',
            'argumentCount' => '1',
        ],
        'COT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'COT'],
            'argumentCount' => '1',
        ],
        'COTH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'COTH'],
            'argumentCount' => '1',
        ],
        'COUNT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'COUNT'],
            'argumentCount' => '1+',
        ],
        'COUNTA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'COUNTA'],
            'argumentCount' => '1+',
        ],
        'COUNTBLANK' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'COUNTBLANK'],
            'argumentCount' => '1',
        ],
        'COUNTIF' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'COUNTIF'],
            'argumentCount' => '2',
        ],
        'COUNTIFS' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'COUNTIFS'],
            'argumentCount' => '2+',
        ],
        'COUPDAYBS' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'COUPDAYBS'],
            'argumentCount' => '3,4',
        ],
        'COUPDAYS' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'COUPDAYS'],
            'argumentCount' => '3,4',
        ],
        'COUPDAYSNC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'COUPDAYSNC'],
            'argumentCount' => '3,4',
        ],
        'COUPNCD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'COUPNCD'],
            'argumentCount' => '3,4',
        ],
        'COUPNUM' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'COUPNUM'],
            'argumentCount' => '3,4',
        ],
        'COUPPCD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'COUPPCD'],
            'argumentCount' => '3,4',
        ],
        'COVAR' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'COVAR'],
            'argumentCount' => '2',
        ],
        'COVARIANCE.P' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'COVAR'],
            'argumentCount' => '2',
        ],
        'COVARIANCE.S' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'CRITBINOM' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'CRITBINOM'],
            'argumentCount' => '3',
        ],
        'CSC' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'CSC'],
            'argumentCount' => '1',
        ],
        'CSCH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'CSCH'],
            'argumentCount' => '1',
        ],
        'CUBEKPIMEMBER' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUBEMEMBER' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUBEMEMBERPROPERTY' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUBERANKEDMEMBER' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUBESET' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUBESETCOUNT' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUBEVALUE' => [
            'category' => Category::CATEGORY_CUBE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '?',
        ],
        'CUMIPMT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'CUMIPMT'],
            'argumentCount' => '6',
        ],
        'CUMPRINC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'CUMPRINC'],
            'argumentCount' => '6',
        ],
        'DATE' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'DATE'],
            'argumentCount' => '3',
        ],
        'DATEDIF' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'DATEDIF'],
            'argumentCount' => '2,3',
        ],
        'DATEVALUE' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'DATEVALUE'],
            'argumentCount' => '1',
        ],
        'DAVERAGE' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database::class, 'DAVERAGE'],
            'argumentCount' => '3',
        ],
        'DAY' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'DAYOFMONTH'],
            'argumentCount' => '1',
        ],
        'DAYS' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'DAYS'],
            'argumentCount' => '2',
        ],
        'DAYS360' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'DAYS360'],
            'argumentCount' => '2,3',
        ],
        'DB' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'DB'],
            'argumentCount' => '4,5',
        ],
        'DBCS' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'DCOUNT' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database::class, 'DCOUNT'],
            'argumentCount' => '3',
        ],
        'DCOUNTA' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database::class, 'DCOUNTA'],
            'argumentCount' => '3',
        ],
        'DDB' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'DDB'],
            'argumentCount' => '4,5',
        ],
        'DEC2BIN' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'DECTOBIN'],
            'argumentCount' => '1,2',
        ],
        'DEC2HEX' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'DECTOHEX'],
            'argumentCount' => '1,2',
        ],
        'DEC2OCT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'DECTOOCT'],
            'argumentCount' => '1,2',
        ],
        'DECIMAL' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'DEGREES' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'rad2deg',
            'argumentCount' => '1',
        ],
        'DELTA' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'DELTA'],
            'argumentCount' => '1,2',
        ],
        'DEVSQ' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'DEVSQ'],
            'argumentCount' => '1+',
        ],
        'DGET' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database::class, 'DGET'],
            'argumentCount' => '3',
        ],
        'DISC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'DISC'],
            'argumentCount' => '4,5',
        ],
        'DMAX' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database::class, 'DMAX'],
            'argumentCount' => '3',
        ],
        'DMIN' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database::class, 'DMIN'],
            'argumentCount' => '3',
        ],
        'DOLLAR' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'DOLLAR'],
            'argumentCount' => '1,2',
        ],
        'DOLLARDE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'DOLLARDE'],
            'argumentCount' => '2',
        ],
        'DOLLARFR' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'DOLLARFR'],
            'argumentCount' => '2',
        ],
        'DPRODUCT' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database::class, 'DPRODUCT'],
            'argumentCount' => '3',
        ],
        'DSTDEV' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database::class, 'DSTDEV'],
            'argumentCount' => '3',
        ],
        'DSTDEVP' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database::class, 'DSTDEVP'],
            'argumentCount' => '3',
        ],
        'DSUM' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database::class, 'DSUM'],
            'argumentCount' => '3',
        ],
        'DURATION' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '5,6',
        ],
        'DVAR' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database::class, 'DVAR'],
            'argumentCount' => '3',
        ],
        'DVARP' => [
            'category' => Category::CATEGORY_DATABASE,
            'functionCall' => [Database::class, 'DVARP'],
            'argumentCount' => '3',
        ],
        'EDATE' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'EDATE'],
            'argumentCount' => '2',
        ],
        'EFFECT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'EFFECT'],
            'argumentCount' => '2',
        ],
        'ENCODEURL' => [
            'category' => Category::CATEGORY_WEB,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'EOMONTH' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'EOMONTH'],
            'argumentCount' => '2',
        ],
        'ERF' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'ERF'],
            'argumentCount' => '1,2',
        ],
        'ERF.PRECISE' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'ERFPRECISE'],
            'argumentCount' => '1',
        ],
        'ERFC' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'ERFC'],
            'argumentCount' => '1',
        ],
        'ERFC.PRECISE' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'ERFC'],
            'argumentCount' => '1',
        ],
        'ERROR.TYPE' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'errorType'],
            'argumentCount' => '1',
        ],
        'EVEN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'EVEN'],
            'argumentCount' => '1',
        ],
        'EXACT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'EXACT'],
            'argumentCount' => '2',
        ],
        'EXP' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'exp',
            'argumentCount' => '1',
        ],
        'EXPONDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'EXPONDIST'],
            'argumentCount' => '3',
        ],
        'EXPON.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'EXPONDIST'],
            'argumentCount' => '3',
        ],
        'FACT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'FACT'],
            'argumentCount' => '1',
        ],
        'FACTDOUBLE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'FACTDOUBLE'],
            'argumentCount' => '1',
        ],
        'FALSE' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical::class, 'FALSE'],
            'argumentCount' => '0',
        ],
        'FDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'F.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'FDIST2'],
            'argumentCount' => '4',
        ],
        'F.DIST.RT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'FILTER' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3+',
        ],
        'FILTERXML' => [
            'category' => Category::CATEGORY_WEB,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'FIND' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'SEARCHSENSITIVE'],
            'argumentCount' => '2,3',
        ],
        'FINDB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'SEARCHSENSITIVE'],
            'argumentCount' => '2,3',
        ],
        'FINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'F.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'F.INV.RT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'FISHER' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'FISHER'],
            'argumentCount' => '1',
        ],
        'FISHERINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'FISHERINV'],
            'argumentCount' => '1',
        ],
        'FIXED' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'FIXEDFORMAT'],
            'argumentCount' => '1-3',
        ],
        'FLOOR' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'FLOOR'],
            'argumentCount' => '2',
        ],
        'FLOOR.MATH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'FLOORMATH'],
            'argumentCount' => '3',
        ],
        'FLOOR.PRECISE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'FLOORPRECISE'],
            'argumentCount' => '2',
        ],
        'FORECAST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'FORECAST'],
            'argumentCount' => '3',
        ],
        'FORECAST.ETS' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3-6',
        ],
        'FORECAST.ETS.CONFINT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3-6',
        ],
        'FORECAST.ETS.SEASONALITY' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2-4',
        ],
        'FORECAST.ETS.STAT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3-6',
        ],
        'FORECAST.LINEAR' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'FORECAST'],
            'argumentCount' => '3',
        ],
        'FORMULATEXT' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'FORMULATEXT'],
            'argumentCount' => '1',
            'passCellReference' => true,
            'passByReference' => [true],
        ],
        'FREQUENCY' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'FTEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'F.TEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'FV' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'FV'],
            'argumentCount' => '3-5',
        ],
        'FVSCHEDULE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'FVSCHEDULE'],
            'argumentCount' => '2',
        ],
        'GAMMA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'GAMMAFunction'],
            'argumentCount' => '1',
        ],
        'GAMMADIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'GAMMADIST'],
            'argumentCount' => '4',
        ],
        'GAMMA.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'GAMMADIST'],
            'argumentCount' => '4',
        ],
        'GAMMAINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'GAMMAINV'],
            'argumentCount' => '3',
        ],
        'GAMMA.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'GAMMAINV'],
            'argumentCount' => '3',
        ],
        'GAMMALN' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'GAMMALN'],
            'argumentCount' => '1',
        ],
        'GAMMALN.PRECISE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'GAMMALN'],
            'argumentCount' => '1',
        ],
        'GAUSS' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'GAUSS'],
            'argumentCount' => '1',
        ],
        'GCD' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'GCD'],
            'argumentCount' => '1+',
        ],
        'GEOMEAN' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'GEOMEAN'],
            'argumentCount' => '1+',
        ],
        'GESTEP' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'GESTEP'],
            'argumentCount' => '1,2',
        ],
        'GETPIVOTDATA' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2+',
        ],
        'GROWTH' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'GROWTH'],
            'argumentCount' => '1-4',
        ],
        'HARMEAN' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'HARMEAN'],
            'argumentCount' => '1+',
        ],
        'HEX2BIN' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'HEXTOBIN'],
            'argumentCount' => '1,2',
        ],
        'HEX2DEC' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'HEXTODEC'],
            'argumentCount' => '1',
        ],
        'HEX2OCT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'HEXTOOCT'],
            'argumentCount' => '1,2',
        ],
        'HLOOKUP' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'HLOOKUP'],
            'argumentCount' => '3,4',
        ],
        'HOUR' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'HOUROFDAY'],
            'argumentCount' => '1',
        ],
        'HYPERLINK' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'HYPERLINK'],
            'argumentCount' => '1,2',
            'passCellReference' => true,
        ],
        'HYPGEOMDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'HYPGEOMDIST'],
            'argumentCount' => '4',
        ],
        'HYPGEOM.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '5',
        ],
        'IF' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical::class, 'statementIf'],
            'argumentCount' => '1-3',
        ],
        'IFERROR' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical::class, 'IFERROR'],
            'argumentCount' => '2',
        ],
        'IFNA' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical::class, 'IFNA'],
            'argumentCount' => '2',
        ],
        'IFS' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical::class, 'IFS'],
            'argumentCount' => '2+',
        ],
        'IMABS' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMABS'],
            'argumentCount' => '1',
        ],
        'IMAGINARY' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMAGINARY'],
            'argumentCount' => '1',
        ],
        'IMARGUMENT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMARGUMENT'],
            'argumentCount' => '1',
        ],
        'IMCONJUGATE' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMCONJUGATE'],
            'argumentCount' => '1',
        ],
        'IMCOS' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMCOS'],
            'argumentCount' => '1',
        ],
        'IMCOSH' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMCOSH'],
            'argumentCount' => '1',
        ],
        'IMCOT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMCOT'],
            'argumentCount' => '1',
        ],
        'IMCSC' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMCSC'],
            'argumentCount' => '1',
        ],
        'IMCSCH' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMCSCH'],
            'argumentCount' => '1',
        ],
        'IMDIV' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMDIV'],
            'argumentCount' => '2',
        ],
        'IMEXP' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMEXP'],
            'argumentCount' => '1',
        ],
        'IMLN' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMLN'],
            'argumentCount' => '1',
        ],
        'IMLOG10' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMLOG10'],
            'argumentCount' => '1',
        ],
        'IMLOG2' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMLOG2'],
            'argumentCount' => '1',
        ],
        'IMPOWER' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMPOWER'],
            'argumentCount' => '2',
        ],
        'IMPRODUCT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMPRODUCT'],
            'argumentCount' => '1+',
        ],
        'IMREAL' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMREAL'],
            'argumentCount' => '1',
        ],
        'IMSEC' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMSEC'],
            'argumentCount' => '1',
        ],
        'IMSECH' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMSECH'],
            'argumentCount' => '1',
        ],
        'IMSIN' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMSIN'],
            'argumentCount' => '1',
        ],
        'IMSINH' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMSINH'],
            'argumentCount' => '1',
        ],
        'IMSQRT' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMSQRT'],
            'argumentCount' => '1',
        ],
        'IMSUB' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMSUB'],
            'argumentCount' => '2',
        ],
        'IMSUM' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMSUM'],
            'argumentCount' => '1+',
        ],
        'IMTAN' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'IMTAN'],
            'argumentCount' => '1',
        ],
        'INDEX' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'INDEX'],
            'argumentCount' => '1-4',
        ],
        'INDIRECT' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'INDIRECT'],
            'argumentCount' => '1,2',
            'passCellReference' => true,
        ],
        'INFO' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'INT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'INT'],
            'argumentCount' => '1',
        ],
        'INTERCEPT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'INTERCEPT'],
            'argumentCount' => '2',
        ],
        'INTRATE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'INTRATE'],
            'argumentCount' => '4,5',
        ],
        'IPMT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'IPMT'],
            'argumentCount' => '4-6',
        ],
        'IRR' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'IRR'],
            'argumentCount' => '1,2',
        ],
        'ISBLANK' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'isBlank'],
            'argumentCount' => '1',
        ],
        'ISERR' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'isErr'],
            'argumentCount' => '1',
        ],
        'ISERROR' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'isError'],
            'argumentCount' => '1',
        ],
        'ISEVEN' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'isEven'],
            'argumentCount' => '1',
        ],
        'ISFORMULA' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'isFormula'],
            'argumentCount' => '1',
            'passCellReference' => true,
            'passByReference' => [true],
        ],
        'ISLOGICAL' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'isLogical'],
            'argumentCount' => '1',
        ],
        'ISNA' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'isNa'],
            'argumentCount' => '1',
        ],
        'ISNONTEXT' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'isNonText'],
            'argumentCount' => '1',
        ],
        'ISNUMBER' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'isNumber'],
            'argumentCount' => '1',
        ],
        'ISO.CEILING' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1,2',
        ],
        'ISODD' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'isOdd'],
            'argumentCount' => '1',
        ],
        'ISOWEEKNUM' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'ISOWEEKNUM'],
            'argumentCount' => '1',
        ],
        'ISPMT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'ISPMT'],
            'argumentCount' => '4',
        ],
        'ISREF' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'ISTEXT' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'isText'],
            'argumentCount' => '1',
        ],
        'JIS' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'KURT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'KURT'],
            'argumentCount' => '1+',
        ],
        'LARGE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'LARGE'],
            'argumentCount' => '2',
        ],
        'LCM' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'LCM'],
            'argumentCount' => '1+',
        ],
        'LEFT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'LEFT'],
            'argumentCount' => '1,2',
        ],
        'LEFTB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'LEFT'],
            'argumentCount' => '1,2',
        ],
        'LEN' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'STRINGLENGTH'],
            'argumentCount' => '1',
        ],
        'LENB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'STRINGLENGTH'],
            'argumentCount' => '1',
        ],
        'LINEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'LINEST'],
            'argumentCount' => '1-4',
        ],
        'LN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'log',
            'argumentCount' => '1',
        ],
        'LOG' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'logBase'],
            'argumentCount' => '1,2',
        ],
        'LOG10' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'log10',
            'argumentCount' => '1',
        ],
        'LOGEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'LOGEST'],
            'argumentCount' => '1-4',
        ],
        'LOGINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'LOGINV'],
            'argumentCount' => '3',
        ],
        'LOGNORMDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'LOGNORMDIST'],
            'argumentCount' => '3',
        ],
        'LOGNORM.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'LOGNORMDIST2'],
            'argumentCount' => '4',
        ],
        'LOGNORM.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'LOGINV'],
            'argumentCount' => '3',
        ],
        'LOOKUP' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'LOOKUP'],
            'argumentCount' => '2,3',
        ],
        'LOWER' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'LOWERCASE'],
            'argumentCount' => '1',
        ],
        'MATCH' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'MATCH'],
            'argumentCount' => '2,3',
        ],
        'MAX' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'MAX'],
            'argumentCount' => '1+',
        ],
        'MAXA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'MAXA'],
            'argumentCount' => '1+',
        ],
        'MAXIFS' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'MAXIFS'],
            'argumentCount' => '3+',
        ],
        'MDETERM' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'MDETERM'],
            'argumentCount' => '1',
        ],
        'MDURATION' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '5,6',
        ],
        'MEDIAN' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'MEDIAN'],
            'argumentCount' => '1+',
        ],
        'MEDIANIF' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2+',
        ],
        'MID' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'MID'],
            'argumentCount' => '3',
        ],
        'MIDB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'MID'],
            'argumentCount' => '3',
        ],
        'MIN' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'MIN'],
            'argumentCount' => '1+',
        ],
        'MINA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'MINA'],
            'argumentCount' => '1+',
        ],
        'MINIFS' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'MINIFS'],
            'argumentCount' => '3+',
        ],
        'MINUTE' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'MINUTE'],
            'argumentCount' => '1',
        ],
        'MINVERSE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'MINVERSE'],
            'argumentCount' => '1',
        ],
        'MIRR' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'MIRR'],
            'argumentCount' => '3',
        ],
        'MMULT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'MMULT'],
            'argumentCount' => '2',
        ],
        'MOD' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'MOD'],
            'argumentCount' => '2',
        ],
        'MODE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'MODE'],
            'argumentCount' => '1+',
        ],
        'MODE.MULT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1+',
        ],
        'MODE.SNGL' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'MODE'],
            'argumentCount' => '1+',
        ],
        'MONTH' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'MONTHOFYEAR'],
            'argumentCount' => '1',
        ],
        'MROUND' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'MROUND'],
            'argumentCount' => '2',
        ],
        'MULTINOMIAL' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'MULTINOMIAL'],
            'argumentCount' => '1+',
        ],
        'MUNIT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'N' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'n'],
            'argumentCount' => '1',
        ],
        'NA' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'NA'],
            'argumentCount' => '0',
        ],
        'NEGBINOMDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'NEGBINOMDIST'],
            'argumentCount' => '3',
        ],
        'NEGBINOM.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '4',
        ],
        'NETWORKDAYS' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'NETWORKDAYS'],
            'argumentCount' => '2-3',
        ],
        'NETWORKDAYS.INTL' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2-4',
        ],
        'NOMINAL' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'NOMINAL'],
            'argumentCount' => '2',
        ],
        'NORMDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'NORMDIST'],
            'argumentCount' => '4',
        ],
        'NORM.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'NORMDIST'],
            'argumentCount' => '4',
        ],
        'NORMINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'NORMINV'],
            'argumentCount' => '3',
        ],
        'NORM.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'NORMINV'],
            'argumentCount' => '3',
        ],
        'NORMSDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'NORMSDIST'],
            'argumentCount' => '1',
        ],
        'NORM.S.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'NORMSDIST2'],
            'argumentCount' => '1,2',
        ],
        'NORMSINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'NORMSINV'],
            'argumentCount' => '1',
        ],
        'NORM.S.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'NORMSINV'],
            'argumentCount' => '1',
        ],
        'NOT' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical::class, 'NOT'],
            'argumentCount' => '1',
        ],
        'NOW' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'DATETIMENOW'],
            'argumentCount' => '0',
        ],
        'NPER' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'NPER'],
            'argumentCount' => '3-5',
        ],
        'NPV' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'NPV'],
            'argumentCount' => '2+',
        ],
        'NUMBERVALUE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'NUMBERVALUE'],
            'argumentCount' => '1+',
        ],
        'OCT2BIN' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'OCTTOBIN'],
            'argumentCount' => '1,2',
        ],
        'OCT2DEC' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'OCTTODEC'],
            'argumentCount' => '1',
        ],
        'OCT2HEX' => [
            'category' => Category::CATEGORY_ENGINEERING,
            'functionCall' => [Engineering::class, 'OCTTOHEX'],
            'argumentCount' => '1,2',
        ],
        'ODD' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'ODD'],
            'argumentCount' => '1',
        ],
        'ODDFPRICE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '8,9',
        ],
        'ODDFYIELD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '8,9',
        ],
        'ODDLPRICE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '7,8',
        ],
        'ODDLYIELD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '7,8',
        ],
        'OFFSET' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'OFFSET'],
            'argumentCount' => '3-5',
            'passCellReference' => true,
            'passByReference' => [true],
        ],
        'OR' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical::class, 'logicalOr'],
            'argumentCount' => '1+',
        ],
        'PDURATION' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'PDURATION'],
            'argumentCount' => '3',
        ],
        'PEARSON' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'CORREL'],
            'argumentCount' => '2',
        ],
        'PERCENTILE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'PERCENTILE'],
            'argumentCount' => '2',
        ],
        'PERCENTILE.EXC' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'PERCENTILE.INC' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'PERCENTILE'],
            'argumentCount' => '2',
        ],
        'PERCENTRANK' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'PERCENTRANK'],
            'argumentCount' => '2,3',
        ],
        'PERCENTRANK.EXC' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2,3',
        ],
        'PERCENTRANK.INC' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'PERCENTRANK'],
            'argumentCount' => '2,3',
        ],
        'PERMUT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'PERMUT'],
            'argumentCount' => '2',
        ],
        'PERMUTATIONA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'PHONETIC' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'PHI' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1',
        ],
        'PI' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'pi',
            'argumentCount' => '0',
        ],
        'PMT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'PMT'],
            'argumentCount' => '3-5',
        ],
        'POISSON' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'POISSON'],
            'argumentCount' => '3',
        ],
        'POISSON.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'POISSON'],
            'argumentCount' => '3',
        ],
        'POWER' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'POWER'],
            'argumentCount' => '2',
        ],
        'PPMT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'PPMT'],
            'argumentCount' => '4-6',
        ],
        'PRICE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'PRICE'],
            'argumentCount' => '6,7',
        ],
        'PRICEDISC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'PRICEDISC'],
            'argumentCount' => '4,5',
        ],
        'PRICEMAT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'PRICEMAT'],
            'argumentCount' => '5,6',
        ],
        'PROB' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3,4',
        ],
        'PRODUCT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'PRODUCT'],
            'argumentCount' => '1+',
        ],
        'PROPER' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'PROPERCASE'],
            'argumentCount' => '1',
        ],
        'PV' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'PV'],
            'argumentCount' => '3-5',
        ],
        'QUARTILE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'QUARTILE'],
            'argumentCount' => '2',
        ],
        'QUARTILE.EXC' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'QUARTILE.INC' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'QUARTILE'],
            'argumentCount' => '2',
        ],
        'QUOTIENT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'QUOTIENT'],
            'argumentCount' => '2',
        ],
        'RADIANS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'deg2rad',
            'argumentCount' => '1',
        ],
        'RAND' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'RAND'],
            'argumentCount' => '0',
        ],
        'RANDARRAY' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '0-5',
        ],
        'RANDBETWEEN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'RAND'],
            'argumentCount' => '2',
        ],
        'RANK' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'RANK'],
            'argumentCount' => '2,3',
        ],
        'RANK.AVG' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2,3',
        ],
        'RANK.EQ' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'RANK'],
            'argumentCount' => '2,3',
        ],
        'RATE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'RATE'],
            'argumentCount' => '3-6',
        ],
        'RECEIVED' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'RECEIVED'],
            'argumentCount' => '4-5',
        ],
        'REPLACE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'REPLACE'],
            'argumentCount' => '4',
        ],
        'REPLACEB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'REPLACE'],
            'argumentCount' => '4',
        ],
        'REPT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => 'str_repeat',
            'argumentCount' => '2',
        ],
        'RIGHT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'RIGHT'],
            'argumentCount' => '1,2',
        ],
        'RIGHTB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'RIGHT'],
            'argumentCount' => '1,2',
        ],
        'ROMAN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'ROMAN'],
            'argumentCount' => '1,2',
        ],
        'ROUND' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'round',
            'argumentCount' => '2',
        ],
        'ROUNDDOWN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'ROUNDDOWN'],
            'argumentCount' => '2',
        ],
        'ROUNDUP' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'ROUNDUP'],
            'argumentCount' => '2',
        ],
        'ROW' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'ROW'],
            'argumentCount' => '-1',
            'passByReference' => [true],
        ],
        'ROWS' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'ROWS'],
            'argumentCount' => '1',
        ],
        'RRI' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'RRI'],
            'argumentCount' => '3',
        ],
        'RSQ' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'RSQ'],
            'argumentCount' => '2',
        ],
        'RTD' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1+',
        ],
        'SEARCH' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'SEARCHINSENSITIVE'],
            'argumentCount' => '2,3',
        ],
        'SEARCHB' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'SEARCHINSENSITIVE'],
            'argumentCount' => '2,3',
        ],
        'SEC' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SEC'],
            'argumentCount' => '1',
        ],
        'SECH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SECH'],
            'argumentCount' => '1',
        ],
        'SECOND' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'SECOND'],
            'argumentCount' => '1',
        ],
        'SEQUENCE' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'SERIESSUM' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SERIESSUM'],
            'argumentCount' => '4',
        ],
        'SHEET' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '0,1',
        ],
        'SHEETS' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '0,1',
        ],
        'SIGN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SIGN'],
            'argumentCount' => '1',
        ],
        'SIN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'sin',
            'argumentCount' => '1',
        ],
        'SINH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'sinh',
            'argumentCount' => '1',
        ],
        'SKEW' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'SKEW'],
            'argumentCount' => '1+',
        ],
        'SKEW.P' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1+',
        ],
        'SLN' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'SLN'],
            'argumentCount' => '3',
        ],
        'SLOPE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'SLOPE'],
            'argumentCount' => '2',
        ],
        'SMALL' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'SMALL'],
            'argumentCount' => '2',
        ],
        'SORT' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1+',
        ],
        'SORTBY' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2+',
        ],
        'SQRT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'sqrt',
            'argumentCount' => '1',
        ],
        'SQRTPI' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SQRTPI'],
            'argumentCount' => '1',
        ],
        'STANDARDIZE' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'STANDARDIZE'],
            'argumentCount' => '3',
        ],
        'STDEV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'STDEV'],
            'argumentCount' => '1+',
        ],
        'STDEV.S' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'STDEV'],
            'argumentCount' => '1+',
        ],
        'STDEV.P' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'STDEVP'],
            'argumentCount' => '1+',
        ],
        'STDEVA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'STDEVA'],
            'argumentCount' => '1+',
        ],
        'STDEVP' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'STDEVP'],
            'argumentCount' => '1+',
        ],
        'STDEVPA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'STDEVPA'],
            'argumentCount' => '1+',
        ],
        'STEYX' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'STEYX'],
            'argumentCount' => '2',
        ],
        'SUBSTITUTE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'SUBSTITUTE'],
            'argumentCount' => '3,4',
        ],
        'SUBTOTAL' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SUBTOTAL'],
            'argumentCount' => '2+',
            'passCellReference' => true,
        ],
        'SUM' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SUM'],
            'argumentCount' => '1+',
        ],
        'SUMIF' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SUMIF'],
            'argumentCount' => '2,3',
        ],
        'SUMIFS' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SUMIFS'],
            'argumentCount' => '3+',
        ],
        'SUMPRODUCT' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SUMPRODUCT'],
            'argumentCount' => '1+',
        ],
        'SUMSQ' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SUMSQ'],
            'argumentCount' => '1+',
        ],
        'SUMX2MY2' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SUMX2MY2'],
            'argumentCount' => '2',
        ],
        'SUMX2PY2' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SUMX2PY2'],
            'argumentCount' => '2',
        ],
        'SUMXMY2' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'SUMXMY2'],
            'argumentCount' => '2',
        ],
        'SWITCH' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical::class, 'statementSwitch'],
            'argumentCount' => '3+',
        ],
        'SYD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'SYD'],
            'argumentCount' => '4',
        ],
        'T' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'RETURNSTRING'],
            'argumentCount' => '1',
        ],
        'TAN' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'tan',
            'argumentCount' => '1',
        ],
        'TANH' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => 'tanh',
            'argumentCount' => '1',
        ],
        'TBILLEQ' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'TBILLEQ'],
            'argumentCount' => '3',
        ],
        'TBILLPRICE' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'TBILLPRICE'],
            'argumentCount' => '3',
        ],
        'TBILLYIELD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'TBILLYIELD'],
            'argumentCount' => '3',
        ],
        'TDIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'TDIST'],
            'argumentCount' => '3',
        ],
        'T.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3',
        ],
        'T.DIST.2T' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'T.DIST.RT' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'TEXT' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'TEXTFORMAT'],
            'argumentCount' => '2',
        ],
        'TEXTJOIN' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'TEXTJOIN'],
            'argumentCount' => '3+',
        ],
        'TIME' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'TIME'],
            'argumentCount' => '3',
        ],
        'TIMEVALUE' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'TIMEVALUE'],
            'argumentCount' => '1',
        ],
        'TINV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'TINV'],
            'argumentCount' => '2',
        ],
        'T.INV' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'TINV'],
            'argumentCount' => '2',
        ],
        'T.INV.2T' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'TODAY' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'DATENOW'],
            'argumentCount' => '0',
        ],
        'TRANSPOSE' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'TRANSPOSE'],
            'argumentCount' => '1',
        ],
        'TREND' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'TREND'],
            'argumentCount' => '1-4',
        ],
        'TRIM' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'TRIMSPACES'],
            'argumentCount' => '1',
        ],
        'TRIMMEAN' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'TRIMMEAN'],
            'argumentCount' => '2',
        ],
        'TRUE' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical::class, 'TRUE'],
            'argumentCount' => '0',
        ],
        'TRUNC' => [
            'category' => Category::CATEGORY_MATH_AND_TRIG,
            'functionCall' => [MathTrig::class, 'TRUNC'],
            'argumentCount' => '1,2',
        ],
        'TTEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '4',
        ],
        'T.TEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '4',
        ],
        'TYPE' => [
            'category' => Category::CATEGORY_INFORMATION,
            'functionCall' => [Functions::class, 'TYPE'],
            'argumentCount' => '1',
        ],
        'UNICHAR' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'CHARACTER'],
            'argumentCount' => '1',
        ],
        'UNICODE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'ASCIICODE'],
            'argumentCount' => '1',
        ],
        'UNIQUE' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '1+',
        ],
        'UPPER' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'UPPERCASE'],
            'argumentCount' => '1',
        ],
        'USDOLLAR' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2',
        ],
        'VALUE' => [
            'category' => Category::CATEGORY_TEXT_AND_DATA,
            'functionCall' => [TextData::class, 'VALUE'],
            'argumentCount' => '1',
        ],
        'VAR' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'VARFunc'],
            'argumentCount' => '1+',
        ],
        'VAR.P' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'VARP'],
            'argumentCount' => '1+',
        ],
        'VAR.S' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'VARFunc'],
            'argumentCount' => '1+',
        ],
        'VARA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'VARA'],
            'argumentCount' => '1+',
        ],
        'VARP' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'VARP'],
            'argumentCount' => '1+',
        ],
        'VARPA' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'VARPA'],
            'argumentCount' => '1+',
        ],
        'VDB' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '5-7',
        ],
        'VLOOKUP' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [LookupRef::class, 'VLOOKUP'],
            'argumentCount' => '3,4',
        ],
        'WEBSERVICE' => [
            'category' => Category::CATEGORY_WEB,
            'functionCall' => [Web::class, 'WEBSERVICE'],
            'argumentCount' => '1',
        ],
        'WEEKDAY' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'WEEKDAY'],
            'argumentCount' => '1,2',
        ],
        'WEEKNUM' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'WEEKNUM'],
            'argumentCount' => '1,2',
        ],
        'WEIBULL' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'WEIBULL'],
            'argumentCount' => '4',
        ],
        'WEIBULL.DIST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'WEIBULL'],
            'argumentCount' => '4',
        ],
        'WORKDAY' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'WORKDAY'],
            'argumentCount' => '2-3',
        ],
        'WORKDAY.INTL' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2-4',
        ],
        'XIRR' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'XIRR'],
            'argumentCount' => '2,3',
        ],
        'XLOOKUP' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '3-6',
        ],
        'XNPV' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'XNPV'],
            'argumentCount' => '3',
        ],
        'XMATCH' => [
            'category' => Category::CATEGORY_LOOKUP_AND_REFERENCE,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '2,3',
        ],
        'XOR' => [
            'category' => Category::CATEGORY_LOGICAL,
            'functionCall' => [Logical::class, 'logicalXor'],
            'argumentCount' => '1+',
        ],
        'YEAR' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'YEAR'],
            'argumentCount' => '1',
        ],
        'YEARFRAC' => [
            'category' => Category::CATEGORY_DATE_AND_TIME,
            'functionCall' => [DateTime::class, 'YEARFRAC'],
            'argumentCount' => '2,3',
        ],
        'YIELD' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Functions::class, 'DUMMY'],
            'argumentCount' => '6,7',
        ],
        'YIELDDISC' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'YIELDDISC'],
            'argumentCount' => '4,5',
        ],
        'YIELDMAT' => [
            'category' => Category::CATEGORY_FINANCIAL,
            'functionCall' => [Financial::class, 'YIELDMAT'],
            'argumentCount' => '5,6',
        ],
        'ZTEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'ZTEST'],
            'argumentCount' => '2-3',
        ],
        'Z.TEST' => [
            'category' => Category::CATEGORY_STATISTICAL,
            'functionCall' => [Statistical::class, 'ZTEST'],
            'argumentCount' => '2-3',
        ],
    ];

    
    private static $controlFunctions = [
        'MKMATRIX' => [
            'argumentCount' => '*',
            'functionCall' => [__CLASS__, 'mkMatrix'],
        ],
        'NAME.ERROR' => [
            'argumentCount' => '*',
            'functionCall' => [Functions::class, 'NAME'],
        ],
    ];

    public function __construct(?Spreadsheet $spreadsheet = null)
    {
        $this->delta = 1 * 10 ** (0 - ini_get('precision'));

        $this->spreadsheet = $spreadsheet;
        $this->cyclicReferenceStack = new CyclicReferenceStack();
        $this->debugLog = new Logger($this->cyclicReferenceStack);
        self::$referenceHelper = ReferenceHelper::getInstance();
    }

    private static function loadLocales(): void
    {
        $localeFileDirectory = __DIR__ . '/locale/';
        foreach (glob($localeFileDirectory . '*', GLOB_ONLYDIR) as $filename) {
            $filename = substr($filename, strlen($localeFileDirectory));
            if ($filename != 'en') {
                self::$validLocaleLanguages[] = $filename;
            }
        }
    }

    
    public static function getInstance(?Spreadsheet $spreadsheet = null)
    {
        if ($spreadsheet !== null) {
            $instance = $spreadsheet->getCalculationEngine();
            if (isset($instance)) {
                return $instance;
            }
        }

        if (!isset(self::$instance) || (self::$instance === null)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    
    public function flushInstance(): void
    {
        $this->clearCalculationCache();
        $this->clearBranchStore();
    }

    
    public function getDebugLog()
    {
        return $this->debugLog;
    }

    
    final public function __clone()
    {
        throw new Exception('Cloning the calculation engine is not allowed!');
    }

    
    public static function getTRUE()
    {
        return self::$localeBoolean['TRUE'];
    }

    
    public static function getFALSE()
    {
        return self::$localeBoolean['FALSE'];
    }

    
    public static function setArrayReturnType($returnType)
    {
        if (
            ($returnType == self::RETURN_ARRAY_AS_VALUE) ||
            ($returnType == self::RETURN_ARRAY_AS_ERROR) ||
            ($returnType == self::RETURN_ARRAY_AS_ARRAY)
        ) {
            self::$returnArrayAsType = $returnType;

            return true;
        }

        return false;
    }

    
    public static function getArrayReturnType()
    {
        return self::$returnArrayAsType;
    }

    
    public function getCalculationCacheEnabled()
    {
        return $this->calculationCacheEnabled;
    }

    
    public function setCalculationCacheEnabled($pValue): void
    {
        $this->calculationCacheEnabled = $pValue;
        $this->clearCalculationCache();
    }

    
    public function enableCalculationCache(): void
    {
        $this->setCalculationCacheEnabled(true);
    }

    
    public function disableCalculationCache(): void
    {
        $this->setCalculationCacheEnabled(false);
    }

    
    public function clearCalculationCache(): void
    {
        $this->calculationCache = [];
    }

    
    public function clearCalculationCacheForWorksheet($worksheetName): void
    {
        if (isset($this->calculationCache[$worksheetName])) {
            unset($this->calculationCache[$worksheetName]);
        }
    }

    
    public function renameCalculationCacheForWorksheet($fromWorksheetName, $toWorksheetName): void
    {
        if (isset($this->calculationCache[$fromWorksheetName])) {
            $this->calculationCache[$toWorksheetName] = &$this->calculationCache[$fromWorksheetName];
            unset($this->calculationCache[$fromWorksheetName]);
        }
    }

    
    public function setBranchPruningEnabled($enabled): void
    {
        $this->branchPruningEnabled = $enabled;
    }

    public function enableBranchPruning(): void
    {
        $this->setBranchPruningEnabled(true);
    }

    public function disableBranchPruning(): void
    {
        $this->setBranchPruningEnabled(false);
    }

    public function clearBranchStore(): void
    {
        $this->branchStoreKeyCounter = 0;
    }

    
    public function getLocale()
    {
        return self::$localeLanguage;
    }

    
    public function setLocale($locale)
    {
        
        $language = $locale = strtolower($locale);
        if (strpos($locale, '_') !== false) {
            [$language] = explode('_', $locale);
        }
        if (count(self::$validLocaleLanguages) == 1) {
            self::loadLocales();
        }
        
        if (in_array($language, self::$validLocaleLanguages)) {
            
            self::$localeFunctions = [];
            self::$localeArgumentSeparator = ',';
            self::$localeBoolean = ['TRUE' => 'TRUE', 'FALSE' => 'FALSE', 'NULL' => 'NULL'];
            
            if ($locale != 'en_us') {
                
                $functionNamesFile = __DIR__ . '/locale/' . str_replace('_', DIRECTORY_SEPARATOR, $locale) . DIRECTORY_SEPARATOR . 'functions';
                if (!file_exists($functionNamesFile)) {
                    
                    $functionNamesFile = __DIR__ . '/locale/' . $language . DIRECTORY_SEPARATOR . 'functions';
                    if (!file_exists($functionNamesFile)) {
                        return false;
                    }
                }
                
                $localeFunctions = file($functionNamesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($localeFunctions as $localeFunction) {
                    [$localeFunction] = explode('##', $localeFunction); 
                    if (strpos($localeFunction, '=') !== false) {
                        [$fName, $lfName] = explode('=', $localeFunction);
                        $fName = trim($fName);
                        $lfName = trim($lfName);
                        if ((isset(self::$phpSpreadsheetFunctions[$fName])) && ($lfName != '') && ($fName != $lfName)) {
                            self::$localeFunctions[$fName] = $lfName;
                        }
                    }
                }
                
                if (isset(self::$localeFunctions['TRUE'])) {
                    self::$localeBoolean['TRUE'] = self::$localeFunctions['TRUE'];
                }
                if (isset(self::$localeFunctions['FALSE'])) {
                    self::$localeBoolean['FALSE'] = self::$localeFunctions['FALSE'];
                }

                $configFile = __DIR__ . '/locale/' . str_replace('_', DIRECTORY_SEPARATOR, $locale) . DIRECTORY_SEPARATOR . 'config';
                if (!file_exists($configFile)) {
                    $configFile = __DIR__ . '/locale/' . $language . DIRECTORY_SEPARATOR . 'config';
                }
                if (file_exists($configFile)) {
                    $localeSettings = file($configFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($localeSettings as $localeSetting) {
                        [$localeSetting] = explode('##', $localeSetting); 
                        if (strpos($localeSetting, '=') !== false) {
                            [$settingName, $settingValue] = explode('=', $localeSetting);
                            $settingName = strtoupper(trim($settingName));
                            switch ($settingName) {
                                case 'ARGUMENTSEPARATOR':
                                    self::$localeArgumentSeparator = trim($settingValue);

                                    break;
                            }
                        }
                    }
                }
            }

            self::$functionReplaceFromExcel = self::$functionReplaceToExcel =
            self::$functionReplaceFromLocale = self::$functionReplaceToLocale = null;
            self::$localeLanguage = $locale;

            return true;
        }

        return false;
    }

    
    public static function translateSeparator($fromSeparator, $toSeparator, $formula, &$inBraces)
    {
        $strlen = mb_strlen($formula);
        for ($i = 0; $i < $strlen; ++$i) {
            $chr = mb_substr($formula, $i, 1);
            switch ($chr) {
                case self::FORMULA_OPEN_FUNCTION_BRACE:
                    $inBraces = true;

                    break;
                case self::FORMULA_CLOSE_FUNCTION_BRACE:
                    $inBraces = false;

                    break;
                case $fromSeparator:
                    if (!$inBraces) {
                        $formula = mb_substr($formula, 0, $i) . $toSeparator . mb_substr($formula, $i + 1);
                    }
            }
        }

        return $formula;
    }

    
    private static function translateFormula(array $from, array $to, $formula, $fromSeparator, $toSeparator)
    {
        
        if (self::$localeLanguage !== 'en_us') {
            $inBraces = false;
            
            if (strpos($formula, self::FORMULA_STRING_QUOTE) !== false) {
                
                
                $temp = explode(self::FORMULA_STRING_QUOTE, $formula);
                $i = false;
                foreach ($temp as &$value) {
                    
                    if ($i = !$i) {
                        $value = preg_replace($from, $to, $value);
                        $value = self::translateSeparator($fromSeparator, $toSeparator, $value, $inBraces);
                    }
                }
                unset($value);
                
                $formula = implode(self::FORMULA_STRING_QUOTE, $temp);
            } else {
                
                $formula = preg_replace($from, $to, $formula);
                $formula = self::translateSeparator($fromSeparator, $toSeparator, $formula, $inBraces);
            }
        }

        return $formula;
    }

    private static $functionReplaceFromExcel = null;

    private static $functionReplaceToLocale = null;

    public function _translateFormulaToLocale($formula)
    {
        if (self::$functionReplaceFromExcel === null) {
            self::$functionReplaceFromExcel = [];
            foreach (array_keys(self::$localeFunctions) as $excelFunctionName) {
                self::$functionReplaceFromExcel[] = '/(@?[^\w\.])' . preg_quote($excelFunctionName, '/') . '([\s]*\()/Ui';
            }
            foreach (array_keys(self::$localeBoolean) as $excelBoolean) {
                self::$functionReplaceFromExcel[] = '/(@?[^\w\.])' . preg_quote($excelBoolean, '/') . '([^\w\.])/Ui';
            }
        }

        if (self::$functionReplaceToLocale === null) {
            self::$functionReplaceToLocale = [];
            foreach (self::$localeFunctions as $localeFunctionName) {
                self::$functionReplaceToLocale[] = '$1' . trim($localeFunctionName) . '$2';
            }
            foreach (self::$localeBoolean as $localeBoolean) {
                self::$functionReplaceToLocale[] = '$1' . trim($localeBoolean) . '$2';
            }
        }

        return self::translateFormula(self::$functionReplaceFromExcel, self::$functionReplaceToLocale, $formula, ',', self::$localeArgumentSeparator);
    }

    private static $functionReplaceFromLocale = null;

    private static $functionReplaceToExcel = null;

    public function _translateFormulaToEnglish($formula)
    {
        if (self::$functionReplaceFromLocale === null) {
            self::$functionReplaceFromLocale = [];
            foreach (self::$localeFunctions as $localeFunctionName) {
                self::$functionReplaceFromLocale[] = '/(@?[^\w\.])' . preg_quote($localeFunctionName, '/') . '([\s]*\()/Ui';
            }
            foreach (self::$localeBoolean as $excelBoolean) {
                self::$functionReplaceFromLocale[] = '/(@?[^\w\.])' . preg_quote($excelBoolean, '/') . '([^\w\.])/Ui';
            }
        }

        if (self::$functionReplaceToExcel === null) {
            self::$functionReplaceToExcel = [];
            foreach (array_keys(self::$localeFunctions) as $excelFunctionName) {
                self::$functionReplaceToExcel[] = '$1' . trim($excelFunctionName) . '$2';
            }
            foreach (array_keys(self::$localeBoolean) as $excelBoolean) {
                self::$functionReplaceToExcel[] = '$1' . trim($excelBoolean) . '$2';
            }
        }

        return self::translateFormula(self::$functionReplaceFromLocale, self::$functionReplaceToExcel, $formula, self::$localeArgumentSeparator, ',');
    }

    public static function localeFunc($function)
    {
        if (self::$localeLanguage !== 'en_us') {
            $functionName = trim($function, '(');
            if (isset(self::$localeFunctions[$functionName])) {
                $brace = ($functionName != $function);
                $function = self::$localeFunctions[$functionName];
                if ($brace) {
                    $function .= '(';
                }
            }
        }

        return $function;
    }

    
    public static function wrapResult($value)
    {
        if (is_string($value)) {
            
            if (preg_match('/^' . self::CALCULATION_REGEXP_ERROR . '$/i', $value, $match)) {
                
                return $value;
            }
            
            return self::FORMULA_STRING_QUOTE . $value . self::FORMULA_STRING_QUOTE;
        } elseif ((is_float($value)) && ((is_nan($value)) || (is_infinite($value)))) {
            
            return Functions::NAN();
        }

        return $value;
    }

    
    public static function unwrapResult($value)
    {
        if (is_string($value)) {
            if ((isset($value[0])) && ($value[0] == self::FORMULA_STRING_QUOTE) && (substr($value, -1) == self::FORMULA_STRING_QUOTE)) {
                return substr($value, 1, -1);
            }
            
        } elseif ((is_float($value)) && ((is_nan($value)) || (is_infinite($value)))) {
            return Functions::NAN();
        }

        return $value;
    }

    
    public function calculate(?Cell $pCell = null)
    {
        try {
            return $this->calculateCellValue($pCell);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    
    public function calculateCellValue(?Cell $pCell = null, $resetLog = true)
    {
        if ($pCell === null) {
            return null;
        }

        $returnArrayAsType = self::$returnArrayAsType;
        if ($resetLog) {
            
            $this->formulaError = null;
            $this->debugLog->clearLog();
            $this->cyclicReferenceStack->clear();
            $this->cyclicFormulaCounter = 1;

            self::$returnArrayAsType = self::RETURN_ARRAY_AS_ARRAY;
        }

        
        $this->cellStack[] = [
            'sheet' => $pCell->getWorksheet()->getTitle(),
            'cell' => $pCell->getCoordinate(),
        ];

        try {
            $result = self::unwrapResult($this->_calculateFormulaValue($pCell->getValue(), $pCell->getCoordinate(), $pCell));
            $cellAddress = array_pop($this->cellStack);
            $this->spreadsheet->getSheetByName($cellAddress['sheet'])->getCell($cellAddress['cell']);
        } catch (\Exception $e) {
            $cellAddress = array_pop($this->cellStack);
            $this->spreadsheet->getSheetByName($cellAddress['sheet'])->getCell($cellAddress['cell']);

            throw new Exception($e->getMessage());
        }

        if ((is_array($result)) && (self::$returnArrayAsType != self::RETURN_ARRAY_AS_ARRAY)) {
            self::$returnArrayAsType = $returnArrayAsType;
            $testResult = Functions::flattenArray($result);
            if (self::$returnArrayAsType == self::RETURN_ARRAY_AS_ERROR) {
                return Functions::VALUE();
            }
            
            if (count($testResult) != 1) {
                
                $r = array_keys($result);
                $r = array_shift($r);
                if (!is_numeric($r)) {
                    return Functions::VALUE();
                }
                if (is_array($result[$r])) {
                    $c = array_keys($result[$r]);
                    $c = array_shift($c);
                    if (!is_numeric($c)) {
                        return Functions::VALUE();
                    }
                }
            }
            $result = array_shift($testResult);
        }
        self::$returnArrayAsType = $returnArrayAsType;

        if ($result === null && $pCell->getWorksheet()->getSheetView()->getShowZeros()) {
            return 0;
        } elseif ((is_float($result)) && ((is_nan($result)) || (is_infinite($result)))) {
            return Functions::NAN();
        }

        return $result;
    }

    
    public function parseFormula($formula)
    {
        
        
        $formula = trim($formula);
        if ((!isset($formula[0])) || ($formula[0] != '=')) {
            return [];
        }
        $formula = ltrim(substr($formula, 1));
        if (!isset($formula[0])) {
            return [];
        }

        
        return $this->internalParseFormula($formula);
    }

    
    public function calculateFormula($formula, $cellID = null, ?Cell $pCell = null)
    {
        
        $this->formulaError = null;
        $this->debugLog->clearLog();
        $this->cyclicReferenceStack->clear();

        $resetCache = $this->getCalculationCacheEnabled();
        if ($this->spreadsheet !== null && $cellID === null && $pCell === null) {
            $cellID = 'A1';
            $pCell = $this->spreadsheet->getActiveSheet()->getCell($cellID);
        } else {
            
            
            $this->calculationCacheEnabled = false;
        }

        
        try {
            $result = self::unwrapResult($this->_calculateFormulaValue($formula, $cellID, $pCell));
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

        if ($this->spreadsheet === null) {
            
            $this->calculationCacheEnabled = $resetCache;
        }

        return $result;
    }

    
    public function getValueFromCache($cellReference, &$cellValue)
    {
        
        
        $this->debugLog->writeDebugLog('Testing cache value for cell ', $cellReference);
        if (($this->calculationCacheEnabled) && (isset($this->calculationCache[$cellReference]))) {
            $this->debugLog->writeDebugLog('Retrieving value for cell ', $cellReference, ' from cache');
            

            $cellValue = $this->calculationCache[$cellReference];

            return true;
        }

        return false;
    }

    
    public function saveValueToCache($cellReference, $cellValue): void
    {
        if ($this->calculationCacheEnabled) {
            $this->calculationCache[$cellReference] = $cellValue;
        }
    }

    
    public function _calculateFormulaValue($formula, $cellID = null, ?Cell $pCell = null)
    {
        $cellValue = null;

        
        if ($pCell !== null && $pCell->getStyle()->getQuotePrefix() === true) {
            return self::wrapResult((string) $formula);
        }

        if (preg_match('/^=\s*cmd\s*\|/miu', $formula) !== 0) {
            return self::wrapResult($formula);
        }

        
        
        $formula = trim($formula);
        if ($formula[0] != '=') {
            return self::wrapResult($formula);
        }
        $formula = ltrim(substr($formula, 1));
        if (!isset($formula[0])) {
            return self::wrapResult($formula);
        }

        $pCellParent = ($pCell !== null) ? $pCell->getWorksheet() : null;
        $wsTitle = ($pCellParent !== null) ? $pCellParent->getTitle() : "\x00Wrk";
        $wsCellReference = $wsTitle . '!' . $cellID;

        if (($cellID !== null) && ($this->getValueFromCache($wsCellReference, $cellValue))) {
            return $cellValue;
        }
        $this->debugLog->writeDebugLog('Evaluating formula for cell ', $wsCellReference);

        if (($wsTitle[0] !== "\x00") && ($this->cyclicReferenceStack->onStack($wsCellReference))) {
            if ($this->cyclicFormulaCount <= 0) {
                $this->cyclicFormulaCell = '';

                return $this->raiseFormulaError('Cyclic Reference in Formula');
            } elseif ($this->cyclicFormulaCell === $wsCellReference) {
                ++$this->cyclicFormulaCounter;
                if ($this->cyclicFormulaCounter >= $this->cyclicFormulaCount) {
                    $this->cyclicFormulaCell = '';

                    return $cellValue;
                }
            } elseif ($this->cyclicFormulaCell == '') {
                if ($this->cyclicFormulaCounter >= $this->cyclicFormulaCount) {
                    return $cellValue;
                }
                $this->cyclicFormulaCell = $wsCellReference;
            }
        }

        $this->debugLog->writeDebugLog('Formula for cell ', $wsCellReference, ' is ', $formula);
        
        $this->cyclicReferenceStack->push($wsCellReference);
        $cellValue = $this->processTokenStack($this->internalParseFormula($formula, $pCell), $cellID, $pCell);
        $this->cyclicReferenceStack->pop();

        
        if ($cellID !== null) {
            $this->saveValueToCache($wsCellReference, $cellValue);
        }

        
        return $cellValue;
    }

    
    private static function checkMatrixOperands(&$operand1, &$operand2, $resize = 1)
    {
        
        
        if (!is_array($operand1)) {
            [$matrixRows, $matrixColumns] = self::getMatrixDimensions($operand2);
            $operand1 = array_fill(0, $matrixRows, array_fill(0, $matrixColumns, $operand1));
            $resize = 0;
        } elseif (!is_array($operand2)) {
            [$matrixRows, $matrixColumns] = self::getMatrixDimensions($operand1);
            $operand2 = array_fill(0, $matrixRows, array_fill(0, $matrixColumns, $operand2));
            $resize = 0;
        }

        [$matrix1Rows, $matrix1Columns] = self::getMatrixDimensions($operand1);
        [$matrix2Rows, $matrix2Columns] = self::getMatrixDimensions($operand2);
        if (($matrix1Rows == $matrix2Columns) && ($matrix2Rows == $matrix1Columns)) {
            $resize = 1;
        }

        if ($resize == 2) {
            
            self::resizeMatricesExtend($operand1, $operand2, $matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns);
        } elseif ($resize == 1) {
            
            self::resizeMatricesShrink($operand1, $operand2, $matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns);
        }

        return [$matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns];
    }

    
    public static function getMatrixDimensions(array &$matrix)
    {
        $matrixRows = count($matrix);
        $matrixColumns = 0;
        foreach ($matrix as $rowKey => $rowValue) {
            if (!is_array($rowValue)) {
                $matrix[$rowKey] = [$rowValue];
                $matrixColumns = max(1, $matrixColumns);
            } else {
                $matrix[$rowKey] = array_values($rowValue);
                $matrixColumns = max(count($rowValue), $matrixColumns);
            }
        }
        $matrix = array_values($matrix);

        return [$matrixRows, $matrixColumns];
    }

    
    private static function resizeMatricesShrink(&$matrix1, &$matrix2, $matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns): void
    {
        if (($matrix2Columns < $matrix1Columns) || ($matrix2Rows < $matrix1Rows)) {
            if ($matrix2Rows < $matrix1Rows) {
                for ($i = $matrix2Rows; $i < $matrix1Rows; ++$i) {
                    unset($matrix1[$i]);
                }
            }
            if ($matrix2Columns < $matrix1Columns) {
                for ($i = 0; $i < $matrix1Rows; ++$i) {
                    for ($j = $matrix2Columns; $j < $matrix1Columns; ++$j) {
                        unset($matrix1[$i][$j]);
                    }
                }
            }
        }

        if (($matrix1Columns < $matrix2Columns) || ($matrix1Rows < $matrix2Rows)) {
            if ($matrix1Rows < $matrix2Rows) {
                for ($i = $matrix1Rows; $i < $matrix2Rows; ++$i) {
                    unset($matrix2[$i]);
                }
            }
            if ($matrix1Columns < $matrix2Columns) {
                for ($i = 0; $i < $matrix2Rows; ++$i) {
                    for ($j = $matrix1Columns; $j < $matrix2Columns; ++$j) {
                        unset($matrix2[$i][$j]);
                    }
                }
            }
        }
    }

    
    private static function resizeMatricesExtend(&$matrix1, &$matrix2, $matrix1Rows, $matrix1Columns, $matrix2Rows, $matrix2Columns): void
    {
        if (($matrix2Columns < $matrix1Columns) || ($matrix2Rows < $matrix1Rows)) {
            if ($matrix2Columns < $matrix1Columns) {
                for ($i = 0; $i < $matrix2Rows; ++$i) {
                    $x = $matrix2[$i][$matrix2Columns - 1];
                    for ($j = $matrix2Columns; $j < $matrix1Columns; ++$j) {
                        $matrix2[$i][$j] = $x;
                    }
                }
            }
            if ($matrix2Rows < $matrix1Rows) {
                $x = $matrix2[$matrix2Rows - 1];
                for ($i = 0; $i < $matrix1Rows; ++$i) {
                    $matrix2[$i] = $x;
                }
            }
        }

        if (($matrix1Columns < $matrix2Columns) || ($matrix1Rows < $matrix2Rows)) {
            if ($matrix1Columns < $matrix2Columns) {
                for ($i = 0; $i < $matrix1Rows; ++$i) {
                    $x = $matrix1[$i][$matrix1Columns - 1];
                    for ($j = $matrix1Columns; $j < $matrix2Columns; ++$j) {
                        $matrix1[$i][$j] = $x;
                    }
                }
            }
            if ($matrix1Rows < $matrix2Rows) {
                $x = $matrix1[$matrix1Rows - 1];
                for ($i = 0; $i < $matrix2Rows; ++$i) {
                    $matrix1[$i] = $x;
                }
            }
        }
    }

    
    private function showValue($value)
    {
        if ($this->debugLog->getWriteDebugLog()) {
            $testArray = Functions::flattenArray($value);
            if (count($testArray) == 1) {
                $value = array_pop($testArray);
            }

            if (is_array($value)) {
                $returnMatrix = [];
                $pad = $rpad = ', ';
                foreach ($value as $row) {
                    if (is_array($row)) {
                        $returnMatrix[] = implode($pad, array_map([$this, 'showValue'], $row));
                        $rpad = '; ';
                    } else {
                        $returnMatrix[] = $this->showValue($row);
                    }
                }

                return '{ ' . implode($rpad, $returnMatrix) . ' }';
            } elseif (is_string($value) && (trim($value, self::FORMULA_STRING_QUOTE) == $value)) {
                return self::FORMULA_STRING_QUOTE . $value . self::FORMULA_STRING_QUOTE;
            } elseif (is_bool($value)) {
                return ($value) ? self::$localeBoolean['TRUE'] : self::$localeBoolean['FALSE'];
            }
        }

        return Functions::flattenSingleValue($value);
    }

    
    private function showTypeDetails($value)
    {
        if ($this->debugLog->getWriteDebugLog()) {
            $testArray = Functions::flattenArray($value);
            if (count($testArray) == 1) {
                $value = array_pop($testArray);
            }

            if ($value === null) {
                return 'a NULL value';
            } elseif (is_float($value)) {
                $typeString = 'a floating point number';
            } elseif (is_int($value)) {
                $typeString = 'an integer number';
            } elseif (is_bool($value)) {
                $typeString = 'a boolean';
            } elseif (is_array($value)) {
                $typeString = 'a matrix';
            } else {
                if ($value == '') {
                    return 'an empty string';
                } elseif ($value[0] == '#') {
                    return 'a ' . $value . ' error';
                }
                $typeString = 'a string';
            }

            return $typeString . ' with a value of ' . $this->showValue($value);
        }
    }

    
    private function convertMatrixReferences($formula)
    {
        static $matrixReplaceFrom = [self::FORMULA_OPEN_FUNCTION_BRACE, ';', self::FORMULA_CLOSE_FUNCTION_BRACE];
        static $matrixReplaceTo = ['MKMATRIX(MKMATRIX(', '),MKMATRIX(', '))'];

        
        if (strpos($formula, self::FORMULA_OPEN_FUNCTION_BRACE) !== false) {
            
            if (strpos($formula, self::FORMULA_STRING_QUOTE) !== false) {
                
                
                $temp = explode(self::FORMULA_STRING_QUOTE, $formula);
                
                $openCount = $closeCount = 0;
                $i = false;
                foreach ($temp as &$value) {
                    
                    if ($i = !$i) {
                        $openCount += substr_count($value, self::FORMULA_OPEN_FUNCTION_BRACE);
                        $closeCount += substr_count($value, self::FORMULA_CLOSE_FUNCTION_BRACE);
                        $value = str_replace($matrixReplaceFrom, $matrixReplaceTo, $value);
                    }
                }
                unset($value);
                
                $formula = implode(self::FORMULA_STRING_QUOTE, $temp);
            } else {
                
                $openCount = substr_count($formula, self::FORMULA_OPEN_FUNCTION_BRACE);
                $closeCount = substr_count($formula, self::FORMULA_CLOSE_FUNCTION_BRACE);
                $formula = str_replace($matrixReplaceFrom, $matrixReplaceTo, $formula);
            }
            
            if ($openCount < $closeCount) {
                if ($openCount > 0) {
                    return $this->raiseFormulaError("Formula Error: Mismatched matrix braces '}'");
                }

                return $this->raiseFormulaError("Formula Error: Unexpected '}' encountered");
            } elseif ($openCount > $closeCount) {
                if ($closeCount > 0) {
                    return $this->raiseFormulaError("Formula Error: Mismatched matrix braces '{'");
                }

                return $this->raiseFormulaError("Formula Error: Unexpected '{' encountered");
            }
        }

        return $formula;
    }

    private static function mkMatrix(...$args)
    {
        return $args;
    }

    
    
    
    private static $operatorAssociativity = [
        '^' => 0, 
        '*' => 0, '/' => 0, 
        '+' => 0, '-' => 0, 
        '&' => 0, 
        '|' => 0, ':' => 0, 
        '>' => 0, '<' => 0, '=' => 0, '>=' => 0, '<=' => 0, '<>' => 0, 
    ];

    
    
    private static $comparisonOperators = ['>' => true, '<' => true, '=' => true, '>=' => true, '<=' => true, '<>' => true];

    
    
    
    private static $operatorPrecedence = [
        ':' => 8, 
        '|' => 7, 
        '~' => 6, 
        '%' => 5, 
        '^' => 4, 
        '*' => 3, '/' => 3, 
        '+' => 2, '-' => 2, 
        '&' => 1, 
        '>' => 0, '<' => 0, '=' => 0, '>=' => 0, '<=' => 0, '<>' => 0, 
    ];

    

    
    private function internalParseFormula($formula, ?Cell $pCell = null)
    {
        if (($formula = $this->convertMatrixReferences(trim($formula))) === false) {
            return false;
        }

        
        
        $pCellParent = ($pCell !== null) ? $pCell->getWorksheet() : null;

        $regexpMatchString = '/^(' . self::CALCULATION_REGEXP_FUNCTION .
                                '|' . self::CALCULATION_REGEXP_CELLREF .
                                '|' . self::CALCULATION_REGEXP_NUMBER .
                                '|' . self::CALCULATION_REGEXP_STRING .
                                '|' . self::CALCULATION_REGEXP_OPENBRACE .
                                '|' . self::CALCULATION_REGEXP_DEFINEDNAME .
                                '|' . self::CALCULATION_REGEXP_ERROR .
                                ')/sui';

        
        $index = 0;
        $stack = new Stack();
        $output = [];
        $expectingOperator = false; 
        
        $expectingOperand = false; 
        

        
        
        $pendingStoreKey = null;
        
        $pendingStoreKeysStack = [];
        $expectingConditionMap = []; 
        $expectingThenMap = []; 
        $expectingElseMap = []; 
        $parenthesisDepthMap = []; 

        
        
        while (true) {
            
            
            $currentCondition = null;
            $currentOnlyIf = null;
            $currentOnlyIfNot = null;
            $previousStoreKey = null;
            $pendingStoreKey = end($pendingStoreKeysStack);

            if ($this->branchPruningEnabled) {
                
                if (isset($expectingConditionMap[$pendingStoreKey]) && $expectingConditionMap[$pendingStoreKey]) {
                    $currentCondition = $pendingStoreKey;
                    $stackDepth = count($pendingStoreKeysStack);
                    if ($stackDepth > 1) { 
                        $previousStoreKey = $pendingStoreKeysStack[$stackDepth - 2];
                    }
                }
                if (isset($expectingThenMap[$pendingStoreKey]) && $expectingThenMap[$pendingStoreKey]) {
                    $currentOnlyIf = $pendingStoreKey;
                } elseif (isset($previousStoreKey)) {
                    if (isset($expectingThenMap[$previousStoreKey]) && $expectingThenMap[$previousStoreKey]) {
                        $currentOnlyIf = $previousStoreKey;
                    }
                }
                if (isset($expectingElseMap[$pendingStoreKey]) && $expectingElseMap[$pendingStoreKey]) {
                    $currentOnlyIfNot = $pendingStoreKey;
                } elseif (isset($previousStoreKey)) {
                    if (isset($expectingElseMap[$previousStoreKey]) && $expectingElseMap[$previousStoreKey]) {
                        $currentOnlyIfNot = $previousStoreKey;
                    }
                }
            }

            $opCharacter = $formula[$index]; 

            if ((isset(self::$comparisonOperators[$opCharacter])) && (strlen($formula) > $index) && (isset(self::$comparisonOperators[$formula[$index + 1]]))) {
                $opCharacter .= $formula[++$index];
            }
            
            $isOperandOrFunction = preg_match($regexpMatchString, substr($formula, $index), $match);
            if ($opCharacter == '-' && !$expectingOperator) {                
                
                $stack->push('Unary Operator', '~', null, $currentCondition, $currentOnlyIf, $currentOnlyIfNot);
                ++$index; 
            } elseif ($opCharacter == '%' && $expectingOperator) {
                
                $stack->push('Unary Operator', '%', null, $currentCondition, $currentOnlyIf, $currentOnlyIfNot);
                ++$index;
            } elseif ($opCharacter == '+' && !$expectingOperator) {            
                ++$index; 
            } elseif ((($opCharacter == '~') || ($opCharacter == '|')) && (!$isOperandOrFunction)) {    
                return $this->raiseFormulaError("Formula Error: Illegal character '~'"); 
            } elseif ((isset(self::$operators[$opCharacter]) || $isOperandOrFunction) && $expectingOperator) {    
                while (
                    $stack->count() > 0 &&
                    ($o2 = $stack->last()) &&
                    isset(self::$operators[$o2['value']]) &&
                    @(self::$operatorAssociativity[$opCharacter] ? self::$operatorPrecedence[$opCharacter] < self::$operatorPrecedence[$o2['value']] : self::$operatorPrecedence[$opCharacter] <= self::$operatorPrecedence[$o2['value']])
                ) {
                    $output[] = $stack->pop(); 
                }

                
                $stack->push('Binary Operator', $opCharacter, null, $currentCondition, $currentOnlyIf, $currentOnlyIfNot);

                ++$index;
                $expectingOperator = false;
            } elseif ($opCharacter == ')' && $expectingOperator) {            
                $expectingOperand = false;
                while (($o2 = $stack->pop()) && $o2['value'] != '(') {        
                    if ($o2 === null) {
                        return $this->raiseFormulaError('Formula Error: Unexpected closing brace ")"');
                    }
                    $output[] = $o2;
                }
                $d = $stack->last(2);

                
                
                if (!empty($pendingStoreKey)) {
                    --$parenthesisDepthMap[$pendingStoreKey];
                }

                if (is_array($d) && preg_match('/^' . self::CALCULATION_REGEXP_FUNCTION . '$/miu', $d['value'], $matches)) {    
                    if (!empty($pendingStoreKey) && $parenthesisDepthMap[$pendingStoreKey] == -1) {
                        
                        if ($d['value'] != 'IF(') {
                            return $this->raiseFormulaError('Parser bug we should be in an "IF("');
                        }
                        if ($expectingConditionMap[$pendingStoreKey]) {
                            return $this->raiseFormulaError('We should not be expecting a condition');
                        }
                        $expectingThenMap[$pendingStoreKey] = false;
                        $expectingElseMap[$pendingStoreKey] = false;
                        --$parenthesisDepthMap[$pendingStoreKey];
                        array_pop($pendingStoreKeysStack);
                        unset($pendingStoreKey);
                    }

                    $functionName = $matches[1]; 
                    $d = $stack->pop();
                    $argumentCount = $d['value']; 
                    $output[] = $d; 
                    $output[] = $stack->pop(); 
                    if (isset(self::$controlFunctions[$functionName])) {
                        $expectedArgumentCount = self::$controlFunctions[$functionName]['argumentCount'];
                        $functionCall = self::$controlFunctions[$functionName]['functionCall'];
                    } elseif (isset(self::$phpSpreadsheetFunctions[$functionName])) {
                        $expectedArgumentCount = self::$phpSpreadsheetFunctions[$functionName]['argumentCount'];
                        $functionCall = self::$phpSpreadsheetFunctions[$functionName]['functionCall'];
                    } else {    
                        return $this->raiseFormulaError('Formula Error: Internal error, non-function on stack');
                    }
                    
                    $argumentCountError = false;
                    if (is_numeric($expectedArgumentCount)) {
                        if ($expectedArgumentCount < 0) {
                            if ($argumentCount > abs($expectedArgumentCount)) {
                                $argumentCountError = true;
                                $expectedArgumentCountString = 'no more than ' . abs($expectedArgumentCount);
                            }
                        } else {
                            if ($argumentCount != $expectedArgumentCount) {
                                $argumentCountError = true;
                                $expectedArgumentCountString = $expectedArgumentCount;
                            }
                        }
                    } elseif ($expectedArgumentCount != '*') {
                        $isOperandOrFunction = preg_match('/(\d*)([-+,])(\d*)/', $expectedArgumentCount, $argMatch);
                        switch ($argMatch[2]) {
                            case '+':
                                if ($argumentCount < $argMatch[1]) {
                                    $argumentCountError = true;
                                    $expectedArgumentCountString = $argMatch[1] . ' or more ';
                                }

                                break;
                            case '-':
                                if (($argumentCount < $argMatch[1]) || ($argumentCount > $argMatch[3])) {
                                    $argumentCountError = true;
                                    $expectedArgumentCountString = 'between ' . $argMatch[1] . ' and ' . $argMatch[3];
                                }

                                break;
                            case ',':
                                if (($argumentCount != $argMatch[1]) && ($argumentCount != $argMatch[3])) {
                                    $argumentCountError = true;
                                    $expectedArgumentCountString = 'either ' . $argMatch[1] . ' or ' . $argMatch[3];
                                }

                                break;
                        }
                    }
                    if ($argumentCountError) {
                        return $this->raiseFormulaError("Formula Error: Wrong number of arguments for $functionName() function: $argumentCount given, " . $expectedArgumentCountString . ' expected');
                    }
                }
                ++$index;
            } elseif ($opCharacter == ',') {            
                if (
                    !empty($pendingStoreKey) &&
                    $parenthesisDepthMap[$pendingStoreKey] == 0
                ) {
                    
                    if ($expectingConditionMap[$pendingStoreKey]) {
                        $expectingConditionMap[$pendingStoreKey] = false;
                        $expectingThenMap[$pendingStoreKey] = true;
                    } elseif ($expectingThenMap[$pendingStoreKey]) {
                        $expectingThenMap[$pendingStoreKey] = false;
                        $expectingElseMap[$pendingStoreKey] = true;
                    } elseif ($expectingElseMap[$pendingStoreKey]) {
                        return $this->raiseFormulaError('Reaching fourth argument of an IF');
                    }
                }
                while (($o2 = $stack->pop()) && $o2['value'] != '(') {        
                    if ($o2 === null) {
                        return $this->raiseFormulaError('Formula Error: Unexpected ,');
                    }
                    $output[] = $o2; 
                }
                
                
                if (($expectingOperand) || (!$expectingOperator)) {
                    $output[] = ['type' => 'NULL Value', 'value' => self::$excelConstants['NULL'], 'reference' => null];
                }
                
                $d = $stack->last(2);
                if (!preg_match('/^' . self::CALCULATION_REGEXP_FUNCTION . '$/miu', $d['value'], $matches)) {
                    return $this->raiseFormulaError('Formula Error: Unexpected ,');
                }
                $d = $stack->pop();
                $itemStoreKey = $d['storeKey'] ?? null;
                $itemOnlyIf = $d['onlyIf'] ?? null;
                $itemOnlyIfNot = $d['onlyIfNot'] ?? null;
                $stack->push($d['type'], ++$d['value'], $d['reference'], $itemStoreKey, $itemOnlyIf, $itemOnlyIfNot); 
                $stack->push('Brace', '(', null, $itemStoreKey, $itemOnlyIf, $itemOnlyIfNot); 
                $expectingOperator = false;
                $expectingOperand = true;
                ++$index;
            } elseif ($opCharacter == '(' && !$expectingOperator) {
                if (!empty($pendingStoreKey)) { 
                    ++$parenthesisDepthMap[$pendingStoreKey];
                }
                $stack->push('Brace', '(', null, $currentCondition, $currentOnlyIf, $currentOnlyIf);
                ++$index;
            } elseif ($isOperandOrFunction && !$expectingOperator) {    
                $expectingOperator = true;
                $expectingOperand = false;
                $val = $match[1];
                $length = strlen($val);
                if (preg_match('/^' . self::CALCULATION_REGEXP_FUNCTION . '$/miu', $val, $matches)) {
                    $val = preg_replace('/\s/u', '', $val);
                    if (isset(self::$phpSpreadsheetFunctions[strtoupper($matches[1])]) || isset(self::$controlFunctions[strtoupper($matches[1])])) {    
                        $valToUpper = strtoupper($val);
                    } else {
                        $valToUpper = 'NAME.ERROR(';
                    }
                    
                    
                    if ($this->branchPruningEnabled && ($valToUpper == 'IF(')) { 
                        $pendingStoreKey = $this->getUnusedBranchStoreKey();
                        $pendingStoreKeysStack[] = $pendingStoreKey;
                        $expectingConditionMap[$pendingStoreKey] = true;
                        $parenthesisDepthMap[$pendingStoreKey] = 0;
                    } else { 
                        if (!empty($pendingStoreKey) && array_key_exists($pendingStoreKey, $parenthesisDepthMap)) {
                            ++$parenthesisDepthMap[$pendingStoreKey];
                        }
                    }

                    $stack->push('Function', $valToUpper, null, $currentCondition, $currentOnlyIf, $currentOnlyIfNot);
                    
                    $ax = preg_match('/^\s*\)/u', substr($formula, $index + $length));
                    if ($ax) {
                        $stack->push('Operand Count for Function ' . $valToUpper . ')', 0, null, $currentCondition, $currentOnlyIf, $currentOnlyIfNot);
                        $expectingOperator = true;
                    } else {
                        $stack->push('Operand Count for Function ' . $valToUpper . ')', 1, null, $currentCondition, $currentOnlyIf, $currentOnlyIfNot);
                        $expectingOperator = false;
                    }
                    $stack->push('Brace', '(');
                } elseif (preg_match('/^' . self::CALCULATION_REGEXP_CELLREF . '$/i', $val, $matches)) {
                    
                    
                    
                    $testPrevOp = $stack->last(1);
                    if ($testPrevOp !== null && $testPrevOp['value'] == ':') {
                        
                        if ($matches[2] == '') {
                            
                            
                            $rangeStartCellRef = $output[count($output) - 1]['value'];
                            preg_match('/^' . self::CALCULATION_REGEXP_CELLREF . '$/i', $rangeStartCellRef, $rangeStartMatches);
                            if ($rangeStartMatches[2] > '') {
                                $val = $rangeStartMatches[2] . '!' . $val;
                            }
                        } else {
                            $rangeStartCellRef = $output[count($output) - 1]['value'];
                            preg_match('/^' . self::CALCULATION_REGEXP_CELLREF . '$/i', $rangeStartCellRef, $rangeStartMatches);
                            if ($rangeStartMatches[2] !== $matches[2]) {
                                return $this->raiseFormulaError('3D Range references are not yet supported');
                            }
                        }
                    }

                    $outputItem = $stack->getStackItem('Cell Reference', $val, $val, $currentCondition, $currentOnlyIf, $currentOnlyIfNot);

                    $output[] = $outputItem;
                } else {    
                    
                    $testPrevOp = $stack->last(1);
                    if ($testPrevOp !== null && $testPrevOp['value'] === ':') {
                        $startRowColRef = $output[count($output) - 1]['value'];
                        [$rangeWS1, $startRowColRef] = Worksheet::extractSheetTitle($startRowColRef, true);
                        $rangeSheetRef = $rangeWS1;
                        if ($rangeWS1 != '') {
                            $rangeWS1 .= '!';
                        }
                        [$rangeWS2, $val] = Worksheet::extractSheetTitle($val, true);
                        if ($rangeWS2 != '') {
                            $rangeWS2 .= '!';
                        } else {
                            $rangeWS2 = $rangeWS1;
                        }
                        $refSheet = $pCellParent;
                        if ($pCellParent !== null && $rangeSheetRef !== $pCellParent->getTitle()) {
                            $refSheet = $pCellParent->getParent()->getSheetByName($rangeSheetRef);
                        }
                        if (
                            (is_int($startRowColRef)) && (ctype_digit($val)) &&
                            ($startRowColRef <= 1048576) && ($val <= 1048576)
                        ) {
                            
                            $endRowColRef = ($refSheet !== null) ? $refSheet->getHighestColumn() : 'XFD'; 
                            $output[count($output) - 1]['value'] = $rangeWS1 . 'A' . $startRowColRef;
                            $val = $rangeWS2 . $endRowColRef . $val;
                        } elseif (
                            (ctype_alpha($startRowColRef)) && (ctype_alpha($val)) &&
                            (strlen($startRowColRef) <= 3) && (strlen($val) <= 3)
                        ) {
                            
                            $endRowColRef = ($refSheet !== null) ? $refSheet->getHighestRow() : 1048576; 
                            $output[count($output) - 1]['value'] = $rangeWS1 . strtoupper($startRowColRef) . '1';
                            $val = $rangeWS2 . $val . $endRowColRef;
                        }
                    }

                    $localeConstant = false;
                    $stackItemType = 'Value';
                    $stackItemReference = null;
                    if ($opCharacter == self::FORMULA_STRING_QUOTE) {
                        
                        $val = self::wrapResult(str_replace('""', self::FORMULA_STRING_QUOTE, self::unwrapResult($val)));
                    } elseif (is_numeric($val)) {
                        if ((strpos($val, '.') !== false) || (stripos($val, 'e') !== false) || ($val > PHP_INT_MAX) || ($val < -PHP_INT_MAX)) {
                            $val = (float) $val;
                        } else {
                            $val = (int) $val;
                        }
                    } elseif (isset(self::$excelConstants[trim(strtoupper($val))])) {
                        $stackItemType = 'Constant';
                        $excelConstant = trim(strtoupper($val));
                        $val = self::$excelConstants[$excelConstant];
                    } elseif (($localeConstant = array_search(trim(strtoupper($val)), self::$localeBoolean)) !== false) {
                        $stackItemType = 'Constant';
                        $val = self::$excelConstants[$localeConstant];
                    } elseif (preg_match('/^' . self::CALCULATION_REGEXP_DEFINEDNAME . '.*/miu', $val, $match)) {
                        $stackItemType = 'Defined Name';
                        $stackItemReference = $val;
                    }
                    $details = $stack->getStackItem($stackItemType, $val, $stackItemReference, $currentCondition, $currentOnlyIf, $currentOnlyIfNot);
                    if ($localeConstant) {
                        $details['localeValue'] = $localeConstant;
                    }
                    $output[] = $details;
                }
                $index += $length;
            } elseif ($opCharacter == '$') {    
                ++$index;
            } elseif ($opCharacter == ')') {    
                if ($expectingOperand) {
                    $output[] = ['type' => 'NULL Value', 'value' => self::$excelConstants['NULL'], 'reference' => null];
                    $expectingOperand = false;
                    $expectingOperator = true;
                } else {
                    return $this->raiseFormulaError("Formula Error: Unexpected ')'");
                }
            } elseif (isset(self::$operators[$opCharacter]) && !$expectingOperator) {
                return $this->raiseFormulaError("Formula Error: Unexpected operator '$opCharacter'");
            } else {    
                return $this->raiseFormulaError('Formula Error: An unexpected error occurred');
            }
            
            if ($index == strlen($formula)) {
                
                
                if ((isset(self::$operators[$opCharacter])) && ($opCharacter != '%')) {
                    return $this->raiseFormulaError("Formula Error: Operator '$opCharacter' has no operands");
                }

                break;
            }
            
            while (($formula[$index] == "\n") || ($formula[$index] == "\r")) {
                ++$index;
            }

            if ($formula[$index] == ' ') {
                while ($formula[$index] == ' ') {
                    ++$index;
                }

                
                
                if (
                    ($expectingOperator) &&
                    ((preg_match('/^' . self::CALCULATION_REGEXP_CELLREF . '.*/Ui', substr($formula, $index), $match)) &&
                        ($output[count($output) - 1]['type'] == 'Cell Reference') ||
                        (preg_match('/^' . self::CALCULATION_REGEXP_DEFINEDNAME . '.*/miu', substr($formula, $index), $match)) &&
                            ($output[count($output) - 1]['type'] == 'Defined Name' || $output[count($output) - 1]['type'] == 'Value')
                    )
                ) {
                    while (
                        $stack->count() > 0 &&
                        ($o2 = $stack->last()) &&
                        isset(self::$operators[$o2['value']]) &&
                        @(self::$operatorAssociativity[$opCharacter] ? self::$operatorPrecedence[$opCharacter] < self::$operatorPrecedence[$o2['value']] : self::$operatorPrecedence[$opCharacter] <= self::$operatorPrecedence[$o2['value']])
                    ) {
                        $output[] = $stack->pop(); 
                    }
                    $stack->push('Binary Operator', '|'); 
                    $expectingOperator = false;
                }
            }
        }

        while (($op = $stack->pop()) !== null) {    
            if ((is_array($op) && $op['value'] == '(') || ($op === '(')) {
                return $this->raiseFormulaError("Formula Error: Expecting ')'"); 
            }
            $output[] = $op;
        }

        return $output;
    }

    private static function dataTestReference(&$operandData)
    {
        $operand = $operandData['value'];
        if (($operandData['reference'] === null) && (is_array($operand))) {
            $rKeys = array_keys($operand);
            $rowKey = array_shift($rKeys);
            $cKeys = array_keys(array_keys($operand[$rowKey]));
            $colKey = array_shift($cKeys);
            if (ctype_upper($colKey)) {
                $operandData['reference'] = $colKey . $rowKey;
            }
        }

        return $operand;
    }

    

    
    private function processTokenStack($tokens, $cellID = null, ?Cell $pCell = null)
    {
        if ($tokens == false) {
            return false;
        }

        
        
        $pCellWorksheet = ($pCell !== null) ? $pCell->getWorksheet() : null;
        $pCellParent = ($pCell !== null) ? $pCell->getParent() : null;
        $stack = new Stack();

        
        $fakedForBranchPruning = [];
        
        $branchStore = [];
        
        foreach ($tokens as $tokenData) {
            $token = $tokenData['value'];

            
            $storeKey = $tokenData['storeKey'] ?? null;
            if ($this->branchPruningEnabled && isset($tokenData['onlyIf'])) {
                $onlyIfStoreKey = $tokenData['onlyIf'];
                $storeValue = $branchStore[$onlyIfStoreKey] ?? null;
                $storeValueAsBool = ($storeValue === null) ?
                    true : (bool) Functions::flattenSingleValue($storeValue);
                if (is_array($storeValue)) {
                    $wrappedItem = end($storeValue);
                    $storeValue = end($wrappedItem);
                }

                if (
                    isset($storeValue)
                    && (
                        !$storeValueAsBool
                        || Functions::isError($storeValue)
                        || ($storeValue === 'Pruned branch')
                    )
                ) {
                    
                    if (!isset($fakedForBranchPruning['onlyIf-' . $onlyIfStoreKey])) {
                        $stack->push('Value', 'Pruned branch (only if ' . $onlyIfStoreKey . ') ' . $token);
                        $fakedForBranchPruning['onlyIf-' . $onlyIfStoreKey] = true;
                    }

                    if (isset($storeKey)) {
                        
                        
                        $branchStore[$storeKey] = 'Pruned branch';
                        $fakedForBranchPruning['onlyIfNot-' . $storeKey] = true;
                        $fakedForBranchPruning['onlyIf-' . $storeKey] = true;
                    }

                    continue;
                }
            }

            if ($this->branchPruningEnabled && isset($tokenData['onlyIfNot'])) {
                $onlyIfNotStoreKey = $tokenData['onlyIfNot'];
                $storeValue = $branchStore[$onlyIfNotStoreKey] ?? null;
                $storeValueAsBool = ($storeValue === null) ?
                    true : (bool) Functions::flattenSingleValue($storeValue);
                if (is_array($storeValue)) {
                    $wrappedItem = end($storeValue);
                    $storeValue = end($wrappedItem);
                }
                if (
                    isset($storeValue)
                    && (
                        $storeValueAsBool
                        || Functions::isError($storeValue)
                        || ($storeValue === 'Pruned branch'))
                ) {
                    
                    if (!isset($fakedForBranchPruning['onlyIfNot-' . $onlyIfNotStoreKey])) {
                        $stack->push('Value', 'Pruned branch (only if not ' . $onlyIfNotStoreKey . ') ' . $token);
                        $fakedForBranchPruning['onlyIfNot-' . $onlyIfNotStoreKey] = true;
                    }

                    if (isset($storeKey)) {
                        
                        
                        $branchStore[$storeKey] = 'Pruned branch';
                        $fakedForBranchPruning['onlyIfNot-' . $storeKey] = true;
                        $fakedForBranchPruning['onlyIf-' . $storeKey] = true;
                    }

                    continue;
                }
            }

            
            if (isset(self::$binaryOperators[$token])) {
                
                if (($operand2Data = $stack->pop()) === null) {
                    return $this->raiseFormulaError('Internal error - Operand value missing from stack');
                }
                if (($operand1Data = $stack->pop()) === null) {
                    return $this->raiseFormulaError('Internal error - Operand value missing from stack');
                }

                $operand1 = self::dataTestReference($operand1Data);
                $operand2 = self::dataTestReference($operand2Data);

                
                if ($token == ':') {
                    $this->debugLog->writeDebugLog('Evaluating Range ', $this->showValue($operand1Data['reference']), ' ', $token, ' ', $this->showValue($operand2Data['reference']));
                } else {
                    $this->debugLog->writeDebugLog('Evaluating ', $this->showValue($operand1), ' ', $token, ' ', $this->showValue($operand2));
                }

                
                switch ($token) {
                    
                    case '>':            
                    case '<':            
                    case '>=':            
                    case '<=':            
                    case '=':            
                    case '<>':            
                        $result = $this->executeBinaryComparisonOperation($cellID, $operand1, $operand2, $token, $stack);
                        if (isset($storeKey)) {
                            $branchStore[$storeKey] = $result;
                        }

                        break;
                    
                    case ':':            
                        if (strpos($operand1Data['reference'], '!') !== false) {
                            [$sheet1, $operand1Data['reference']] = Worksheet::extractSheetTitle($operand1Data['reference'], true);
                        } else {
                            $sheet1 = ($pCellParent !== null) ? $pCellWorksheet->getTitle() : '';
                        }

                        [$sheet2, $operand2Data['reference']] = Worksheet::extractSheetTitle($operand2Data['reference'], true);
                        if (empty($sheet2)) {
                            $sheet2 = $sheet1;
                        }

                        if ($sheet1 == $sheet2) {
                            if ($operand1Data['reference'] === null) {
                                if ((trim($operand1Data['value']) != '') && (is_numeric($operand1Data['value']))) {
                                    $operand1Data['reference'] = $pCell->getColumn() . $operand1Data['value'];
                                } elseif (trim($operand1Data['reference']) == '') {
                                    $operand1Data['reference'] = $pCell->getCoordinate();
                                } else {
                                    $operand1Data['reference'] = $operand1Data['value'] . $pCell->getRow();
                                }
                            }
                            if ($operand2Data['reference'] === null) {
                                if ((trim($operand2Data['value']) != '') && (is_numeric($operand2Data['value']))) {
                                    $operand2Data['reference'] = $pCell->getColumn() . $operand2Data['value'];
                                } elseif (trim($operand2Data['reference']) == '') {
                                    $operand2Data['reference'] = $pCell->getCoordinate();
                                } else {
                                    $operand2Data['reference'] = $operand2Data['value'] . $pCell->getRow();
                                }
                            }

                            $oData = array_merge(explode(':', $operand1Data['reference']), explode(':', $operand2Data['reference']));
                            $oCol = $oRow = [];
                            foreach ($oData as $oDatum) {
                                $oCR = Coordinate::coordinateFromString($oDatum);
                                $oCol[] = Coordinate::columnIndexFromString($oCR[0]) - 1;
                                $oRow[] = $oCR[1];
                            }
                            $cellRef = Coordinate::stringFromColumnIndex(min($oCol) + 1) . min($oRow) . ':' . Coordinate::stringFromColumnIndex(max($oCol) + 1) . max($oRow);
                            if ($pCellParent !== null) {
                                $cellValue = $this->extractCellRange($cellRef, $this->spreadsheet->getSheetByName($sheet1), false);
                            } else {
                                return $this->raiseFormulaError('Unable to access Cell Reference');
                            }
                            $stack->push('Cell Reference', $cellValue, $cellRef);
                        } else {
                            $stack->push('Error', Functions::REF(), null);
                        }

                        break;
                    case '+':            
                        $result = $this->executeNumericBinaryOperation($operand1, $operand2, $token, 'plusEquals', $stack);
                        if (isset($storeKey)) {
                            $branchStore[$storeKey] = $result;
                        }

                        break;
                    case '-':            
                        $result = $this->executeNumericBinaryOperation($operand1, $operand2, $token, 'minusEquals', $stack);
                        if (isset($storeKey)) {
                            $branchStore[$storeKey] = $result;
                        }

                        break;
                    case '*':            
                        $result = $this->executeNumericBinaryOperation($operand1, $operand2, $token, 'arrayTimesEquals', $stack);
                        if (isset($storeKey)) {
                            $branchStore[$storeKey] = $result;
                        }

                        break;
                    case '/':            
                        $result = $this->executeNumericBinaryOperation($operand1, $operand2, $token, 'arrayRightDivide', $stack);
                        if (isset($storeKey)) {
                            $branchStore[$storeKey] = $result;
                        }

                        break;
                    case '^':            
                        $result = $this->executeNumericBinaryOperation($operand1, $operand2, $token, 'power', $stack);
                        if (isset($storeKey)) {
                            $branchStore[$storeKey] = $result;
                        }

                        break;
                    case '&':            
                        
                        
                        
                        if (is_bool($operand1)) {
                            $operand1 = ($operand1) ? self::$localeBoolean['TRUE'] : self::$localeBoolean['FALSE'];
                        }
                        if (is_bool($operand2)) {
                            $operand2 = ($operand2) ? self::$localeBoolean['TRUE'] : self::$localeBoolean['FALSE'];
                        }
                        if ((is_array($operand1)) || (is_array($operand2))) {
                            
                            self::checkMatrixOperands($operand1, $operand2, 2);

                            try {
                                
                                $matrix = new Shared\JAMA\Matrix($operand1);
                                
                                $matrixResult = $matrix->concat($operand2);
                                $result = $matrixResult->getArray();
                            } catch (\Exception $ex) {
                                $this->debugLog->writeDebugLog('JAMA Matrix Exception: ', $ex->getMessage());
                                $result = '#VALUE!';
                            }
                        } else {
                            $result = self::FORMULA_STRING_QUOTE . str_replace('""', self::FORMULA_STRING_QUOTE, self::unwrapResult($operand1) . self::unwrapResult($operand2)) . self::FORMULA_STRING_QUOTE;
                        }
                        $this->debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($result));
                        $stack->push('Value', $result);

                        if (isset($storeKey)) {
                            $branchStore[$storeKey] = $result;
                        }

                        break;
                    case '|':            
                        $rowIntersect = array_intersect_key($operand1, $operand2);
                        $cellIntersect = $oCol = $oRow = [];
                        foreach (array_keys($rowIntersect) as $row) {
                            $oRow[] = $row;
                            foreach ($rowIntersect[$row] as $col => $data) {
                                $oCol[] = Coordinate::columnIndexFromString($col) - 1;
                                $cellIntersect[$row] = array_intersect_key($operand1[$row], $operand2[$row]);
                            }
                        }
                        if (count(Functions::flattenArray($cellIntersect)) === 0) {
                            $this->debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($cellIntersect));
                            $stack->push('Error', Functions::null(), null);
                        } else {
                            $cellRef = Coordinate::stringFromColumnIndex(min($oCol) + 1) . min($oRow) . ':' .
                                Coordinate::stringFromColumnIndex(max($oCol) + 1) . max($oRow);
                            $this->debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($cellIntersect));
                            $stack->push('Value', $cellIntersect, $cellRef);
                        }

                        break;
                }

                
            } elseif (($token === '~') || ($token === '%')) {
                if (($arg = $stack->pop()) === null) {
                    return $this->raiseFormulaError('Internal error - Operand value missing from stack');
                }
                $arg = $arg['value'];
                if ($token === '~') {
                    $this->debugLog->writeDebugLog('Evaluating Negation of ', $this->showValue($arg));
                    $multiplier = -1;
                } else {
                    $this->debugLog->writeDebugLog('Evaluating Percentile of ', $this->showValue($arg));
                    $multiplier = 0.01;
                }
                if (is_array($arg)) {
                    self::checkMatrixOperands($arg, $multiplier, 2);

                    try {
                        $matrix1 = new Shared\JAMA\Matrix($arg);
                        $matrixResult = $matrix1->arrayTimesEquals($multiplier);
                        $result = $matrixResult->getArray();
                    } catch (\Exception $ex) {
                        $this->debugLog->writeDebugLog('JAMA Matrix Exception: ', $ex->getMessage());
                        $result = '#VALUE!';
                    }
                    $this->debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($result));
                    $stack->push('Value', $result);
                    if (isset($storeKey)) {
                        $branchStore[$storeKey] = $result;
                    }
                } else {
                    $this->executeNumericBinaryOperation($multiplier, $arg, '*', 'arrayTimesEquals', $stack);
                }
            } elseif (preg_match('/^' . self::CALCULATION_REGEXP_CELLREF . '$/i', $token, $matches)) {
                $cellRef = null;
                if (isset($matches[8])) {
                    if ($pCell === null) {
                        
                        $cellValue = Functions::REF();
                    } else {
                        $cellRef = $matches[6] . $matches[7] . ':' . $matches[9] . $matches[10];
                        if ($matches[2] > '') {
                            $matches[2] = trim($matches[2], "\"'");
                            if ((strpos($matches[2], '[') !== false) || (strpos($matches[2], ']') !== false)) {
                                
                                return $this->raiseFormulaError('Unable to access External Workbook');
                            }
                            $matches[2] = trim($matches[2], "\"'");
                            $this->debugLog->writeDebugLog('Evaluating Cell Range ', $cellRef, ' in worksheet ', $matches[2]);
                            if ($pCellParent !== null) {
                                $cellValue = $this->extractCellRange($cellRef, $this->spreadsheet->getSheetByName($matches[2]), false);
                            } else {
                                return $this->raiseFormulaError('Unable to access Cell Reference');
                            }
                            $this->debugLog->writeDebugLog('Evaluation Result for cells ', $cellRef, ' in worksheet ', $matches[2], ' is ', $this->showTypeDetails($cellValue));
                        } else {
                            $this->debugLog->writeDebugLog('Evaluating Cell Range ', $cellRef, ' in current worksheet');
                            if ($pCellParent !== null) {
                                $cellValue = $this->extractCellRange($cellRef, $pCellWorksheet, false);
                            } else {
                                return $this->raiseFormulaError('Unable to access Cell Reference');
                            }
                            $this->debugLog->writeDebugLog('Evaluation Result for cells ', $cellRef, ' is ', $this->showTypeDetails($cellValue));
                        }
                    }
                } else {
                    if ($pCell === null) {
                        
                        $cellValue = Functions::REF();
                    } else {
                        $cellRef = $matches[6] . $matches[7];
                        if ($matches[2] > '') {
                            $matches[2] = trim($matches[2], "\"'");
                            if ((strpos($matches[2], '[') !== false) || (strpos($matches[2], ']') !== false)) {
                                
                                return $this->raiseFormulaError('Unable to access External Workbook');
                            }
                            $this->debugLog->writeDebugLog('Evaluating Cell ', $cellRef, ' in worksheet ', $matches[2]);
                            if ($pCellParent !== null) {
                                $cellSheet = $this->spreadsheet->getSheetByName($matches[2]);
                                if ($cellSheet && $cellSheet->cellExists($cellRef)) {
                                    $cellValue = $this->extractCellRange($cellRef, $this->spreadsheet->getSheetByName($matches[2]), false);
                                    $pCell->attach($pCellParent);
                                } else {
                                    $cellValue = null;
                                }
                            } else {
                                return $this->raiseFormulaError('Unable to access Cell Reference');
                            }
                            $this->debugLog->writeDebugLog('Evaluation Result for cell ', $cellRef, ' in worksheet ', $matches[2], ' is ', $this->showTypeDetails($cellValue));
                        } else {
                            $this->debugLog->writeDebugLog('Evaluating Cell ', $cellRef, ' in current worksheet');
                            if ($pCellParent->has($cellRef)) {
                                $cellValue = $this->extractCellRange($cellRef, $pCellWorksheet, false);
                                $pCell->attach($pCellParent);
                            } else {
                                $cellValue = null;
                            }
                            $this->debugLog->writeDebugLog('Evaluation Result for cell ', $cellRef, ' is ', $this->showTypeDetails($cellValue));
                        }
                    }
                }
                $stack->push('Value', $cellValue, $cellRef);
                if (isset($storeKey)) {
                    $branchStore[$storeKey] = $cellValue;
                }

                
            } elseif (preg_match('/^' . self::CALCULATION_REGEXP_FUNCTION . '$/miu', $token, $matches)) {
                if ($pCellParent) {
                    $pCell->attach($pCellParent);
                }

                $functionName = $matches[1];
                $argCount = $stack->pop();
                $argCount = $argCount['value'];
                if ($functionName != 'MKMATRIX') {
                    $this->debugLog->writeDebugLog('Evaluating Function ', self::localeFunc($functionName), '() with ', (($argCount == 0) ? 'no' : $argCount), ' argument', (($argCount == 1) ? '' : 's'));
                }
                if ((isset(self::$phpSpreadsheetFunctions[$functionName])) || (isset(self::$controlFunctions[$functionName]))) {    
                    if (isset(self::$phpSpreadsheetFunctions[$functionName])) {
                        $functionCall = self::$phpSpreadsheetFunctions[$functionName]['functionCall'];
                        $passByReference = isset(self::$phpSpreadsheetFunctions[$functionName]['passByReference']);
                        $passCellReference = isset(self::$phpSpreadsheetFunctions[$functionName]['passCellReference']);
                    } elseif (isset(self::$controlFunctions[$functionName])) {
                        $functionCall = self::$controlFunctions[$functionName]['functionCall'];
                        $passByReference = isset(self::$controlFunctions[$functionName]['passByReference']);
                        $passCellReference = isset(self::$controlFunctions[$functionName]['passCellReference']);
                    }
                    
                    $args = $argArrayVals = [];
                    for ($i = 0; $i < $argCount; ++$i) {
                        $arg = $stack->pop();
                        $a = $argCount - $i - 1;
                        if (
                            ($passByReference) &&
                            (isset(self::$phpSpreadsheetFunctions[$functionName]['passByReference'][$a])) &&
                            (self::$phpSpreadsheetFunctions[$functionName]['passByReference'][$a])
                        ) {
                            if ($arg['reference'] === null) {
                                $args[] = $cellID;
                                if ($functionName != 'MKMATRIX') {
                                    $argArrayVals[] = $this->showValue($cellID);
                                }
                            } else {
                                $args[] = $arg['reference'];
                                if ($functionName != 'MKMATRIX') {
                                    $argArrayVals[] = $this->showValue($arg['reference']);
                                }
                            }
                        } else {
                            $args[] = self::unwrapResult($arg['value']);
                            if ($functionName != 'MKMATRIX') {
                                $argArrayVals[] = $this->showValue($arg['value']);
                            }
                        }
                    }

                    
                    krsort($args);

                    if (($passByReference) && ($argCount == 0)) {
                        $args[] = $cellID;
                        $argArrayVals[] = $this->showValue($cellID);
                    }

                    if ($functionName != 'MKMATRIX') {
                        if ($this->debugLog->getWriteDebugLog()) {
                            krsort($argArrayVals);
                            $this->debugLog->writeDebugLog('Evaluating ', self::localeFunc($functionName), '( ', implode(self::$localeArgumentSeparator . ' ', Functions::flattenArray($argArrayVals)), ' )');
                        }
                    }

                    
                    $args = $this->addCellReference($args, $passCellReference, $functionCall, $pCell);

                    if (!is_array($functionCall)) {
                        foreach ($args as &$arg) {
                            $arg = Functions::flattenSingleValue($arg);
                        }
                        unset($arg);
                    }

                    $result = call_user_func_array($functionCall, $args);

                    if ($functionName != 'MKMATRIX') {
                        $this->debugLog->writeDebugLog('Evaluation Result for ', self::localeFunc($functionName), '() function call is ', $this->showTypeDetails($result));
                    }
                    $stack->push('Value', self::wrapResult($result));
                    if (isset($storeKey)) {
                        $branchStore[$storeKey] = $result;
                    }
                }
            } else {
                
                if (isset(self::$excelConstants[strtoupper($token)])) {
                    $excelConstant = strtoupper($token);
                    $stack->push('Constant Value', self::$excelConstants[$excelConstant]);
                    if (isset($storeKey)) {
                        $branchStore[$storeKey] = self::$excelConstants[$excelConstant];
                    }
                    $this->debugLog->writeDebugLog('Evaluating Constant ', $excelConstant, ' as ', $this->showTypeDetails(self::$excelConstants[$excelConstant]));
                } elseif ((is_numeric($token)) || ($token === null) || (is_bool($token)) || ($token == '') || ($token[0] == self::FORMULA_STRING_QUOTE) || ($token[0] == '#')) {
                    $stack->push('Value', $token);
                    if (isset($storeKey)) {
                        $branchStore[$storeKey] = $token;
                    }
                    
                } elseif (preg_match('/^' . self::CALCULATION_REGEXP_DEFINEDNAME . '$/miu', $token, $matches)) {
                    $definedName = $matches[6];
                    if ($pCell === null || $pCellWorksheet === null) {
                        return $this->raiseFormulaError("undefined name '$token'");
                    }

                    $this->debugLog->writeDebugLog('Evaluating Defined Name ', $definedName);
                    $namedRange = DefinedName::resolveName($definedName, $pCellWorksheet);
                    if ($namedRange === null) {
                        return $this->raiseFormulaError("undefined name '$definedName'");
                    }

                    $result = $this->evaluateDefinedName($pCell, $namedRange, $pCellWorksheet, $stack);
                    if (isset($storeKey)) {
                        $branchStore[$storeKey] = $result;
                    }
                } else {
                    return $this->raiseFormulaError("undefined name '$token'");
                }
            }
        }
        
        if ($stack->count() != 1) {
            return $this->raiseFormulaError('internal error');
        }
        $output = $stack->pop();
        $output = $output['value'];

        return $output;
    }

    private function validateBinaryOperand(&$operand, &$stack)
    {
        if (is_array($operand)) {
            if ((count($operand, COUNT_RECURSIVE) - count($operand)) == 1) {
                do {
                    $operand = array_pop($operand);
                } while (is_array($operand));
            }
        }
        
        if (is_string($operand)) {
            
            
            if ($operand > '' && $operand[0] == self::FORMULA_STRING_QUOTE) {
                $operand = self::unwrapResult($operand);
            }
            
            if (!is_numeric($operand)) {
                
                if ($operand > '' && $operand[0] == '#') {
                    $stack->push('Value', $operand);
                    $this->debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($operand));

                    return false;
                } elseif (!Shared\StringHelper::convertToNumberIfFraction($operand)) {
                    
                    $stack->push('Error', '#VALUE!');
                    $this->debugLog->writeDebugLog('Evaluation Result is a ', $this->showTypeDetails('#VALUE!'));

                    return false;
                }
            }
        }

        
        return true;
    }

    
    private function executeBinaryComparisonOperation($cellID, $operand1, $operand2, $operation, Stack &$stack, $recursingArrays = false)
    {
        
        if ((is_array($operand1)) || (is_array($operand2))) {
            $result = [];
            if ((is_array($operand1)) && (!is_array($operand2))) {
                foreach ($operand1 as $x => $operandData) {
                    $this->debugLog->writeDebugLog('Evaluating Comparison ', $this->showValue($operandData), ' ', $operation, ' ', $this->showValue($operand2));
                    $this->executeBinaryComparisonOperation($cellID, $operandData, $operand2, $operation, $stack);
                    $r = $stack->pop();
                    $result[$x] = $r['value'];
                }
            } elseif ((!is_array($operand1)) && (is_array($operand2))) {
                foreach ($operand2 as $x => $operandData) {
                    $this->debugLog->writeDebugLog('Evaluating Comparison ', $this->showValue($operand1), ' ', $operation, ' ', $this->showValue($operandData));
                    $this->executeBinaryComparisonOperation($cellID, $operand1, $operandData, $operation, $stack);
                    $r = $stack->pop();
                    $result[$x] = $r['value'];
                }
            } else {
                if (!$recursingArrays) {
                    self::checkMatrixOperands($operand1, $operand2, 2);
                }
                foreach ($operand1 as $x => $operandData) {
                    $this->debugLog->writeDebugLog('Evaluating Comparison ', $this->showValue($operandData), ' ', $operation, ' ', $this->showValue($operand2[$x]));
                    $this->executeBinaryComparisonOperation($cellID, $operandData, $operand2[$x], $operation, $stack, true);
                    $r = $stack->pop();
                    $result[$x] = $r['value'];
                }
            }
            
            $this->debugLog->writeDebugLog('Comparison Evaluation Result is ', $this->showTypeDetails($result));
            
            $stack->push('Array', $result);

            return $result;
        }

        
        if (is_string($operand1) && $operand1 > '' && $operand1[0] == self::FORMULA_STRING_QUOTE) {
            $operand1 = self::unwrapResult($operand1);
        }
        if (is_string($operand2) && $operand2 > '' && $operand2[0] == self::FORMULA_STRING_QUOTE) {
            $operand2 = self::unwrapResult($operand2);
        }

        
        if (Functions::getCompatibilityMode() != Functions::COMPATIBILITY_OPENOFFICE) {
            if (is_string($operand1)) {
                $operand1 = strtoupper($operand1);
            }
            if (is_string($operand2)) {
                $operand2 = strtoupper($operand2);
            }
        }

        $useLowercaseFirstComparison = is_string($operand1) && is_string($operand2) && Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE;

        
        switch ($operation) {
            
            case '>':
                if ($useLowercaseFirstComparison) {
                    $result = $this->strcmpLowercaseFirst($operand1, $operand2) > 0;
                } else {
                    $result = ($operand1 > $operand2);
                }

                break;
            
            case '<':
                if ($useLowercaseFirstComparison) {
                    $result = $this->strcmpLowercaseFirst($operand1, $operand2) < 0;
                } else {
                    $result = ($operand1 < $operand2);
                }

                break;
            
            case '=':
                if (is_numeric($operand1) && is_numeric($operand2)) {
                    $result = (abs($operand1 - $operand2) < $this->delta);
                } else {
                    $result = strcmp($operand1, $operand2) == 0;
                }

                break;
            
            case '>=':
                if (is_numeric($operand1) && is_numeric($operand2)) {
                    $result = ((abs($operand1 - $operand2) < $this->delta) || ($operand1 > $operand2));
                } elseif ($useLowercaseFirstComparison) {
                    $result = $this->strcmpLowercaseFirst($operand1, $operand2) >= 0;
                } else {
                    $result = strcmp($operand1, $operand2) >= 0;
                }

                break;
            
            case '<=':
                if (is_numeric($operand1) && is_numeric($operand2)) {
                    $result = ((abs($operand1 - $operand2) < $this->delta) || ($operand1 < $operand2));
                } elseif ($useLowercaseFirstComparison) {
                    $result = $this->strcmpLowercaseFirst($operand1, $operand2) <= 0;
                } else {
                    $result = strcmp($operand1, $operand2) <= 0;
                }

                break;
            
            case '<>':
                if (is_numeric($operand1) && is_numeric($operand2)) {
                    $result = (abs($operand1 - $operand2) > 1E-14);
                } else {
                    $result = strcmp($operand1, $operand2) != 0;
                }

                break;
        }

        
        $this->debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($result));
        
        $stack->push('Value', $result);

        return $result;
    }

    
    private function strcmpLowercaseFirst($str1, $str2)
    {
        $inversedStr1 = Shared\StringHelper::strCaseReverse($str1);
        $inversedStr2 = Shared\StringHelper::strCaseReverse($str2);

        return strcmp($inversedStr1, $inversedStr2);
    }

    
    private function executeNumericBinaryOperation($operand1, $operand2, $operation, $matrixFunction, &$stack)
    {
        
        if (!$this->validateBinaryOperand($operand1, $stack)) {
            return false;
        }
        if (!$this->validateBinaryOperand($operand2, $stack)) {
            return false;
        }

        
        
        
        if ((is_array($operand1)) || (is_array($operand2))) {
            
            self::checkMatrixOperands($operand1, $operand2, 2);

            try {
                
                $matrix = new Shared\JAMA\Matrix($operand1);
                
                $matrixResult = $matrix->$matrixFunction($operand2);
                $result = $matrixResult->getArray();
            } catch (\Exception $ex) {
                $this->debugLog->writeDebugLog('JAMA Matrix Exception: ', $ex->getMessage());
                $result = '#VALUE!';
            }
        } else {
            if (
                (Functions::getCompatibilityMode() != Functions::COMPATIBILITY_OPENOFFICE) &&
                ((is_string($operand1) && !is_numeric($operand1) && strlen($operand1) > 0) ||
                    (is_string($operand2) && !is_numeric($operand2) && strlen($operand2) > 0))
            ) {
                $result = Functions::VALUE();
            } else {
                
                switch ($operation) {
                    
                    case '+':
                        $result = $operand1 + $operand2;

                        break;
                    
                    case '-':
                        $result = $operand1 - $operand2;

                        break;
                    
                    case '*':
                        $result = $operand1 * $operand2;

                        break;
                    
                    case '/':
                        if ($operand2 == 0) {
                            
                            $stack->push('Error', '#DIV/0!');
                            $this->debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails('#DIV/0!'));

                            return false;
                        }
                        $result = $operand1 / $operand2;

                        break;
                    
                    case '^':
                        $result = $operand1 ** $operand2;

                        break;
                }
            }
        }

        
        $this->debugLog->writeDebugLog('Evaluation Result is ', $this->showTypeDetails($result));
        
        $stack->push('Value', $result);

        return $result;
    }

    
    protected function raiseFormulaError($errorMessage)
    {
        $this->formulaError = $errorMessage;
        $this->cyclicReferenceStack->clear();
        if (!$this->suppressFormulaErrors) {
            throw new Exception($errorMessage);
        }
        trigger_error($errorMessage, E_USER_ERROR);

        return false;
    }

    
    public function extractCellRange(&$pRange = 'A1', ?Worksheet $pSheet = null, $resetLog = true)
    {
        
        $returnValue = [];

        if ($pSheet !== null) {
            $pSheetName = $pSheet->getTitle();
            if (strpos($pRange, '!') !== false) {
                [$pSheetName, $pRange] = Worksheet::extractSheetTitle($pRange, true);
                $pSheet = $this->spreadsheet->getSheetByName($pSheetName);
            }

            
            $aReferences = Coordinate::extractAllCellReferencesInRange($pRange);
            $pRange = $pSheetName . '!' . $pRange;
            if (!isset($aReferences[1])) {
                $currentCol = '';
                $currentRow = 0;
                
                sscanf($aReferences[0], '%[A-Z]%d', $currentCol, $currentRow);
                if ($pSheet->cellExists($aReferences[0])) {
                    $returnValue[$currentRow][$currentCol] = $pSheet->getCell($aReferences[0])->getCalculatedValue($resetLog);
                } else {
                    $returnValue[$currentRow][$currentCol] = null;
                }
            } else {
                
                foreach ($aReferences as $reference) {
                    $currentCol = '';
                    $currentRow = 0;
                    
                    sscanf($reference, '%[A-Z]%d', $currentCol, $currentRow);
                    if ($pSheet->cellExists($reference)) {
                        $returnValue[$currentRow][$currentCol] = $pSheet->getCell($reference)->getCalculatedValue($resetLog);
                    } else {
                        $returnValue[$currentRow][$currentCol] = null;
                    }
                }
            }
        }

        return $returnValue;
    }

    
    public function extractNamedRange(&$pRange = 'A1', ?Worksheet $pSheet = null, $resetLog = true)
    {
        
        $returnValue = [];

        if ($pSheet !== null) {
            $pSheetName = $pSheet->getTitle();
            if (strpos($pRange, '!') !== false) {
                [$pSheetName, $pRange] = Worksheet::extractSheetTitle($pRange, true);
                $pSheet = $this->spreadsheet->getSheetByName($pSheetName);
            }

            
            $namedRange = DefinedName::resolveName($pRange, $pSheet);
            if ($namedRange === null) {
                return Functions::REF();
            }

            $pSheet = $namedRange->getWorksheet();
            $pRange = $namedRange->getValue();
            $splitRange = Coordinate::splitRange($pRange);
            
            if (ctype_alpha($splitRange[0][0])) {
                $pRange = $splitRange[0][0] . '1:' . $splitRange[0][1] . $namedRange->getWorksheet()->getHighestRow();
            } elseif (ctype_digit($splitRange[0][0])) {
                $pRange = 'A' . $splitRange[0][0] . ':' . $namedRange->getWorksheet()->getHighestColumn() . $splitRange[0][1];
            }

            
            $aReferences = Coordinate::extractAllCellReferencesInRange($pRange);
            if (!isset($aReferences[1])) {
                
                [$currentCol, $currentRow] = Coordinate::coordinateFromString($aReferences[0]);
                if ($pSheet->cellExists($aReferences[0])) {
                    $returnValue[$currentRow][$currentCol] = $pSheet->getCell($aReferences[0])->getCalculatedValue($resetLog);
                } else {
                    $returnValue[$currentRow][$currentCol] = null;
                }
            } else {
                
                foreach ($aReferences as $reference) {
                    
                    [$currentCol, $currentRow] = Coordinate::coordinateFromString($reference);
                    if ($pSheet->cellExists($reference)) {
                        $returnValue[$currentRow][$currentCol] = $pSheet->getCell($reference)->getCalculatedValue($resetLog);
                    } else {
                        $returnValue[$currentRow][$currentCol] = null;
                    }
                }
            }
        }

        return $returnValue;
    }

    
    public function isImplemented($pFunction)
    {
        $pFunction = strtoupper($pFunction);
        $notImplemented = !isset(self::$phpSpreadsheetFunctions[$pFunction]) || (is_array(self::$phpSpreadsheetFunctions[$pFunction]['functionCall']) && self::$phpSpreadsheetFunctions[$pFunction]['functionCall'][1] === 'DUMMY');

        return !$notImplemented;
    }

    
    public function getFunctions()
    {
        return self::$phpSpreadsheetFunctions;
    }

    
    public function getImplementedFunctionNames()
    {
        $returnValue = [];
        foreach (self::$phpSpreadsheetFunctions as $functionName => $function) {
            if ($this->isImplemented($functionName)) {
                $returnValue[] = $functionName;
            }
        }

        return $returnValue;
    }

    
    private function addCellReference(array $args, $passCellReference, $functionCall, ?Cell $pCell = null)
    {
        if ($passCellReference) {
            if (is_array($functionCall)) {
                $className = $functionCall[0];
                $methodName = $functionCall[1];

                $reflectionMethod = new ReflectionMethod($className, $methodName);
                $argumentCount = count($reflectionMethod->getParameters());
                while (count($args) < $argumentCount - 1) {
                    $args[] = null;
                }
            }

            $args[] = $pCell;
        }

        return $args;
    }

    private function getUnusedBranchStoreKey()
    {
        $storeKeyValue = 'storeKey-' . $this->branchStoreKeyCounter;
        ++$this->branchStoreKeyCounter;

        return $storeKeyValue;
    }

    private function getTokensAsString($tokens)
    {
        $tokensStr = array_map(function ($token) {
            $value = $token['value'] ?? 'no value';
            while (is_array($value)) {
                $value = array_pop($value);
            }

            return $value;
        }, $tokens);

        return '[ ' . implode(' | ', $tokensStr) . ' ]';
    }

    
    private function evaluateDefinedName(Cell $pCell, DefinedName $namedRange, Worksheet $pCellWorksheet, Stack $stack)
    {
        $definedNameScope = $namedRange->getScope();
        if ($definedNameScope !== null && $definedNameScope !== $pCellWorksheet) {
            
            $result = Functions::REF();
            $stack->push('Error', $result, $namedRange->getName());

            return $result;
        }

        $definedNameValue = $namedRange->getValue();
        $definedNameType = $namedRange->isFormula() ? 'Formula' : 'Range';
        $definedNameWorksheet = $namedRange->getWorksheet();

        if ($definedNameValue[0] !== '=') {
            $definedNameValue = '=' . $definedNameValue;
        }

        $this->debugLog->writeDebugLog("Defined Name is a {$definedNameType} with a value of {$definedNameValue}");

        $recursiveCalculationCell = ($definedNameWorksheet !== null && $definedNameWorksheet !== $pCellWorksheet)
            ? $definedNameWorksheet->getCell('A1')
            : $pCell;
        $recursiveCalculationCellAddress = $recursiveCalculationCell !== null
            ? $recursiveCalculationCell->getCoordinate()
            : null;

        
        $definedNameValue = self::$referenceHelper->updateFormulaReferencesAnyWorksheet(
            $definedNameValue,
            Coordinate::columnIndexFromString($pCell->getColumn()) - 1,
            $pCell->getRow() - 1
        );

        $this->debugLog->writeDebugLog("Value adjusted for relative references is {$definedNameValue}");

        $recursiveCalculator = new self($this->spreadsheet);
        $recursiveCalculator->getDebugLog()->setWriteDebugLog($this->getDebugLog()->getWriteDebugLog());
        $recursiveCalculator->getDebugLog()->setEchoDebugLog($this->getDebugLog()->getEchoDebugLog());
        $result = $recursiveCalculator->_calculateFormulaValue($definedNameValue, $recursiveCalculationCellAddress, $recursiveCalculationCell);

        if ($this->getDebugLog()->getWriteDebugLog()) {
            $this->debugLog->mergeDebugLog(array_slice($recursiveCalculator->getDebugLog()->getLog(), 3));
            $this->debugLog->writeDebugLog("Evaluation Result for Named {$definedNameType} {$namedRange->getName()} is {$this->showTypeDetails($result)}");
        }

        $stack->push('Defined Name', $result, $namedRange->getName());

        return $result;
    }
}
