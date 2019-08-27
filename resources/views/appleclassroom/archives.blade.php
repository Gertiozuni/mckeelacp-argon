@extends('layouts.app')

@section( 'title', 'Archives' )

@section('content')
    @include('layouts.headers.cards', [ 'title' => 'Archives' ] )
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('Date') }}</th>
                                        <th scope="col">{{ __('Files') }}</th>
                                        <th scope="col">{{ __('Students Added') }}</th>
                                        <th scope="col">{{ __('Students Removed') }}</th>
                                        <th scope="col">{{ __('Teachers Added') }}</th>
                                        <th scope="col">{{ __('Teachers Removed') }}</th>
                                        <th scope="col">{{ __('Done By') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach( $archives as $arc )
                                        <tr class='myTableRow'>
                                            <td>{{ $arc->created_at->format('d-m-Y') }}</td>
                                            <td>
                                                <a href="{{ url( '/appleclassroom/download/' . $arc->id . '?type=results' ) }}">Results</a><br />

                                                <a href="{{ url( '/appleclassroom/download/' . $arc->id . '?type=originals' ) }}">Originals</a>
                                            </td>
                                            <td>{{ $arc->students_added }}</td>
                                            <td>{{ $arc->students_removed }}</td>
                                            <td>{{ $arc->teachers_added }}</td>
                                            <td>{{ $arc->teachers_removed }}</td>
                                            <td>{{ $arc->user->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer py-4">
                            <nav class="d-flex justify-content-end" aria-label="...">
                                {{ $archives->links() }}
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
                
            @include('layouts.footers.auth')
        </div>
@endsection