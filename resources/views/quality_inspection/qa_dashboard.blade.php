@extends('layouts.user_app', [
    'namePage' => 'MES',
    'activePage' => 'qa_dashboard',
    'pageHeader' => 'Quality Assurance Dashboard',
  'pageSpan' => Auth::user()->employee_name . ' - ' . $user_details->designation_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-0 ml-0 mr-0" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
  <div class="col-md-12 p-2">
    <div class="card m-0">
      <div class="card-body p-1">
        <div class="row p-0 m-0">
          <div class="col-9 p-1">
            <div class="card shadow-none border">
              <div class="card-header p-0">
                <h6 class="text-white1 font-weight-bold text-left m-2 rounded-top" style="font-size: 10.5pt;">In Process Quality Inspection Log(s)</h6>
                <table class="w-100 table-bordered text-center rounded" border="1">
                  <col style="width: 25%;">
                  <col style="width: 25%;">
                  <col style="width: 26%;">
                  <col style="width: 12%;">
                  <col style="width: 12%;">
                  <tbody>
                    <tr class="text-uppercase font-weight-bold text-white" style="background-color: #012f6a;">
                      <th rowspan="2" class="p-1">Operation</th>
                      <th rowspan="2" class="p-1">QA Inspector</th>
                      <th rowspan="2" class="p-1">Inspection Logs</th>
                      <th colspan="2" class="p-1">Qty</th>
                    </tr>
                    <tr class="text-white">
                      <th class="bg-success p-0">Inspected</th>
                      <th class="bg-danger p-0">Rejects</th>
                    </tr>
                    <tr class="bg-dark text-white">
                      <td class="p-2 text-uppercase">Fabrication</td>
                      <td class="p-2">
                        @forelse ($summary['fabrication']['inspectors'] as $qa_staff)
                          @php
                            $qa_staff_name = array_key_exists($qa_staff, $qa_staffs) ? $qa_staffs[$qa_staff] : null;
                          @endphp
                          <span class="d-block">{{ $qa_staff_name }}</span>
                        @empty
                          <span class="d-block">-</span>
                        @endforelse
                      </td>
                      <td class="p-2">{{ $summary['fabrication']['total_logs'] }}</td>
                      <td class="p-2">{{ $summary['fabrication']['qty_checked'] }}</td>
                      <td class="p-2">{{ $summary['fabrication']['qty_rejects'] }}</td>
                    </tr>
                    <tr class="bg-dark text-white">
                      <td class="p-2 text-uppercase">Painting</td>
                      <td class="p-2">
                        @forelse ($summary['painting']['inspectors'] as $qa_staff)
                          @php
                            $qa_staff_name = array_key_exists($qa_staff, $qa_staffs) ? $qa_staffs[$qa_staff] : null;
                          @endphp
                          <span class="d-block">{{ $qa_staff_name }}</span>
                        @empty
                          <span class="d-block">-</span>
                        @endforelse
                      </td>
                      <td class="p-2">{{ $summary['painting']['total_logs'] }}</td>
                      <td class="p-2">{{ $summary['painting']['qty_checked'] }}</td>
                      <td class="p-2">{{ $summary['painting']['qty_rejects'] }}</td>
                    </tr>
                    <tr class="bg-dark text-white">
                      <td class="p-2 text-uppercase">Assembly</td>
                      <td class="p-2">
                        @forelse ($summary['assembly']['inspectors'] as $qa_staff)
                          @php
                            $qa_staff_name = array_key_exists($qa_staff, $qa_staffs) ? $qa_staffs[$qa_staff] : null;
                          @endphp
                          <span class="d-block">{{ $qa_staff_name }}</span>
                        @empty
                          <span class="d-block">-</span>
                        @endforelse
                      </td>
                      <td class="p-2">{{ $summary['assembly']['total_logs'] }}</td>
                      <td class="p-2">{{ $summary['assembly']['qty_checked'] }}</td>
                      <td class="p-2">{{ $summary['assembly']['qty_rejects'] }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card shadow-none border">
              <div class="card-header pt-2 pl-3 pr-3 pb-2" style="background-color:#D35400;">
                <h6 class="text-white font-weight-bold text-left m-0 rounded-top" style="font-size: 10.5pt;">Reject(s)/Defect(s) for Confirmation <span class="badge badge-info float-right" id="reject-count-overall">0</span></h6>
              </div>
              <div class="card-body pb-1 pl-1 pr-1 pt-0" style="min-height: 300px;">
                <ul class="nav nav-tabs m-0 border-0 p-0 dashboard-custom-tabs" role="tablist" style="font-size: 9pt;">
                  <li class="nav-item font-weight-bold">
                    <a class="nav-link active border rounded m-1 pb-1 pt-1" data-toggle="tab" href="#fab" role="tab" data-operation="1">Fabrication <span class="badge badge-primary" id="reject-count-fabrication">0</span></a>
                  </li>
                  <li class="nav-item font-weight-bold">
                    <a class="nav-link border rounded m-1 pb-1 pt-1" data-toggle="tab" href="#pa" role="tab" data-operation="2">Painting <span class="badge badge-primary" id="reject-count-painting">0</span></a>
                  </li>
                  <li class="nav-item font-weight-bold">
                    <a class="nav-link border rounded m-1 pb-1 pt-1" data-toggle="tab" href="#wa" role="tab" data-operation="3">Wiring and Assembly <span class="badge badge-primary" id="reject-count-assembly">0</span></a>
                  </li>
                </ul>
                <div class="tab-content" id="reject-for-confirmation-content">
                  <div class="tab-pane fade show active" id="fab" role="tabpanel" data-operation="1">
                    <div class="form-group mb-1 ml-1 mr-1 pull-right" style="margin-top: -35px;">
                      <input type="text" class="form-control rounded bg-white search-reject-confirmation" data-operation="1" placeholder="Search" style="padding: 6px 8px;" autocomplete="off">
                    </div>
                    <div class="table-div m-1 p-0"></div>
                  </div>
                  <div class="tab-pane fade" id="pa" role="tabpanel" data-operation="2">
                    <div class="form-group mb-1 ml-1 mr-1 pull-right" style="margin-top: -35px;">
                      <input type="text" class="form-control rounded bg-white search-reject-confirmation" data-operation="2" placeholder="Search" style="padding: 6px 8px;" autocomplete="off">
                    </div>
                    <div class="table-div m-1 p-0"></div>
                  </div>
                  <div class="tab-pane fade" id="wa" role="tabpanel" data-operation="3">
                    <div class="form-group mb-1 ml-1 mr-1 pull-right" style="margin-top: -35px;">
                      <input type="text" class="form-control rounded bg-white search-reject-confirmation" data-operation="3" placeholder="Search" style="padding: 6px 8px;" autocomplete="off">
                    </div>
                    <div class="table-div m-1 p-0"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-3 p-1">
            <div class="card shadow-none border">
              <div class="card-header pt-2 pl-3 pr-3 pb-2" style="background-color: #D35400;">
                <h6 class="text-white font-weight-bold text-left m-0 rounded-top" style="font-size: 10.5pt;">For Outgoing Inspection</h6>
              </div>
              <div class="card-body p-0" style="min-height: 550px;" id="wip-orders-div"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .classheader{
    position: sticky; top: 0; 
    z-index: 2;
    position: -webkit-sticky;
    border: 0.8px solid white;
  }
  .margin-top{
    margin-top: -18px;
  }
  #fabrication .form-control, #painting .form-control{
      border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #assembly .form-control{
      border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  .span-title{
    display: block;
    font-weight: bold;
    font-size: 11pt;
  }
  .span-qty{
    display: block;
    font-weight: bold;
    font-size: 22pt;
  }
  .span-uom{
    display: block;
    font-size:8pt;
  }
</style>

<!-- Modal -->
<div class="modal fade" id="quality-inspection-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="min-width: 80%;">
     <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #f57f17;">
           <h5 class="modal-title">&nbsp;
              QA <span class="qc-type"></span> <span class="qc-workstation font-weight-bold"></span>
           </h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
        </div>
        <div class="modal-body">
           <div id="quality-inspection-div"></div>
        </div>
     </div>
  </div>
</div>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />

<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />

<script>
  $(document).ready(function(){
    load_dashboard();
    function load_dashboard(){
      count_reject_for_confirmation();
      $("#reject-for-confirmation-content .tab-pane").each(function( index ) {
        const operation = $(this).data('operation');
        const el = $(this);
        reject_for_confirmation(operation, el);
      });
    }

    function reject_for_confirmation(operation, el, page = null){
      var q = $(el).find('.search-reject-confirmation').eq(0).val();
      $.ajax({
        url:"/get_reject_for_confirmation/" + operation + "?page=" + page,
        type:"GET",
        data: {q},
        success:function(data){
          $(el).find('.table-div').eq(0).html(data);
        }
      });
    }

    function count_reject_for_confirmation(){
      $.ajax({
        url:"/count_reject_for_confirmation",
        type:"GET",
        success:function(data){
          $('#reject-count-fabrication').text(data.fabrication);
          $('#reject-count-painting').text(data.painting);
          $('#reject-count-assembly').text(data.assembly);
          $('#reject-count-overall').text(data.overall);
        }
      });
    }

    $(document).on('keyup', '.search-reject-confirmation', function(){
      var operation = $(this).data('operation');
      var el = $(this).closest('.tab-pane');
      reject_for_confirmation(operation, el);
    });

    $(document).on('click', '.paginate-reject-confirmation a', function(event){
      event.preventDefault();
      var el = $(this).closest('.tab-pane');
      var operation = el.data('operation');
      var page = $(this).attr('href').split('page=')[1];
      reject_for_confirmation(operation, el, page);
    });

    $(document).on('click', '.reject-confirmation-btn', function(e){
      e.preventDefault();
      
      var inspection_type = $(this).data('inspection-type');
      var workstation = $(this).data('workstation');
      var production_order = $(this).data('production-order');
      var process_id = $(this).data('process-id');
      var qa_id = $(this).data('qaid');

      $.ajax({
        url:"/get_reject_confirmation_checklist/" + production_order + "/" + workstation + "/" + process_id + "/" + qa_id,
        type:"GET",
        success:function(data){
          $('#quality-inspection-div').html(data);
          $('#quality-inspection-modal .qc-type').text(inspection_type);
          $('#quality-inspection-modal .qc-workstation').text('[' + workstation + ']');
          $('#quality-inspection-modal').modal('show');
        }
      });
    });

    $(document).on('submit', '#reject-confirmation-frm', function(e){
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#quality-inspection-modal').modal('hide');
            reject_for_confirmation(data.details.operation_id, $("#reject-for-confirmation-content .tab-pane").find('.active'));
            count_reject_for_confirmation();
          }else{
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

    function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 1000,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }
  });
</script>
@endsection