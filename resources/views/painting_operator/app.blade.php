<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="perfect-scrollbar-on">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <title>MES</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  {{--  <!--     Fonts and icons     -->  --}}
  <link href="{{ asset('/css/fontsgoogleapis.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('/css/all.css') }}">
  {{--  <!-- CSS Files -->  --}}
  <link href="{{ asset('/css/bootstrap.css') }}" rel="stylesheet" />
  <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('/css/now-ui-dashboard.css?v=1.3.0') }}" rel="stylesheet" />
  {{--  <!-- CSS Just for demo purpose, don't include it in your project -->  --}}
  <link href="{{ asset('/css/demo.css') }}" rel="stylesheet" />
</head>

<body class="">
  <div class="wrapper">
    <div class="sidebar" data-color="orange">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
    -->
      <div class="logo">
        {{-- <a href="http://www.creative-tim.com" class="simple-text logo-mini">
          CT
        </a> --}}
        <a href="#" class="simple-text logo-normal text-center">
          {{ $namePage }} - Painting
        </a>
      </div>
      <div class="sidebar-wrapper" id="sidebar-wrapper">
        <ul class="nav">
          
        </ul>
      </div>
    </div>
   
    <div class="main-panel" id="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent  bg-primary  navbar-absolute">
        <div class="container-fluid">
          
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <ul class="navbar-nav">
              @if(!in_array($activePage, ['painting_task']))
              
              <li class="nav-item active">
                <a class="nav-link text-center" href="#" id="quality-check-modal-btn" >
                  <i class="now-ui-icons ui-1_check"></i> Quality Check
                  <p hidden>
                    <span class="d-lg-none d-md-block">Quality Check</span>
                  </p>
                </a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="#" id="jt-search-btn">
                  <i class="now-ui-icons ui-1_zoom-bold"></i> Production Order Search
                  <p>
                    <span class="d-lg-none d-md-block">Production Order Search</span>
                  </p>
                </a>
              </li>
              @endif
              <li class="nav-item active {{ $process == 'Unloading' ? 'd-none' : null }}">
                <a class="nav-link" href="#" id="view-painting-schedule-btn">
                  <i class="now-ui-icons design_bullet-list-67"></i>Schedule
                  <p>
                    <span class="d-lg-none d-md-block">Schedule</span>
                  </p>
                </a>
              </li>
              @if($activePage == 'painting_task')
              <li class="nav-item active">
                <a class="nav-link text-center" href="#" style="font-size: 11pt;" id="logout_click">
                  {{--  <i class="now-ui-icons media-1_button-power"></i>  --}}
                  Logout
                  {{--  <img src="{{ asset('img/sign-out.png') }}" style="width: 70%;">  --}}
                  <p hidden>
                    <span class="d-lg-none d-md-block">Logout</span>
                  </p>
                </a>
              </li>
              @endif
              <li class="nav-item dropdown active">
                <a href="#" class="nav-link dropdown-toggle" onclick="return false;" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="now-ui-icons education_atom"></i>
                  <p>
                    <span class="d-lg-none d-md-block">Some Actions</span>
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <a class="dropdown-item"  id="view-chemical-records-btn" href="#">Painting Chemical Records</a>
                  <a class="dropdown-item"  id="view-water-Monitoring-btn" href="#">Water Discharged Monitoring</a>
                  <a class="dropdown-item"  id="view-powder-Monitoring-btn" href="#">Powder Coating Monitoring</a>
                  <a class="dropdown-item"  id="pending-for-maintenance-trigger" href="#">Pending for Maintenance</a>
                  @if (Auth::user())
                    <a class="dropdown-item"  id="machine-breakdown-modal-btn" href="#">Maintenance Request</a>
                  @endif
                </div>
              </li>
              <li class="nav-item active">
                <a class="nav-link text-center" href="#" id="refresh_page_id" onClick="document.location.reload(true)">
                  <i class="now-ui-icons arrows-1_refresh-69"></i> 
                  <p hidden>
                    <span class="d-lg-none d-md-block"></span>
                  </p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      @yield('content')
      <footer class="footer">
        <div class="container-fluid">
          <div class="copyright" id="copyright">
            &copy;
            <script>
              document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear()))
            </script>
            <a href="#" target="_blank">FUMACO IT Dev. Team</a>
          </div>
        </div>
      </footer>
    </div>
  </div>

