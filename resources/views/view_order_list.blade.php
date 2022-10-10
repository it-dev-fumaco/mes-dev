@extends('layouts.user_app', [
  'namePage' => 'MES',
  'activePage' => 'view_order_list',
  'pageHeader' => 'Production Planning',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-0" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
    <div class="col-md-12 p-2">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 pb-0">
                         <div class="d-flex flex-row col-12 m-0">
                            <div class="pl-2 col-4" style="border-left: 5px solid #229954;">
                                <small class="d-block">Customer Order</small>
                                <span class="d-block font-weight-bolder" style="font-size: 17pt;" id="wa-scheduled">0</span>
                            </div>
                            <div class="pl-2 col-4" style="border-left: 5px solid #E67E22;">
                                <small class="d-block">Consignment Order</small>
                                <span class="d-block font-weight-bolder" style="font-size: 17pt;" id="wa-ongoing">0</span>
                            </div>
                            <div class="pl-2 col-4" style="border-left: 5px solid #48C9B0;">
                                <small class="d-block">Sample Order</small>
                                <span class="d-block font-weight-bolder" style="font-size: 17pt;" id="wa-for-feedback">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-0">
                        <form action="/order_list" method="GET" autocomplete="off">
                            <div class="d-flex flex-row justify-content-end p-0 m-0">
                                <div class="form-group col-6 p-1 m-0">
                                    <input type="text" name="q" class="form-control rounded-0" placeholder="Order Inquiry" value="{{ request('q') }}">
                                </div>
                                <div class="form-group p-1 m-0">
                                    <button class="btn btn-info m-0" type="submit">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-8 p-2">
                        <form id="order-list-form">
                            <div class="d-flex flex-row rounded-top align-items-center justify-content-between" style="background-color: #0277BD;">
                                <h6 class="m-2 p-2 text-uppercase text-white text-center">Order List</h6>
                                <div class="pt-2 pb-2">
                                    @foreach ($order_types as $order_type)
                                    <label class="pill-chk-item mr-1 ml-1 mb-0 mt-0">
                                        <input type="checkbox" class="order-types-chk" value="{{ $order_type }}" name="order_types[]">
                                        <span class="pill-chk-label">{{ $order_type }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                <div class="p-2 m-0">
                                    <input type="text" name="q" class="form-control rounded bg-white m-0 order-list-search" placeholder="Search" value="{{ request('q') }}" autocomplete="off">
                                </div>
                            </div>
                        </form>
                        <div id="order-list-div"></div>
                    </div>
                    <div class="col-md-4 p-2">
                        <form id="order-list-form">
                            <div class="d-flex flex-row rounded-top align-items-center justify-content-between" style="background-color: #0277BD;">
                                <h6 class="m-2 p-2 text-uppercase text-white text-center">Delivery Alert</h6>
                                <div class="p-2 m-0">
                                    <input type="text" name="q" class="form-control rounded bg-white m-0 order-list-search" placeholder="Search" value="{{ request('q') }}" autocomplete="off">
                                </div>
                            </div>
                        </form>
                        <table class="table table-bordered table-striped">
                            <col style="width: 25%;">
                            <col style="width: 25%;">
                            <col style="width: 25%;">
                            <col style="width: 25%;">
                            <thead class="text-primary text-center font-weight-bold text-uppercase" style="font-size: 7pt;">
                                <th class="p-2">Order No.</th>
                                <th class="p-2">Item</th>
                                <th class="p-2">Qty</th>
                                <th class="p-2">Status</th>
                            </thead>
                            <tbody class="text-center" style="font-size: 9pt;">
                                {{-- @forelse ($list as $r)
                                <tr>
                                    <td class="p-2">{{ $r->name }}</td>
                                    <td class="p-2">{{ $r->customer }}</td>
                                    <td class="p-2">{{ $r->project }}</td>
                                    <td class="p-2">{{ $r->order_type }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6"></td>
                                </tr>
                                @endforelse --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-12">
                        <h6 class="border mt-0 mb-0 p-2 text-uppercase text-white mt-3" style="background-color: #0277BD;">Open Production Order(s)</h6>
                        <table class="table table-bordered table-striped">
                            <col style="width: 25%;">
                            <col style="width: 25%;">
                            <col style="width: 25%;">
                            <col style="width: 25%;">
                            <thead class="text-primary text-center font-weight-bold text-uppercase" style="font-size: 7pt;">
                                <th class="p-2">Order No.</th>
                                <th class="p-2">Item</th>
                                <th class="p-2">Qty</th>
                                <th class="p-2">Status</th>
                            </thead>
                            <tbody class="text-center" style="font-size: 9pt;">
                                {{-- @forelse ($list as $r)
                                <tr>
                                    <td class="p-2">{{ $r->name }}</td>
                                    <td class="p-2">{{ $r->customer }}</td>
                                    <td class="p-2">{{ $r->project }}</td>
                                    <td class="p-2">{{ $r->order_type }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6"></td>
                                </tr>
                                @endforelse --}}
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="view-bom-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder">Modal Title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="bom-details-div"></div>
            </div>
        </div>
    </div>
</div>

<div id="loader-wrapper" hidden>
    <div id="loader"></div>
    <div class="loader-section section-left"></div>
    <div class="loader-section section-right"></div>
</div>

<style>
    .pill-chk-item {
        cursor: pointer;
        display: inline-block;
        float: left;
        font-size: 11px !important;
        font-weight: normal;
        line-height: 20px;
        margin: 0 12px 12px 0;
        text-transform: capitalize;
    }
    .pill-chk-item input[type="checkbox"] {
        display: none;
    }
    .pill-chk-item input[type="checkbox"]:checked + .pill-chk-label {
        background-color: #F96332;
        border: 1px solid #F96332;
        color: #fff;
        padding: 5px 25px;
    }
    .pill-chk-label {
        border: 1px solid #FFF;
        border-radius: 20px;
        color: #FFF;
        display: block;
        padding: 5px 25px;
        text-decoration: none;
    }
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
<script>
$(document).ready(function(){
    loadOrderList();
    $(document).on('click', '.order-types-chk', function() {
        loadOrderList();
    });

    $(document).on('keyup', '.order-list-search', function(){
        loadOrderList();
    });

    $(document).on('click', '.order-list-pagination a', function(e){
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        loadOrderList(page);
    });

    $(document).on("click", ".view-bom", function(e){
        e.preventDefault();
        var sel_id = $(this).data('id');
        $.ajax({
            url: "/view_bom/" + sel_id,
            type:"GET",
            success:function(data){
                $('#bom-details-div').html(data);
                $('#view-bom-modal .modal-title').html(sel_id);
                $('#view-bom-modal').modal('show');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if(jqXHR.status == 401) {
                    showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
                }
            },
        });
    });
    
    function loadOrderList(page){
        $('#loader-wrapper').removeAttr('hidden');
        $.ajax({
            url: "/get_order_list?page=" + page,
            type:"GET",
            data: $('#order-list-form').serialize(),
            success:function(data){
                $('#loader-wrapper').attr('hidden', true);
                $('#order-list-div').html(data);
            }
        });
    }
});
</script>
@endsection