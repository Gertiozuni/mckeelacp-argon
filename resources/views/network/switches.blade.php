@extends('layouts.app')

@section( 'title', 'Switches' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => 'Switches' ])

    <switches-view :campuses="{{ $campuses }}" inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-12 text-right">
                                    @if( Auth::user()->hasAnyPermission( 'admin', 'edit network' ) )
                                        <a href="{{ url('/network/switches/form') }}" class="btn btn-sm btn-primary">Add Switch</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <tabs>
                            <tab v-for="campus in campuses" :name="campus.name">
                                <div class="table-responsive" v-for="(switches, location) in groupBy(campus.switches, 'location')">
                                    <h1 v-text="location"></h1>
                                    <table class="table align-items-center table-flush">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">IP</th>
                                                <th scope="col">MAC</th>
                                                <th scope="col">Model</th>
                                                <th scope="col">Ethernet</th>
                                                <th scope="col">Fiber</th>
                                                <th scope="col">Location</th>
                                                <th scope="col">Uptime</th>
                                                <th scope="col">Last Sync</th>
                                                <th scope="col" class="text-right">Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class='myTableRow' v-for="(s, index) in switches">
                                                <td><a :href="`{{url( '/network/switch' ) }}/${s.id}`" v-text="s.ip_address"></a></td>
                                                <td v-text="s.mac_address"></td>
                                                <td v-text="s.model"></td>
                                                <td v-text="s.ports_count"></td>
                                                <td v-text="s.fiber_ports"></td>
                                                <td v-text="s.sub_location"></td>
                                                <td v-text="s.active ? moment(s.uptime).format('DD-MM-YYYY') : 'Inactive'"></td>
                                                <td v-text="s.checked_in ? moment(s.checked_in).format('DD-MM-YYYY') : ''"></td>
                                                <td class="text-right">
                                                    @if( Auth::user()->hasAnyPermission( 'admin', 'edit network' ) )
                                                        <a :href="`{{ url( '/network/switches/form' ) }}/${s.id}`">
                                                            <button class="btn btn-sm btn-primary" type="button">
                                                                <span class="btn-inner--icon"><i class="fas fa-pencil-alt"></i></span>
                                                            </button>
                                                        </a>
                                                        <button class="btn btn-sm btn-danger" type="button">
                                                            <span class="btn-inner--icon"><i class="fas fa-trash" @click='deleteSwitch(s)'></i></span>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </tab>
                        </tabs>
                    </div>
                </div>
            </div>
            @include('layouts.footers.auth')
        </div>
    </campus-view>
@endsection
