@extends('layouts.app')

@section( 'title', 'Vlans' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => 'Vlans' ])
    <vlans-view v-cloak :vlans="{{$vlans}}" inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                </div>
                                <div class="col-4 text-right">
                                    <input type="text" v-model="search" placeholder="Search"/>
                                    @if( Auth::user()->hasAnyPermission( 'admin', 'edit vlans' ) )
                                        <a href="{{ url('/network/vlans/form') }}" class="btn btn-sm btn-primary">Add Vlan</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Vlan</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Subnet</th>
                                        <th scope="col" class="text-right">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="vlan of filteredVlans" class='myTableRow' ref="table">
                                        <td v-text="vlan.vlan"></td>
                                        <td v-text="vlan.description"></td>
                                        <td v-text="'/' + vlan.subnet"></td>
                                        <td class="text-right">
                                            @if( Auth::user()->hasAnyPermission( 'admin', 'edit vlans' ) )
                                                <base-button
                                                    tag="a"
                                                    :href="`{{ url( '/network/vlans/form' ) }}/${vlan.id}`"
                                                    type="primary"
                                                    size="sm"
                                                    icon="fas fa-pencil-alt"
                                                ></base-button>
                                                <base-button
                                                    type="danger"
                                                    icon="fas fa-trash"
                                                    size="sm"
                                                    @click.native="deleteVlan( vlan )"
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
                
            @include('layouts.footers.auth')
        </div>
    </campus-view>
@endsection