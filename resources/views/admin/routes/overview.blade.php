@extends('layouts.admin')
@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header>
                Total Anzahl Zöpfe: {{$route->zopf_count()}} <br>
                Routen Art: {{$routetype['name']}} <br>
                @if ($route->route_status['id']> config('status.route_offen'))
                    <a type="button" class="btn btn-info btn-sm" target="_blank"
                       href="{{route('routes.downloadPDF', $route->id)}}">Download
                        PDF</a>
                @endif
            </header>
            @if($route->route_status['id'] === config('status.route_offen'))
                {!! Form::model($route, ['method' => 'POST', 'action'=>['AdminRoutesController@send',$route->id]]) !!}

                <div class="form-group">
                    {!! Form::submit('Vorbereitet', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}
            @endif
            @if($route->route_status['id'] === config('status.route_vorbereitet'))
                {!! Form::model($route, ['method' => 'POST', 'action'=>['AdminRoutesController@send',$route->id]]) !!}

                <div class="form-group">
                    {!! Form::submit('Lossenden', ['class' => 'btn btn-primary'])!!}
                </div>
                {!! Form::close()!!}
            @endif
            <div class="row">
                <div class="col-sm-6">
                    @if ($orders)
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Vorname</th>
                                <th scope="col">Strasse</th>
                                <th scope="col">PLZ</th>
                                <th scope="col">Ort</th>
                                <th scope="col">Anzahl</th>
                                <th scope="col">Status</th>
                            </tr>
                            </thead>
                            @foreach ($orders as $order)
                                <tbody>
                                <tr>
                                    <td><a href="{{route('orders.edit',$order)}}">{{$order->address['name']}}</a></td>
                                    <td>{{$order->address['firstname']}}</td>
                                    <td>{{$order->address['street']}}</td>
                                    <td>{{$order->address['plz']}}</td>
                                    <td>{{$order->address['city']}}</td>
                                    <td>{{$order['quantity']}}</td>
                                    <td>{{$order->order_status['name']}}</td>
                                </tr>
                                </tbody>
                            @endforeach
                        </table>

                    @endif
                </div>
                <div class="col-sm-6">
                    <div style="height:630px" id="map-canvas"></div>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @include('includes.google-maps')
    <script>
        var route_type = @json($route->route_type);
        var travel_mode = 'DRIVING';
        if (route_type) {
            travel_mode = route_type['travel_mode'];
        }
        setMapsArguments(@json($orders), @json($key), @json($center), travel_mode, false);
        window.onload = loadScript;
    </script>
@endsection
