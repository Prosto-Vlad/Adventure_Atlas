<?php

namespace App\Controllers;
use App\Models\IconsModel;


class Home extends BaseController
{
    public function index(): string
    {
        $iconModel = new IconsModel();

        $icons = $iconModel->getAll();

        $data = [
            'icons' => $icons
        ];

        return view('home', $data);
    }
}
