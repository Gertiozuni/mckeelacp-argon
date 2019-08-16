@extends('layouts.app', ['title' => __('Roles Management')])
@push( 'head' )
    <link href="{{ asset( 'plugins/multi-select/multi-select.css' ) }}" rel="stylesheet">
@endpush 
@section('content')
    @include('layouts.headers.cards')

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ ucfirst( $role->name ) . ' - Permissions' }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ url( '/roles' ) }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ url( '/roles' . ( $role->id ? ( '/' . $role->id ) : '' ) . '/permissions') }}" method="post" data-ajax="false" enctype="multipart/form-data">
                            @csrf

                            <div class="pl-lg-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select id='permissions' multiple='multiple' name="permissions[]">
                                            @foreach( $permissions as $perm )
                                                @if( $role->permissions->contains( 'name', $perm->name ) )
                                                    <option value="{{ $perm->name }}" selected>{{ $perm->name }}</option>
                                                @else
                                                    <option value="{{ $perm->name }}">{{ $perm->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push( 'js' )
    <script type="text/javascript" src="{{ asset( 'plugins/multi-select/multi-select.js' ) }}"></script>

    <script>
        $('#permissions').multiSelect({
            selectableHeader: "<div class='custom-header'>Add Roles</div>",
            selectionHeader: "<div class='custom-header'>Remove Roles</div>"
        });
    </script>
@endpush