@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url') ] )
            System has detected some changes
        @endcomponent
    @endslot

@foreach( $messages as $ip => $switch )
# {{ $ip }}
@component('mail::panel')
@if( isset( $switch[ 'uptime' ] ) )
{{ $switch[ 'uptime' ] }}
@endif

@if( isset( $switch[ 'ports' ] ) )
@foreach( $switch[ 'ports' ] as $port => $change )
## Port {{ $port }} - {{ $change[ 'description' ] }}
@if( isset( $change[ 'status' ] ) )
* {{ $change[ 'status' ] }}
@endif
@if( isset( $change[ 'mode' ] ) )
* {{ $change[ 'mode' ] }}
@endif	
@if( isset( $change[ 'vlansremoved' ] ) )
{{ $change[ 'vlansremoved' ] }}
@endif
@if( isset( $change[ 'vlansadded' ] ) )
{{ $change[ 'vlansadded' ] }}
@endif
@endforeach
@endif
@endcomponent
@endforeach


    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            Jeff is cool
        @endcomponent
    @endslot
@endcomponent