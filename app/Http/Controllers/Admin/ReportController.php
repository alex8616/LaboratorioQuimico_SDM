<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Element;
use App\Models\Package;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Foreach_;
use Psy\Readline\HoaConsole;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class ReportController extends Controller
{

    public function searchPackageDate(){
        return view('admin.reports.searchDate');
    }

    public function searchPackageUser(){
        return view('admin.reports.searchUser');
    }

    public function searchReportDay(){
        return view('admin.reports.searchDay');
    }


    public function returnReportUser(Request $request){

        $persona = Person::where('ci', $request->ci)->first();
        $packages = $persona->user->packages()
        ->orderBy("created_at", "desc")
        ->get();
        $total = collect($packages)->pluck('total')->sum();
        // dd($packages);
        return view('admin.reports.user', compact('packages', 'persona','total'));
    }


    public function returnReportDate(Request $request){
        // dd($request->all());
        // $package = Package::find(1);
        // $packages = Package::orderBy('id', 'asc')->paginate(10);

        $from = Carbon::create($request->from);
        $to = Carbon::create($request->to);
        $to->addHours(23);
        // dd($to);


        $packages = Package::whereBetween('created_at', [$from, $to])->get();
        // $packages = Package::whereDate('created_at', '=', Carbon::now()->format('Y-m-d'))->paginate(10);
        $packagesT = Package::whereBetween('created_at', [$from, $to])->get();
        $total = 0;
        foreach($packagesT as $package){
            $totalPaquete = 0;
            foreach($package->elements as $element){
                $totalPaquete += $element->price;
            }
            $total += $totalPaquete;
        }
        return view('admin.reports.date', compact('packages', 'total', 'from', 'to'));
    }


    public function reportDay(Request $request){
        $day = Carbon::create($request->day);
        $packages = Package::whereDate('created_at', $day)->get();

        $view = \Illuminate\Support\Facades\View::make('admin.packages.dayPdf', compact('packages'));

        $html = $view->render();

        PDF::SetMargins(2, 2, 2);
        PDF::SetAutoPageBreak(TRUE, 2);
        PDF::SetTitle('Certificado de Analisis');
        PDF::AddPage('P', 'A5');

        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output('reporte '.$packages[0]->created_at.'.pdf');
    }

    public function somePackage(){
        return view('admin.reports.somePackage');
        
    }

    public function reportSome(Request $request){
        
        $codes = $request->codes;
        // dd($codes);
        foreach($codes as $code){
            $packages[] = Package::find($code);
        }
     
        $info = array(
            'Name' => 'Laboratorio Quimico SDM',
            'Location' => 'Potosí - Bolivia',
            'Reason' => 'Certificado Digital de Analisis Quimico de Minerales',
            'ContactInfo' => '',
        );
        $view = \Illuminate\Support\Facades\View::make('admin.packages.somePdf', compact('packages'));

        $html = $view->render();

        PDF::SetMargins(2, 2, 2);
        PDF::SetAutoPageBreak(TRUE, 2);
        PDF::SetTitle('Certificado de Analisis');
        PDF::AddPage('P', 'A5');

        PDF::writeHTML($html, true, false, true, false, '');
        // PDF::Text(80, 205, 'QRCODE H - COLORED');
        PDF::Output('reporte '.$packages[0]->company->name.'-'.$packages[0]->updated_at.'.pdf');
        // return view('admin.reports.some', compact('packages'));
    }


    public function mostrarDatos(Request $request) {
        $ids = $request->input('ids');
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        $reportes = Package::whereIn('id', $ids)->get();
        
        $fecha = Carbon::now()->format('j \d\e F \d\e\l Y');

        $data = url('/admin/reports/mostrar-datos-digital',$ids);
        $size = 100; // El tamaño del código QR
        $qrCode = QrCode::size($size)->generate($data); // Generar el código QR

        $pdf = PDF::loadView('admin.reports.mostrar-datos', compact('reportes','qrCode','fecha'))
                    ->setPaper(array(0,0,419.53,595.28), 'portrait');
        return $pdf->stream('Reporte_de_venta'.time().'pdf');
        
        //return view('admin.reports.mostrar-datos', compact('reportes'));        
    }
    
    public function mostrarDatosDigital($ids) {
        if (!is_array($ids)) {
            $ids = explode('/', $ids);
        }
        $reportes = Package::whereIn('id', $ids)->get();
        
        $pdf = PDF::loadView('admin.reports.mostrar-datos-digital', compact('reportes'))
                    ->setPaper(array(0,0,320,418), 'portrait');
        return $pdf->stream('Reporte_de_venta'.time().'pdf');
        //return view('admin.reports.mostrar-datos', compact('reportes'));  
    }
    

    public function recuperarDatos(Request $request) {
        $ids = $request->input('ids');
        $reportes = Package::whereIn('id', $ids)->get();
        return response()->json(['reportes' => $reportes]);
    }

    public function especificodate(Request $request){
        return view('admin.reports.reportesespecifico');
        return response()->json($request);
    }

    public function reporteespecifico(Request $request){
        $precio = $request->precio_elemento;
        $from = Carbon::create($request->fecha_desde);
        $to = Carbon::create($request->fecha_hasta);
        $to->addHours(23);

        $persona = Person::findOrFail($request->cliente_id);
        $packages = Package::with('elements')
                    ->where('user_id', '=', $request->cliente_id)
                    ->whereBetween('created_at', [$from, $to])
                    ->get();
        $elementos = Element::get();

        $element_counts = [];
        $total = 0;

        foreach ($packages as $package) {
            foreach ($package->elements as $element) {
                $element_name = $element->name;
                if (!isset($element_counts[$element_name])) {
                    $element_counts[$element_name] = 0;
                }
                $element_counts[$element_name] += 1;
                $total += 1;
            }
        }
        $fecha = now();
        setlocale(LC_TIME, 'es_ES.utf8');
        $fecha_carbon = Carbon::parse($fecha);
        $fecha_formateada = $fecha_carbon->isoFormat('dddd, DD [de] MMMM [de] YYYY');
        //return response()->json($packages);
        $pdf = PDF::loadView('admin.reports.reportespecifico', compact('fecha_formateada','total','packages','from','to','persona','elementos','precio','element_counts'));
        return $pdf->stream('Reporte'.time().'pdf'); 
    }

}

