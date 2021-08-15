<?php

namespace App\Http\Controllers;

use App\Order;
use App\Route;
use App\Action;
use App\Logbook;
use App\Helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $routes = Route::where('user_id', $user->id)->where('route_status_id', config('status.route_unterwegs'))->get();
        $action = Auth::user()->getAction();  
        if($action){
            $smartsupp_token = $action['SmartsuppToken'];
        }
        else{
            $smartsupp_token = null;
        }
        return view('home', compact('user','routes', 'action', 'smartsupp_token'));
    }

    public function routes($id)
    {
        $user = Auth::user();
        $action = Auth::user()->getAction();  
        $routes = Route::where('user_id', $user->id)->where('route_status_id', config('status.route_unterwegs'))->get(); 
        $route = Route::FindOrFail($id); 
        $orders = $route->orders;
        $smartsupp_token = $action['SmartsuppToken'];

        return view('home.main', compact('route', 'orders', 'routes', 'smartsupp_token'));
    }

    public function maps($id)
    {        
        $user = Auth::user();
        $action = Auth::user()->getAction();  
        $routes = Route::where('user_id', $user->id)->where('route_status_id', config('status.route_unterwegs'))->get();  
        $route = Route::FindOrFail($id); 
        $orders = Order::where('route_id',$route['id']);
        $orders = $orders->with('address')->get();
        $center = $action->center;
        $key = $action['APIKey'];
        $smartsupp_token = $action['SmartsuppToken'];
        return view('home.map', compact('orders', 'route','routes','center', 'key', 'smartsupp_token'));
    }

    public function delivered($id)
    { 
        return $this->check_route($id, config('status.order_ausgeliefert'));
    }

    public function deposited($id)
    { 
        return $this->check_route($id, config('status.order_hinterlegt')); 
    }

    public function check_route($id, $new_status)
    {    
        $order = Order::findOrFail($id);
        $action = Auth::user()->getAction();  
        $route_id = $order['route_id']; 
        if($order['quantity']===1){
            $text = 'Ein Zopf wurde';
        }
        else
        {
            $text = $order['quantity'].' Zöpfe wurden';
        }
        $text = $text.' an '.$order->address['firstname'].' '.$order->address['name'];
        if($new_status===config('status.order_hinterlegt')){
            $text = $text.' hinterlegt.';
        }
        else
        {
            $text = $text.' übergeben.';
        }
        Helper::CreateLogEntry(Auth::user()->id, $action['id'], $text, now(),  $order['quantity']);
        $order->update(['order_status_id' => $new_status]);
        $orders = Order::where('route_id',$route_id);
        if($orders->min('order_status_id') > config('status.order_unterwegs')){
            $route = Route::FindOrFail($route_id);
            Helper::CreateLogEntry(Auth::user()->id, $action['id'], 'Route '.$route['name'].' wurde abgeschlossen', now());
            $route->update(['route_status_id' => config('status.route_abgeschlossen')]);
            return redirect('/');
        }
        return back();
    }
}
