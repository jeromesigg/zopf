@extends('layouts.admin')
@section('content')

    <x-page-title :title="$title" :help="$help"/>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->
            <div class="row">
                <div class="col-sm-6">
                    @include('includes.form_error')
                    {!! Form::open(['method' => 'POST', 'action'=>'AdminOrdersController@store']) !!}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {!! Form::label('firstname', 'Vorname:') !!}
                            {!! Form::text('firstname', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('name', 'Name:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                        </div>
                        {!! Form::hidden('address_id', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('pick_up', 'Abholen:') !!}
                        {!! Form::checkbox('pick_up', 'yes',  false) !!}
                    </div>
                    <div class="address">
                        <div class="form-group">
                            {!! Form::label('street', 'Strasse:') !!}
                            {!! Form::text('street', null, ['class' => 'form-control ']) !!}
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                {!! Form::label('plz', 'PLZ:') !!}
                                {!! Form::text('plz', null, ['class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-md-9">
                                {!! Form::label('city', 'Ort:') !!}
                                {!! Form::text('city', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('quantity', 'Anzahl:') !!}
                        {!! Form::text('quantity', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('routes_id', 'Route:') !!}
                        {!! Form::select('routes_id', [''=>'Wähle Route'] + $routes, null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('comment', 'Bemerkung:') !!}
                        {!! Form::text('comment', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Adresse Erfassen', ['class' => 'btn btn-primary'])!!}
                    </div>
                    {!! Form::close()!!}
                </div>
            </div>
    </section>
@endsection


@section('scripts')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            $(document).ready(function () {
                $('input[type="checkbox"]').click(function () {

                    $(".address").toggle();
                });
            });
        }, false);
    </script>
@endsection
{{--  //autocomplete script
    // $(document).on('focus','.autocomplete_txt',function(){
    // type = $(this).attr('name');

    // $(this).autocomplete({
    //     minLength: 2,
    //     highlight: true,
    //     source: function( request, response ) {
    //             $.ajax({
    //                 url: "{{ route('searchajaxaddress') }}",
    //                 dataType: "json",
    //                 data: {
    //                     term : request.term
    //                 },
    //                 success: function(data) {
    //                     var array = $.map(data, function (item) {
    //                     return {
    //                         label: function(item) {
    //                          if(item['name']!=undefined){
    //                             return item['firstname'] + ' ' + item['name'] + ', ' + item['street'] + ', ' + item['city_plz'] + ' ' + item['city_name']
    //                             } else{
    //                                 return "Nichts gefunden";
    //                             }},
    //                         value: item['id'],
    //                         data : item
    //                     }
    //                 });
    //                     response(array)
    //                 }
    //             });
    //     },
    //     select: function( event, ui ) {

    //         var data = ui.item.data;
    //         $("[name='address_name']").val(data.name);
    //         $("[name='address_firstname']").val(data.firstname);
    //         $("[name='address_id']").val(data.address_id);
    //         $("[name='address_street']").val(data.street);
    //         $("[name='address_city_name']").val(data.city_name);
    //         $("[name='address_city_plz']").val(data.city_plz);
    //     }
    // });


    // });--}}
