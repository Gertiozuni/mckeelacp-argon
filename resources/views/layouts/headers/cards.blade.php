<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-md-12 {{ $class ?? '' }}">
                <h1 class="display-2 text-white">{{ $title }}</h1>
                @if (isset($description) && $description)
                    <p class="text-white mt-0 mb-5">{{ $description }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
        	<flash :session="{{ json_encode(session('flash_notification')[0]) }}"></flash>
        </div>
    </div>
</div>

