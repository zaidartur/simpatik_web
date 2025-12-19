@extends('layouts.layout')

@section('title', 'Duplikat Surat')


@section('css')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/table/datatable/datatables.css') }}">
    
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/table/datatable/dt-global_style.css') }}">

    <link href="{{ asset('templates/assets/css/light/components/modal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/src/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/light/bootstrap-touchspin/custom-jquery.bootstrap-touchspin.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/plugins/css/dark/bootstrap-touchspin/custom-jquery.bootstrap-touchspin.min.css') }}">
    <!-- END PAGE LEVEL STYLES -->

    <style>
        table.dataTable td,
        table.dataTable th {
            white-space: normal !important; /* Allows text to wrap */
            word-wrap: break-word !important; /* Breaks long words if necessary */
        }

        .table-overlay {
            position: absolute;
            top: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.7);
            width: 100%;
            height: 100%;
            z-index: 100;
            display: none;

            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: none; 
        }

        /* Spinner */
        .table-overlay .spinner {
            border: 5px solid #ccc;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        /* Spinner Animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection


@section('content')
<div class="layout-px-spacing">

    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">

            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="widget-content widget-content-area br-8 p-3">
                    <div class="row justify-content-space-between">
                        <div class="col-6">
                            <h4 class="">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                Duplikat Surat
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="widget-content widget-content-area br-8 m-3 p-4" style="position: relative;">
                    <div class="row g-4 mb-5 justify-content-between header-div">
                        <div class="col-md-12 col-sm-12 search-div">
                            <input type="text" class="form-control mb-2" name="nosurat" id="nosurat" placeholder="Masukkan nomor surat..." onkeypress="_input_cari(event)">
                            <div class="row justify-content-center">
                                <button type="button" class="btn btn-outline-primary col-6" id="btsearch" onclick="_cari()">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                    <span class="btn-text-inner">Cari</span>
                                </button>
                            </div>
                        </div>
                        {{-- <div class="col-md-6 col-sm-12 row justify-content-center print-div">
                            <div class="col-12 mb-2 mt-4">
                                <input type="text" class="" name="jmlsurat" id="jmlsurat" placeholder="Masukkan jumlah duplikat..." onkeypress="_input_print(event)">
                            </div>
                            <div class="row justify-content-center">
                                <button type="button" class="btn btn-outline-secondary col-6" id="btprint" onclick="_print()">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                    <span class="btn-text-inner">Cetak</span>
                                </button>
                            </div>
                        </div> --}}
                    </div>
                    <div class="col-12 row g-4">
                        <input type="hidden" name="id_surat" id="id_surat" value="">
                        <hr class="col-12 mb-5">
                        <div class="col-md-4 col-sm-12">
                            <div id="results"></div>
                        </div>
                        <div class="col-md-8 col-sm-12">
                            <div class="" id="description"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
</div>
@endsection


@section('js')
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('templates/plugins/src/table/datatable/datatables.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
    {{-- <script src="{{ asset('templates/plugins/src/bootstrap-touchspin/custom-bootstrap-touchspin.js') }}"></script> --}}
    <script>
        //
    </script>
    <!-- END PAGE LEVEL SCRIPTS -->

    <script>
        // $(document).ready(function() {
        //     $("input[name='jmlsurat']").TouchSpin({
        //         buttondown_class: "btn btn-danger",
        //         buttonup_class: "btn btn-success"
        //     });
        // })

        async function _cari() {
            let text = $('#nosurat').val()
            if (text) {
                let serach = $('#btsearch')
                $('#id_surat').val('')

                serach.html(`<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader spin me-2"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg> Memproses...`)
                serach.removeClass('btn-outline-primary')
                serach.addClass('btn-primary')
                serach.attr('disabled', '')
                $('#results').html('')
                $('#description').html('')
                _reset()
                await new Promise(rsv => {
                    $.ajax({
                        url: "{{ route('outbox.check') }}",
                        type: "POST",
                        data: { _token: $('meta[name="csrf-token"]').attr('content'), nosurat: text },
                        dataType: "JSON",
                        success: function(res) {
                            if (res.status === 'success') {
                                console.log(res.data)
                                // let content = ''
                                let content = `<div class="row">
                                                <div class="col-12">
                                                    <div class="list-group" id="list-tab" role="tablist">`

                                let desc = `<div class="col-12">
                                                <div class="tab-content" id="nav-tabContent">`

                                res.data.forEach((dt, d) => {
                                    content += `<a class="list-group-item list-group-item-action ${d === 0 ? 'active' : ''} d-flex justify-content-between align-items-center" data-value="${dt.NO}" id="list_${dt.NO}_list" data-bs-toggle="list" href="#list_${dt.NO}" role="tab" aria-controls="list_${dt.NO}">
                                                    ${dt.NOSURAT}
                                                    ${d === 0 ? '<span class="badge bg-warning rounded-pill"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></span>' : ''}
                                                </a>`

                                    desc += `
                                                    <div class="tab-pane fade ${d === 0 ? 'show active' : ''}" id="list_${dt.NO}" role="tabpanel" aria-labelledby="list_${dt.NO}_list">
                                                        <div class="">
                                                            ${template_content(dt)}
                                                        </div>
                                                    </div>
                                    `
                                });

                                content += `
                                                    </div>
                                                </div>
                                            </div>
                                            `
                                desc += `                                                   
                                                </div>
                                            </div>`

                                $('#results').html(content)
                                $('#description').html(desc)
                                $('#id_surat').val(res.data[0].NO)
                                _show_print()

                                $('#list-tab a').on('click', function (e) {
                                    e.preventDefault()
                                    // console.log(e.target.id)
                                    const id = e.target.id
                                    // console.log(id)
                                    $('#id_surat').val(id.split('_')[1])
                                    $('#list-tab a').each(function () {
                                        $(this).find('span').remove();
                                    });
                                    $(`#${id}`).append('<span class="badge bg-warning rounded-pill"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></span>')

                                    $(this).tab('show')
                                })
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: res.message
                                });
                            }

                            serach.html('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg><span class="btn-text-inner">Cari</span>')
                            serach.removeClass('btn-primary')
                            serach.addClass('btn-outline-primary')
                            serach.removeAttr('disabled')
                            rsv()
                        },
                        error: function() {
                            Toast.fire({
                                icon: 'error',
                                title: 'Terjadi kesalahan pada sistem.'
                            });

                            serach.html('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg><span class="btn-text-inner">Cari</span>')
                            serach.removeClass('btn-primary')
                            serach.addClass('btn-outline-primary')
                            serach.removeAttr('disabled')
                        }
                    })
                })
            } else {
                Toast.fire({
                    icon: 'error',
                    title: 'Mohon masukkan nomor surat.'
                });
            }
        }

        function _input_cari(e) {
            if (e.keyCode === 13 || e.key === 'Enter') {
                _cari()
            }
        }

        function _reset() {
            $('.print-div').remove()
            $('.search-div').removeClass('col-md-6')
            $('.search-div').addClass('col-md-12')
        }

        function _show_print() {
            let print = `<div class="col-md-6 col-sm-12 row justify-content-center print-div">
                            <div class="col-12 mb-2 mt-4">
                                <input type="text" class="" name="jmlsurat" id="jmlsurat" placeholder="Masukkan jumlah duplikat..." onkeypress="_input_print(event)">
                            </div>
                            <div class="row justify-content-center">
                                <button type="button" class="btn btn-outline-secondary col-6" id="btprint" onclick="_print()">
                                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                    <span class="btn-text-inner">Konfirmasi & Cetak</span>
                                </button>
                            </div>
                        </div>`

            $('.search-div').removeClass('col-md-12')
            $('.search-div').addClass('col-md-6')
            $('.header-div').append(print)
            $("input[name='jmlsurat']").TouchSpin({
                buttondown_class: "btn btn-danger",
                buttonup_class: "btn btn-success"
            });
        }

        async function _print() {
            let jml = $('#jmlsurat').val()
            // console.log(Number.isInteger(parseInt(jml)), parseInt(jml))
            if (Number.isInteger(parseInt(jml)) && parseInt(jml) > 0) {
                Toast.fire({
                    icon: 'success',
                    title: jml
                });

                let prints = $('#btprint')
                prints.html(`<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader spin me-2"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg> Memproses...`)
                prints.removeClass('btn-outline-secondary')
                prints.addClass('btn-secondary')
                prints.attr('disabled', '')
                const uid = $('#id_surat').val()
                await new Promise((resolve) => {
                    $.ajax({
                        url: "{{ route('outbox.duplikat') }}",
                        type: "POST",
                        data: {uid: btoa(uid), jumlah: parseInt(jml), _token: $('meta[name="csrf-token"]').attr('content')},
                        dataType: "JSON",
                        success: function (r) {
                            //

                            prints.html('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg><span class="btn-text-inner">Konfirmasi & Cetak</span>')
                            prints.removeClass('btn-secondary')
                            prints.addClass('btn-outline-secondary')
                            prints.removeAttr('disabled')
                            resolve()
                        },
                        error: function() {
                            prints.html('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg><span class="btn-text-inner">Konfirmasi & Cetak</span>')
                            prints.removeClass('btn-secondary')
                            prints.addClass('btn-outline-secondary')
                            prints.removeAttr('disabled')
                        }
                    })
                })
            } else {
                Toast.fire({
                    icon: 'error',
                    title: 'Mohon masukkan jumlah duplikat.'
                });
            }
        }

        function _input_print(e) {
            if (e.keyCode === 13 || e.key === 'Enter') {
                _print()
            }
        }

        function template_content(datas) {
            let txt = ''
            txt += `<h5 class="mb-3">Nomor Surat: <b>${datas.NOSURAT}</b></h5>`
            txt += `<div class="">`
            txt += `<table class="table table-bordered" style="width: 100%">
                        <tr>
                            <td>KEPADA : <b>${datas.drkpd ?? '' }</b></td>
                        </tr>
                        <tr>
                            <td><div class="title">INDEX : <b>${datas.noagenda2}</b></div></td>
                        </tr>
                        <tr>
                            <td>TGL. SURAT : <b>${datas.TGLSURAT ?? ''}</b></td>
                        </tr>
                        <tr>
                            <td>KODE : <b>${datas.KLAS3 ?? ''}</b></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="height: 70px;">ISI : <b>${datas.ISI ?? ''}</b></td>
                        </tr>
                        <tr>
                            <td colspan="3">ALAMAT : <b>${datas.NAMAKOTA ?? ''}</b></td>
                        </tr>
                        <tr>
                            <td>UNIT PENGOLAH : <b>${datas.NAMAUP ?? '' }</b></td>
                        </tr>
                        <tr>
                            <td>TGL. PEMBUATAN : <b>${datas.TGLTERIMA ?? ''}</b></td>
                        </tr>
                    </table>`
            txt += '</div>'

            return txt
        }
    </script>
@endsection