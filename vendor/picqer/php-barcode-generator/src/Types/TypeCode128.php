<?php

namespace Picqer\Barcode\Types;

use Picqer\Barcode\Barcode;
use Picqer\Barcode\BarcodeBar;
use Picqer\Barcode\Exceptions\BarcodeException;
use Picqer\Barcode\Exceptions\InvalidCharacterException;
use Picqer\Barcode\Exceptions\InvalidLengthException;



class TypeCode128 implements TypeInterface
{
    protected $type = null;

    protected $conversionTable = [
        '212222', 
        '222122', 
        '222221', 
        '121223', 
        '121322', 
        '131222', 
        '122213', 
        '122312', 
        '132212', 
        '221213', 
        '221312', 
        '231212', 
        '112232', 
        '122132', 
        '122231', 
        '113222', 
        '123122', 
        '123221', 
        '223211', 
        '221132', 
        '221231', 
        '213212', 
        '223112', 
        '312131', 
        '311222', 
        '321122', 
        '321221', 
        '312212', 
        '322112', 
        '322211', 
        '212123', 
        '212321', 
        '232121', 
        '111323', 
        '131123', 
        '131321', 
        '112313', 
        '132113', 
        '132311', 
        '211313', 
        '231113', 
        '231311', 
        '112133', 
        '112331', 
        '132131', 
        '113123', 
        '113321', 
        '133121', 
        '313121', 
        '211331', 
        '231131', 
        '213113', 
        '213311', 
        '213131', 
        '311123', 
        '311321', 
        '331121', 
        '312113', 
        '312311', 
        '332111', 
        '314111', 
        '221411', 
        '431111', 
        '111224', 
        '111422', 
        '121124', 
        '121421', 
        '141122', 
        '141221', 
        '112214', 
        '112412', 
        '122114', 
        '122411', 
        '142112', 
        '142211', 
        '241211', 
        '221114', 
        '413111', 
        '241112', 
        '134111', 
        '111242', 
        '121142', 
        '121241', 
        '114212', 
        '124112', 
        '124211', 
        '411212', 
        '421112', 
        '421211', 
        '212141', 
        '214121', 
        '412121', 
        '111143', 
        '111341', 
        '131141', 
        '114113', 
        '114311', 
        '411113', 
        '411311', 
        '113141', 
        '114131', 
        '311141', 
        '411131', 
        '211412', 
        '211214', 
        '211232', 
        '233111', 
        '200000'  
    ];

