<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Post;
use Illuminate\Http\Request;
use PDF;
use Artesaos\SEOTools\Facades\SEOTools;

class HomeController extends Controller
{
    public function index()
    {
        


        $posts = Post::latest()
            ->take(3)
            ->get();
        // foreach($posts as $post){
        //     dd($post->category);
        // }
        return view('welcome', compact('posts'));
        // return view('welcome');
    }

    public function createPdf($key){
        $packa = Package::where('key', '=', $key)->get();
        $package = $packa[0];


        $info = array(
            'Name' => 'Laboratorio Quimico SDM',
            'Location' => 'Potosí - Bolivia',
            'Reason' => 'Certificado Digital de Analisis Quimico de Minerales',
            'ContactInfo' => 'http://www.labsdm.net',
        );
        $view = \Illuminate\Support\Facades\View::make('admin.packages.pdf', compact('package'));

        $html = $view->render();

        PDF::SetMargins(5, 5, 5);
        PDF::SetAutoPageBreak(TRUE, 2);
        PDF::SetTitle('Certificado');
        PDF::AddPage('P', 'A5');
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        PDF::write2DBarcode(url('paquete/pdf/'.$package->key), 'QRCODE,H', 110, 115, 30, 30, $style, 'Q');
        PDF::writeHTML($html, true, false, true, false, '');
        // PDF::Text(80, 205, 'QRCODE H - COLORED');
        PDF::Output('paquete-'.$package->code.'.pdf');
    }

    public function showCert($key){
        $packa = Package::where('key', '=', $key)->get();
        $package = $packa[0];
        return view('packages.cert', compact('package'));
    }
}
