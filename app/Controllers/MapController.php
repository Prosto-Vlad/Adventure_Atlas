<?php
namespace App\Controllers;

use App\Libraries\PerlinNoise;
use App\Libraries\VoronoiMapGenerator;
use App\Libraries\GridBasedDungeonGenerator;
use App\Models\MapModel;
use App\Models\LocationModel;
use App\Models\IconsModel;


class MapController extends BaseController
{
    private $noiseGenerator;

    public function __construct() {
        $this->noiseGenerator = new PerlinNoise();
    }

    public function generateWorldMap()
    {
        $json = $this->request->getJSON();

        $width = $json->width;
        $height = $json->height;
        
        if (!$width || !$height) {
            return $this->response->setJSON(['success' => false, 'message' => 'Некоректні дані']);
        }

        $scale = 0.01;
        
        $canvas = imagecreatetruecolor($width, $height);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $nx = $x * $scale;
                $ny = $y * $scale;
                $elevation = $this->noiseGenerator->noise($nx, $ny);

                if ($elevation < -0.1) {
                    $color = imagecolorallocate($canvas, 0, 0, 255); 
                } elseif ($elevation < 0) {
                    $color = imagecolorallocate($canvas, 240, 240, 64); 
                } elseif ($elevation < 0.6) {
                    $color = imagecolorallocate($canvas, 34, 139, 34); 
                } else {
                    $color = imagecolorallocate($canvas, 139, 69, 19); 
                }

                imagesetpixel($canvas, $x, $y, $color);
            }
        }

        ob_start(); 
        imagewebp($canvas);
        $imageData = ob_get_contents(); 
        ob_end_clean(); 

        imagedestroy($canvas);

        session()->setFlashdata('imageData', base64_encode($imageData));
        session()->setFlashdata('type_id', 1);

        return $this->response->setJSON(['success' => true, 'redirect' => '/map/view-temp']);
    }

    public function generateCityMap()
    {
        $json = $this->request->getJSON();
        $width = $json->width;
        $height = $json->height;
        $pointCount = $json->pointCount ?? 100;
        $roadWidth = $json->roadWidth ?? 5;
        $wallWidth = $json->wallWidth ?? 2;
        $towerRadius = $json->towerRadius ?? 10;

        $generator = new VoronoiMapGenerator(
            $width, 
            $height,
            $pointCount,
            $roadWidth,
            $wallWidth,
            $towerRadius,
        ); 
        $canvas = $generator->generate();

        ob_start(); 
        imagewebp($canvas);
        $imageData = ob_get_contents(); 
        ob_end_clean(); 

        imagedestroy($canvas);

        session()->setFlashdata('imageData', base64_encode($imageData));
        session()->setFlashdata('type_id', 2);

        return $this->response->setJSON(['success' => true, 'redirect' => '/map/view-temp']);
    }

    public function generateLocationMap()
    {
        $json = $this->request->getJSON();
        $width = $json->width;
        $height = $json->height;
        $gridSize = $json->gridSize;
        $maxRooms = $json->maxRooms ?? 10;
        $roomMinSize = $json->roomMinSize ?? 3;
        $roomMaxSize = $json->roomMaxSize ?? 6;

        $generator = new GridBasedDungeonGenerator($width, $height, $gridSize, $maxRooms, $roomMinSize, $roomMaxSize); 
        $canvas = $generator->generate();

        ob_start(); 
        imagewebp($canvas);
        $imageData = ob_get_contents(); 
        ob_end_clean(); 

        imagedestroy($canvas);

        session()->setFlashdata('imageData', base64_encode($imageData));
        session()->setFlashdata('type_id', 3);

        return $this->response->setJSON(['success' => true, 'redirect' => '/map/view-temp']);
    }

    public function viewTempMap()
    {     
        $imageData = session()->getFlashdata('imageData');
        $typeId = session()->getFlashdata('type_id');
        if (!$imageData) {
            return redirect()->to('/')->with('error', 'Помилка при отриманні тимчасової мапи');
        }

        $imageData = base64_decode($imageData);
        $iconsModel = new \App\Models\IconsModel();
        $icons = $iconsModel->findAll();

        return view('viewTempMap', ['imageDataUrl' => 'data:image/webp;base64,' . base64_encode($imageData), 'icons' => $icons, 'type_id' => $typeId]);
    }

    public function viewMap($id)
    {
        $mapModel = new \App\Models\MapModel();
        $locationModel = new \App\Models\LocationModel();
        $iconModel = new \App\Models\IconsModel();
        $notesModel = new \App\Models\NotesModel();

        $map = $mapModel->find($id);

        if (!$map) {
            return redirect()->to('/gallery')->with('error', 'Мапу не знайдено');
        }

        $locations = $locationModel->select('locations.*, icons.image_path')
                           ->join('icons', 'locations.icon_id = icons.id')
                           ->where('locations.map_id', $id)
                           ->findAll();

        $icons = $iconModel->findAll();     
        
        $notes = $notesModel->where('map_id', $id)->findAll();

        

        return view('viewMap', [
            'id' => $id,
            'map' => $map,
            'locations' => $locations,
            'icons' => $icons,
            'notes' => $notes,
        ]);
    }

    public function saveMap()
    {
        $mapModel = new \App\Models\MapModel();
        $locationModel = new \App\Models\LocationModel();

        $json = $this->request->getJSON();

        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Необхідно увійти в систему']);
        }

        $directoryPath = 'userMaps/' . session()->get('username');
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }

        $imageData = $json->imageData;
        $imagePath = $directoryPath . '/' . $json->name . '.webp';
        $base64String = explode(',', $imageData)[1];
        $imageDataDecoded = base64_decode($base64String);

        if (!file_put_contents($imagePath, $imageDataDecoded)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Не вдалося зберегти зображення']);
        }

        $mapData = [
            'user_id' => $userId,
            'type_id' => $json->type_id,
            'name' => $json->name,
            'image_path' => $imagePath, 
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $mapModel->insert($mapData);
        $mapId = $mapModel->getInsertID();

        if (!$mapId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Помилка під час збереження мапи']);
        }

        foreach ($json->locations as $location) {
            $locationData = [
                'map_id' => $mapId,
                'icon_id' => $location->icon_id,
                'name' => $location->name,
                'coordinates' => json_encode($location->coordinates),
                'size' => $location->size ?? 1, 
            ];
            $locationModel->insert($locationData);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Мапа і локації успішно збережені']);
    }

    public function updateMap($id)
    {
        $mapModel = new \App\Models\MapModel();
        $json = $this->request->getJSON();
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Необхідно увійти в систему']);
        }
        
        $directoryPath = 'userMaps/' . session()->get('username');
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }
        
        $imageData = $json->imageData;
        $imagePath = $directoryPath . '/' . $json->name . '.webp';
        $base64String = explode(',', $imageData)[1];
        $imageDataDecoded = base64_decode($base64String);
        
        if (!file_put_contents($imagePath, $imageDataDecoded)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Не вдалося зберегти зображення']);
        }
        
        $mapData = [
            'image_path' => $imagePath, 
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $mapModel->update($id, $mapData);
        
        $locationModel = new \App\Models\LocationModel();
        $locationModel->where('map_id', $json->id)->delete();
        if($json->locations != null && !empty($json->locations)){
            foreach ($json->locations as $location) {
                $locationData = [
                    'map_id' => $id,
                    'icon_id' => $location->icon_id,
                    'name' => $location->name,
                    'coordinates' => json_encode($location->coordinates),
                    'size' => $location->size ?? 1, 
                ];
                $locationModel->insert($locationData);
            }
        }
        
        $notesModel = new \App\Models\NotesModel();
        $notesModel->where('map_id', $json->id)->delete();
        if($json->notes != null && !empty($json->notes)){
            foreach ($json->notes as $note) {
                $noteData = [
                    'map_id' => $id,
                    'content' => $note->content,
                    'style' => json_encode($note->style), 
                    'coordinates' => json_encode(['x' => $note->style->x, 'y' => $note->style->y]), 
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $notesModel->insert($noteData);
            }
        }
        
        
       
    }

    public function deleteMap($id)
    {
        $mapModel = new \App\Models\MapModel();
        $map = $mapModel->find($id);

        if ($map['user_id'] !== session()->get('user_id')) {
            return redirect()->to('/gallery')->with('error', 'Немає прав на видалення цієї мапи');
        }
        
        if (file_exists($map['image_path'])) {
            unlink($map['image_path']);
        }

        $mapModel->delete($id);

        return redirect()->to('/gallery');
    }
}