    public function getBarcodeData(string $code): Barcode
    {
        if (strlen(trim($code)) === 0) {
            throw new InvalidLengthException('You should provide a barcode string.');
        }

        
        $keys_a = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_';
        $keys_a .= chr(0) . chr(1) . chr(2) . chr(3) . chr(4) . chr(5) . chr(6) . chr(7) . chr(8) . chr(9);
        $keys_a .= chr(10) . chr(11) . chr(12) . chr(13) . chr(14) . chr(15) . chr(16) . chr(17) . chr(18) . chr(19);
        $keys_a .= chr(20) . chr(21) . chr(22) . chr(23) . chr(24) . chr(25) . chr(26) . chr(27) . chr(28) . chr(29);
        $keys_a .= chr(30) . chr(31);

        
        $keys_b = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~' . chr(127);

        
        $fnc_a = [241 => 102, 242 => 97, 243 => 96, 244 => 101];
        $fnc_b = [241 => 102, 242 => 97, 243 => 96, 244 => 100];

        
        $code_data = [];

        
        $len = strlen($code);

        switch (strtoupper($this->type ?? "")) {
            case 'A':
                $startid = 103;
                for ($i = 0; $i < $len; ++$i) {
                    $char = $code[$i];
                    $char_id = ord($char);
                    if (($char_id >= 241) AND ($char_id <= 244)) {
                        $code_data[] = $fnc_a[$char_id];
                    } elseif ($char_id <= 95) {
                        $code_data[] = strpos($keys_a, $char);
                    } else {
                        throw new InvalidCharacterException('Char ' . $char . ' is unsupported');
                    }
                }
                break;

            case 'B':
                $startid = 104;
                for ($i = 0; $i < $len; ++$i) {
                    $char = $code[$i];
                    $char_id = ord($char);
                    if (($char_id >= 241) AND ($char_id <= 244)) {
                        $code_data[] = $fnc_b[$char_id];
                    } elseif (($char_id >= 32) AND ($char_id <= 127)) {
                        $code_data[] = strpos($keys_b, $char);
                    } else {
                        throw new InvalidCharacterException('Char ' . $char . ' is unsupported');
                    }
                }
                break;

            case 'C':
                $startid = 105;
                if (ord($code[0]) == 241) {
                    $code_data[] = 102;
                    $code = substr($code, 1);
                    --$len;
                }
                if (($len % 2) != 0) {
                    throw new InvalidLengthException('Length must be even');
                }
                for ($i = 0; $i < $len; $i += 2) {
                    $chrnum = $code[$i] . $code[$i + 1];
                    if (preg_match('/([0-9]{2})/', $chrnum) > 0) {
                        $code_data[] = intval($chrnum);
                    } else {
                        throw new InvalidCharacterException();
                    }
                }
                break;

            default:
                
                $sequence = [];
                
                $numseq = [];
                preg_match_all('/([0-9]{4,})/', $code, $numseq, PREG_OFFSET_CAPTURE);
                if (isset($numseq[1]) AND ! empty($numseq[1])) {
                    $end_offset = 0;
                    foreach ($numseq[1] as $val) {
                        $offset = $val[1];

                        
                        $slen = strlen($val[0]);
                        if (($slen % 2) != 0) {
                            
                            ++$offset;
                            $val[0] = substr($val[0], 1);
                        }

                        if ($offset > $end_offset) {
                            
                            $sequence = array_merge($sequence,
                                $this->get128ABsequence(substr($code, $end_offset, ($offset - $end_offset))));
                        }
                        
                        $slen = strlen($val[0]);
                        if (($slen % 2) != 0) {
                            
                            --$slen;
                        }
                        $sequence[] = ['C', substr($code, $offset, $slen), $slen];
                        $end_offset = $offset + $slen;
                    }
                    if ($end_offset < $len) {
                        $sequence = array_merge($sequence, $this->get128ABsequence(substr($code, $end_offset)));
                    }
                } else {
                    
                    $sequence = array_merge($sequence, $this->get128ABsequence($code));
                }

                
                foreach ($sequence as $key => $seq) {
                    switch ($seq[0]) {
                        case 'A':
                            if ($key == 0) {
                                $startid = 103;
                            } elseif ($sequence[($key - 1)][0] != 'A') {
                                if (($seq[2] == 1) AND ($key > 0) AND ($sequence[($key - 1)][0] == 'B') AND (! isset($sequence[($key - 1)][3]))) {
                                    
                                    $code_data[] = 98;
                                    
                                    $sequence[$key][3] = true;
                                } elseif (! isset($sequence[($key - 1)][3])) {
                                    $code_data[] = 101;
                                }
                            }
                            for ($i = 0; $i < $seq[2]; ++$i) {
                                $char = $seq[1][$i];
                                $char_id = ord($char);
                                if (($char_id >= 241) AND ($char_id <= 244)) {
                                    $code_data[] = $fnc_a[$char_id];
                                } else {
                                    $code_data[] = strpos($keys_a, $char);
                                }
                            }
                            break;

                        case 'B':
                            if ($key == 0) {
                                $tmpchr = ord($seq[1][0]);
                                if (($seq[2] == 1) AND ($tmpchr >= 241) AND ($tmpchr <= 244) AND isset($sequence[($key + 1)]) AND ($sequence[($key + 1)][0] != 'B')) {
                                    switch ($sequence[($key + 1)][0]) {
                                        case 'A':
                                        {
                                            $startid = 103;
                                            $sequence[$key][0] = 'A';
                                            $code_data[] = $fnc_a[$tmpchr];
                                            break;
                                        }
                                        case 'C':
                                        {
                                            $startid = 105;
                                            $sequence[$key][0] = 'C';
                                            $code_data[] = $fnc_a[$tmpchr];
                                            break;
                                        }
                                    }
                                    break;
                                } else {
                                    $startid = 104;
                                }
                            } elseif ($sequence[($key - 1)][0] != 'B') {
                                if (($seq[2] == 1) AND ($key > 0) AND ($sequence[($key - 1)][0] == 'A') AND (! isset($sequence[($key - 1)][3]))) {
                                    
                                    $code_data[] = 98;
                                    
                                    $sequence[$key][3] = true;
                                } elseif (! isset($sequence[($key - 1)][3])) {
                                    $code_data[] = 100;
                                }
                            }
                            for ($i = 0; $i < $seq[2]; ++$i) {
                                $char = $seq[1][$i];
                                $char_id = ord($char);
                                if (($char_id >= 241) AND ($char_id <= 244)) {
                                    $code_data[] = $fnc_b[$char_id];
                                } else {
                                    $code_data[] = strpos($keys_b, $char);
                                }
                            }
                            break;

                        case 'C':
                            if ($key == 0) {
                                $startid = 105;
                            } elseif ($sequence[($key - 1)][0] != 'C') {
                                $code_data[] = 99;
                            }
                            for ($i = 0; $i < $seq[2]; $i += 2) {
                                $chrnum = $seq[1][$i] . $seq[1][$i + 1];
                                $code_data[] = intval($chrnum);
                            }
                            break;

                        default:
                            throw new InvalidCharacterException('Do not support different mode then A, B or C.');
                    }
                }
        }

        
        if (! isset($startid)) {
            throw new BarcodeException('Could not determine start char for barcode.');
        }

        $sum = $startid;
        foreach ($code_data as $key => $val) {
            $sum += ($val * ($key + 1));
        }
        
        $code_data[] = ($sum % 103);
        
        $code_data[] = 106;
        $code_data[] = 107;
        
        array_unshift($code_data, $startid);

        
        $barcode = new Barcode($code);
        foreach ($code_data as $val) {
            $seq = $this->conversionTable[$val];
            for ($j = 0; $j < 6; ++$j) {
                if (($j % 2) == 0) {
                    $t = true; 
                } else {
                    $t = false; 
                }
                $w = $seq[$j];

                $barcode->addBar(new BarcodeBar($w, 1, $t));
            }
        }

        return $barcode;
    }


    
    protected function get128ABsequence($code)
    {
        $len = strlen($code);
        $sequence = [];
        
        $numseq = [];
        preg_match_all('/([\x00-\x1f])/', $code, $numseq, PREG_OFFSET_CAPTURE);
        if (isset($numseq[1]) AND ! empty($numseq[1])) {
            $end_offset = 0;
            foreach ($numseq[1] as $val) {
                $offset = $val[1];
                if ($offset > $end_offset) {
                    
                    $sequence[] = [
                        'B',
                        substr($code, $end_offset, ($offset - $end_offset)),
                        ($offset - $end_offset)
                    ];
                }
                
                $slen = strlen($val[0]);
                $sequence[] = ['A', substr($code, $offset, $slen), $slen];
                $end_offset = $offset + $slen;
            }
            if ($end_offset < $len) {
                $sequence[] = ['B', substr($code, $end_offset), ($len - $end_offset)];
            }
        } else {
            
            $sequence[] = ['B', $code, $len];
        }

        return $sequence;
    }
}
