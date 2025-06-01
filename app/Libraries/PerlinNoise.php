<?php

namespace App\Libraries;

class PerlinNoise
{
    private static $grad3 = [[1,1,0],[-1,1,0],[1,-1,0],[-1,-1,0],
                             [1,0,1],[-1,0,1],[1,0,-1],[-1,0,-1],
                             [0,1,1],[0,-1,1],[0,1,-1],[0,-1,-1]];

    private static $p = [];
    private static $perm = [];
    private static $permMod12 = [];

    public function __construct() {
        self::init();
    }

    private static function init() {
        self::$p = [];
        self::$perm = [];
        self::$permMod12 = [];

        for ($i = 0; $i < 256; $i++) {
            self::$p[$i] = mt_rand(0, 255);
        }

        for ($i = 0; $i < 512; $i++) {
            self::$perm[$i] = self::$p[$i & 255];
            self::$permMod12[$i] = self::$perm[$i] % 12;
        }
    }

    public function noise($xin, $yin) {
        $G2 = (3.0 - sqrt(3.0)) / 6.0;

        $s = ($xin + $yin) * 0.5 * (sqrt(3.0) - 1.0);
        $i = floor($xin + $s);
        $j = floor($yin + $s);
        $t = ($i + $j) * $G2;

        $X0 = $i - $t;
        $Y0 = $j - $t;
        $x0 = $xin - $X0;
        $y0 = $yin - $Y0;

        $i1 = ($x0 > $y0) ? 1 : 0;
        $j1 = ($x0 > $y0) ? 0 : 1;

        $x1 = $x0 - $i1 + $G2;
        $y1 = $y0 - $j1 + $G2;
        $x2 = $x0 - 1.0 + 2.0 * $G2;
        $y2 = $y0 - 1.0 + 2.0 * $G2;

        $ii = $i & 255;
        $jj = $j & 255;

        $gi0 = self::$permMod12[$ii + self::$perm[$jj]];
        $gi1 = self::$permMod12[$ii + $i1 + self::$perm[$jj + $j1]];
        $gi2 = self::$permMod12[$ii + 1 + self::$perm[$jj + 1]];

        $n0 = 0.5 - $x0 * $x0 - $y0 * $y0;
        if ($n0 < 0) {
            $n0 = 0.0;
        } else {
            $n0 *= $n0;
            $n0 *= $n0 * self::dot(self::$grad3[$gi0], $x0, $y0);
        }

        $n1 = 0.5 - $x1 * $x1 - $y1 * $y1;
        if ($n1 < 0) {
            $n1 = 0.0;
        } else {
            $n1 *= $n1;
            $n1 *= $n1 * self::dot(self::$grad3[$gi1], $x1, $y1);
        }

        $n2 = 0.5 - $x2 * $x2 - $y2 * $y2;
        if ($n2 < 0) {
            $n2 = 0.0;
        } else {
            $n2 *= $n2;
            $n2 *= $n2 * self::dot(self::$grad3[$gi2], $x2, $y2);
        }

        return 70.0 * ($n0 + $n1 + $n2);
    }

    private static function dot($g, $x, $y) {
        return $g[0] * $x + $g[1] * $y;
    }
}