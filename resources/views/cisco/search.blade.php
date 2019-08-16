@extends('layouts.app')

@section( 'title', 'Search iPads' )

@section('content')
    @include('layouts.headers.cards')
    <ciscosearch-view inline-template>
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
                                        <td>@{{ device.name }}</td>
                                        <td>@{{ device.serialNumber }}</td>
                                        <td>@{{ device.wifiMac }}</td>
                                        <td>@{{ device.network }}</td>
                                        <td>@{{ device.ssid }}</td>
                                        <td>@{{ device.osName }}</td>
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
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Search iPads</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="pl-lg-4">
                                
                                <textarea class="form-control" name='search' id='search' v-model="serials" rows="20" placeholder="DLXZ5WDO, DLXQ5ASN... &#10; or &#10;DLXZ5WDO &#10;DLXQ5ASN"></textarea>
                                
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-4" @click="search">{{ __('Search') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ciscosearch-view>
@endsection