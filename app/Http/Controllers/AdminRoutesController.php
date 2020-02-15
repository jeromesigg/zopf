<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use App\Order;
use App\Route;
use App\Action;
use DataTables;
use App\Address;
use App\RouteType;
use App\OrderStatus;
use App\RouteStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminRoutesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('admin.routes.index');
    }

    public function createDataTables()
    {
        if(!Auth::user()->isAdmin()){
            $group = Auth::user()->group;
            $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
            $routes = Route::where('action_id', $action['id'])->get();

        }
        else{
            $routes = Route::all();
        }

        return DataTables::of($routes)
        ->addColumn('status', function ($routes) {
            return $routes->route_status['name'];
        })
        ->addColumn('user', function ($routes) {
            return $routes->user['username'];
        })
        ->addColumn('routetype', function ($routes) {
            return $routes->route_type['name'];
        })
        ->addColumn('zopf_count', function ($routes) {
            return $routes->zopf_count();
        })
        ->addColumn('order_count', function ($routes) {
            return $routes->order_count();
        })
        ->addColumn('Actions', function($routes) {
            $buttons = '<a href='.\URL::route('routes.edit', $routes->id).' type="button" class="btn btn-success btn-sm">Bearbeiten</a>
            <a href='.\URL::route('routes.overview', $routes->id).' type="button" class="btn btn-info btn-sm">Übersicht</a>';
            // if($routes->route_status['id']==5){
            //     $buttons = $buttons .'
            //     <button data-remote='.\URL::route('routes.send', $routes->id).' id="send" class="btn btn-secondary btn-sm">Vorbereitet</button>';
            // };
            // if($routes->route_status['id']==10){
            //     $buttons = $buttons .'
            //     <button data-remote='.\URL::route('routes.send', $routes->id).' id="send" class="btn btn-secondary btn-sm">Lossenden</button>';
            // };
            return $buttons;
        })
        ->rawColumns(['Actions'])
        ->make(true);

    }

    public function overview($id)
    {
        //
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();      
        $route = Route::findOrFail($id);
        $center = $action->address;
        $orders = $route->orders;
        $routetype = $route->route_type;
        return view('admin.routes.overview', compact('route', 'orders', 'center', 'routetype'));
    }

    public function downloadPDF($id) {
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();      
        $route = Route::findOrFail($id);
        $center = $action->address;
        $orders = $route->orders;

        $url = 'directions/json?origin=' . $center['lat'] . ',' . $center['lng'];
        $url = $url . '&destination=' . $center['lat'] . ',' . $center['lng'];
        $url = $url . '&waypoints=';
        foreach ($orders as $order){
            $address = Address::findOrFail($order['address_id']);
            $url = $url . $address['lat'] . ',' . $address['lng'] . '|';
        }
        $url = rtrim($url, "| ");
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://maps.googleapis.com/maps/api/']); 
        $request = $client->get($url . '&optimize:true&key=AIzaSyANAmxiZYaDqNi7q5xxC6RicESrCmQFutw');
        $response = json_decode($request->getBody(), true);
        $path = $response['routes'][0]['overview_polyline']['points'];

        $url = 'https://maps.googleapis.com/maps/api/staticmap?size=512x512&scale=1&maptype=roadmap&markers=color:red%7C' . $center['lat'] . ',' . $center['lng'];

        foreach ($orders as $order){
            $address = Address::findOrFail($order['address_id']);
            $url = $url . '&markers=color:red%7C' . $address['lat'] . ',' . $address['lng'];
        }
        $url = $url . '&path=enc:' . $path;
        $url = $url . '&key=AIzaSyANAmxiZYaDqNi7q5xxC6RicESrCmQFutw';
        $image = file_get_contents($url);
        $folder = 'images/' . $group['name'] . '/' . $action['name'] .'_'. $action['year'] . '/'; 
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder, 0775, true, true);
        }
        $path = $folder.$route['name'].'.png';
        Storage::disk('public')->put($path, $image);
        $routetype = $route->route_type;
        $pdf = PDF::loadView('admin.routes.pdf', compact('route', 'orders', 'center', 'routetype', 'path')); 
        // return view('admin.routes.pdf', compact('route', 'orders', 'center', 'routetype', 'path'));       
        return $pdf->download($route['name'].'.pdf');
}
    
    public function map()
    {
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();         
        $orders = Order::where('action_id', $action['id'])->get();
        $routes = Route::where('action_id', $action['id'])->get();
        $routes = $routes->pluck('name')->all();
        $statuses = OrderStatus::pluck('name')->all();
        $center = $action->address;
        return view('admin.routes.map', compact('orders', 'routes', 'statuses', 'center'));
    }

    public function mapfilter(Request $request)
    {
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
        $route = $request->route;
        $status = $request->status;
        $orders = Order::where('action_id', $action['id']);
        if(isset($route) and $route!="Alle"){
            $route = Route::where('name',$route)->first();
            $orders = $orders->where('route_id',$route['id']);
        }

        if(isset($status) and $status!="Alle"){
            $order_status = OrderStatus::where('name',$status)->first();
            $orders->where('order_status_id',$order_status['id']);
        }
        
        $orders = $orders->with('address')->get();
        return $orders;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $group = Auth::user()->group;
        $users = User::where('group_id', $group['id'])->get();
        $users = $users->pluck('username','id')->all();
        $route_types = RouteType::pluck('name','id')->all();
        return view('admin.routes.create', compact('users','route_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
        $input['name'] = $request->name;
        $input['action_id'] =  $action['id'];
        $input['route_status_id'] = 5;
        if($request->user_id==null){
            $input['user_id'] = Auth::user()->id;
        }
        else{
            $input['user_id'] =  $request->user_id;
        }
        Route::create($input);

        return redirect('/admin/routes/create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $route = Route::findOrFail($id);
        $group = Auth::user()->group;
        $action = Action::where('group_id', $group['id'])->where('action_status_id',5)->first();
        $users = User::where('group_id', $group['id'])->get();
        $users = $users->pluck('username','id')->all();
        $route_statuses = RouteStatus::pluck('name','id')->all();
        $route_types = RouteType::pluck('name','id')->all();

        return view('admin.routes.edit', compact('route', 'users','route_statuses','route_types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $route = Route::findOrFail($id);
        $route->update($request->all());
        return redirect('/admin/routes');
    }

    public function send($id)
    {
        //
        $route = Route::findOrFail($id);
        $route->update(['route_status_id' =>  $route->route_status['id']  + 5]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Route::findOrFail($id)->delete();
        return redirect('/admin/routes');
    }
}
