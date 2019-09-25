@extends('layouts.app')

@section( 'title', 'Switches' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => $switch->ip_address . ' - Logs'])

    <switch-logs-view :switch="{{ $switch }}" :logs-init="{{ json_encode( $logs ) }}" inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <a href="{{ '/switches' }}">
                                        <vue-button
                                            text='Back'
                                            color='primary'
                                            size='small'
                                        ></vue-button>
                                    </a>
                                </div>
                                <div class="col-4 text-right">
                                    <input type="text" placeholder="search" v-model='search' v-on:keyup.enter="getLogs">
                                </div>
                            </div>
                        </div>
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" style="width: 100px;">Date</th>
                                    <th scope="col" style="width: 50px;">Port</th>
                                    <th scope="col">Event</th>
                                    <th scope="col">User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class='myTableRow' v-for="log of logs.data">
                                    <td v-text="moment(log.uptime).format('DD-MM-YYYY HH:mm:ss')"></td>
                                    <td v-text="log.port ? log.port.port : ''"></td>
                                    <td data-toggle="tooltip" data-placement="bottom" :title="log.event">@{{ log.event | truncate(100, '...')}}</td>
                                    {{-- <td v-text="truncate(log.event, 10, ...)"></td> --}}
                                    <td v-text="log.user ? log.user.name : 'System'"></td>
                                </tr>
                            </tbody>
                        </table>         
                    </div>
                    <div class="card-footer py-4" v-show="logs.total > 20">
                        <nav class="d-flex justify-content-end" aria-label="...">
                            <pagination :data="logs" @pagination-change-page="getLogs" :limit="3"></pagination>
                        </nav>
                    </div>
                </div>
            </div>
            @include('layouts.footers.auth')
        </div>
    </switch-logs-view>
@endsection
