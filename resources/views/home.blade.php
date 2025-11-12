@extends('layouts.layout')

@section('title', 'Dashboard')


@section('css')
    <link href="{{ asset('templates/plugins/src/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('templates/plugins/css/light/fullcalendar/custom-fullcalendar.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css">

    <link href="{{ asset('templates/plugins/css/dark/fullcalendar/custom-fullcalendar.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css">
@endsection


@section('content')
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">

        <div class="row layout-top-spacing layout-spacing" id="cancel-row">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="calendar-container">
                    <div class="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detail Surat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="">
                                    <label class="form-label">Nomor Surat</label>
                                    <input id="event-number" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="">
                                    <label class="form-label">Dari/Kepada</label>
                                    <textarea name="event-title" id="event-title" class="form-control" cols="30" rows="3"></textarea>
                                    {{-- <input id="event-title" type="text" class="form-control"> --}}
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="">
                                    <label class="form-label">Isi Surat</label>
                                    <textarea name="event-content" id="event-content" class="form-control" cols="30" rows="5"></textarea>
                                    {{-- <input id="event-title" type="text" class="form-control"> --}}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="">
                                    <label class="form-label">Tanggal Surat</label>
                                    <input id="event-date" type="date" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-12">

                                <div class="d-flex mt-4">
                                    <div class="n-chk">
                                        <div class="form-check form-check-primary form-check-inline">
                                            <input class="form-check-input" type="radio" name="event-level" value="Masuk" id="rMasuk" disabled>
                                            <label class="form-check-label" for="rMasuk">Masuk</label>
                                        </div>
                                    </div>
                                    <div class="n-chk">
                                        <div class="form-check form-check-danger form-check-inline">
                                            <input class="form-check-input" type="radio" name="event-level" value="Keluar" id="rKeluar" disabled>
                                            <label class="form-check-label" for="rKeluar">Keluar</label>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-success btn-update-event" data-fc-event-public-id="">Update changes</button> --}}
                        {{-- <button type="button" class="btn btn-primary btn-add-event">Add Event</button> --}}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection


@section('js')
    <script src="{{ asset('templates/plugins/src/fullcalendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/uuid/uuid4.min.js') }}"></script>
    <script src="{{ asset('templates/assets/js/custom-fullcalendar.js') }}"></script>

    <script>
        //
    </script>
@endsection
