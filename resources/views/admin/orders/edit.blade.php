@extends('layouts.admin')
@section('content')
<div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/orders">Bestellungen</a></li>
            <li class="breadcrumb-item active">Bearbeiten</li>
            </ul>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <header> 
                <h1 class="h3 display">Bestellung Bearbeiten</h1>
            </header>
            <div class="row">
                <div class="col-sm-6">
                    @include('includes.form_error')
                    {!! Form::model($order, ['method' => 'Patch', 'action'=>['AdminOrdersController@update',$order->id]]) !!}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {!! Form::label('firstname', 'Vorname:') !!}
                            {!! Form::text('firstname', $order->address['firstname'], ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', $order->address['name'], ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('street', 'Strasse:') !!}
                        {!! Form::text('street', $order->address['street'], ['class' => 'form-control ']) !!}
                    </div>

                    <div class="form-row">
                            <div class="form-group col-md-3">  
                            {!! Form::label('plz', 'PLZ:') !!}
                            {!! Form::text('plz', $order->address['plz'], ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-md-9">
                            {!! Form::label('city', 'Ort:') !!}
                            {!! Form::text('city', $order->address['city'], ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('quantity', 'Anzahl:') !!}
                        {!! Form::text('quantity', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('route_id', 'Route:') !!}
                        {!! Form::select('route_id', $routes, null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('comment', 'Bemerkung:') !!}
                        {!! Form::text('comment', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Adresse Erstellen', ['class' => 'btn btn-primary'])!!}
                    </div>
                {!! Form::close()!!}

                {!! Form::model($order, ['method' => 'DELETE', 'action'=>['AdminOrdersController@destroy',$order->id]]) !!}
                <div class="form-group">
                    {!! Form::submit('Bestellung löschen', ['class' => 'btn btn-danger'])!!}
                </div>
                {!! Form::close()!!}
            </div>
        </div>
    </section>
@endsection