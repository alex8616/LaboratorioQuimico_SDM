<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidFormPackage;
use App\Models\Company;
use App\Models\Element;
use App\Models\Package;
use App\Models\Person;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDF;
use Illuminate\Support\Facades\Auth;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Datatable;

class PackageController extends Controller
{
   
    public function index(){
        return view('admin.packages.index');
    }

    public function create(){
        /*$companys = DB::table('companies')->select('id', 'name')->get();
        $companies = array('' => 'Seleccione Empresa') + $companys->pluck('name', 'id')->toArray();*/

        /*$elementos = DB::table('elements')->select('id', 'name')->orderBy('id')->get();
        $elements = $elementos->pluck('name', 'id')->toArray();*/

        $companies = Company::select('id','name')->get();
        $elements = Element::select('id', 'name')->get();
        $package = Package::all();

        //return response()->json($elements);   
        return view('admin.packages.create', compact('package','companies','elements'));
    }

    public function store(Request $request){
        return response()->json($request);
        
        /*// dd($request->all());
        $package = new Package();
        $package->key = Str::uuid();
        $package->code = $request->code;
        $package->features = $request->features;
        if($request->renown){
            $package->renown = strtoupper($request->renown);
        }
        $package->fecha = Carbon::now()->isoFormat('LL');
        $package->user_id = $request->user_id;
        $package->company_id = $request->company_id;
        $package->save();
        $package->elements()->sync($request->elements);

        return redirect()->route('admin.packages.index')->with('info', 'El Paquete se creó con exito');
        // return redirect()->route('admin.packages.edit', $company)->with('info', 'La Empresa se creó con exito');*/
    }

    public function packagestore(Request $request){
        //return response()->json($request->caracteristicas_paquete);
        $user = Auth::user(); 
        
        $data = $request->only('arrayelemens');
        $aux = 0;
        $results = array();
        $aux = 0;
        foreach ($data['arrayelemens'] as $id => $cantidad) {
            $results[] = array("element_id" => $id, "value" => $cantidad);
        }

        // Convertir el array a una cadena de texto JSON
        $json = json_encode($results);

        // Decodificar el JSON
        $data2 = json_decode($json, true);

        // Extraer los IDs de elemento del array de objetos
        $ids = collect($data2)->pluck('element_id')->toArray();

        // Obtener los elementos correspondientes a esos IDs y sus precios
        $elementos = Element::whereIn('id', $ids)->get();
        $precios = [];

        foreach ($elementos as $elemento) {
            $precios[$elemento->id] = $elemento->price;
            $aux +=  $elemento->price;
        }

        $package = new Package();
        $package->key = Str::uuid();
        $package->status = 1;
        $package->user_id = $request->cliente_id;
        $package->company_id = $request->compania;
        if($request->renombre_paqueta){
            $package->renown = strtoupper($request->renombre_paqueta);
        }        $package->code = $request->Codigo_paqueta;
        $package->features = $request->caracteristicas_paquete;
        $package->fecha = Carbon::now()->isoFormat('LL');
        $package->total = $aux;
        $package->save();
        $package->elements()->sync($results);
        
        return response()->json($package);

        /*// dd($request->all());
        $package = new Package();
        $package->key = Str::uuid();
        $package->code = $request->code;
        $package->features = $request->features;
        if($request->renown){
            $package->renown = strtoupper($request->renown);
        }
        $package->fecha = Carbon::now()->isoFormat('LL');
        $package->user_id = $request->user_id;
        $package->company_id = $request->company_id;
        $package->save();
        $package->elements()->sync($request->elements);

        return redirect()->route('admin.packages.index')->with('info', 'El Paquete se creó con exito');
        // return redirect()->route('admin.packages.edit', $company)->with('info', 'La Empresa se creó con exito');*/
    }

    public function show(Package $package){
        return view('admin.packages.show', compact('package'));
    }

