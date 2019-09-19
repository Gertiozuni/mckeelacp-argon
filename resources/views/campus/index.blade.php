@extends('layouts.app')

@section( 'title', 'Campuses' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => 'Campuses' ])
    <campus-view :campusesprop="{{$campuses}}" inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ url('/campuses/form') }}" class="btn btn-sm btn-primary">{{ __('Add Campus') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('Name') }}</th>
                                        <th scope="col">{{ __('Abbreviation') }}</th>
                                        <th scope="col" class="text-right">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="campus of campuses" class='myTableRow' ref="table">
                                        <td v-text="campus.name"></td>
                                        <td v-text="campus.abbreviation"></td>
                                        <td class="text-right">
                                            @if( Auth::user()->hasAnyPermission( 'admin', 'edit campuses' ) )
                                                <a :href="`{{ url( '/campuses/form' ) }}/${campus.id}`">
                                                    <button class="btn btn-sm btn-primary" type="button">
                                                        <span class="btn-inner--icon"><i class="fas fa-pencil-alt"></i></span>
                                                    </button>
                                                </a>
                                                <button class="btn btn-sm btn-danger" type="button" @click="deleteCampus( campus.id )">
                                                    <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
                
            @include('layouts.footers.auth')
        </div>
    </campus-view>
@endsection