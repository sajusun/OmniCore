<!-- BACK-TO-TOP -->
<a href="#top" id="back-to-top"><i class="fa fa-long-arrow-up"></i></a>


<!-- JQUERY JS -->
<script src="{{ asset('backend/plugins/jquery/jquery.min.js') }}"></script>

<!-- BOOTSTRAP JS -->
<script src="{{ asset('backend/plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('backend/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

<!-- SIDE-MENU JS -->
<script src="{{ asset('backend/plugins/sidemenu/sidemenu.js') }}"></script>

<!-- Perfect SCROLLBAR JS-->
<script src="{{ asset('backend/plugins/p-scroll/perfect-scrollbar.js') }}"></script>
<!-- <script src="{{ asset('backend/plugins/p-scroll/pscroll.js') }}"></script> -->

<!-- STICKY JS -->
<script src="{{ asset('backend/js/sticky.js') }}"></script>


<!-- APEXCHART JS -->
{{-- <script src="{{ asset('backend/js/apexcharts.js') }}"></script> --}}

<!-- INTERNAL SELECT2 JS -->
<script src="{{ asset('backend/plugins/select2/select2.full.min.js') }}"></script>

<!-- CHART-CIRCLE JS-->
<script src="{{ asset('backend/plugins/circle-progress/circle-progress.min.js') }}"></script>

{{-- DATA TABLE JS --}}
{{-- <script src="{{ asset('backend/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/js/dataTables.buttons.min.js') }}"></script> --}}
{{-- <script src="{{ asset('backend/plugins/datatable/js/butsns.bootstrap5.min.js') }}"></script> --}}
{{-- <script src="{{ asset('backend/plugins/datatable/js/jszip.min.js') }}"></script> --}}
<script src="{{ asset('backend/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
{{-- <script src="{{ asset('backend/plugins/datatable/js/butsns.html5.min.js') }}"></script> --}}
<script src="{{ asset('backend/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('backend/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
<script src="{{ asset('backend/js/table-data.js') }}"></script>


<!-- INTERNAL Summernote Editor js -->
<script src="{{ asset('backend/plugins/summernote-editor/summernote1.js') }}"></script>
<script src="{{ asset('backend/js/summernote.js') }}"></script>

{{-- toaster js --}}
<script src="{{ asset('backend/js/toastr.min.js') }}"></script>


{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<!-- loader -->
<script src="{{ asset('default') }}/nprogress/nprogress.js"></script>


<!-- Toster -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>
    toastr.options = {
        "closeButton": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000"
    };
</script>

@include('backend.partials._toster')

@include('backend.partials._ajax')

@include('backend.partials._custom-script')


@stack('scripts')