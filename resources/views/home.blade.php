@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">Laravel 8 Vue Autocomplete Example</div>

                    <div class="card-body">
                        @include('flash-message')

                        <autocomplete-component :books="{{json_encode($books)}}"></autocomplete-component>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
