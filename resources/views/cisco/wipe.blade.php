@extends('layouts.app')

@section( 'title', 'Wipe iPads' )

@section('content')
    @include('layouts.headers.cards')
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Wipe iPads</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ url( '/cisco/wipe/ ' ) }}">
                        	@csrf
	                        <div class="pl-lg-4">
	                            
	                            <textarea class="form-control" name='serials' id='serials' rows="20" placeholder="DLXZ5WDO, DLXQ5ASN... &#10; or &#10;DLXZ5WDO &#10;DLXQ5ASN"></textarea>
	                            
	                        </div>
	                        <div class="text-center">
	                            <button type="submit" class="btn btn-success mt-4">{{ __('Search') }}</button>
	                        </div>
	                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection