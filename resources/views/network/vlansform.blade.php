@extends('layouts.app')

@section( 'title', 'Vlans' )

@section('content')
    @include('layouts.headers.cards')

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Roles Management</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ url( '/network/vlans' ) }}" class="btn btn-sm btn-primary">Back to list</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ url( '/network/vlans' . ( $vlan->id ? ( '/' . $vlan->id ) : '' ) ) }}" method="post" data-ajax="false" enctype="multipart/form-data">
                            @csrf

                            @if( $vlan->id )
                                @method('PATCH')
                            @endif

                            <div class="pl-lg-4">
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="vlan">Vlan</label>
                                        <input type="text" name="vlan" id="vlan" class="form-control form-control-alternative{{ $errors->has('vlan') ? ' is-invalid' : '' }}" value="{{ old('vlan', $vlan->vlan) }}" required autofocus>

                                        @if ($errors->has('vlan'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('vlan') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="description">Description</label>
                                        <input type="text" name="description" id="description" class="form-control form-control-alternative{{ $errors->has('description') ? ' is-invalid' : '' }}" value="{{ old('description', $vlan->description) }}" autofocus>

                                        @if ($errors->has('description'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('description') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="subnet">Subnet</label>
                                        <input type="text" name="subnet" id="subnet" class="form-control form-control-alternative{{ $errors->has('subnet') ? ' is-invalid' : '' }}" value="{{ old('subnet', $vlan->subnet) }}" required autofocus>

                                        @if ($errors->has('subnet'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('subnet') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        @include('layouts.footers.auth')
    </div>
@endsection