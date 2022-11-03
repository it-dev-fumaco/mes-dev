@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
  'activePage' => 'qa_logs',
  'pageHeader' => 'QA Inspection Logs',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-0 ml-0 mr-0" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
  <div class="col-md-12 p-2">
    <div class="card m-0" style="min-height: 900px;">
      <div class="card-body p-1">
        <div class="row p-0 m-0">
          <div class="col-12 p-1">
            <ul class="nav nav-tabs m-0 border-0 p-0" role="tablist" style="font-size: 9pt;">
              <li class="nav-item font-weight-bold">
                <a class="nav-link active border rounded m-1" data-toggle="tab" href="#fab" role="tab">Fabrication</a>
              </li>
              <li class="nav-item font-weight-bold">
                <a class="nav-link border rounded m-1" data-toggle="tab" href="#pa" role="tab">Painting</a>
              </li>
              <li class="nav-item font-weight-bold">
                <a class="nav-link border rounded m-1" data-toggle="tab" href="#wa" role="tab">Wiring and Assembly</a>
              </li>
            </ul>
            <div class="card shadow-none">
              <div class="card-body pb-1 pl-1 pr-1 pt-0" style="min-height: 300px;">
                <div class="tab-content" id="qa-logs-content">
                  <div class="tab-pane fade show active" id="fab" role="tabpanel">
                    <form>
                      <input type="hidden" name="operation" value="1">
                      <div class="row p-0 m-0">
                        <div class="col-4 offset-8 p-0" style="margin-top: -30px;">
                          <button class="btn btn-secondary btn-sm pull-right m-0" type="button" id="clear-filter-fabrication">Clear Filters</button>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">Date Range</label>
                            <input type="text" name="date_range" class="form-control rounded pt-1 pb-1 pl-2 pr-2" id="date-range-fabrication">
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">Workstation</label>
                            <select name="workstation" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-filters" id="select-workstation-fabrication"></select>
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">Process</label>
                            <select name="process" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-filters" id="select-process-fabrication"></select>
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">QC Status</label>
                            <select name="qc_status" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-qc-status select-filters">
                              <option value="">Select Status</option>
                              <option value="QC Passed">QC Passed</option>
                              <option value="QC Failed">QC Failed</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">QC Inspector</label>
                            <select name="qc_inspector" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-qc-inspector select-filters"></select>
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">Operator</label>
                            <select name="operator" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-filters" id="select-operator-fabrication"></select>
                          </div>
                        </div>
                      </div>
                    </form>
                    <div class="table-div m-1 p-0"></div>
                  </div>
                  <div class="tab-pane fade" id="pa" role="tabpanel">
                    <form>
                      <input type="hidden" name="operation" value="0">
                      <div class="row p-0 m-0">
                        <div class="col-4 offset-8 p-0" style="margin-top: -30px;">
                          <button class="btn btn-secondary btn-sm pull-right m-0" type="button" id="clear-filter-painting">Clear Filters</button>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">Date Range</label>
                            <input type="text" name="date_range" class="form-control rounded pt-1 pb-1 pl-2 pr-2" id="date-range-painting">
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">Workstation</label>
                            <select name="workstation" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-filters" id="select-workstation-painting"></select>
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">Process</label>
                            <select name="process" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-filters" id="select-process-painting"></select>
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">QC Status</label>
                            <select name="qc_status" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-qc-status">
                              <option value="">Select Status</option>
                              <option value="QC Passed">QC Passed</option>
                              <option value="QC Failed">QC Failed</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">QC Inspector</label>
                            <select name="qc_inspector" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-qc-inspector select-filters"></select>
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">Operator</label>
                            <select name="operator" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-filters" id="select-operator-painting"></select>
                          </div>
                        </div>
                      </div>
                    </form>
                    <div class="table-div m-1 p-0"></div>
                  </div>
                  <div class="tab-pane fade" id="wa" role="tabpanel">
                    <form>
                      <input type="hidden" name="operation" value="3">
                      <div class="row p-0 m-0">
                        <div class="col-4 offset-8 p-0" style="margin-top: -30px;">
                          <button class="btn btn-secondary btn-sm pull-right m-0" type="button" id="clear-filter-assembly">Clear Filters</button>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">Date Range</label>
                            <input type="text" name="date_range" class="form-control rounded pt-1 pb-1 pl-2 pr-2" id="date-range-assembly">
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">Workstation</label>
                            <select name="workstation" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-filters" id="select-workstation-assembly"></select>
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">Process</label>
                            <select name="process" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-filters" id="select-process-assembly"></select>
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">QC Status</label>
                            <select name="qc_status" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-qc-status">
                              <option value="">Select Status</option>
                              <option value="QC Passed">QC Passed</option>
                              <option value="QC Failed">QC Failed</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">QC Inspector</label>
                            <select name="qc_inspector" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-qc-inspector select-filters"></select>
                          </div>
                        </div>
                        <div class="col-2 p-1">
                          <div class="form-group">
                            <label for="">Operator</label>
                            <select name="operator" class="form-control rounded pt-1 pb-1 pl-2 pr-2 select-filters" id="select-operator-assembly"></select>
                          </div>
                        </div>
                      </div>
                    </form>
                    <div class="table-div m-1 p-0"></div>
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
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<script> 
  $(document).ready(function(){
    $('#date-range-fabrication').daterangepicker({
      "showDropdowns": true,
      "startDate": moment(),
      "endDate": moment(),
      "ranges": {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      "linkedCalendars": false,
      "autoUpdateInput": true,
      "alwaysShowCalendars": true,
    }, function(start, end, label) {
      logs_per_operation($('#date-range-fabrication').closest('.tab-pane'));
    });
    
    $('#date-range-fabrication').on('apply.daterangepicker', function(ev, picker) {
      logs_per_operation($('#date-range-fabrication').closest('.tab-pane'));
    });

    $('#date-range-painting').daterangepicker({
      "showDropdowns": true,
      "startDate": moment(),
      "endDate": moment(),
      "ranges": {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      "linkedCalendars": false,
      "autoUpdateInput": true,
      "alwaysShowCalendars": true,
    }, function(start, end, label) {
      logs_per_operation($('#date-range-painting').closest('.tab-pane'));
    });
    
    $('#date-range-painting').on('apply.daterangepicker', function(ev, picker) {
      logs_per_operation($('#date-range-painting').closest('.tab-pane'));
    });

    $('#date-range-assembly').daterangepicker({
      "showDropdowns": true,
      "startDate": moment(),
      "endDate": moment(),
      "ranges": {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      "linkedCalendars": false,
      "autoUpdateInput": true,
      "alwaysShowCalendars": true,
    }, function(start, end, label) {
      logs_per_operation($('#date-range-assembly').closest('.tab-pane'));
    });
    
    $('#date-range-assembly').on('apply.daterangepicker', function(ev, picker) {
      logs_per_operation($('#date-range-assembly').closest('.tab-pane'));
    });

    $('.select-filters').change(function(e) {
      logs_per_operation($(this).closest('.tab-pane'));
    });

    load_logs();
    function load_logs(){
      $("#qa-logs-content .tab-pane").each(function(i) {
        logs_per_operation($(this));
      });
    }

    function logs_per_operation(el){
      $.ajax({
        url:"/tbl_qa_inspection_log_report",
        type:"GET",
        data: $(el).find('form').eq(0).serialize(),
        success:function(data){
          $(el).find('.table-div').eq(0).html(data);
        }
      }); 
    }

    $('.select-qc-status').select2({
      placeholder: 'Select Status',
      dropdownAutoWidth: false,
      width: '100%',
      cache: false
    });

    $('#select-workstation-fabrication').select2({
      width: '100%',
      placeholder: 'Select Workstation',
      ajax: {
        url: '/qa_logs_filters',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            q: data.term, // search term
            type: 'workstation',
            operation: 1
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
        cache: true
      }
    });

    $('#select-process-fabrication').select2({
      width: '100%',
      placeholder: 'Select Process',
      ajax: {
        url: '/qa_logs_filters',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            q: data.term, // search term
            type: 'process',
            operation: 1
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
        cache: true
      }
    });

    $('#select-operator-fabrication').select2({
      width: '100%',
      placeholder: 'Select Operator',
      ajax: {
        url: '/qa_logs_filters',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            q: data.term, // search term
            type: 'operator',
            operation: 1
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
        cache: true
      }
    });

    $('#select-workstation-painting').select2({
      width: '100%',
      placeholder: 'Select Workstation',
      ajax: {
        url: '/qa_logs_filters',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            q: data.term, // search term
            type: 'workstation',
            operation: 0
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
        cache: true
      }
    });

    $('#select-process-painting').select2({
      width: '100%',
      placeholder: 'Select Process',
      ajax: {
        url: '/qa_logs_filters',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            q: data.term, // search term
            type: 'process',
            operation: 0
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
        cache: true
      }
    });

    $('#select-operator-painting').select2({
      width: '100%',
      placeholder: 'Select Operator',
      ajax: {
        url: '/qa_logs_filters',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            q: data.term, // search term
            type: 'operator',
            operation: 0
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
        cache: true
      }
    });

    $('#select-workstation-assembly').select2({
      width: '100%',
      placeholder: 'Select Workstation',
      ajax: {
        url: '/qa_logs_filters',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            q: data.term, // search term
            type: 'workstation',
            operation: 3
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
        cache: true
      }
    });

    $('#select-process-assembly').select2({
      width: '100%',
      placeholder: 'Select Process',
      ajax: {
        url: '/qa_logs_filters',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            q: data.term, // search term
            type: 'process',
            operation: 3
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
        cache: true
      }
    });

    $('#select-operator-assembly').select2({
      width: '100%',
      placeholder: 'Select Operator',
      ajax: {
        url: '/qa_logs_filters',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            q: data.term, // search term
            type: 'operator',
            operation: 3
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
        cache: true
      }
    });

    $('.select-qc-inspector').select2({
      width: '100%',
      placeholder: 'Select QC Inspector',
      ajax: {
        url: '/qa_logs_filters',
        method: 'GET',
        dataType: 'json',
        data: function (data) {
          return {
            q: data.term, // search term
            type: 'qc_inspector'
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
        cache: true
      }
    });
    
    $("#clear-filter-fabrication").click(function(e){
      e.preventDefault();
      $("#select-workstation-fabrication").empty();
      $("#select-process-fabrication").empty();
      $("#select-operator-fabrication").empty();
      var el = $(this).closest('.tab-pane');
      el.find('.select-qc-inspector').val(null).trigger('change');
      el.find('.select-qc-status').val(null).trigger('change');
      logs_per_operation(el);
    });

     $("#clear-filter-painting").click(function(e){
      e.preventDefault();
      $("#select-workstation-painting").empty();
      $("#select-process-painting").empty();
      $("#select-operator-painting").empty();
      var el = $(this).closest('.tab-pane');
      el.find('.select-qc-inspector').val(null).trigger('change');
      el.find('.select-qc-status').val(null).trigger('change');
      logs_per_operation(el);
    });

     $("#clear-filter-assembly").click(function(e){
      e.preventDefault();
      $("#select-workstation-assembly").empty();
      $("#select-process-assembly").empty();
      $("#select-operator-assembly").empty();
      var el = $(this).closest('.tab-pane');
      el.find('.select-qc-inspector').val(null).trigger('change');
      el.find('.select-qc-status').val(null).trigger('change');
      logs_per_operation(el);
    });
  });
</script>
@endsection