@extends('layouts.painting_app', [
  'namePage' => 'Painting',
  'activePage' => 'painting_production_orders',
])
@section('content')
@include('modals.item_track_modal')
<div class="panel-header">
  <div class="header text-center" style="margin-top: -70px;">
		<div class="row">
			<div class="col-md-12">
				<table style="text-align: center; width: 60%;">
					<tr>
						<td style="width: 35%; border-right: 5px solid white;">
							<h2 class="title">
								<div class="pull-right" style="margin-right: 20px;">
									<span style="display: block; font-size: 15pt;">{{ date('M-d-Y') }}</span>
									<span style="display: block; font-size: 10pt;">{{ date('l') }}</span>
								</div>
							</h2>
						</td>
						<td style="width: 15%; border-right: 5px solid white;">
							<h2 class="title" style="margin: auto; font-size: 17pt;"><span id="current-time">--:--:-- --</span></h2>
						</td>
						<td style="width: 50%">
							<h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt; font-size: 19pt;">Production Order(s)</h2>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="content" style="margin-top: -200px;">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 offset-md-6" style="margin-bottom: -40px; z-index: 1;">
              <div class="pull-right">
                <button type="button" class="btn btn-primary" id="manual-production-create-btn">
                  <i class="now-ui-icons ui-1_simple-add"></i> Create Production Order
                </button>
              </div>
            </div>
            <div class="col-md-12">
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="open-tab" data-toggle="tab" href="#open" role="tab" aria-controls="open" aria-selected="true">Open</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="open-mreq-tab" data-toggle="tab" href="#open-mreq" role="tab" aria-controls="open-mreq" aria-selected="false">Open MREQ</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="done-tab" data-toggle="tab" href="#done" role="tab" aria-controls="done" aria-selected="false">Done</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="cancelled-tab" data-toggle="tab" href="#cancelled" role="tab" aria-controls="cancelled" aria-selected="false">Cancelled</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="po-track-tab" data-toggle="tab" href="#po_track" role="tab" aria-controls="po-track" aria-selected="false">Sales Order Item Tracking</a>
                </li>
              </ul>
              <!-- Tab panes -->
              <div class="tab-content">
                <div class="tab-pane" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                  <div class="row" style="margin-top: 12px;">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-header" style="background-color: #C62828;">
                          <div class="row">
                            <div class="col-md-9">
                                <h5 class="text-white font-weight-bold align-middle">Cancelled Production Order(s)</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg" placeholder="Search" id="search-cancelled-prod" style="background-color: white;">
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="cancelled-production-div"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane active" id="open" role="tabpanel" aria-labelledby="open-tab">
                  <div class="row" style="margin-top: 12px;">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-header" style="background-color: #f57f17;">
                          <div class="row">
                            <div class="col-md-9">
                                <h5 class="text-white font-weight-bold align-middle">Open Production Order(s)</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg search-open-prod" data-type="SO" placeholder="Search" style="background-color: white;">
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="open-so-production-div"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="done" role="tabpanel" aria-labelledby="done-tab">
                  <div class="row" style="margin-top: 12px;">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-header" style="background-color: #229954;">
                          <div class="row">
                            <div class="col-md-9">
                                <h5 class="text-white font-weight-bold align-middle">Item(s) Ready for Feedback</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg search-feedback-prod" placeholder="Search" style="background-color: white;">
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="for-feedback-production-div"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane" id="open-mreq" role="tabpanel" aria-labelledby="open-mreq-tab">
                  <div class="row" style="margin-top: 12px;">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-header" style="background-color: #f57f17;">
                          <div class="row">
                            <div class="col-md-9">
                                <h5 class="text-white font-weight-bold align-middle">Open MREQ Production Order(s)</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg search-open-prod" data-type="MREQ" placeholder="Search" style="background-color: white;">
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="open-mreq-production-div"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                 <div class="tab-pane" id="po_track" role="tabpanel" aria-labelledby="po-track-tab">
                  <div class="row" style="margin-top: 12px;">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-header" style="background-color:  #117a65;">
                          <div class="row">
                            <div class="col-md-9">
                                <h5 class="text-white font-weight-bold align-middle">Sales Order Item Tracking</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                  <input type="text" class="form-control form-control-lg" placeholder="Search" id="search-information_so" style="background-color: white;">
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="tbl_item_status_tracking">   
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Manual Create Production Prder -->
<div class="modal fade" id="manual-production-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 60%;">
    <form action="/create_production_order" method="post" autocomplete="off">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create Production Order</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                {{--  --}}
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Reference Type</label>
                    <select class="form-control" id="sel-reference-type" name="reference_type">
                      <option value="SO">Sales Order</option>
                      <option value="MREQ">Material Request</option>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-6" id="input-so-div">
                  <div class="form-group">
                    <label>Sales Order</label>
                    <select class="form-control sel-reference-no sel-so sel2" id="sel-so" name="sales_order" data-type="SO" required>
                      <option value="">Select SO</option>
                      @forelse($so_list as $so)
                      <option value="{{ $so->name }}">{{ $so->name }}</option>
                      @empty
                      <option value="">No Sales Order(s) Found.</option>
                      @endforelse
                    </select>
                  </div>
                </div>

                <div class="col-md-6" id="input-mreq-div" hidden>
                  <div class="form-group">
                    <label>Material Request</label>
                    <select class="form-control sel-reference-no sel-mreq sel2" id="sel-mreq" name="material_request" data-type="MREQ" required>
                      <option value="">Select MREQ</option>
                      @forelse($mreq_list as $mreq)
                      <option value="{{ $mreq->name }}">{{ $mreq->name }}</option>
                      @empty
                      <option value="">No Material Request(s) Found.</option>
                      @endforelse
                    </select>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label>Item to Manufacture</label>
                    <select class="form-control sel2" id="sel-item" name="item_code" required>
                      <option value="">Select Item Code</option>
                      @forelse($item_list as $item)
                      <option value="{{ $item->name }}">{{ $item->name }}</option>
                      @empty
                      <option value="">No Item(s) Found.</option>
                      @endforelse
                    </select>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label>Description</label>
                    <textarea style="min-height: 110px;" name="description" class="form-control readonly"></textarea>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>Qty to Manufacture</label>
                    <input type="text" name="qty" class="form-control" value="0" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>Stock UoM</label>
                    <input type="text" name="stock_uom" class="form-control readonly">
                  </div>
                </div>

                <div class="col-md-6">
                  <label>BOM No</label>
                  <div class="input-group">
                    <select class="form-control" id="sel-bom" name="bom" required></select>
                    <div class="input-group-append">
                      <button class="btn btn-info" type="button" style="margin: 0;" id="view-bom-btn"><i class="now-ui-icons ui-1_zoom-bold"></i></button>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>Item Classification</label>
                    <input type="text" name="item_classification" class="form-control readonly">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>WIP Warehouse</label>
                    <select class="form-control" name="wip" required>
                      @forelse($warehouse_list as $warehouse)
                      <option value="{{ $warehouse }}" {{ ($warehouse == 'Fabrication  - FI') ? 'selected' : '' }}>{{ $warehouse }}</option>
                      @empty
                      <option value="">No Warehouse(s) Found.</option>
                      @endforelse
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>Target Warehouse</label>
                    <select class="form-control" name="target" required>
                      @forelse($warehouse_list as $warehouse)
                      <option value="{{ $warehouse }}">{{ $warehouse }}</option>
                      @empty
                      <option value="">No Warehouse(s) Found.</option>
                      @endforelse
                    </select>
                  </div>
                </div>

                {{--  --}}
              </div>
            </div>

            <div class="col-md-6">
              <div class="row">
                {{--  --}}

                <div class="col-md-12">
                  <div class="form-group">
                    <label>Planned Start Date</label>
                    <input type="text" name="planned_date" class="form-control date-picker">
                  </div>
                </div>
            
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Parent Item Code</label>
                    <select class="form-control sel2" name="parent_code">
                      <option value="">Select Parent Item Code</option>
                      @forelse($parent_code_list as $item)
                      <option value="{{ $item->name }}">{{ $item->name }}</option>
                      @empty
                      <option value="">No Item(s) Found.</option>
                      @endforelse
                    </select>
                  </div>
                </div>
                
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Customer</label>
                    <input type="text" name="customer" class="form-control readonly">
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label>Project</label>
                    <input type="text" name="project" class="form-control readonly">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>Delivery Date</label>
                    <input type="text" name="delivery_date" class="form-control readonly">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>Classification</label>
                    <input type="text" name="classification" class="form-control readonly">
                  </div>
                </div>

                {{--  --}}
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info">Submit</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Review BOM -->
<div class="modal fade" id="review-bom-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="min-width: 70%;">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bolder;">Modal Title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <input type="text" id="production-order-val" style="display: none;">
            <div id="review-bom-details-div"></div>
         </div>
      </div>
   </div>
