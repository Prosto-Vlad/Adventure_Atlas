<?php

namespace App\Libraries;

class GridBasedDungeonGenerator
{
    private $width;
    private $height;
    private $gridSize;
    private $maxRooms;
    private $roomMinSize;
    private $roomMaxSize;
    private $dungeon;
    private $roomOutlineColor;
    private $corridorColor;
    private $gridColor;
    private $rooms = [];

    public function __construct($width, $height, $gridSize, $maxRooms, $roomMinSize, $roomMaxSize)
    {
        $this->width = $width;
        $this->height = $height;
        $this->gridSize = $gridSize;
        $this->maxRooms = $maxRooms;
        $this->roomMinSize = $roomMinSize;
        $this->roomMaxSize = $roomMaxSize;
        $this->dungeon = array_fill(0, $height / $gridSize, array_fill(0, $width / $gridSize, 0));
    }

    public function generate()
    {
        $this->placeRooms();
        $this->connectRoomsMST();
        $this->ensureConnectivity();

        return $this->drawDungeon();
    }

    private function placeRooms()
    {

        for ($i = 0; $i < $this->maxRooms; $i++) {
            $roomWidth = rand($this->roomMinSize, $this->roomMaxSize);
            $roomHeight = rand($this->roomMinSize, $this->roomMaxSize);

            $x = rand(0, floor($this->width / $this->gridSize) - $roomWidth - 1);
            $y = rand(0, floor($this->height / $this->gridSize) - $roomHeight - 1);

            $rect = compact('x', 'y', 'roomWidth', 'roomHeight');

            if ($this->canPlaceRoom($rect)) {
                $this->addRoomToDungeon($rect);
                $this->rooms[] = $rect;
            }
        }
    }

    private function canPlaceRoom($rect)
    {
        for ($i = $rect['x']; $i < $rect['x'] + $rect['roomWidth']; $i++) {
            for ($j = $rect['y']; $j < $rect['y'] + $rect['roomHeight']; $j++) {
                if (isset($this->dungeon[$j][$i]) && $this->dungeon[$j][$i] !== 0) {
                    return false;
                }
            }
        }
        return true;
    }

    private function addRoomToDungeon($rect)
    {
        for ($i = $rect['x']; $i < $rect['x'] + $rect['roomWidth']; $i++) {
            for ($j = $rect['y']; $j < $rect['y'] + $rect['roomHeight']; $j++) {
                $this->dungeon[$j][$i] = 1;
            }
        }
    }

    private function connectRoomsMST()
    {
        $edges = [];

        $centers = $this->getRoomCenters();
        for ($i = 0; $i < count($centers); $i++) {
            for ($j = $i + 1; $j < count($centers); $j++) {
                $cost = $this->manhattanDistance($centers[$i], $centers[$j]);
                $edges[] = [$cost, $i, $j];
            }
        }

        usort($edges, function ($a, $b) {
            return $a[0] - $b[0];
        });

        $parent = array_keys($centers);
        $rank = array_fill(0, count($centers), 0);

        function find($i, & $parent)
        {
            if ($parent[$i] == $i) {
                return $i;
            }
            return $parent[$i] = find($parent[$i], $parent);
        }

        function union($x, $y, & $parent, & $rank)
        {
            $xRoot = find($x, $parent);
            $yRoot = find($y, $parent);

            if ($xRoot == $yRoot) return;

            if ($rank[$xRoot] < $rank[$yRoot]) {
                $parent[$xRoot] = $yRoot;
            } elseif ($rank[$xRoot] > $rank[$yRoot]) {
                $parent[$yRoot] = $xRoot;
            } else {
                $parent[$yRoot] = $xRoot;
                $rank[$xRoot]++;
            }
        }

        foreach ($edges as [$cost, $i, $j]) {
            if (find($i, $parent) !== find($j, $parent)) {
                union($i, $j, $parent, $rank);
                $this->createCorridor($centers[$i], $centers[$j]);
            }
        }
    }

    private function ensureConnectivity()
    {

    }

    private function createCorridor($start, $end)
    {
        $x1 = $start['x'];
        $y1 = $start['y'];
        $x2 = $end['x'];
        $y2 = $end['y'];

        for ($x = min($x1, $x2); $x <= max($x1, $x2); $x++) {
            $this->dungeon[$y1][$x] = 1;
        }

        for ($y = min($y1, $y2); $y <= max($y1, $y2); $y++) {
            $this->dungeon[$y][$x2] = 1;
        }
    }

    private function getRoomCenters()
    {
        $centers = [];
        foreach ($this->rooms as $room) {
            $centers[] = [
                'x' => $room['x'] + floor($room['roomWidth'] / 2),
                'y' => $room['y'] + floor($room['roomHeight'] / 2),
            ];
        }
        return $centers;
    }

    private function manhattanDistance($point1, $point2)
    {
        return abs($point1['x'] - $point2['x']) + abs($point1['y'] - $point2['y']);
    }

    public function addNote($x, $y, $content)
    {
        $this->notes[] = [
            'x' => $x,
            'y' => $y,
            'content' => $content
        ];
    }

    private function drawDungeon()
    {
        $image = imagecreatetruecolor($this->width, $this->height);

        $backgroundColor = imagecolorallocate($image, 200, 200, 200);
        $this->roomOutlineColor = imagecolorallocate($image, 0, 0, 0);
        $this->corridorColor = imagecolorallocate($image, 100, 100, 100);
        $this->gridColor = imagecolorallocate($image, 150, 150, 150);

        imagefill($image, 0, 0, $backgroundColor);

        $this->drawGrid($image);

        foreach ($this->rooms as $room) {
            $x1 = $room['x'] * $this->gridSize;
            $y1 = $room['y'] * $this->gridSize;
            $x2 = ($room['x'] + $room['roomWidth']) * $this->gridSize - 1;
            $y2 = ($room['y'] + $room['roomHeight']) * $this->gridSize - 1;

            imagerectangle($image, $x1, $y1, $x2, $y2, $this->roomOutlineColor);
        }

        for ($y = 0; $y < $this->height / $this->gridSize; $y++) {
            for ($x = 0; $x < $this->width / $this->gridSize; $x++) {
                if ($this->dungeon[$y][$x] === 1) {
                    imagefilledrectangle($image, $x * $this->gridSize, $y * $this->gridSize, ($x + 1) * $this->gridSize - 1, ($y + 1) * $this->gridSize - 1, $this->corridorColor);
                }
            }
        }

        return $image;
    }

    private function drawGrid($image)
    {
        $dashLength = 5;

        for ($x = 0; $x < $this->width; $x += $this->gridSize) {
            for ($y = 0; $y < $this->height; $y += $dashLength * 2) {
                imageline($image, $x, $y, $x, min($this->height, $y + $dashLength), $this->gridColor);
            }
        }

        for ($y = 0; $y < $this->height; $y += $this->gridSize) {
            for ($x = 0; $x < $this->width; $x += $dashLength * 2) {
                imageline($image, $x, $y, min($this->width, $x + $dashLength), $y, $this->gridColor);
            }
        }
    }
}