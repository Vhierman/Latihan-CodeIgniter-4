<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Home  Web Programming Prima'
        ];
        return view('pages/home', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'About Me'
        ];
        return view('pages/about', $data);
    }

    public function contact()
    {
        $data = [
            'title' => 'Contact Us',
            'alamat' => [
                [
                    'tipe' => 'Rumah',
                    'alamat' => 'Kp.Sarimulya',
                    'kota' => 'Tangerang Selatan'
                ],
                [
                    'tipe' => 'Kantor',
                    'alamat' => 'Taman Tekno BSD',
                    'kota' => 'Tangerang Selatan'
                ]
            ]
        ];
        return view('pages/contact', $data);
    }
    //--------------------------------------------------------------------

}
