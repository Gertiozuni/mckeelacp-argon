@extends('layouts.app')

@section( 'title', 'Apple Classroom' )

@section('content')
    @include('layouts.headers.cards')
    <appleclassroom-view inline-template>
        <div class="container-fluid mt--7">
            <div class="row">
                <div class="col-xl-12 order-xl-1">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Apple Classroom</h3>
                                </div>
                            </div>
                        </div>
                            <div class="card-body">
                                <div class="pl-lg-4">
                                    <dropzone 
                                        ref="myVueDropzone" 
                                        id="dropzone" 
                                        @vdropzone-success-multiple="updateClassroom()"
                                        @vdropzone-error-multiple="uploadError(file, message, xhr)"
                                        :options="{
                                            url: '/appleclassroom/upload',
                                            uploadMultiple: true,
                                            thumbnailWidth: 150,
                                            autoProcessQueue: false,
                                            addRemoveLinks: true,
                                            parallelUploads: 6,
                                            maxFiles: 6,
                                            acceptedFiles: '.csv,.xls,.xlsx',
                                            headers: { 'X-CSRF-TOKEN': `${token}` }
                                        }"                        
                                    />
                                </div>
                            <div class="text-center">
                                <button type="submit" @click="upload" class="btn btn-success mt-4">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </appleclassroom-view>
@endsection

@push( 'js' )
@endpush