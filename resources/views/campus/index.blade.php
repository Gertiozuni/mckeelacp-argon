@extends('layouts.app')

@section( 'title', 'Campuses' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => 'Campuses' ])
    <campus-view v-cloak :campusesprop="{{$campuses}}" inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                </div>
                                <div class="col-4 text-right">
                                    <base-button tag="a" type="primary" size="sm" href="{{ url('/campuses/form') }}" role="button">Add Campus</base-button>
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
                                                <base-button tag="a" type="primary" size="sm" :href="`{{ url( '/campuses/form' ) }}/${campus.id}`" icon="fas fa-pencil-alt"></base-button>
                                                <base-button type="danger" size="sm" icon="fas fa-trash" @click.native="deleteCampus( campus.id )"></base-button>
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