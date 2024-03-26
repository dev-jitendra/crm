<?php

namespace Picqer\Barcode\Types;

use Picqer\Barcode\Barcode;
use Picqer\Barcode\BarcodeBar;
use Picqer\Barcode\Exceptions\InvalidCharacterException;



class TypeCode93 implements TypeInterface
{
    protected $conversionTable = [
        48 => '131112', 
        49 => '111213', 
        50 => '111312', 
        51 => '111411', 
        52 => '121113', 
        53 => '121212', 
        54 => '121311', 
        55 => '111114', 
        56 => '131211', 
        57 => '141111', 
        65 => '211113', 
        66 => '211212', 
        67 => '211311', 
        68 => '221112', 
        69 => '221211', 
        70 => '231111', 
        71 => '112113', 
        72 => '112212', 
        73 => '112311', 
        74 => '122112', 
        75 => '132111', 
        76 => '111123', 
        77 => '111222', 
        78 => '111321', 
        79 => '121122', 
        80 => '131121', 
        81 => '212112', 
        82 => '212211', 
        83 => '211122', 
        84 => '211221', 
        85 => '221121', 
        86 => '222111', 
        87 => '112122', 
        88 => '112221', 
        89 => '122121', 
        90 => '123111', 
        45 => '121131', 
        46 => '311112', 
        32 => '311211', 
        36 => '321111', 
        47 => '112131', 
        43 => '113121', 
        37 => '211131', 
        97 => '121221', 
        98 => '312111', 
        99 => '311121', 
        100 => '122211', 
        42 => '111141', 
    ];

    public function getBarcodeData(string $code): Barcode
    {
        $encode = [
            chr(0) => 'bU',
            chr(1) => 'aA',
            chr(2) => 'aB',
            chr(3) => 'aC',
            chr(4) => 'aD',
            chr(5) => 'aE',
            chr(6) => 'aF',
            chr(7) => 'aG',
            chr(8) => 'aH',
            chr(9) => 'aI',
            chr(10) => 'aJ',
            chr(11) => 'aK',
            chr(12) => 'aL',
            chr(13) => 'aM',
            chr(14) => 'aN',
            chr(15) => 'aO',
            chr(16) => 'aP',
            chr(17) => 'aQ',
            chr(18) => 'aR',
            chr(19) => 'aS',
            chr(20) => 'aT',
            chr(21) => 'aU',
            chr(22) => 'aV',
            chr(23) => 'aW',
            chr(24) => 'aX',
            chr(25) => 'aY',
            chr(26) => 'aZ',
            chr(27) => 'bA',
            chr(28) => 'bB',
            chr(29) => 'bC',
            chr(30) => 'bD',
            chr(31) => 'bE',
            chr(32) => ' ',
            chr(33) => 'cA',
            chr(34) => 'cB',
            chr(35) => 'cC',
            chr(36) => '$',
            chr(37) => '%',
            chr(38) => 'cF',
            chr(39) => 'cG',
            chr(40) => 'cH',
            chr(41) => 'cI',
            chr(42) => 'cJ',
            chr(43) => '+',
            chr(44) => 'cL',
            chr(45) => '-',
            chr(46) => '.',
            chr(47) => '/',
            chr(48) => '0',
            chr(49) => '1',
            chr(50) => '2',
            chr(51) => '3',
            chr(52) => '4',
            chr(53) => '5',
            chr(54) => '6',
            chr(55) => '7',
            chr(56) => '8',
            chr(57) => '9',
            chr(58) => 'cZ',
            chr(59) => 'bF',
            chr(60) => 'bG',
            chr(61) => 'bH',
            chr(62) => 'bI',
            chr(63) => 'bJ',
            chr(64) => 'bV',
            chr(65) => 'A',
            chr(66) => 'B',
            chr(67) => 'C',
            chr(68) => 'D',
            chr(69) => 'E',
            chr(70) => 'F',
            chr(71) => 'G',
            chr(72) => 'H',
            chr(73) => 'I',
            chr(74) => 'J',
            chr(75) => 'K',
            chr(76) => 'L',
            chr(77) => 'M',
            chr(78) => 'N',
            chr(79) => 'O',
            chr(80) => 'P',
            chr(81) => 'Q',
            chr(82) => 'R',
            chr(83) => 'S',
            chr(84) => 'T',
            chr(85) => 'U',
            chr(86) => 'V',
            chr(87) => 'W',
            chr(88) => 'X',
            chr(89) => 'Y',
            chr(90) => 'Z',
            chr(91) => 'bK',
            chr(92) => 'bL',
            chr(93) => 'bM',
            chr(94) => 'bN',
            chr(95) => 'bO',
            chr(96) => 'bW',
            chr(97) => 'dA',
            chr(98) => 'dB',
            chr(99) => 'dC',
            chr(100) => 'dD',
            chr(101) => 'dE',
            chr(102) => 'dF',
            chr(103) => 'dG',
            chr(104) => 'dH',
            chr(105) => 'dI',
            chr(106) => 'dJ',
            chr(107) => 'dK',
            chr(108) => 'dL',
            chr(109) => 'dM',
            chr(110) => 'dN',
            chr(111) => 'dO',
            chr(112) => 'dP',
            chr(113) => 'dQ',
            chr(114) => 'dR',
            chr(115) => 'dS',
            chr(116) => 'dT',
            chr(117) => 'dU',
            chr(118) => 'dV',
            chr(119) => 'dW',
            chr(120) => 'dX',
            chr(121) => 'dY',
            chr(122) => 'dZ',
            chr(123) => 'bP',
            chr(124) => 'bQ',
            chr(125) => 'bR',
            chr(126) => 'bS',
            chr(127) => 'bT',
        ];

        $code_ext = '';
        $clen = strlen($code);
        for ($i = 0; $i < $clen; ++$i) {
            if (ord($code[$i]) > 127) {
                throw new InvalidCharacterException('Only supports till char 127');
            }
            $code_ext .= $encode[$code[$i]];
        }

        
        $code_ext .= $this->checksum_code93($code_ext);

        
        $code = '*' . $code_ext . '*';

        $barcode = new Barcode($code);

        for ($i = 0; $i < strlen($code); ++$i) {
            $char = ord($code[$i]);
            if (! isset($this->conversionTable[$char])) {
                throw new InvalidCharacterException('Char ' . $char . ' is unsupported');
            }

            for ($j = 0; $j < 6; ++$j) {
                if (($j % 2) == 0) {
                    $drawBar = true;
                } else {
                    $drawBar = false;
                }
                $barWidth = $this->conversionTable[$char][$j];

                $barcode->addBar(new BarcodeBar($barWidth, 1, $drawBar));
            }
        }

        $barcode->addBar(new BarcodeBar(1, 1, true));

        return $barcode;
    }

    
    protected function checksum_code93($code)
    {
        $chars = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%', 'a', 'b', 'c', 'd'];

        
        $len = strlen($code);
        $p = 1;
        $check = 0;
        for ($i = ($len - 1); $i >= 0; --$i) {
            $k = array_keys($chars, $code[$i]);
            $check += ($k[0] * $p);
            ++$p;
            if ($p > 20) {
                $p = 1;
            }
        }
        $check %= 47;
        $c = $chars[$check];
        $code .= $c;

        
        $p = 1;
        $check = 0;
        for ($i = $len; $i >= 0; --$i) {
            $k = array_keys($chars, $code[$i]);
            $check += ($k[0] * $p);
            ++$p;
            if ($p > 15) {
                $p = 1;
            }
        }
        $check %= 47;
        $k = $chars[$check];

        $checksum = $c . $k;

        return $checksum;
    }
}