    public function edit(Package $package){
        $companys = DB::table('companies')->select('id', 'name')->get();
        $companies = array('' => 'Seleccione Empresa') + $companys->pluck('name', 'id')->toArray();
        return view('admin.packages.edit', compact('package', 'companies'));
    }

    public function update(Request $request, Package $package){
        // dd($request->all());
        $package->code = $request->code;
        $package->features = $request->features;
        if($request->renown){
            $package->renown = $request->renown;
        }
        $package->company_id = $request->company_id;
        $package->status = true;
        
        if(preg_match('/^[0-1][0-9][\/][0-3][0-9][\/][0-9]{4}$/', $request->fecha)){
            $package->fecha = Carbon::create($request->fecha)->isoFormat('LL');
        }
        $package->save();
        for ($i = 0; $i < count($request->elements); $i++) {
            $package->elements()->updateExistingPivot($request->elements[$i], ['value' => $request->values[$i]]);
        }
        
        
        // return redirect()->route('admin.packages.index')->with('info', 'El Paquete se actualizó con exito');
        return redirect()->route('admin.packages.show', $package)->with('info', 'El paquete se editó con exito');
    }

    public function destroy(Package $package){
        $package->delete();
        return redirect()->route('admin.packages.index')->with('info', 'El Paquete fue eliminado con exito');
    }

    public function createPdf($id){
        $package = Package::findorfail($id);

        $info = array(
            'Name' => 'Laboratorio Quimico SDM',
            'Location' => 'Potosí - Bolivia',
            'Reason' => 'Certificado de analisis',
            'ContactInfo' => 'http://www.labsdm.net',
        );
        $view = \Illuminate\Support\Facades\View::make('admin.packages.pdf', compact('package'));

        $html = $view->render();
        PDF::SetMargins(6, 7, 6, 1);
        // PDF::SetAutoPageBreak(TRUE, 2);
        PDF::SetTitle('Certificado de Analisis');
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
        PDF::write2DBarcode(url('paquete/show/'.$package->key), 'QRCODE,H', 110, 115, 30, 30, $style, 'Q');
        PDF::writeHTML($html, true, false, true, false, '');
        // PDF::Text(80, 205, 'QRCODE H - COLORED');
        PDF::Output('paquete-'.$package->key.'.pdf', 'I');
    }

    public function yesterdayReport(){
        // $package = Package::find(1);
        // $packages = Package::orderBy('id', 'asc')->paginate(10);
        $fechaHoy = Carbon::yesterday()->isoFormat('LL');
        $packages = Package::whereDate('created_at', '=', Carbon::yesterday()->format('Y-m-d'))->paginate();
        $packagesT = Package::whereDate('created_at', '=', Carbon::yesterday()->format('Y-m-d'))->get();
        $total = 0;
        foreach($packagesT as $package){
            $totalPaquete = 0;
            foreach($package->elements as $element){
                $totalPaquete += $element->price;
            }
            $total += $totalPaquete;
        }
        return view('admin.packages.ayer', compact('packages', 'fechaHoy', 'total'));
    }