<style type="text/css">
  .numpad{
    font-weight: bolder;
    font-size: 18pt;
  }
  .custom-bg-selected-active-input{
    background-color: #5562eb;
  }
</style>

  <!--   Core JS Files   -->
  <script src="{{ asset('js/core/ajax.min.js') }}"></script> 
  <script src="{{ asset('js/core/jquery.min.js') }}"></script>
  <script src="{{ asset('js/core/popper.min.js') }}"></script>
  <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
  <!--  Notifications Plugin    -->
  <script src="{{ asset('/js/plugins/bootstrap-notify.js') }}"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{ asset('/js/now-ui-dashboard.min.js?v=1.3.0') }}" type="text/javascript"></script>
  @yield('script')

  <script>
    $(document).ready(function() {
      $(document).on('click', '#quality-inspection-frm .next-tab', function(e){
        e.preventDefault();
        var tab_id = $(this).data('tab-id');
        var tab_qty_reject = parseInt($('#' + tab_id + '-qty-reject').val());
        var tab_qty_checked = parseInt($('#' + tab_id + '-qty-checked').val());
        var tab_qty = parseInt($('#' + tab_id + '-qty').val());
        var tab_reject_level = parseInt($('#' + tab_id + ' .reject-level').text());

        if(tab_qty_checked <= 0){
          showNotification("danger", 'Please enter quantity checked.', "now-ui-icons travel_info");
          return false;
        }

        var checklist_unchecked = $('#' + tab_id + ' .chk-list input:checkbox:not(:checked)').length;
        if(checklist_unchecked > 0){
          if(tab_qty_reject <= 0){
            showNotification("danger", 'Please enter quantity reject.', "now-ui-icons travel_info");
            return false;
          }

          if(tab_qty_reject > tab_qty_checked){
            showNotification("danger", 'Reject quantity cannot be greater than quantity checked.', "now-ui-icons travel_info");
            return false;
          }
        }else{
          $('#' + tab_id + '-qty-reject').val(0);
        }

        if(tab_qty_checked > tab_qty){
          showNotification("danger", 'Quantity checked cannot be greater than '+ tab_qty +'.', "now-ui-icons travel_info");
          return false;
        }

        var sample_size = $('#' + tab_id + ' .sample-size').text();
        if(sample_size != $('#' + tab_id + '-qty-checked').val()){
          if($('#' + tab_id + '-validated-sample-size').val() == 0){
            $('#confirm-sample-size-modal .sample-size').text(sample_size);
            $('#sample-size-tab-id').val(tab_id);
            $('#confirm-sample-size-modal').modal('show');
            return false;
          }
        }
        
        var active_tab_qa = $('#quality-inspection-modal .nav-tabs li > .active').attr('id');
        var invalid = false;
        var declared_reject_qty = $('#quality-inspection-modal').find('input[name="qty_reject"]').eq(0).val();
        if (active_tab_qa == 'tab-occurence') {
          $('#occurence-div input').each(function(){
            if ($(this).val() <= 0) {
              invalid = true;
              showNotification("danger", 'Please specify qty of items with ' + $(this).data('reason'), "now-ui-icons travel_info");
              return false;
            }

            if ($(this).val() > declared_reject_qty) {
              invalid = true;
              showNotification("danger", 'Qty for ' + $(this).data('reason') + ' cannot be greater than rejected qty (' + declared_reject_qty + ').', "now-ui-icons travel_info");
              return false;
            }
          });
        }

        if (invalid) {
          return false;
        }
        
        var next_tab_id = $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').attr('id');
        if(next_tab_id != 'tablast'){
          if(declared_reject_qty <= 0){
            $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').removeClass('custom-tabs-1').addClass('active');
          }else{
            $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').addClass('custom-tabs-1').removeClass('active');
          }
        }

        if (next_tab_id == 'tab-occurence') {
          $('#occurence-div').empty();
        }  
        
        var no_rej = '';
        var table = '<table style="width: 100%; font-size: 10pt;" border="1">' + 
          '<col style="width:60%;"><col style="width:40%;">' +
          '<tr><th class="text-center" style="border: 1px solid #ABB2B9; padding: 2px 0;">Reject Reason</th><th class="text-center" style="border: 1px solid #ABB2B9; padding: 2px 0;">Qty</th></tr>';
        
        var reject_id = '';
        var reject_values = '';
        var qty_checked = 0;
        var qty_reject = 0;
        $('#quality-inspection-modal .custom-tabs-1').each(function(){
          var tab_pane_id = $('#' + $(this).attr('id') + '-inspection');
          var q = tab_pane_id.find('input[name="qty_checked"]').eq(0).val();
          var r = tab_pane_id.find('input[name="qty_reject"]').eq(0).val();
          if(q){
            qty_checked = qty_checked + parseInt(q);
            qty_reject = qty_reject + parseInt(r);
          }

          $('#final-qty-rejected').text(qty_reject);

          var reject_type_occurence = '';
          $('#' + $(this).attr('id') + '-inspection input:checkbox:not(:checked)').each(function(i){
            if($.isNumeric($(this).val())){
              reject_id += $(this).val() + ',';
              reject_values += $('#' + $(this).attr('id') + '-input').val() + ',';
              
              reject_type_occurence += '<div class="col-6 p-1"><div class="d-flex flex-row align-items-center border rounded"><div class="col-9 pt-1 pb-1 pl-2 pr-2">'+$(this).data('reject-reason')+'</div>' +
              '<div class="col-3 p-1"><input type="text" class="form-control rounded qty-input p-1" style="font-size: 16px;" data-edit="1" data-qa="1" id="rto'+i+'" value="0" name="occ['+$(this).val()+']" data-reason="'+$(this).data('reject-reason')+'" readonly required></div></div></div>';
            }
          });

          if (next_tab_id == 'tab-occurence') {
            $('#occurence-div').append(reject_type_occurence);
          }

          var checklist_category = tab_pane_id.find('.checklist-category').eq(0).text();
          var reject_qty = tab_pane_id.find('input[name="qty_reject"]').eq(0).val();
          var reason = '';
          $('#' + $(this).attr('id') + '-inspection input:checkbox:not(:checked)').each(function(){
            if($.isNumeric($(this).val())){
              reason += $(this).data('reject-reason') + ', ';
            }
          });

          if(checklist_category){
            if(parseInt(tab_pane_id.find('input[name="qty_checked"]').eq(0).val()) > 0){
              if(reject_qty <= 0){
                reason = 'No Reject';
                no_rej += '<br>' + tab_pane_id.find('.chklist-cat').text();
              }else{
                $('#occurence-div .d-flex').each(function(){
                  table += '<tr>' + 
                    '<td class="text-center" style="border: 1px solid #ABB2B9; padding: 2px;">' + $(this).find('div').eq(0).text() + '</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9; padding: 2px;">' + $(this).find('input').eq(0).val() + '</td>' +
                    '</tr>';
                });
              }
            }
          }

          $('#qa-result-div-1').html(no_rej);
        });

        table += '</table>';

        $('#rejection-types-input').val(reject_id);
        $('#rejection-values-input').val(reject_values);
        $('#final-qty-checked').text(qty_checked);

        $('#total-rejects-input').val(qty_reject);
        $('#total-checked-input').val(qty_checked);

        if(qty_reject > 0){
          $('#quality-inspection-frm .reject-details-tr').removeAttr('hidden');
          $('#qc-status').addClass('text-danger').removeClass('text-success').text('QC Failed');
          $('#qa-result-div').html(table);
        }else{
          $('#quality-inspection-frm .reject-details-tr').attr('hidden', true);
          $('#qc-status').addClass('text-success').removeClass('text-danger').text('QC Passed');
          $('#qa-result-div').empty();
        }

        active_input = null;
        
        $('#quality-inspection-modal .nav-tabs .nav-item > .active').parent().next().find('.custom-tabs-1').tab('show');
        $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').removeAttr('active');
      });

      $(document).on('click', '#quality-inspection-frm .prev-tab', function() {
        active_input = null;

        var prev_tab_id = $('#quality-inspection-modal .nav-tabs li > .active').parent().prev().find('a[data-toggle="tab"]').attr('id');
        if (prev_tab_id == 'tabitem') {
          $('#quality-inspection-modal .nav-tabs .nav-item > .active').parent().prev().find('.custom-tabs-1').tab('show');
        } else {
          var next_tab_id = $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').attr('id');
          if(next_tab_id != 'tablast'){
            $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').removeClass('custom-tabs-1').addClass('active');
          }else{
            $('#quality-inspection-modal .nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').addClass('custom-tabs-1').removeClass('active');
          }
          $('#quality-inspection-modal .nav-tabs .nav-item > .active').parent().prev().find('.custom-tabs-1').tab('show');
        }
      });
    });
  </script>
</body>
</html>
