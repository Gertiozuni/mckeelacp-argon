@extends('layouts.app')

@section( 'title', 'Switches' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => $switch->ip_address ])
    <switch-view :network-switch="{{ $switch }}" inline-template>
        <div class="container-fluid mt--7">
    {{-- @todo switch ports 
        <switch-view :networkSwitch="{{ $switch }}" inline-template>
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <switch :switch="{{ $switch }}">
                            <div id='firstRow'>
                                @foreach( $switch->ports as $port )
                                    @if( $port->port % 2 !== 0 )
                                        <template v-slot:topports>
                                            <port port="{{ $port }}" type="top"></port>
                                        </template>
                                    @endif
                                @endforeach
                            </div>
                            
                            <div id='secondRow'>
                                <template v-slot:topports>
                                    <port port="{{ $port }}" type="bottom"></port>
                                </template>
                                @foreach( $switch->portsinfo->where( 'port', '>', $switch->ports - 4 ) as $port )
                                        <div class="{{ $port->active && $port->fiber == 1 ? 'port-squares active-port' : 'port-squares' }}" id="{{ $switch->id }}">
                                            {{ $port->port }}
                                        </div>
                                @endforeach
                            </div>
                        </switch>
                    </div>
                </div>
            </div>
            @include('layouts.footers.auth')--}}


            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                        
                                </div>
                                <div class="col-4 text-right">
                                    <input type="text" v-model="search" placeholder="Search"/>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Port </th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Mode</th>
                                        <th scope="col">Vlan</th>
                                        <th scope="col">Last Updated</th>
                                        <th scope="col">Last Sync</th>
                                        <th scope="col" class="text-right">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="port of ports" class='myTableRow' ref="table">
                                        <td v-text="port.port"></td>
                                        <td>
                                            <vue-badge
                                                :text="port.active ? 'Active' : 'Inactive'"
                                                :color="port.active ? 'success' : 'danger'"
                                            >
                                        </td>

                                        @if( Auth::user()->can('admin', 'edit port' ) )
                                            <td class="description" v-if="! port.editing" v-text="port.description" @click="enableEdit(port)">
                                                <div v-if="port.editing">
                                                    <input v-model="tempValue" class="input"/>
                                                </div>
                                            </td>
                                            <td v-if="port.editing">
                                                <input ref="edit" @blur="disableEdit(port)" v-model="tempValue" class="input"/>
                                            </td>
                                        @else
                                            <td v-text="port.description"></td>
                                        @endif
                                        
                                        <td v-text="port.mode"></td>
                                        <td v-if="(port.vlans.length == 1)">
                                            @{{ getVlans(port.vlans)[0] }}
                                        </td>
                                        <td v-else-if="(port.vlans.length >= 1)">
                                            <multiselect  
                                                :options="getVlans(port.vlans)"
                                                :searchable="true"
                                                placeholder="Vlans"
                                            > 
                                            </multiselect>
                                        </td>
                                        <td v-else>
                
                                        </td>
                                        <td v-text="port.last_updated ? moment(port.last_updated).format('DD-MM-YYYY') : ''"></td>
                                        <td v-text="moment(port.checked_in).format('DD-MM-YYYY')"></td>
                                        <td class="text-right">
                                            @if( Auth::user()->can( 'admin', 'edit network' ) )
                                                <a :href="`{{ url( '/network/vlans/form' ) }}/${port.id}`">
                                                    <vue-button 
                                                        icon='fas fa-ethernet'
                                                        size='small'
                                                        color='primary'
                                                        :tooltip="{ title: 'Change Mode', placement: 'top' }"
                                                    >
                                                    </vue-button>
                                                </a>
                                                <vue-button 
                                                    icon='fas fa-project-diagram'
                                                    size='small'
                                                    color='info'
                                                    :tooltip="{ title: 'Manage Vlans', placement: 'top' }"
                                                >
                                                </vue-button>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        

    </switch-view> 
@endsection
