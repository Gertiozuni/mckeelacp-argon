@extends('layouts.app')

@section( 'title', 'Switchs' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => $switch->id ? 'Edit Switch' : 'Add Switch' ])

    <switchform-view v-cloak :campuses="{{ $campuses }}" :switch="{{ $switch }}" inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col-xl-12 order-xl-1">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                </div>
                                <div class="col-4 text-right">
                                    <base-button tag="a" type="primary" size="sm" href="{{ url('/network/switches') }}" role="button">Back to Switches</base-button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ url( '/network/switches' . ( $switch->id ? ( '/' . $switch->id ) : '' ) ) }}" method="post" data-ajax="false" enctype="multipart/form-data">
                                @csrf

                                @if( $switch->id )
                                    @method('PATCH')
                                @endif

                                <div class="pl-lg-4">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="ip_address">IP Address</label>
                                            <input type="text" name="ip_address" id="ip_address"
                                                class="form-control form-control-alternative"
                                                value="{{ old('ip_address', $switch->ip_address) }}" required autofocus
                                            >

                                            @if ($errors->has('ip_address'))
                                                <span class="invalid-input" role="alert">
                                                    <strong>{{ $errors->first('ip_address') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="campus_id">Campus</label>
                                            <multiselect
                                                v-model="value"
                                                :options="select"
                                                :searchable="true"
                                                :multiple="false"
                                                label="label"
                                                track-by="id"
                                            >
                                            </multiselect>

                                            @if ($errors->has('campus_id'))
                                                <span class="invalid-input" role="alert">
                                                    <strong>{{ $errors->first('campus_id') }}</strong>
                                                </span>
                                            @endif

                                            <input type="hidden" name="campus_id" :value="value ? value.id : null">
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="fiber_ports">Fiber Ports</label>
                                            <input type="text" name="fiber_ports" id="fiber_ports"
                                                class="form-control form-control-alternative"
                                                value="{{ old('fiber_ports', $switch->fiber_ports) }}" autofocus>

                                            @if ($errors->has('fiber_ports'))
                                                <span class="invalid-input" role="alert">
                                                    <strong>{{ $errors->first('fiber_ports') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="location">Location</label>
                                            <input type="text" name="location" id="location"
                                                class="form-control form-control-alternative"
                                                value="{{ old('location', $switch->location) }}" autofocus>

                                            @if ($errors->has('location'))
                                                <span class="invalid-input" role="alert">
                                                    <strong>{{ $errors->first('location') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label" for="sub_location">Sub Location</label>
                                            <input type="text" name="sub_location" id="sub_location"
                                                class="form-control form-control-alternative"
                                                value="{{ old('sub_location', $switch->sub_location) }}" autofocus>

                                            @if ($errors->has('sub_location'))
                                                <span class="invalid-input" role="alert">
                                                    <strong>{{ $errors->first('sub_location') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <base-button type="success" native-type='submit' role="button" class='mt-4'>Save</base-button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @include('layouts.footers.auth')
        </div>
    </switchform-view>
@endsection
