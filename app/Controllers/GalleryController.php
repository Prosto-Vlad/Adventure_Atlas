<?php

namespace App\Controllers;
use App\Models\MapModel;
use App\Models\MapTypeModel;



class GalleryController extends BaseController
{
    public function index(): string
    {
        $isLoggedIn = session()->get('logged_in');
        $userName = $isLoggedIn ? session()->get('username') : 'Увійти або зареєструватись';

        $mapModel = new MapModel();
        $mapTypeModel = new MapTypeModel();
        $maps = $mapModel->getUserMaps(session()->get('user_id'));
        $mapTypes = $mapTypeModel->findAll();

        $data = [
            'userName' => $userName,
            'isLoggedIn' => $isLoggedIn,
            'maps' => $maps,
            'mapTypes' => $mapTypes
        ];
        


        return view('gallery', $data);
    }
}