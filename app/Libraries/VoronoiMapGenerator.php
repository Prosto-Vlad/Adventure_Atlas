<?php

namespace App\Libraries;

class VoronoiMapGenerator
{
    private $width;
    private $height;
    private $pointCount;
    private $roadWidth;
    private $wallWidth;
    private $towerRadius;
    private $perlinNoise;

    public function __construct($width = 1000, $height = 600, $pointCount = 100, $roadWidth = 5, $wallWidth = 2, $towerRadius = 10)
    {
        $this->width = $width;
        $this->height = $height;
        $this->pointCount = $pointCount;
        $this->roadWidth = $roadWidth;
        $this->wallWidth = $wallWidth;
        $this->towerRadius = $towerRadius;
        $this->perlinNoise = new PerlinNoise(); // Ініціалізуємо шум Перліна
    }

    public function generate()
    {
        $canvas = imagecreatetruecolor($this->width, $this->height);

        $roadColor = imagecolorallocate($canvas, 150, 150, 150);
        $wallColor = imagecolorallocate($canvas, 102, 51, 0);
        $towerColor = imagecolorallocate($canvas, 150, 75, 0);

        // Заповнення фону травою з вкрапленнями
        $this->createGrassPattern($canvas);

        // Додавання вигнутої дороги до міста
        $this->addCurvedRoadToCity($canvas, $roadColor);

        // Генерація випадкових точок
        $points = [];
        for ($i = 0; $i < $this->pointCount; $i++) {
            do {
                $angle = deg2rad(rand(0, 360));
                $variation = rand(-20, 20);
                $distance = $this->getVaryingRadius($angle, $variation, $i / $this->pointCount);
                $x = ($this->width / 2) + $distance * cos($angle);
                $y = ($this->height / 2) + $distance * sin($angle);
            } while (!$this->isWithinBounds($x, $y));
            $points[] = [$x, $y];
        }

        // Генерація кольорів зон
        $colors = [];
        foreach ($points as $point) {
            $colors[] = imagecolorallocate($canvas, 245, 222, 179);
        }

        // Побудова зон Вороного
        $zoneMap = array_fill(0, $this->width, array_fill(0, $this->height, -1));

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                if ($this->isInsideCustomShape($x, $y)) {
                    $closestPoint = null;
                    $shortestDist = PHP_INT_MAX;

                    foreach ($points as $index => $point) {
                        $dist = pow($point[0] - $x, 2) + pow($point[1] - $y, 2);
                        if ($dist < $shortestDist) {
                            $shortestDist = $dist;
                            $closestPoint = $index;
                        }
                    }

                    $zoneMap[$x][$y] = $closestPoint;
                }
            }
        }

        // Візуалізація карти, включаючи дороги та стіни
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                if ($zoneMap[$x][$y] !== -1) {
                    if ($this->isWallArea($x, $y)) {
                        imagesetpixel($canvas, $x, $y, $wallColor); // Стіна
                    } elseif ($this->isBoundary($zoneMap, $x, $y, $this->roadWidth) && !$this->isWallArea($x, $y)) {
                        imagesetpixel($canvas, $x, $y, $roadColor); // Дорога
                    } else {
                        imagesetpixel($canvas, $x, $y, $colors[$zoneMap[$x][$y]]);
                    }
                }
            }
        }
        
        // Додавання башт
        $this->addTowers($canvas, $towerColor);

        return $canvas;
    }

    private function createGrassPattern($canvas)
    {
        $grassColor = imagecolorallocate($canvas, 14, 124, 14);
        $lightGrassColor = imagecolorallocate($canvas, 34, 139, 34);
        // Різноманітність трави
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $noiseValue = $this->perlinNoise->noise($x * 0.01, $y * 0.01);
                $color = ($noiseValue > 0.01) ? $grassColor : $lightGrassColor;
                imagesetpixel($canvas, $x, $y, $color);
            }
        }
    }

    private function getVaryingRadius($angle, $variation, $t)
    {
        $baseRadius = min($this->width, $this->height) * 0.4;
        $noiseFactor = 50 * $this->perlinNoise->noise(cos($angle) * $t, sin($angle) * $t); // Шум Перліна для модифікації радіусу
        return $baseRadius + $variation + $noiseFactor;
    }

    private function isWithinBounds($x, $y)
    {
        return $x >= 0 && $x < $this->width && $y >= 0 && $y < $this->height;
    }

    private function isInsideCustomShape($x, $y)
    {
        $centerX = $this->width / 2;
        $centerY = $this->height / 2;
        $dx = $x - $centerX;
        $dy = $y - $centerY;

        $distance = sqrt($dx * $dx + $dy * $dy);
        $angle = atan2($dy, $dx);

        $variation = sin($angle * 5) * 20;
        $radius = $this->getVaryingRadius($angle, $variation, 0.5);

        return $distance <= $radius;
    }

    private function isBoundary($zoneMap, $x, $y, $roadWidth)
    {
        $currentZone = $zoneMap[$x][$y];

        // Перевірка сусідніх пікселів
        for ($dx = -$roadWidth; $dx <= $roadWidth; $dx++) {
            for ($dy = -$roadWidth; $dy <= $roadWidth; $dy++) {
                $nx = $x + $dx;
                $ny = $y + $dy;

                if ($nx >= 0 && $nx < $this->width && $ny >= 0 && $ny < $this->height) {
                    if ($zoneMap[$nx][$ny] !== $currentZone && !$this->isWallArea($nx, $ny)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function isWallArea($x, $y)
    {
        $centerX = $this->width / 2;
        $centerY = $this->height / 2;

        $dx = $x - $centerX;
        $dy = $y - $centerY;

        $distance = sqrt($dx * $dx + $dy * $dy);
        $angle = atan2($dy, $dx);

        $variation = sin($angle * 5) * 20;
        $radius = $this->getVaryingRadius($angle, $variation, 0.5);

        // Зона стіни визначає стіну певної ширини (wallWidth)
        return $distance > ($radius - $this->wallWidth) && $distance <= $radius;
    }
    
    private function addTowers($canvas, $towerColor)
    {
        $centerX = $this->width / 2;
        $centerY = $this->height / 2;
        $numTowers = rand(10,15); // Кількість башт

        for ($i = 0; $i < 360; $i += 360 / $numTowers) {
            $angle = deg2rad($i);

            $variation = sin($angle * 5) * 20;
            $radius = $this->getVaryingRadius($angle, $variation, 0.5);
            $x = $centerX + ($radius - $this->wallWidth / 2) * cos($angle);
            $y = $centerY + ($radius - $this->wallWidth / 2) * sin($angle);

            $this->drawCircle($canvas, $x, $y, $this->towerRadius, $towerColor);
        }
    }

    private function addCurvedRoadToCity($canvas, $roadColor)
    {
        $startEdge = rand(0, 3);  // Випадковий вибір краю (0 - зліва, 1 - справа, 2 - зверху, 3 - знизу)
        $startX = $startEdge === 1 ? $this->width - 1 : ($startEdge === 0 ? 0 : rand(0, $this->width - 1));
        $startY = $startEdge === 3 ? $this->height - 1 : ($startEdge === 2 ? 0 : rand(0, $this->height - 1));

        $centerX = $this->width / 2;
        $centerY = $this->height / 2;

        // Генерація вигнутої дороги
        $length = max($centerX, $centerY);
        for ($i = 0; $i < $length; $i++) {
            $t = $i / $length;
            $currentX = (1 - $t) * $startX + $t * $centerX;
            $currentY = (1 - $t) * $startY + $t * $centerY + sin($t * 10) * 50 * (1 - $t);  // Вигин

            for ($dx = -$this->roadWidth; $dx <= $this->roadWidth; $dx++) {
                for ($dy = -$this->roadWidth; $dy <= $this->roadWidth; $dy++) {
                    $px = $currentX + $dx;
                    $py = $currentY + $dy;
                    if ($px >= 0 && $px < $this->width && $py >= 0 && $py < $this->height) {
                        imagesetpixel($canvas, $px, $py, $roadColor);
                    }
                }
            }
        }
    }

    private function drawCircle($canvas, $x_center, $y_center, $radius, $color)
    {
        for ($y = -$radius; $y <= $radius; $y++) {
            for ($x = -$radius; $x <= $radius; $x++) {
                if (($x * $x) + ($y * $y) <= ($radius * $radius)) {
                    imagesetpixel($canvas, $x_center + $x, $y_center + $y, $color);
                }
            }
        }
    }
}