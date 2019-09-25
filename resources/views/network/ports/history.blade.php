@extends('layouts.app')

@section( 'title', 'Switches' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => 'Switch ' . $port->switch->ip_address . ' - Port ' . $port->port ])

    <port-history-view :port="{{ $port }}" :history-prop="{{ json_encode($histories) }}" inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <a href="{{ $port->switch->url() }}">
                                        <vue-button
                                            text='Back'
                                            color='primary'
                                            size='small'
                                        ></vue-button>
                                    </a>
                                </div>
                                <div class="col-4 text-right">
                                    <input type="text" placeholder="search" v-model='search' v-on:keyup.enter="getHistories">
                                </div>
                            </div>
                        </div>
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Event</th>
                                    <th scope="col">User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class='myTableRow' v-for="history of histories.data">
                                    <td v-text="moment(history.uptime).format('DD-MM-YYYY HH:mm:ss')"></td>
                                    <td v-text="history.info"></td>
                                    <td v-text="history.user ? history.user.name : 'System'"></td>
                                </tr>
                            </tbody>
                        </table>         
                    </div>
                    <div class="card-footer py-4" v-show="histories.total > 20">
                        <nav class="d-flex justify-content-end" aria-label="...">
                            <pagination :data="histories" @pagination-change-page="getHistories" :limit="3"></pagination>
                        </nav>
                    </div>
                </div>
            </div>
            @include('layouts.footers.auth')
        </div>
    </port-history-view>
@endsection