    public function certPdf($id){
        $package = Package::findorfail($id);
        // $ruta = storage_path() . '\app\public\tikects/';
        // dd($package->user->people->name);
        // dd($package);
        // return view('admin.packages.index');
        PDF::SetMargins(1, 2, 3, 0);
        $view = \Illuminate\Support\Facades\View::make('admin.packages.cert', compact('package'));
        $html = $view->render();
        $medidas = array(80, 300);
        PDF::AddPage('P', $medidas, true, 'UTF-8', true);
        $style = array(
            'border' => 0,
            'vpadding' => '2',
            'hpadding' => '2',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        PDF::write2DBarcode(url('paquete/pdf/'.$package->key), 'QRCODE, Q', 22, 20, 35, 35, $style, 'L');
        PDF::writeHTML($html, true, false, true, false, '');
        // PDF::Text(80, 205, 'QRCODE H - COLORED');
        PDF::Output('paquete-'.$package->key.'.pdf', 'I');
    }

    public function packagesCompany(){
        $user = User::where('id', auth()->user()->id)->first();
        $persona = $user->people;
        $company = $user->companies->first();
        $packages = Package::where('user_id', $user->id)
                    ->where('company_id', $company->id)
                    ->orderBy("created_at", "asc")
                    ->paginate(15);
        $n = 1;
        // dd($packages);
        return view('admin.packages.company', compact('packages', 'company', 'persona', 'n'));
    }

    public function packagesUser(){
        $user = User::where('id', auth()->user()->id)->first();
        $persona = $user->people;
        $packages = Package::where('user_id', $user->id)
        ->orderBy("created_at", "desc")
        ->get();
        $n = 1;
        // dd($packages);
        return view('admin.packages.user', compact('packages', 'persona', 'n'));
    }

    public function autocompletecliente(Request $request){
        $datacliente = Person::select('*', DB::raw("CONCAT(name,' ',app,' ',IFNULL(apm,'')) as value"))
            ->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%'. $request->get('search'). '%')
                    ->orWhere('app', 'LIKE', '%'. $request->get('search'). '%')
                    ->orWhere(function($query) use ($request){
                        $query->whereNull('apm')
                        ->where('name', 'LIKE', '%'. $request->get('search'). '%')
                        ->orWhere('app', 'LIKE', '%'. $request->get('search'). '%');
                    });
            })
            ->get();
        return response()->json($datacliente);
    }
    
    public function data(){
        $date = Carbon::now()->isoFormat('LL');

        $packages = Package::select('packages.*')
            ->with('elements')
            ->with('user.people')
            ->where('fecha', '=', $date)
            ->get();

        // Obtener el total de los precios de los paquetes
        $total = collect($packages)
            ->pluck('total')
            ->sum();

        return response()->json([
            'data' => $packages,
            'total' => $total
        ]);
    }


    public function tabledit($id){
        $elemets = Package::with('elements')->findOrFail($id);
        return response()->json($elemets);
    }

    public function tableadd($id){
        $package = Package::find($id);
        $allelements = Element::get();
        
        $packageElements = $package->elements;
        
        // Filtrar los elementos que no están en el paquete
        $elementsNotInPackage = $allelements->reject(function($element) use ($packageElements) {
            return $packageElements->contains('id', $element->id);
        });
    
        return response()->json([
            'id' => $package->id,
            'elements' => $package->elements,
            'elementsNotInPackage' => $elementsNotInPackage->toArray(),
            'elementPrices' => $allelements->pluck('price', 'id')->toArray()
        ]);
    }
    
    /*public function updatetable(Request $request, $id){        
        $DatosDetalle = Package::findOrFail($id); 
        return response()->json($DatosDetalle);       
    }*/

    public function updatetable(Request $request, $id){
        $package = Package::findOrFail($id);
        
        $element_ids = $request->input('element_ids');
        $element_names = $request->input('element_names');
        $element_values = $request->input('element_values');
        
        $package->save();
        // Iterar sobre los elementos actualizados y actualizar sus valores en la tabla pivote
        foreach ($element_ids as $index => $element_id) {
            //$package->elements()->updateExistingPivot($request->elements[$i], ['value' => $request->values[$i]]);
            $package->elements()->updateExistingPivot($element_id, [
                'value' => $element_values[$index]
            ]);
        }
        return response()->json(['success' => true]);
    }

    public function addtableupdate(Request $request, $id){
        //return response()->json($request);
        $package = Package::findOrFail($id);
        $inputValue = $request->element_value;
        $selectValue = $request->select_elementos;

        $package->total += $request->element_price;    
        $package->elements()->attach([$selectValue => ['value' => $inputValue]]);
        $package->save();

        return response()->json($package);
    }
    
    public function destroyelement($id, $element_id){
        $package = Package::find($id);
        $package->elements()->detach($element_id);
        return response()->json(['success' => true]);
    }

}
