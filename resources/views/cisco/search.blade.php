@extends('layouts.app')

@section( 'title', 'Search iPads' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => 'Search iPads' ])
    <ciscosearch-view v-cloak inline-template>
        <div class="container-fluid mt--7">
            <div class="row" v-show="devices">
                <div class="col-xl-12 order-xl-1">
                    <div class="card bg-secondary shadow">
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Serial</th>
                                        <th scope="col">MAC</th>
                                        <th scope="col">Network</th>
                                        <th scope="col">SSID</th>
                                        <th scope="col">OS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="device of devices" class='myTableRow'>
                                        <td v-text="device.name"></td>
                                        <td v-text="device.serialNumber"></td>
                                        <td v-text="device.wifiMac"></td>
                                        <td v-text="device.network"></td>
                                        <td v-text="device.ssid"></td>
                                        <td v-text="device.osName"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12 order-xl-1">
                    <div class="card bg-secondary shadow">
                        <div class="card-body">
                            <div class="pl-lg-4">
                                <textarea class="form-control" name='search' id='search' v-model="serials" rows="20" placeholder="DLXZ5WDO, DLXQ5ASN... &#10; or &#10;DLXZ5WDO &#10;DLXQ5ASN"></textarea>
                            </div>
                            <div class="text-center">
                                <base-button type="success" class="mt-4" @click.native="search">Search</base-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ciscosearch-view>
@endsection