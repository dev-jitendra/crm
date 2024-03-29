<?php

namespace PhpOffice\PhpSpreadsheet\Shared\JAMA;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;


class QRDecomposition
{
    const MATRIX_RANK_EXCEPTION = 'Can only perform operation on full-rank matrix.';

    
    private $QR = [];

    
    private $m;

    
    private $n;

    
    private $Rdiag = [];

    
    public function __construct($A)
    {
        if ($A instanceof Matrix) {
            
            $this->QR = $A->getArray();
            $this->m = $A->getRowDimension();
            $this->n = $A->getColumnDimension();
            
            for ($k = 0; $k < $this->n; ++$k) {
                
                $nrm = 0.0;
                for ($i = $k; $i < $this->m; ++$i) {
                    $nrm = hypo($nrm, $this->QR[$i][$k]);
                }
                if ($nrm != 0.0) {
                    
                    if ($this->QR[$k][$k] < 0) {
                        $nrm = -$nrm;
                    }
                    for ($i = $k; $i < $this->m; ++$i) {
                        $this->QR[$i][$k] /= $nrm;
                    }
                    $this->QR[$k][$k] += 1.0;
                    
                    for ($j = $k + 1; $j < $this->n; ++$j) {
                        $s = 0.0;
                        for ($i = $k; $i < $this->m; ++$i) {
                            $s += $this->QR[$i][$k] * $this->QR[$i][$j];
                        }
                        $s = -$s / $this->QR[$k][$k];
                        for ($i = $k; $i < $this->m; ++$i) {
                            $this->QR[$i][$j] += $s * $this->QR[$i][$k];
                        }
                    }
                }
                $this->Rdiag[$k] = -$nrm;
            }
        } else {
            throw new CalculationException(Matrix::ARGUMENT_TYPE_EXCEPTION);
        }
    }

    

    
    public function isFullRank()
    {
        for ($j = 0; $j < $this->n; ++$j) {
            if ($this->Rdiag[$j] == 0) {
                return false;
            }
        }

        return true;
    }

    

    
    public function getH()
    {
        $H = [];
        for ($i = 0; $i < $this->m; ++$i) {
            for ($j = 0; $j < $this->n; ++$j) {
                if ($i >= $j) {
                    $H[$i][$j] = $this->QR[$i][$j];
                } else {
                    $H[$i][$j] = 0.0;
                }
            }
        }

        return new Matrix($H);
    }

    

    
    public function getR()
    {
        $R = [];
        for ($i = 0; $i < $this->n; ++$i) {
            for ($j = 0; $j < $this->n; ++$j) {
                if ($i < $j) {
                    $R[$i][$j] = $this->QR[$i][$j];
                } elseif ($i == $j) {
                    $R[$i][$j] = $this->Rdiag[$i];
                } else {
                    $R[$i][$j] = 0.0;
                }
            }
        }

        return new Matrix($R);
    }

    

    
    public function getQ()
    {
        $Q = [];
        for ($k = $this->n - 1; $k >= 0; --$k) {
            for ($i = 0; $i < $this->m; ++$i) {
                $Q[$i][$k] = 0.0;
            }
            $Q[$k][$k] = 1.0;
            for ($j = $k; $j < $this->n; ++$j) {
                if ($this->QR[$k][$k] != 0) {
                    $s = 0.0;
                    for ($i = $k; $i < $this->m; ++$i) {
                        $s += $this->QR[$i][$k] * $Q[$i][$j];
                    }
                    $s = -$s / $this->QR[$k][$k];
                    for ($i = $k; $i < $this->m; ++$i) {
                        $Q[$i][$j] += $s * $this->QR[$i][$k];
                    }
                }
            }
        }

        return new Matrix($Q);
    }

    

    
    public function solve($B)
    {
        if ($B->getRowDimension() == $this->m) {
            if ($this->isFullRank()) {
                
                $nx = $B->getColumnDimension();
                $X = $B->getArrayCopy();
                
                for ($k = 0; $k < $this->n; ++$k) {
                    for ($j = 0; $j < $nx; ++$j) {
                        $s = 0.0;
                        for ($i = $k; $i < $this->m; ++$i) {
                            $s += $this->QR[$i][$k] * $X[$i][$j];
                        }
                        $s = -$s / $this->QR[$k][$k];
                        for ($i = $k; $i < $this->m; ++$i) {
                            $X[$i][$j] += $s * $this->QR[$i][$k];
                        }
                    }
                }
                
                for ($k = $this->n - 1; $k >= 0; --$k) {
                    for ($j = 0; $j < $nx; ++$j) {
                        $X[$k][$j] /= $this->Rdiag[$k];
                    }
                    for ($i = 0; $i < $k; ++$i) {
                        for ($j = 0; $j < $nx; ++$j) {
                            $X[$i][$j] -= $X[$k][$j] * $this->QR[$i][$k];
                        }
                    }
                }
                $X = new Matrix($X);

                return $X->getMatrix(0, $this->n - 1, 0, $nx);
            }

            throw new CalculationException(self::MATRIX_RANK_EXCEPTION);
        }

        throw new CalculationException(Matrix::MATRIX_DIMENSION_EXCEPTION);
    }
}