</div>

<!-- Modal Cancel Production Order -->
<div class="modal fade" id="cancel-production-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form action="/cancel_production_order" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modal Title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <input type="hidden" name="id">
              <input type="hidden" name="production_order">
              <p style="font-size: 14pt; margin: 0;" class="text-center">Cancel Production Order <b><span></span></b>?</p>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 5px 10px;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- Modal Confirm Feedback Production Order -->
<div class="modal fade" id="confirm-feedback-production-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form action="#" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Feedback</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <input type="hidden" name="production_order">
              <p style="font-size: 12pt; margin: 0;" class="text-center">Qty for Feedback = <b><span class="completed-qty"></span></b></p>
              <p style="font-size: 12pt; margin: 0;" class="text-center">Target Warehouse = <b><span class="target-warehouse"></span></b></p>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 5px 10px;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="submit-feedback-btn">Submit</button>
        </div>
      </div>
    </form>
  </div>
</div>




<style type="text/css">
  .breadcrumb-c {
    font-size: 10pt;
    font-weight: bold;
    padding: 0px;
    background: transparent;
    list-style: none;
    overflow: hidden;
    margin-top: 10px;
    margin-bottom: 10px;
    width: 100%;
    border-radius: 4px;
  }

  .breadcrumb-c>li {
    display: table-cell;
    vertical-align: top;
    width: 1%;
  }

  .breadcrumb-c>li+li:before {
    padding: 0;
  }

  .breadcrumb-c li a {
    color: white;
    text-decoration: none;
    padding: 10px 0 10px 5px;
    position: relative;
    display: inline-block;
    width: calc( 100% - 10px );
    background-color: hsla(0, 0%, 83%, 1);
    text-align: center;
    text-transform: capitalize;
  }

  .breadcrumb-c li.completed a {
    background: brown;
    background: hsla(153, 57%, 51%, 1);
  }

  .breadcrumb-c li.completed a:after {
    border-left: 30px solid hsla(153, 57%, 51%, 1);
  }

  .breadcrumb-c li.active a {
    background: #ffc107;
  }

  .breadcrumb-c li.active a:after {
    border-left: 30px solid #ffc107;
  }

  .breadcrumb-c li:first-child a {
    padding-left: 1px;
  }

  .breadcrumb-c li:last-of-type a {
    width: calc( 100% - 38px );
  }

  .breadcrumb-c li a:before {
    content: " ";
    display: block;
    width: 0;
    height: 0;
    border-top: 50px solid transparent;
    border-bottom: 50px solid transparent;
    border-left: 30px solid white;
    position: absolute;
    top: 50%;
    margin-top: -50px;
    margin-left: 1px;
    left: 100%;
    z-index: 1;
  }

  .breadcrumb-c li a:after {
    content: " ";
    display: block;
    width: 0;
    height: 0;
    border-top: 50px solid transparent;
    border-bottom: 50px solid transparent;
    border-left: 30px solid hsla(0, 0%, 83%, 1);
    position: absolute;
    top: 50%;
    margin-top: -50px;
    left: 100%;
    z-index: 2;
  }
