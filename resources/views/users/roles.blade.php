@extends('layouts.app')

@section( 'title', 'Roles' )

@section('content')
    @include('layouts.headers.cards')
    <roles-view inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ __('Roles') }}</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ url('/roles/form') }}" class="btn btn-sm btn-primary">{{ __('Add Role') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('Name') }}</th>
                                        <th scope="col" class="text-right">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="role of roles" class='myTableRow'>
                                        <td>@{{ role.name }}</td>
                                        <td class="text-right">
                                            @if( Auth::users()->can( 'admin', 'edit users' ) )
                                                <a :href="`{{ url( '/roles/form' ) }}/${role.id}`">
                                                    <button class="btn btn-sm btn-primary" type="button">
                                                        <span class="btn-inner--icon"><i class="fas fa-pencil-alt"></i></span>
                                                    </button>
                                                </a>
                                                <a :href="`{{ url( '/roles/show' ) }}/${role.id}`">
                                                    <button class="btn btn-sm btn-info" type="button">
                                                        <span class="btn-inner--icon"><i class="fas fa-list"></i></span>
                                                    </button>
                                                </a>
                                                <button v-if="role.name !== 'admin'" class="btn btn-sm btn-danger" type="button" @click="deleteRole( role.id )">
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
                                <pagination :data="pagination" @pagination-change-page="getRoles" :limit="3"></pagination>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
                
            @include('layouts.footers.auth')
        </div>
    </roles-view>
@endsection