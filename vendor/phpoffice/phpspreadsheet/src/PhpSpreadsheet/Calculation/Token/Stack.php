<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Token;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class Stack
{
    
    private $stack = [];

    
    private $count = 0;

    
    public function count()
    {
        return $this->count;
    }

    
    public function push(
        $type,
        $value,
        $reference = null,
        $storeKey = null,
        $onlyIf = null,
        $onlyIfNot = null
    ): void {
        $stackItem = $this->getStackItem($type, $value, $reference, $storeKey, $onlyIf, $onlyIfNot);

        $this->stack[$this->count++] = $stackItem;

        if ($type == 'Function') {
            $localeFunction = Calculation::localeFunc($value);
            if ($localeFunction != $value) {
                $this->stack[($this->count - 1)]['localeValue'] = $localeFunction;
            }
        }
    }

    public function getStackItem(
        $type,
        $value,
        $reference = null,
        $storeKey = null,
        $onlyIf = null,
        $onlyIfNot = null
    ) {
        $stackItem = [
            'type' => $type,
            'value' => $value,
            'reference' => $reference,
        ];

        if (isset($storeKey)) {
            $stackItem['storeKey'] = $storeKey;
        }

        if (isset($onlyIf)) {
            $stackItem['onlyIf'] = $onlyIf;
        }

        if (isset($onlyIfNot)) {
            $stackItem['onlyIfNot'] = $onlyIfNot;
        }

        return $stackItem;
    }

    
    public function pop()
    {
        if ($this->count > 0) {
            return $this->stack[--$this->count];
        }

        return null;
    }

    
    public function last($n = 1)
    {
        if ($this->count - $n < 0) {
            return null;
        }

        return $this->stack[$this->count - $n];
    }

    
    public function clear(): void
    {
        $this->stack = [];
        $this->count = 0;
    }

    public function __toString()
    {
        $str = 'Stack: ';
        foreach ($this->stack as $index => $item) {
            if ($index > $this->count - 1) {
                break;
            }
            $value = $item['value'] ?? 'no value';
            while (is_array($value)) {
                $value = array_pop($value);
            }
            $str .= $value . ' |> ';
        }

        return $str;
    }
}