</style>

<style type="text/css">
.modal-lg-custom {
    max-width: 80% !important;
}
#manual-production-modal .form-control {
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
}
#manual-production-modal .form-control:hover, #manual-production-modal .form-control:focus, #manual-production-modal .form-control:active {
  box-shadow: none;
}
#manual-production-modal .form-control:focus {
  border: 1px solid #34495e;
}

.select2.select2-container {
  width: 100% !important;
}

.select2.select2-container .select2-selection {
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  height: 34px;
  margin-bottom: 15px;
  outline: none;
  transition: all 0.15s ease-in-out;
}

.select2.select2-container .select2-selection .select2-selection__rendered {
  color: #333;
  line-height: 32px;
  padding-right: 33px;
}

.select2.select2-container .select2-selection .select2-selection__arrow {
  background: #f8f8f8;
  border-left: 1px solid #ccc;
  -webkit-border-radius: 0 3px 3px 0;
  -moz-border-radius: 0 3px 3px 0;
  border-radius: 0 3px 3px 0;
  height: 32px;
  width: 33px;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single {
  background: #f8f8f8;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--single .select2-selection__arrow {
  -webkit-border-radius: 0 3px 0 0;
  -moz-border-radius: 0 3px 0 0;
  border-radius: 0 3px 0 0;
}

.select2.select2-container.select2-container--open .select2-selection.select2-selection--multiple {
  border: 1px solid #34495e;
}

.select2.select2-container.select2-container--focus .select2-selection {
  border: 1px solid #34495e;
}

.select2.select2-container .select2-selection--multiple {
  height: auto;
  min-height: 34px;
}

.select2.select2-container .select2-selection--multiple .select2-search--inline .select2-search__field {
  margin-top: 0;
  height: 32px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__rendered {
  display: block;
  padding: 0 4px;
  line-height: 29px;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice {
  background-color: #f8f8f8;
  border: 1px solid #ccc;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
  margin: 4px 4px 0 0;
  padding: 0 6px 0 22px;
  height: 24px;
  line-height: 24px;
  font-size: 12px;
  position: relative;
}

.select2.select2-container .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
  position: absolute;
  top: 0;
  left: 0;
  height: 22px;
  width: 22px;
  margin: 0;
  text-align: center;
  color: #e74c3c;
  font-weight: bold;
  font-size: 16px;
}

.select2-container .select2-dropdown {
  background: transparent;
  border: none;
  margin-top: -5px;
}

.select2-container .select2-dropdown .select2-search {
  padding: 0;
}

.select2-container .select2-dropdown .select2-search input {
  outline: none;
  border: 1px solid #34495e;
  border-bottom: none;
  padding: 4px 6px;
}

.select2-container .select2-dropdown .select2-results {
  padding: 0;
}

.select2-container .select2-dropdown .select2-results ul {
  background: #fff;
  border: 1px solid #34495e;
}

.select2-container .select2-dropdown .select2-results ul .select2-results__option--highlighted[aria-selected] {
  background-color: #3498db;
}


</style>
@endsection

@section('script')
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>

<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />
<script>
$(document).ready(function(){
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $('.sel2').select2({
    dropdownParent: $("#manual-production-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });

  $(document).on('click', '.prod-details-btn', function(e){
    e.preventDefault();
    var jtno = $(this).data('jtno');
    $('#jt-workstations-modal .modal-title').text(jtno);
    if(jtno){
      getJtDetails($(this).data('jtno'));
    }else{
      showNotification("danger", 'No Job Ticket found.', "now-ui-icons travel_info");
    }
  });

  $('.date-picker').datepicker({
    'format': 'yyyy-mm-dd',
    'autoclose': true
  });

  function showNotification(color, message, icon){
        $.notify({
          icon: icon,
          message: message
        },{
          type: color,
          timer: 500,
          placement: {
            from: 'top',
            align: 'center'
          }
        });
  }
  
    function getJtDetails(jtno){
      $('#process-bc').empty();
      $('#jt-details-tbl tbody').empty();
      $.ajax({
      url:"/get_jt_details/" + jtno,
      type:"GET",
      success:function(data){
        if (data.success < 1) {
          showNotification("danger", data.message, "now-ui-icons travel_info");
        }else{
          $('#production-search-content').html(data);
          $('#jt-workstations-modal').modal('show');
        }
      }
      });
    }

    $(document).on('click', '.create-feedback-btn', function(e){
      e.preventDefault();
      $('#submit-feedback-btn').removeAttr('disabled');
      var production_order = $(this).data('production-order');
      var completed_qty = $(this).data('completed-qty');
      var target_warehouse = $(this).data('target-warehouse');

      $('#confirm-feedback-production-modal input[name="production_order"]').val(production_order);
      $('#confirm-feedback-production-modal .completed-qty').text(completed_qty);
      $('#confirm-feedback-production-modal .target-warehouse').text(target_warehouse);
      $('#confirm-feedback-production-modal').modal('show');
    });

    $('#confirm-feedback-production-modal form').submit(function(e){
      e.preventDefault();
      $('#submit-feedback-btn').attr('disabled', true);
      var production_order = $('#confirm-feedback-production-modal input[name="production_order"]').val();
      var completed_qty = $('#confirm-feedback-production-modal .completed-qty').text();

      $.ajax({
        url:"/create_stock_entry/" + production_order,
        type:"POST",
        data: {fg_completed_qty: completed_qty},
        success:function(response){
          if (response.success == 0) {
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", response.message, "now-ui-icons travel_info");
            get_for_feedback_production(1, $('.search-feedback-prod').val());
            $('#confirm-feedback-production-modal').modal('hide');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });


  
  
  
  
  setInterval(updateClock, 1000);
  function updateClock(){
    var currentTime = new Date();
    var currentHours = currentTime.getHours();
    var currentMinutes = currentTime.getMinutes();
    var currentSeconds = currentTime.getSeconds();
    // Pad the minutes and seconds with leading zeros, if required
    currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
    currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
    // Choose either "AM" or "PM" as appropriate
    var timeOfDay = (currentHours < 12) ? "AM" : "PM";
    // Convert the hours component to 12-hour format if needed
    currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;
    // Convert an hours component of "0" to "12"
    currentHours = (currentHours === 0) ? 12 : currentHours;
    currentHours = (currentHours < 10 ? "0" : "") + currentHours;
    // Compose the string for display
    var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;

    $("#current-time").html(currentTimeString);
  }

// Manual Create Production Order
  $('#manual-production-create-btn').click(function(e){
    e.preventDefault();
    $('#manual-production-modal').modal('show');
  });

  $('#sel-reference-type').change(function(){
    set_attr_for_reference_no($(this).val());
  });

  set_attr_for_reference_no($('#sel-reference-type').val());
  function set_attr_for_reference_no(reference_type){
    if (reference_type == 'SO') {
      $('#input-so-div').removeAttr('hidden');
      $('#input-mreq-div').attr('hidden', true);
      $('#manual-production-modal .sel-so').attr('required', true);
      $('#manual-production-modal .sel-mreq').removeAttr('required');
      $('#manual-production-modal .sel-mreq').val('');
    }else{
      $('#input-mreq-div').removeAttr('hidden');
      $('#input-so-div').attr('hidden', true);
      $('#manual-production-modal .sel-mreq').attr('required', true);
      $('#manual-production-modal .sel-so').removeAttr('required');
      $('#manual-production-modal .sel-so').val('');
    }
  }

  $('#sel-item').change(function(e){
    e.preventDefault();
    if ($(this).val()) {
      $.ajax({
        url:"/get_item_details/" + $(this).val(),
        type:"GET",
        success:function(response){
          if (response.success == 0) {
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }else{
            $('#manual-production-modal textarea[name="description"]').text(response.description);
            $('#manual-production-modal input[name="stock_uom"]').val(response.stock_uom);
            $('#manual-production-modal input[name="item_classification"]').val(response.item_classification);
          }

          get_bom(response.name);
        }
      });
    }
  });

  function get_bom(item_code){
    $('#sel-bom').empty();
    $.ajax({
      url:"/get_item_bom/" + item_code,
      type:"GET",
      success:function(response){
        if (response.length > 0) {
          var opt = '';
          $.each(response, function(i, d){
            opt += '<option value="' + d.name + '" data-reviewed="' + d.is_reviewed + '">' + d.name + '</option>';
          });

          $('#sel-bom').append(opt);
        }else{
          showNotification("danger", 'No BOM found for Item ' + item_code, "now-ui-icons travel_info");
        }

        get_bom_status($('#sel-bom').find(':selected').data('reviewed'));
      }
    });
  }

  $('#sel-bom').change(function(){
    get_bom_status($(this).find(':selected').data('reviewed'));
  });

  function get_bom_status(reviewed){
    if (reviewed == 0) {
      showNotification("info", 'Please review and update BOM', "now-ui-icons travel_info");
    }else{
    }
  }

  $('.sel-reference-no').change(function(){
    var ref_type = $(this).data('type');
    var ref_no = $(this).val();
    $.ajax({
      url:"/get_reference_details/" + ref_type + "/" + ref_no,
      type:"GET",
      success:function(response){
        if (response.success == 0) {
          showNotification("danger", 'No BOM found for Item ' + item_code, "now-ui-icons travel_info");
        }else{
          $('#manual-production-modal input[name="customer"]').val(response.customer);
          $('#manual-production-modal input[name="project"]').val(response.project);
          $('#manual-production-modal input[name="delivery_date"]').val(response.delivery_date);
          var classification = (response.purpose) ? response.purpose : response.sales_type;
          classification = (classification != 'Sample') ? 'Customer Order' : 'Sample';
          $('#manual-production-modal input[name="classification"]').val(classification);
        }
      }
    });
  });

  $('.readonly').each(function(){
    $(this).attr('readonly','readonly');
  });

  $('#manual-production-modal form').submit(function(e){
    e.preventDefault();
    var qty = $('#manual-production-modal input[name="qty"]').val();
    if (qty <= 0) {
      showNotification("danger", 'Qty cannot be less than or equal to 0.', "now-ui-icons travel_info");
      return false;
    }else{
      $.ajax({
        url: "/create_production_order",
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          console.log(data);
          if (data.success == 0) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", 'Production Order <b>' + data + '</b> has been created.', "now-ui-icons ui-1_check");
            $('#manual-production-modal').modal('hide');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      });
    }
  });

  $('#view-bom-btn').click(function(e){
    e.preventDefault();
    $('#production-order-val').val('');
    var bom = $('#sel-bom').val();
    $.ajax({
      url: "/view_bom_for_review/" + bom,
      type:"GET",
      success:function(data){
        $('#review-bom-details-div').html(data);
      }
    });

    $('#review-bom-modal .modal-title').html('Review & Finalize BOM [' + bom + ']');
    $('#review-bom-modal').modal('show');
  });

  $(document).on('click', '#add-operation-btn', function(){
    var workstation = $('#sel-workstation option:selected').text();
    var wprocess = $('#sel-process').val();

    if (!$('#sel-workstation').val()) {
      showNotification("info", 'Please select Workstation', "now-ui-icons travel_info");
      return false;
    }

    // if ($('#bom-workstations-tbl td:contains(' + workstation + ')').length) {
    //   showNotification("info", 'Workstation <b>' + workstation + '</b> already exist.', "now-ui-icons travel_info");
    //   return false;
    // }

    var rowno = $('#bom-workstations-tbl tr').length;
    var sel = '<div class="form-group" style="margin: 0;"><select class="form-control form-control-lg">' + $('#sel-process').html() + '</select></div>';
    if (workstation) {
      var markup = "<tr><td class='text-center'>" + rowno + "</td><td>" + workstation + "</td><td>" + sel + "</td><td class='td-actions text-center'><button type='button' class='btn btn-danger delete-row'><i class='now-ui-icons ui-1_simple-remove'></i></button></td></tr>";
      $("#bom-workstations-tbl tbody").append(markup);
    }
  });

  $(document).on('change', '#sel-workstation', function(){
    var workstation = $(this).val();
    $('#sel-process').empty();
    if (workstation) {
      $.ajax({
        url: '/get_workstation_process/' + workstation,
        type:"GET",
        success:function(data){
          if (data.length > 0) {
            var opt = '<option value="">Select Process</option>';
            $.each(data, function(i, v){
              opt += '<option value="' + v.process_id + '">' + v.process_name + '</option>';
            });

            $('#sel-process').append(opt);
          }
        }
      });
    }
  });

  $(document).on('click', '#submit-bom-review-btn', function(){
    var production_order = $('#production-order-val').val();

    var id = [];
    var workstation = [];
    var wprocess = [];
    var workstation_process = [];
    var bom = $('#bom-workstations-tbl input[name=bom_id]').val();
    var user = $('#bom-workstations-tbl input[name=username]').val();
    $("#bom-workstations-tbl > tbody > tr").each(function () {
      id.push($(this).find('span').eq(0).text());
      workstation.push($(this).find('td').eq(1).text());
      wprocess.push($(this).find('select').eq(0).val());
      workstation_process.push($(this).find('select option:selected').eq(0).text());
    });

    var filtered_process = wprocess.filter(function (el) {
      return el != null && el != "";
    });

    if (workstation.length != filtered_process.length) {
      showNotification("danger", 'Please select process', "now-ui-icons travel_info");
      return false;
    }

    var processArr = workstation_process.sort();
    var processDup = [];
    for (var i = 0; i < processArr.length - 1; i++) {
        if (processArr[i + 1] == processArr[i]) {
            processDup.push(processArr[i]);
            showNotification("danger", 'Process <b>' + processArr[i] + '</b> already exist.', "now-ui-icons travel_info");
            return false;
        }
    }

    $.ajax({
      url: '/submit_bom_review/' + bom,
      type:"POST",
      data: {user: user, id: id, workstation: workstation, wprocess: wprocess, production_order: production_order},
      success:function(data){
        console.log(data);
        $('#review-bom-modal').modal('hide');
        showNotification("success", data.message, "now-ui-icons ui-1_check");
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      }
    });
  });

  $(document).on("click", ".delete-row", function(e){
    e.preventDefault();
    $(this).parents("tr").remove();

    $('#bom-workstations-tbl tbody tr').each(function (idx) {
      $(this).children("td:eq(0)").html(idx + 1);
    });
  });

  $(document).on('click', '.cancel-production-btn', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    var production_order = $(this).data('production-order');

    $('#cancel-production-modal input[name="id"]').val(id);
    $('#cancel-production-modal input[name="production_order"]').val(production_order);
    $('#cancel-production-modal .modal-title').text('Cancel Production Order');
    $('#cancel-production-modal span').eq(1).text(production_order);
    $('#cancel-production-modal').modal('show');
  });

  $('#cancel-production-modal form').submit(function(e){
    e.preventDefault();
    $.ajax({
      url: '/cancel_production_order',
      type:"POST",
      data: $(this).serialize(),
      success:function(data){
        if (!data.success) {
          showNotification("danger", data.message, "now-ui-icons travel_info");
        }else{
          showNotification("success", data.message, "now-ui-icons ui-1_check");
          location.reload();
          $('#cancel-production-modal').modal('hide');
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      }
    });
  });

  $('.modal').on('hidden.bs.modal', function(){
    var frm = $(this).find('form')[0];
    if (frm) frm.reset();
  });

  $(document).on('show.bs.modal', '.modal', function (event) {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
      $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
  });

  $(document).on('click', '.view-bom-details-btn', function(e){
    e.preventDefault();
    $('#production-order-val').val($(this).data('production-order'));
    $.ajax({
      url: "/view_bom_for_review/" + $(this).data('bom'),
      type:"GET",
      success:function(data){
        $('#review-bom-details-div').html(data);
      }
    });

    $('#review-bom-modal .modal-title').html('BOM Update [' + $(this).data('bom') + ']');
    $('#review-bom-modal').modal('show');
  });

  get_open_production('SO', 1, '');
  get_open_production('MREQ', 1, '');
  get_item_list();
  function get_open_production(reference, page, query){
    $.ajax({
      url: "/get_painting_open_production_orders/" + reference + "?page=" + page,
      type:"GET",
      data: {search_string: query},
      success:function(data){
        console.log(reference);
        if (reference == 'SO') {
          $('#open-so-production-div').html(data);
        }
        if (reference == 'MREQ') {
          $('#open-mreq-production-div').html(data);
        }
      }
    });
  }

  get_cancelled_production(1);
  function get_cancelled_production(page, query){
    $.ajax({
      url: "/get_painting_cancelled_production_orders/?page=" + page,
      type:"GET",
      data: {search_string: query},
      success:function(data){
          $('#cancelled-production-div').html(data);
      }
    });
  }

  get_for_feedback_production(1);
  function get_for_feedback_production(page, query){
    $.ajax({
      url: "/get_painting_for_feedback_production?page=" + page,
      type:"GET",
      data: {search_string: query},
      success:function(data){
          $('#for-feedback-production-div').html(data);
      }
    });
  }

  $(document).on('click', '.for-feedback-production-pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    get_for_feedback_production(page);
  });

  $(document).on('keyup', '.search-feedback-prod', function(){
    var query = $(this).val();
    get_for_feedback_production(1, query);
  });


  $(document).on('keyup', '.search-open-prod', function(){
    var query = $(this).val();
    var type = $(this).data('type');
    get_open_production(type, 1, query);
  });

  $(document).on('click', '.open-production-pagination-SO a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    get_open_production('SO', page);
  });

  $(document).on('click', '.open-production-pagination-MREQ a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    get_open_production('MREQ', page);
  });

  $(document).on('click', '.cancelled-production-pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    get_cancelled_production(page);
  });

  $(document).on('keyup', '#search-cancelled-prod', function(){
    var query = $(this).val();
    get_cancelled_production(1, query);
  });
});
</script>
<script type="text/javascript">
  $(document).on('click', '.tbl_item_list_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    get_item_list(page);
  });
  $(document).on('click', '.btn_trackmodal', function(event){
    event.preventDefault();
    var guideid = $(this).attr('data-guideid');
    var itemcode = $(this).attr('data-itemcode');
    var customer = $(this).attr('data-customer');
    $.ajax({
      url: "/get_bom_tracking/" + guideid + "/" + itemcode,
      type:"GET",
      success:function(data){
          $('#track-view-modal #tbl_flowchart').html(data);
          $('#track-view-modal').modal('show');
      }
    });
  });
</script>
<script type="text/javascript">
    $(document).on('keyup', '#search-information_so', function(){
    var query = $(this).val();
    get_search_information_details(1, query);
  });
</script>
<script type="text/javascript">
    function get_search_information_details(page, query){
    $.ajax({
      url: "/get_search_information_details/?page=" + page,
      type:"GET",
      data: {search_string: query},
      success:function(data){
          $('#tbl_item_status_tracking').html(data);
      }
    });
  }
</script>
<script type="text/javascript">
  function get_item_list(page){
    $.ajax({
          url:"/get_item_status_tracking/?page="+page,
          type:"GET",
          success:function(data){
            $('#tbl_item_status_tracking').html(data);
          }
        }); 
  }
</script>
@endsection