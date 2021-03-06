@extends('layouts.admin')


@section('content')
    <div class="breadcrumb-holder">
        <div class="container-fluid">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/admin/orders">Bestellungen</a></li>
            <li class="breadcrumb-item active">Karte</li>
            </ul>
        </div>
    </div>
    <section>
        <div class="container-fluid">
            <!-- Page Header-->

            <div class="row">  
                <div class="col-sm-3">
                    <header> 
                        <h1 class="h3 display">Karte der Bestellungen</h1>
                    </header>
                    <table class="table table-borderless" id="btns">
                        <tbody>
                            <td>
                                <table class="table table-borderless" id="city_btn">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <button class="btn btn-outline-primary btn-sm active">Alle</button>    
                                            </td>
                                        </tr>
                                        @foreach ($cities as $city)
                                        <tr>
                                            <td>
                                                <button class="btn btn-outline-primary btn-sm">{{$city}}</button>
                                            </td>
                                        </tr>
                                        @endforeach 
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <table class="table table-borderless"  id="status_btn">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <button class="btn btn-outline-secondary btn-sm active">Alle</button>    
                                            </td>
                                        </tr>
                                        @foreach ($statuses as $status)
                                        <tr>
                                            <td>
                                                <button class="btn btn-outline-secondary btn-sm">{{$status}}</button>
                                        </td>
                                    </tr>
                                        @endforeach 
                                    </tbody>
                                </table>
                            </td>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-9">
                    <div style="height: 800px" id="map-canvas"></div>
                </div>
            </div>          
        
        </div>
    </section>
    
@endsection
@section('scripts')
    <script>

    // Get the container element
    var btnContainer_city = document.getElementById("city_btn");

    // Get all buttons with class="btn" inside the container
    var btns_city = btnContainer_city.getElementsByClassName("btn");

    // Loop through the buttons and add the active class to the current/clicked button
    for (var i = 0; i < btns_city.length; i++) {
        btns_city[i].addEventListener("click", function() {
        var current = btnContainer_city.getElementsByClassName("active");
        // If there's no active class
        if (current.length > 0) {
            current[0].className = current[0].className.replace(" active", "");
        }

        // Add the active class to the current/clicked button
        this.className += " active";
    });
    }

    // Get the container element
    var btnContainer_status = document.getElementById("status_btn");

    // Get all buttons with class="btn" inside the container
    var btns_status = btnContainer_status.getElementsByClassName("btn");

    // Loop through the buttons and add the active class to the current/clicked button
    for (var i = 0; i < btns_status.length; i++) {
        btns_status[i].addEventListener("click", function() {
        var current = btnContainer_status.getElementsByClassName("active");
        // If there's no active class
        if (current.length > 0) {
            current[0].className = current[0].className.replace(" active", "");
        }

        // Add the active class to the current/clicked button
        this.className += " active";
    });
    }

    var btnContainer = document.getElementById("btns");

    // Get all buttons with class="btn" inside the container
    var btns = btnContainer.getElementsByClassName("btn");

    // Loop through the buttons and add the active class to the current/clicked button
    for (var i = 0; i < btns.length; i++) {
        btns[i].addEventListener("click", function() {
            active_btn = btnContainer.getElementsByClassName("active");
            city_btn = active_btn[0];
            status_btn = active_btn[1];
            $.ajax({
                url: "{!! route('orders.mapfilter')!!}",
                data: {
                    city: city_btn.textContent,
                    status: status_btn.textContent
                },
                success:function(response){
                    setMapsArguments(response, @json($key))
                    initialize();
                }
            });
        });
    }

    setMapsArguments(@json($orders), @json($key), @json($center))
    window.onload = loadScript;   
    </script>
@endsection