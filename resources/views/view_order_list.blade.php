@extends('layouts.user_app', [
  'namePage' => 'MES',
  'activePage' => 'view_order_list',
  'pageHeader' => 'Open Order(s)',
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
                                <small class="d-block">Sales Order</small>
                                <span class="d-block font-weight-bolder" style="font-size: 17pt;" id="customer-order-text">0</span>
                            </div>
                            <div class="pl-2 col-4" style="border-left: 5px solid #E67E22;">
                                <small class="d-block">Consignment Order</small>
                                <span class="d-block font-weight-bolder" style="font-size: 17pt;" id="consignment-order-text">0</span>
                            </div>
                            <div class="pl-2 col-4" style="border-left: 5px solid #48C9B0;">
                                <small class="d-block">Sample Order</small>
                                <span class="d-block font-weight-bolder" style="font-size: 17pt;" id="sample-order-text">0</span>
                            </div>
							<div class="pl-2 col-4" style="border-left: 5px solid #31590954;">
                                <small class="d-block">Others</small>
                                <span class="d-block font-weight-bolder" style="font-size: 17pt;" id="other-order-text">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 p-2">
                        <form id="order-list-form" class="pl-2 pr-2">
                            <div class="row rounded-top align-items-center pt-2 pb-2 pr-0 mr-0" style="background-color: #0277BD;">
								<div class="col-1 p-0">
									<h6 class="m-2 text-uppercase text-white text-center" style="white-space: nowrap !important;">Open Order(s)</h6>
								</div>
								<div class="col-5 p-0">
									<div>
										@foreach ($order_types as $order_type)
										<label class="pill-chk-item mr-1 ml-1 mb-0 mt-0">
											<input type="checkbox" class="order-types-chk" value="{{ $order_type['id'] }}" name="order_types[]">
											<span class="pill-chk-label">{{ $order_type['type'] }}</span>
										</label>
										@endforeach
									</div>
								</div>
                                <div class="col-4 p-0 row">
									<div class="col-5">
										<div class="custom-control custom-checkbox pt-2">
											<input type="checkbox" name="reschedule" class="custom1-control-input" id="reschedule-checkbox">
											<label class="custom-cont6rol-label font-weight-bold" for="reschedule-checkbox" style="color: #fff">For Rescheduling</label>
										</div>
									</div>
									<div class="col-7">
										<input type="text" name="q" class="form-control rounded bg-white m-0 order-list-search" placeholder="Search" value="{{ request('q') }}" autocomplete="off">
									</div>
                                </div>
								<div class="col-2 mr-0 row">
									<div class="col-6">
										<input type="text" id="date-approved-filter" class="form-control rounded bg-white m-0 date-range" placeholder="Approved Date" value="" style="color: #000 !important"/>
									</div>
									<div class="col-6 p-0">
										@php
											$sort_arr = [
												[
													'display' => 'Date Approved',
													'value' => 'date_approved'
												],
												[
													'display' => 'Customer',
													'value' => 'customer'
												],
												[
													'display' => 'Delivery Date',
													'value' => 'delivery_date'
												]
											];
										@endphp
										<div class="row p-0">
											<div class="col-9">
												<select class="form-control rounded bg-white m-0 w-100" id="sort-selection">
													<option value="" style="color: #000" selected disabled>Sort By</option>
													@foreach ($sort_arr as $sort)
														<option value="{{ $sort['value'] }}" style="color: #000">{{ $sort['display'] }}</option>
													@endforeach
												</select>
											</div>
											<div class="col-2" id="ctrl-sorting" data-sort='desc' style="display: flex; justify-content: center; align-items: center; color: #fff; z-index: 90;">
												<i class="fa fa-sort-amount-desc"></i>
											</div>
										</div>
									</div>
                                </div>
                            </div>
                        </form>
                        <div id="order-list-div"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="view-order-details-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 90%;">
        <div id="view-order-modal-content"></div>
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

<iframe src="#" id="iframe-print-order" style="display: none;"></iframe>

<div id="loader-wrapper" hidden>
    <div id="loader"></div>
    <div class="loader-section section-left"></div>
    <div class="loader-section section-right"></div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
		padding: 3px 10px;
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
	$(document).on('change', '#sort-selection', function (e){
		e.preventDefault();

		loadOrderList();
	});

	$(document).on('click', '#ctrl-sorting', function (e){
		e.preventDefault();

		if($(this).data('sort') == 'asc'){
			$(this).data('sort', 'desc');
			$(this).find('i').addClass('fa-sort-amount-desc').removeClass('fa-sort-amount-asc');
		}else{
			$(this).data('sort', 'asc');
			$(this).find('i').addClass('fa-sort-amount-asc').removeClass('fa-sort-amount-desc');
		}

		var order_by = $(this).data('sort');

		loadOrderList();
	});

	$('.date-range').daterangepicker({
		autoUpdateInput: false,
		autoApply: true,
		locale: {
			cancelLabel: 'Clear'
		},
		opens: 'left'
	});

	$('.date-range').on('apply.daterangepicker', function(ev, picker) {
		$(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));

		loadOrderList();
	});

	$('.date-range').on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');

		loadOrderList();
	});

	$('.date-range').on('keyup', function(){
		loadOrderList();
	});

	$(document).on('click', '.view-order-details-btn', function(e) {
		e.preventDefault();
		$('#view-order-details-modal').modal('show');
		$('#view-order-modal-content').empty();
		var id = $(this).data('id');
		$.ajax({
            url: "/view_order_detail/" + id,
            type:"GET",
            success:function(data){
                $('#view-order-modal-content').html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if(jqXHR.status == 401) {
                    showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
                }
            },
        });

		$.ajax({
            url: "/createViewOrderLog",
            type:"POST",
            data: {order_no: id, _token: '{{ csrf_token() }}'},
        });
	});

	$(document).on('click', '.print-order-btn', function(e){
      e.preventDefault();
	  $("#iframe-print-order").attr("src", $(this).attr('href'));
    });

	$(document).on('hidden.bs.modal', '.order-modal', function () {
		$("#iframe-print-order").attr("src", '#');
	});

	@if (session()->has('success'))
		showNotification("success", '{{ session()->get("success") }}', "now-ui-icons travel_info");
	@elseif(session()->has('error'))
		showNotification("danger", '{{ session()->get("error") }}', "now-ui-icons travel_info");
	@endif

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
    }); 

    loadOrderList();
    loadOrderTypes();
    $(document).on('click', '.order-types-chk', function() {
        loadOrderList();
    });

	$(document).one('submit', '.order-items-form', function(e) {
		e.preventDefault();
		var checked_items = $(this).find('input[type="checkbox"]:checked').length;
		if (checked_items <= 0) {
			showNotification("danger", 'Please select items', "now-ui-icons travel_info");
			return false;
		} else {
			$(this).submit();
		}
	});

	$(document).on('click', '.order-items-form input[type="checkbox"]', function(e) {
		var frm = $(this).closest('form').eq(0);
		if (frm.find('input[type="checkbox"]:checked').length > 0) {
			frm.find('button[type="submit"]').removeClass('btn-secondary disabled').addClass('btn-primary');
		} else {
			frm.find('button[type="submit"]').addClass('btn-secondary disabled').removeClass('btn-primary');
		}
	});

	$(document).on('click', '#reschedule-checkbox', function (){
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
        var sel_id = $(this).closest('.input-group').find('.custom-select').eq(0).val();
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
		var date_filter = $('#date-approved-filter').val() ? '&date_approved=' + $('#date-approved-filter').val() : '';

		var sort_by = $('#sort-selection').val() ? '&sort_by=' + $('#sort-selection').val() : '';
		var order_by = $('#ctrl-sorting').data('sort') ? '&order_by=' + $('#ctrl-sorting').data('sort') : '';

        $('#loader-wrapper').removeAttr('hidden');
        $.ajax({
            url: "/get_order_list?page=" + page + date_filter + sort_by + order_by,
            type:"GET",
            data: $('#order-list-form').serialize(),
            success:function(data){
                $('#loader-wrapper').attr('hidden', true);
                $('#order-list-div').html(data);
            }
        });
    }

    function loadOrderTypes(){
        $.ajax({
            url: "/orderTypes",
            type:"GET",
            success:function(data){
                $('#consignment-order-text').text(data.consignment_order);
                $('#sample-order-text').text(data.sample_order);
                $('#customer-order-text').text(data.customer_order);
                $('#other-order-text').text(data.other_order);
            }
        });
    }

	function showNotification(color, message, icon){
		$.notify({
			icon: icon,
			message: message
		},{
			type: color,
			timer: 2000,
			placement: {
				from: 'top',
				align: 'center'
			}
		});
	}

	setInterval(checkNewOrders, 30000);
	function checkNewOrders(){
        $.ajax({
            url: "/checkNewOrders",
            type:"GET",
            success:function(data){
				if (data) {
					loadOrderList();
				}
            }
        });
    }
});
</script>
@endsection