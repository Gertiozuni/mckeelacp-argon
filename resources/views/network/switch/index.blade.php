@extends('layouts.app')

@section( 'title', 'Switches' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => $switch->ip_address ])
    <switch-view v-cloak :network-switch="{{ $switch }}" :vlans="{{ $vlans }}" inline-template>
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
                                    <tr v-for="port of searchPorts" class='myTableRow' ref="table">
                                        <td v-text="port.port"></td>

                                        <td>
                                            <vue-badge
                                                :text="port.active && ! port.fiber ? 'Active' : 'Inactive'"
                                                :color="port.active && ! port.fiber ? 'success' : 'danger'"
                                            >
                                        </td>

                                        @if( Auth::user()->hasAnyPermission('admin', 'edit port' ) )
                                            <td class="description" v-if="! port.editing" v-text="port.description" @click="enableEdit(port)">
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
                                            @if( Auth::user()->hasAnyPermission( 'admin', 'edit network' ) )
                                                <base-button 
                                                    icon='fas fa-ethernet'
                                                    size='sm'
                                                    type='primary'
                                                    :tooltip="{ title: 'Change Mode', placement: 'top' }"
                                                    @click.native="toggleModal('mode',port)"
                                                ></base-button>
                                                <base-button 
                                                    icon='fas fa-project-diagram'
                                                    size='sm'
                                                    type='info'
                                                    :tooltip="{ title: 'Manage Vlans', placement: 'top' }"
                                                    @click.native="toggleModal('vlans',port)"
                                                ></base-button>
                                                <base-button
                                                    v-if="port.logs_count > 0"
                                                    tag='a'
                                                    :href="`{{ url( '/' )}}/network/port/${port.id}/logs`"
                                                    icon='fas fa-history'
                                                    size='sm'
                                                    type='default'
                                                    :tooltip="{ title: 'View Logs', placement: 'top' }"
                                                ></base-button>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr v-for="port in searchPorts.slice(ports.length - networkSwitch.fiber_ports)" class='myTableRow' ref="table">
                                        <td v-text="port.port + 'f'"></td>

                                        <td>
                                            <vue-badge
                                                :text="port.active && port.fiber ? 'Active' : 'Inactive'"
                                                :color="port.active && port.fiber ? 'success' : 'danger'"
                                            >
                                        </td>

                                        @if( Auth::user()->hasAnyPermission('admin', 'edit port' ) )
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
                                            @if( Auth::user()->hasAnyPermission( 'admin', 'edit network' ) )
                                            <base-button 
                                                    icon='fas fa-ethernet'
                                                    size='sm'
                                                    type='primary'
                                                    :tooltip="{ title: 'Change Mode', placement: 'top' }"
                                                    @click.native="toggleModal('mode',port)"
                                                ></base-button>
                                                <base-button 
                                                    icon='fas fa-project-diagram'
                                                    size='sm'
                                                    type='info'
                                                    :tooltip="{ title: 'Manage Vlans', placement: 'top' }"
                                                    @click.native="toggleModal('vlans',port)"
                                                ></base-button>
                                                <base-button
                                                    v-if="port.logs_count > 0"
                                                    tag='a'
                                                    :href="`{{ url( '/' )}}/network/port/${port.id}/logs`"
                                                    icon='fas fa-history'
                                                    size='sm'
                                                    type='default'
                                                    :tooltip="{ title: 'View Logs', placement: 'top' }"
                                                ></base-button>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
           
            <vue-modal :show.sync="modal.active">
                <h1 slot="header" class="modal-title" id="modal-title-default" v-if="modal.active">Port @{{ activePort.port }} - @{{ modal.type == 'mode' ? 'Change Mode' : 'Manage Vlans' }}</h1>
    
                <form role="form" v-if="modal.type == 'mode'">
                    
                    <div class="form-group mb-3">
                        <label class="form-control-label" for="mode">Mode</label>
                        <multiselect 
                            v-model="mode.mode" 
                            :options="modes"
                            :searchable="true"
                            placeholder="Vlans"
                            @input="selectVlans"
                        > 
                        </multiselect>                    
                    </div>

                    <div class="form-group">
                        <label class="form-control-label" for="mode">Vlan</label>
                        <multiselect
                            v-if="mode.mode == 'access'"
                            v-model="mode.vlans" 
                            :options="vlans"
                            :searchable="true"
                            placeholder="Vlans"
                            label="vlan"
                            :multiple="false"
                            track-by="vlan"
                        > 
                        </multiselect>  
                        <multiselect
                            v-if="mode.mode == 'general'"
                            v-model="mode.vlans" 
                            :options="vlans"
                            :searchable="true"
                            placeholder="Vlans"
                            label="vlan"
                            :multiple="true"
                            track-by="vlan"
                            :close-on-select="false"
                        > 
                        </multiselect>  
                    </div>
                    <div class="form-group" v-if="mode.mode == 'general'">
                        <div class="custom-control custom-checkbox mb-3">
                            <input class="custom-control-input" v-model="taggedCheck" id="tagged" type="checkbox">
                            <label class="custom-control-label" for="tagged">Tagged</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox mb-3">
                            <input class="custom-control-input" v-model="configCheck" id="copyconfig" type="checkbox">
                            <label class="custom-control-label" for="copyconfig">Copy running config to startup config</label>
                        </div>
                    </div>
                </form>

                <form role="form" v-if="modal.type == 'vlans'">
                    <div class="form-group">
                        <label class="form-control-label" for="mode">Vlans</label>
                        <multiselect
                            v-if="vlan.mode == 'access'"
                            v-model="vlan.vlans" 
                            :options="vlans"
                            :searchable="true"
                            placeholder="Vlans"
                            label="vlan"
                            :multiple="false"
                            track-by="vlan"
                        > 
                        </multiselect>  
                        <multiselect
                            v-if="vlan.mode == 'general'"
                            v-model="vlan.vlans" 
                            :options="vlans"
                            :searchable="true"
                            placeholder="Vlans"
                            label="vlan"
                            :multiple="true"
                            track-by="vlan"
                            :close-on-select="false"
                        > 
                        </multiselect>
                    </div>   
                    
                    <div class="form-group" v-if="vlan.mode == 'general'">
                        <div class="custom-control custom-checkbox mb-3">
                            <input class="custom-control-input" v-model="taggedCheck" id="tagged" type="checkbox">
                            <label class="custom-control-label" for="tagged">Tagged</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox mb-3">
                            <input class="custom-control-input" v-model="configCheck" id="copyconfig" type="checkbox">
                            <label class="custom-control-label" for="copyconfig">Copy running config to startup config</label>
                        </div>
                    </div>
                </form>

                <template slot="footer">
                        <base-button 
                            v-if="modal.type == 'mode'"
                            type="primary"
                            text="Change"
                            @click.native="submitModeChange"
                            :disabled="modal.disabled"
                        >Change</base-button>
                        <base-button 
                            v-if="modal.type == 'vlans'"
                            type="primary"
                            text="Save"
                            @click.native="submitVlansChange"
                            :disabled="modal.disabled"
                        >Save</base-button>
                        <base-button 
                            type="link"
                            class='ml-auto'
                            @click.native="hideModal"
                        >Close</base-button>
                    </template>
            </vue-modal>
        </div>
    </switch-view> 
@endsection
