@extends('layouts.layout')

@section('content')
    <h1>Hallo {{$aktUser->username}}</h1>
    {!! Form::model($aktUser, ['method' => 'PATCH', 'class' => 'card', 'action'=>['UsersController@update', $aktUser->id]]) !!}

        <div class="card-header">
            <h3 class="card-title">Mein Profil</h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="form-group">
                {!! Form::label('username', 'Name:') !!}
                {!! Form::text('username', null, ['class' => 'form-control', 'autocomplete'=>"username"]) !!}
            </div>
            @if (!$aktUser['demo'])

                <div class="form-group">
                    {!! Form::label('email', 'E-Mail:') !!}
                    {!! Form::text('email', null, ['class' => 'form-control','autocomplete'=>"email"]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('password', 'Passwort:') !!}
                    {!! Form::password('password', ['class' => 'form-control', 'autocomplete'=>"new-password"]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('password_confirmation', 'Passwort Wiederholen:') !!}
                    {!! Form::password('password_confirmation', ['class' => 'form-control', 'autocomplete'=>"new-password"]) !!}
                </div>
            @endif
        </div>
        <div class="card-footer text-right">
            {!! Form::submit('Speichern', ['class' => 'btn btn-primary'])!!}
        </div>
    {!! Form::close()!!}
@endsection
