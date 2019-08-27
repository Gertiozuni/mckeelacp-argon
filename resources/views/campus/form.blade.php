@extends('layouts.app')

@section( 'title', 'Campuses' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => ( $campus->id ? 'Edit' : 'Add' ) . ' Campus' ])
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ $campus->id ? $campus->name : '' }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ url( '/campuses' ) }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ url( '/campuses' . ( $campus->id ? ( '/' . $campus->id ) : '' ) ) }}" method="post" data-ajax="false" enctype="multipart/form-data">
                            @csrf

                            @if( $campus->id )
                                @method('PATCH')
                            @endif

                            <div class="pl-lg-4">
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="name">{{ __('Name') }}</label>
                                        <input type="text" name="name" id="name" class="form-control form-control-alternative{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name', $campus->name) }}" required autofocus>

                                        @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label" for="abbreviation">{{ __('Abbreviation') }}</label>
                                        <input type="text" name="abbreviation" id="abbreviation" class="form-control form-control-alternative{{ $errors->has('abbreviation') ? ' is-invalid' : '' }}" value="{{ old('abbreviation', $campus->abbreviation ) }}" required autofocus>

                                        @if ($errors->has('abbreviation'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('abbreviation') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-control-label" for="code">{{ __('Code') }}</label>
                                        <input type="text" name="code" id="code" class="form-control form-control-alternative{{ $errors->has('code') ? ' is-invalid' : '' }}" value="{{ old('code', $campus->code ) }}" required autofocus>

                                        @if ($errors->has('code'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('code') }}</strong>
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