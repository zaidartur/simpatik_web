    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('templates/plugins/src/global/vendors.min.js') }}"></script>
    <script src="{{ asset('templates/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    <script src="{{ asset('templates/plugins/src/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/mousetrap/mousetrap.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/waves/waves.min.js') }}"></script>
    <script src="{{ asset('templates/layouts/vertical-light-menu/app.js') }}"></script>
    <script src="{{ asset('templates/assets/js/custom.js') }}"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('templates/plugins/src/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('templates/plugins/src/sweetalerts2/sweetalerts2.min.js') }}"></script>

    <script>
        const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
        // SweetAlert Mixin
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
    </script>