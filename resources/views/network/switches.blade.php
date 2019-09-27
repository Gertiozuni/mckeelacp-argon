@extends('layouts.app')

@section( 'title', 'Switches' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => 'Switches' ])

    <switches-view v-cloak :campuses="{{ $campuses }}" inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-12 text-right">
                                    @if( Auth::user()->hasAnyPermission( 'admin', 'edit network' ) )
                                        <base-button
                                            tag="a"
                                            href="{{ url('/network/switches/form') }}"
                                            type="primary"
                                            size="sm"
                                        >Add Switch</base-button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <tabs fill class="flex-column flex-md-row">
                            <card shadow>
                                <tab-pane v-for="campus in campuses">
                                    <span slot="title" v-text="campus.name"></span>
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
                                                        <base-button
                                                            v-if="s.logs_count > 0"
                                                            tag="a"
                                                            :href="`{{ url( '/' )}}/network/switch/${s.id}/logs`"
                                                            icon='fas fa-history'
                                                            size='sm'
                                                            type='default'
                                                            :tooltip="{ title: 'View Logs', placement: 'top' }"
                                                        ></base-button>
                                                        @if( Auth::user()->hasAnyPermission( 'admin', 'edit network' ) )
                                                            <base-button
                                                                tag="a"
                                                                :href="`{{ url( '/' )}}/network/switches/form/${s.id}`"
                                                                icon='fas fa-pencil-alt'
                                                                size='sm'
                                                                type='primary'
                                                                :tooltip="{ title: 'Edit', placement: 'top' }"
                                                            ></base-button>
                                                            
                                                            <base-button
                                                                icon='fas fa-trash' 
                                                                @click='deleteSwitch(s)'
                                                                size='sm'
                                                                type='danger'
                                                                :tooltip="{ title: 'Delete', placement: 'top' }"
                                                            ></base-button>
                                                        @endif
                                                        
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </tab-pane>
                            </card>
                        </tabs>
                    </div>
                </div>
            </div>
            @include('layouts.footers.auth')
        </div>
    </campus-view>
@endsection
