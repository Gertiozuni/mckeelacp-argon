@extends('layouts.app')

@section( 'title', 'Permissions' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => 'Permissions' ])
    <permissions-view :permissions-init="{{ json_encode( $permissions ) }}" inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                </div>
                                <div class="col-4 text-right">
                                    <input type="text" name="search" placeholder="search" id="search" v-model='search' v-on:keyup.enter="getPermissions">
                                    <a href="{{ url('/permissions/form') }}" class="btn btn-sm btn-primary">{{ __('Add Permission') }}</a>
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
                                    <tr v-for="permission of permissions.data" class='myTableRow'>
                                        <td v-text="permission.name"></td>
                                        <td class="text-right">
                                            @if( Auth::user()->hasAnyPermission( 'admin', 'edit users' ) )
                                                <a :href="`{{ url( '/permissions/form' ) }}/${permission.id}`">
                                                    <button class="btn btn-sm btn-primary" type="button">
                                                        <span class="btn-inner--icon"><i class="fas fa-pencil-alt"></i></span>
                                                    </button>
                                                </a>
                                                <button class="btn btn-sm btn-danger" type="button" @click="deletePermission( permission.id, ' permission.name' )">
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
                                <pagination :data="permissions" @pagination-change-page="getPermissions" :limit="3"></pagination>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
                
            @include('layouts.footers.auth')
        </div>
    </permissions-view>
@endsection