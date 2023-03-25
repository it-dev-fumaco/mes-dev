@extends('layouts.user_app', [
  'namePage' => 'Delivery Schedule List',
  'activePage' => 'weekly_rejection_report',
  'pageHeader' => 'Delivery Schedule List',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
    <div class="panel-header"></div>
    <div class="row p-2" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
        <div class="col-12 m-0 pt-2 bg-white border">
            <div class="row p-2 justify-content-between" style="display: flex; justify-content: center; align-items: center;">
                <a class="p-2" href="/view_delivery_list/{{ Carbon\Carbon::parse($date)->subWeek()->format('Y-m-d') }}">
                    <i class="fa fa-arrow-circle-left" style="font-size: 23pt"></i>
                </a>
                <h5 class="m-0">
                    {{ Carbon\Carbon::parse($date)->startOfWeek()->format('F d, Y') .' - '. Carbon\Carbon::parse($date)->endOfWeek()->format('F d, Y') }}
                </h5>
                <a class="p-2" href="/view_delivery_list/{{ Carbon\Carbon::parse($date)->addWeek()->format('Y-m-d') }}">
                    <i class="fa fa-arrow-circle-right" style="font-size: 23pt"></i>
                </a>
            </div>
            <div class="card">
                <div class="card-header p-2">
                    
                    <div class="row p-2" style="background-color: #0276BC;">
                        <div class="col-1 text-white" style="display: flex; justify-content: center; align-items: center; font-weight: 700">Filters</div>
                        <div class="col-2 offset-2 p-1">
                            <select id="customer-selection" class="form-control bg-white selection">
                                <option value="" disabled selected>Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer }}">{{ $customer }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2 p-1">
                            <select id="project-selection" class="form-control bg-white selection">
                                <option value="" disabled selected>Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project }}">{{ $project }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2 p-1">
                            <select id="reference-selection" class="form-control bg-white selection">
                                <option value="" disabled selected>Reference</option>
                                @foreach ($reference_arr as $reference)
                                    <option value="{{ $reference }}">{{ $reference }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <div class="row">
                                <div class="col-10">
                                    <input type="text" id="search-string" class="form-control text-input bg-white rounded" placeholder="Search...">
                                </div>
                                <div class="col-2 p-0">
                                    <button class="btn btn-secondary btn-sm" id="clear-filters" style="margin: 3px 0 0 -10px;">
                                        <i class="now-ui-icons arrows-1_refresh-69"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0" style="background-color: #F2F2F2">
                    <div id="delivery-table" class="overflow-auto m-0" style="max-height: 80vh;"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="loader-wrapper" hidden>
        <div id="loader"></div>
        <div class="loader-section section-left"></div>
        <div class="loader-section section-right"></div>
    </div>
@endsection
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('/css/fontawesome-free/font-awesome.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/css/fontawesome-free/css/all.min.css') }}">
    <style>
        #loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 99999;
        }
        #loader {
            display: block;
            position: relative;
            left: 50%;
            top: 50%;
            width: 150px;
            height: 150px;
            margin: -75px 0 0 -75px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #3498db;
            -webkit-animation: spin 2s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
            animation: spin 2s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
            z-index: 999999;
        }
        #loader:before {
            content: "";
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #e74c3c;
            -webkit-animation: spin 3s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
            animation: spin 3s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
        }
        #loader:after {
            content: "";
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #f9c922;
            -webkit-animation: spin 1.5s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
            animation: spin 1.5s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
        }
        @-webkit-keyframes spin {
            0%   { 
                -webkit-transform: rotate(0deg);  /* Chrome, Opera 15+, Safari 3.1+ */
                -ms-transform: rotate(0deg);  /* IE 9 */
                transform: rotate(0deg);  /* Firefox 16+, IE 10+, Opera */
            }
            100% {
                -webkit-transform: rotate(360deg);  /* Chrome, Opera 15+, Safari 3.1+ */
                -ms-transform: rotate(360deg);  /* IE 9 */
                transform: rotate(360deg);  /* Firefox 16+, IE 10+, Opera */
            }
        }
        @keyframes spin {
            0%   { 
                -webkit-transform: rotate(0deg);  /* Chrome, Opera 15+, Safari 3.1+ */
                -ms-transform: rotate(0deg);  /* IE 9 */
                transform: rotate(0deg);  /* Firefox 16+, IE 10+, Opera */
            }
            100% {
                -webkit-transform: rotate(360deg);  /* Chrome, Opera 15+, Safari 3.1+ */
                -ms-transform: rotate(360deg);  /* IE 9 */
                transform: rotate(360deg);  /* Firefox 16+, IE 10+, Opera */
            }
        }
        #loader-wrapper .loader-section {
            position: fixed;
            top: 0;
            width: 100%;
            height: 100%;
            background-color:  #e5e7e9 ;
            z-index: 1000;
            opacity: 50%;
            -webkit-transform: translateX(0);  /* Chrome, Opera 15+, Safari 3.1+ */
            -ms-transform: translateX(0);  /* IE 9 */
            transform: translateX(0);  /* Firefox 16+, IE 10+, Opera */
        }
    </style>
@endsection
@section('script')
    <script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
    <script>
        $(document).ready(function(){
            load_tbl();
            $('.selection').select2();

            $('#daterange').daterangepicker({
                autoUpdateInput: false,
                opens: 'left',
                placeholder: 'Select delivery date range',
                locale: {
                    format: 'YYYY-MMM-DD',
                    separator: " to "
                }
            });

            $("#daterange").on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MMM-DD') + ' to ' + picker.endDate.format('YYYY-MMM-DD'));
                load_tbl();
            });

            $("#daterange").on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                load_tbl();
            });

            $(document).on('change', '.selection', function (e){
                e.preventDefault();
                load_tbl();
            });

            $(document).on('keyup', '#search-string', function (e){
                e.preventDefault();
                load_tbl();
            });

            $(document).on('click', '#clear-filters', function (e){
                e.preventDefault();
                $('.text-input').val('');
                $('.selection').prop('selectedIndex', 0).trigger('change');
            });

            $(document).on('click', '#delivery-list-pagination a', function(event){
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                load_tbl();
            });

            $('#reschedule_delivery_frm').submit(function(e){
                e.preventDefault();
                var url = $(this).attr("action");
                $.ajax({
                    url: url,
                    type:"POST",
                    data: $(this).serialize(),
                    success:function(data){
                        if (data.success < 1) {
                            showNotification("danger", data.message, "now-ui-icons travel_info");
                        }else{
                            showNotification("success", data.message, "now-ui-icons ui-1_check");
                            $('#reschedule-delivery-modal').modal('hide');
                            load_tbl();
                        }
                    }
                });
            });

            function load_tbl(){
                $('#loader-wrapper').removeAttr('hidden');
                $.ajax({
                    url:"/view_delivery_list/{{ $date }}",
                    type:"GET",
                    data: {
                        project: $('#project-selection').val(),
                        reference: $('#reference-selection').val(),
                        customer: $('#customer-selection').val(),
                        search_string: $('#search-string').val(),
                        limit: $('#row-limit').val(),
                    },
                    success:function(reponse){
                        $('#delivery-table').html(reponse);
                        $('#loader-wrapper').attr('hidden', true);
                    },
                    error: function(response){
                        showNotification("danger", 'An error occured. Please try again.', "now-ui-icons travel_info");
                        $('#loader-wrapper').attr('hidden', true);
                    }
                }); 
            }
        });
    </script>
@endsection