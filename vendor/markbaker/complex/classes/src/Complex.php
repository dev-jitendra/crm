<?php


namespace Complex;


class Complex
{
    
    const EULER = 2.7182818284590452353602874713526624977572;

    
    const NUMBER_SPLIT_REGEXP =
        '` ^
            (                                   # Real part
                [-+]?(\d+\.?\d*|\d*\.?\d+)          # Real value (integer or float)
                ([Ee][-+]?[0-2]?\d{1,3})?           # Optional real exponent for scientific format
            )
            (                                   # Imaginary part
                [-+]?(\d+\.?\d*|\d*\.?\d+)          # Imaginary value (integer or float)
                ([Ee][-+]?[0-2]?\d{1,3})?           # Optional imaginary exponent for scientific format
            )?
            (                                   # Imaginary part is optional
                ([-+]?)                             # Imaginary (implicit 1 or -1) only
                ([ij]?)                             # Imaginary i or j - depending on whether mathematical or engineering
            )
        $`uix';

    
    protected $realPart = 0.0;

    
    protected $imaginaryPart = 0.0;

    
    protected $suffix;


    
    private static function parseComplex($complexNumber)
    {
        
        if (is_numeric($complexNumber)) {
            return [$complexNumber, 0, null];
        }

        
        $complexNumber = str_replace(
            ['+-', '-+', '++', '--'],
            ['-', '-', '+', '+'],
            $complexNumber
        );

        
        $validComplex = preg_match(
            self::NUMBER_SPLIT_REGEXP,
            $complexNumber,
            $complexParts
        );

        if (!$validComplex) {
            
            $validComplex = preg_match('/^([\-\+]?)([ij])$/ui', $complexNumber, $complexParts);
            if (!$validComplex) {
                throw new Exception('Invalid complex number');
            }
            
            $imaginary = 1;
            if ($complexParts[1] === '-') {
                $imaginary = 0 - $imaginary;
            }
            return [0, $imaginary, $complexParts[2]];
        }

        
        if (($complexParts[4] === '') && ($complexParts[9] !== '')) {
            if ($complexParts[7] !== $complexParts[9]) {
                $complexParts[4] = 1;
                if ($complexParts[8] === '-') {
                    $complexParts[4] = -1;
                }
            } else {
                
                
                $complexParts[4] = $complexParts[1];
                $complexParts[1] = 0;
            }
        }

        
        return [
            $complexParts[1],
            $complexParts[4],
            !empty($complexParts[9]) ? $complexParts[9] : 'i'
        ];
    }


    public function __construct($realPart = 0.0, $imaginaryPart = null, $suffix = 'i')
    {
        if ($imaginaryPart === null) {
            if (is_array($realPart)) {
                
                list ($realPart, $imaginaryPart, $suffix) = array_values($realPart) + [0.0, 0.0, 'i'];
            } elseif ((is_string($realPart)) || (is_numeric($realPart))) {
                
                list($realPart, $imaginaryPart, $suffix) = self::parseComplex($realPart);
            }
        }

        if ($imaginaryPart != 0.0 && empty($suffix)) {
            $suffix = 'i';
        } elseif ($imaginaryPart == 0.0 && !empty($suffix)) {
            $suffix = '';
        }

        
        $this->realPart = (float) $realPart;
        $this->imaginaryPart = (float) $imaginaryPart;
        $this->suffix = strtolower($suffix);
    }

    
    public function getReal(): float
    {
        return $this->realPart;
    }

    
    public function getImaginary(): float
    {
        return $this->imaginaryPart;
    }

    
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    
    public function isReal(): bool
    {
        return $this->imaginaryPart == 0.0;
    }

    
    public function isComplex(): bool
    {
        return !$this->isReal();
    }

    public function format(): string
    {
        $str = "";
        if ($this->imaginaryPart != 0.0) {
            if (\abs($this->imaginaryPart) != 1.0) {
                $str .= $this->imaginaryPart . $this->suffix;
            } else {
                $str .= (($this->imaginaryPart < 0.0) ? '-' : '') . $this->suffix;
            }
        }
        if ($this->realPart != 0.0) {
            if (($str) && ($this->imaginaryPart > 0.0)) {
                $str = "+" . $str;
            }
            $str = $this->realPart . $str;
        }
        if (!$str) {
            $str = "0.0";
        }

        return $str;
    }

    public function __toString(): string
    {
        return $this->format();
    }

    
    public static function validateComplexArgument($complex): Complex
    {
        if (is_scalar($complex) || is_array($complex)) {
            $complex = new Complex($complex);
        } elseif (!is_object($complex) || !($complex instanceof Complex)) {
            throw new Exception('Value is not a valid complex number');
        }

        return $complex;
    }

    
    public function reverse(): Complex
    {
        return new Complex(
            $this->imaginaryPart,
            $this->realPart,
            ($this->realPart == 0.0) ? null : $this->suffix
        );
    }

    public function invertImaginary(): Complex
    {
        return new Complex(
            $this->realPart,
            $this->imaginaryPart * -1,
            ($this->imaginaryPart == 0.0) ? null : $this->suffix
        );
    }

    public function invertReal(): Complex
    {
        return new Complex(
            $this->realPart * -1,
            $this->imaginaryPart,
            ($this->imaginaryPart == 0.0) ? null : $this->suffix
        );
    }

    protected static $functions = [
        'abs',
        'acos',
        'acosh',
        'acot',
        'acoth',
        'acsc',
        'acsch',
        'argument',
        'asec',
        'asech',
        'asin',
        'asinh',
        'atan',
        'atanh',
        'conjugate',
        'cos',
        'cosh',
        'cot',
        'coth',
        'csc',
        'csch',
        'exp',
        'inverse',
        'ln',
        'log2',
        'log10',
        'negative',
        'pow',
        'rho',
        'sec',
        'sech',
        'sin',
        'sinh',
        'sqrt',
        'tan',
        'tanh',
        'theta',
    ];

    protected static $operations = [
        'add',
        'subtract',
        'multiply',
        'divideby',
        'divideinto',
    ];

    
    public function __call($functionName, $arguments)
    {
        $functionName = strtolower(str_replace('_', '', $functionName));

        
        if (in_array($functionName, self::$functions, true)) {
            $functionName = "\\" . __NAMESPACE__ . "\\{$functionName}";
            return $functionName($this, ...$arguments);
        }
        
        if (in_array($functionName, self::$operations, true)) {
            $functionName = "\\" . __NAMESPACE__ . "\\{$functionName}";
            return $functionName($this, ...$arguments);
        }
        throw new Exception('Complex Function or Operation does not exist');
    }
}
