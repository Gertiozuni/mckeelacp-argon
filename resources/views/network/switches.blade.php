@extends('layouts.app')

@section( 'title', 'Switches' )

@section('content')
    @include('layouts.headers.cards')

    <switches-view inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <tabs :items="{{ $campuses }}">
                        </tabs>
                    </div>
                </div>
            </div>
        </div>
    </switches-view>

@endsection