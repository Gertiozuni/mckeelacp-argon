@extends('layouts.app')

@section( 'title', 'Users' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => 'Users' ])
    <users-view inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ url('/users/form') }}" class="btn btn-sm btn-primary">{{ __('Add user') }}</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('Name') }}</th>
                                        <th scope="col">{{ __('Email') }}</th>
                                        <th scope="col">{{ __('Role') }}</th>
                                        <th scope="col" class="text-right">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user of users" class='myTableRow'>
                                        <td v-text="user.name"></td>
                                        <td v-text="user.email"></td>
                                        <td v-text="user.roles.length > 0 ? capitalize(user.roles[0].name) : ''"></td>
                                        <td class="text-right">
                                            @if( Auth::user()->hasAnyPermission( 'admin', 'edit users' ) )
                                                <a :href="`{{ url( '/users/form' ) }}/${user.id}`">
                                                    <button class="btn btn-sm btn-primary" type="button">
                                                        <span class="btn-inner--icon"><i class="fas fa-pencil-alt"></i></span>
                                                    </button>
                                                </a>
                                                <button v-if="user.id != {{ Auth::user()->id }}" class="btn btn-sm btn-danger" type="button" @click="deleteUser( user.id, user.name )">
                                                    <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer py-4">
                            <nav class="d-flex justify-content-end" aria-label="...">
                                <pagination :data="pagination" @pagination-change-page="getUsers" :limit="3"></pagination>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
                
            @include('layouts.footers.auth')
        </div>
    </users-view>
@endsection