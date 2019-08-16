@extends('layouts.app', ['title' => __('Roles Management')])
@push( 'head' )
    <link href="{{ asset( 'plugins/multi-select/multi-select.css' ) }}" rel="stylesheet">
@endpush 
@section('content')
    @include('layouts.headers.cards')
    <role-view :role="{{$role}}" :permissions="{{$permissions}}" inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col-xl-12 order-xl-1">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">@{{ capitalize( role.name ) }} - Permissions</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="{{ url( '/roles' ) }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="pl-lg-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <multiselect 
                                            v-model="value" 
                                            :options="select"
                                            :searchable="true"
                                            :multiple="true"
                                        > 
                                        </multiselect>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" @click="submitRole" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </role-view>
@endsection