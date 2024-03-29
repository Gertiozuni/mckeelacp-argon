@extends('layouts.app')

@section( 'title', 'Permissions Form' )

@section('content')
    @include('users.partials.header', ['title' => ( $perm->id ? 'Edit' : 'Add' ) . ' Permission'])   

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                {{ $perm->id ? $perm->name : '' }}
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ url( '/permissions' ) }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ url( '/permissions' . ( $perm->id ? ( '/' . $perm->id ) : '' ) ) }}" method="post" data-ajax="false" enctype="multipart/form-data">
                            @csrf

                            @if( $perm->id )
                                @method('PATCH')
                            @endif

                            <div class="pl-lg-4">
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="name">{{ __('Name') }}</label>
                                        <input type="text" name="name" id="name" class="form-control form-control-alternative{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}" value="{{ old('name', $perm->name) }}" required autofocus>

                                        @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
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
        
        @include('layouts.footers.auth')
    </div>
@endsection