@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
  'activePage' => 'stock_adjustment_entries',
])

@section('content')
<div class="panel-header" style="margin-top: -73px;">
  <div class="header text-center">
    <div class="row">
      <div class="col-md-12">
        <table style="text-align: center; width: 100%;">
          <tr>
            <td style="width: 27%; border-right: 5px solid white;">
              <h2 class="title">
                <div class="pull-right" style="margin-right: 20px;">
                  <span style="display: block; font-size: 20pt;">{{ date('M-d-Y') }}</span>
                  <span style="display: block; font-size: 12pt;">{{ date('l') }}</span>
                </div>
              </h2>
            </td>
            <td style="width: 14%; border-right: 5px solid white;">
              <h3 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h3>
            </td>
            <td style="width: 59%">
              <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Fabrication Inventory</h2>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="content" style="margin-top: -118px;">
  <div class="row">
    <div class="col-md-2 offset-md-1">
      <div class="card" style="background-color: #0277BD;" >
        <div class="card-body" style="padding-bottom: 0;">
          <div class="row">
            <div class="col-md-12" style="margin-top: -10px;">
              <h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Model</h5>
            </div>
          </div>
          <div class="row" style="background-color: #ffffff; padding-top: 9px;">
            @foreach($attributes as $attr)
            @if(in_array($attr['attribute'], ['Product Code', 'Model']))
            <div class="col-md-12" style="margin: 0">
              <span style="display: block; font-size: 9pt; margin-top: -8px;">{{ $attr['attribute'] }}</span>

              <div class="form-group">
                <select class="form-control inv-filters">
                  <option value="">Select {{ $attr['attribute'] }}</option>
                  @foreach($attr['values'] as $val)
                  <option>{{ $val }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            @endif
            @endforeach
          </div>
        </div>
      </div>
      <div class="card" style="background-color: #0277BD; margin-top: -10px;" >
        <div class="card-body" style="padding-bottom: 0;">
          <div class="row">
            <div class="col-md-12" style="margin-top: -10px;">
              <h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Cutting Size</h5>
            </div>
          </div>
          <div class="row" style="background-color: #ffffff; padding-top: 9px;">
            @foreach($attributes as $attr)
            @if(in_array($attr['attribute'], ['Length', 'Width', 'Thickness']))
            <div class="col-md-12" style="margin: 0">
              <span style="display: block; font-size: 9pt; margin-top: -8px;">{{ $attr['attribute'] }}</span>

              <div class="form-group">
                <select class="form-control inv-filters">
                  <option value="">Select {{ $attr['attribute'] }}</option>
                  @foreach($attr['values'] as $val)
                  <option>{{ $val }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            @endif
            @endforeach
          </div>
        </div>
      </div>
      <div class="card" style="background-color: #0277BD; margin-top: -10px;" >
        <div class="card-body" style="padding-bottom: 0;">
          <div class="row">
            <div class="col-md-12" style="margin-top: -10px;">
              <h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Specifications</h5>
            </div>
          </div>
          <div class="row" style="background-color: #ffffff; padding-top: 9px;">
            @foreach($attributes as $attr)
            @if(!in_array($attr['attribute'], ['Product Code', 'Length', 'Width', 'Thickness', 'Model']))
            <div class="col-md-12" style="margin: 0">
              <span style="display: block; font-size: 9pt; margin-top: -8px;">{{ $attr['attribute'] }}</span>

              <div class="form-group">
                <select class="form-control inv-filters">
                  <option value="">Select {{ $attr['attribute'] }}</option>
                  @foreach($attr['values'] as $val)
                  <option>{{ $val }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            @endif
            @endforeach
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card" style="background-color: #0277BD;" >
        <div class="card-body" style="padding-bottom: 0;">
          <div class="row">
            <div class="col-md-6">
              
              <h5 class="text-white font-weight-bold align-middle">Fabrication Inventory</h5>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text"  class="form-control" placeholder="Search Inventory" id="inv-filter-text" style="background-color: #ffffff;">
              </div>
            </div>
          </div>
          <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
                  <div class="card card-nav-tabs card-plain">
                          <div class="card-header card-header-danger">
                              <div class="nav-tabs-navigation">
                                  <div class="nav-tabs-wrapper">
                                    <ul class="nav nav-tabs" data-tabs="tabs">
                                      <li class="nav-item">
                                        <a class="nav-link active" href="#inventory_list" data-toggle="tab">Inventory List</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link" href="#inventory_history" data-toggle="tab" onclick="inventory_history_list()">Transaction History</a>
                                      </li>
                                      
                                    </ul>
                                  </div>
                              </div>
                            </div>
                            <div class="card-body ">
                                <div class="tab-content text-center">
                                    <div class="tab-pane active" id="inventory_list">
                                      <div class="col-md-6 offset-md-3">
                                        
                                      </div>
                                      <div class="col-md-12" style="min-height: 930px;">
                                        <div id="adjuststock-list-tbl" style="font-size:15px;"></div>
                                      </div>
                                    </div>
                                    <div class="tab-pane" id="inventory_history">
                    
                                      <div class="row">
                                        <div class="col-md-6" style="float:left;">
                                      <div class="form-group">
                                            <input type="text"  class="form-control" placeholder="Search Transaction History" id="inventory_history_search">
                                        </div>
                                      </div>
                                      <div class="col-md-6">
                                        <button class="btn btn-primary btn-stock-adjust-entry" style="float:right;margin-top:-5px;">+ Stock Adjustment Entry</button>
                                      </div>
                                      
                                      </div>
                                      <div class="col-md-12">
                                      
                                        <div id="inventory-list-tbl" style="font-size:15px;"></div>
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

<!-- Modal -->
<div class="modal fade" id="add-stock-entries-adjustment" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/submit/stock_entries_adjustment" method="POST" id="add-stock-entries-adjustment-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header" style="background-color: #0277BD;color:white;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Stock Adjustment Entry<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                  <label>Item Code:</label>
                    <select class="form-control sel2" id="itemcode_line" name="item_code"  style="background-color: white;font-size: 11pt;display:inline-block;" onchange="get_balanceqty()">
                    </select>
                      <div id="item_desc_div">
                        <label style="padding-top:10px;">Item Description:</label>
                        <br>
                        <label id="item_description_label"></label>
                        <input type="hidden" name="item_description_input" id="item_description_input">
                      </div>
                  </div>
               </div>
               <div class="row" style="padding-top:10px;">
                  <div class="col-md-6">
                    <label>Balance QTY:</label>
                  </div>
                  <div class="col-md-6 text-center" id="entry_type_div">
                    <b><label id="entry_type_label" style="font-weight:bold;font-size:20px;"></label></b>
                    <input type="hidden" id="entry_type_box" name="entry_type_box">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group" style="padding-top:10px;">
                        <input type="hidden" name="orig_balance_qty" id="orig_balance_qty">
                        <input type="text" class="form-control form-control-lg balance_qty_id" name="balance_qty" id="balance_qty_id" value="0" required>
                     </div>
                </div>
                  </div>
                
               <div class="row">
                    <div class="col-md-6 text-center" style="text-center:center;">
                        <div class="form-group" style="padding-top:10px;">
                            <label>Planned QTY:</label><b><label style="padding-left:15px; font-size:18px;" id="planned_qty_id" style="font-weight: bold;">0</label></b>
                        </div>                      
                    </div>
                    <div class="col-md-6 text-center" style="text-center:center;">
                        <div class="form-group" style="padding-top:10px;">
                            <label>In Progress QTY:</label><b><label style="padding-left:15px; font-size:18px;" id="actual_qty_id" style="font-weight: bold;">0</label></b>  
                        </div>
                    </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
<style>
.span_bold{
  font-weight: bold;

}
.boldwrap {
  font-weight: bold;
}

.select2-rendered__match {
  background-color: yellow;
  color: black;
}
.myFont{
  font-size:9pt;
}

.select2{
  font-size: 10pt;
}

</style>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />
<script>
$(document).ready(function(){
  $('.inv-filters').select2({ dropdownCssClass: "myFont" });

  $('.inv-filters').change(function(e){
    e.preventDefault();

    get_filters();
  });

  $('#inv-filter-text').keyup(function(e){
    e.preventDefault();

    get_filters();
  });

  function get_filters(){
    var filters = [];
   
    $('.inv-filters').each(function() {
      if ($(this).val()) {
        if (filters.indexOf($(this).val()) < 0){
          filters.push($(this).val());
        }
      }
    });

    var query = $('#inv-filter-text').val();

    stock_adjustment_list(1, filters, query);
  }

    $('#balance_qty_id').val("");
    $('#item_description_input').val("");
    $('#actual_qty_id').text(0);
    $('#planned_qty_id').text(0);
    stock_adjustment_list();
    inventory_history_list();
    $('#item_desc_div').hide();
    $('#entry_type_div').hide();
    $('.schedule-date').datepicker({
        'format': 'yyyy-mm-dd',
        'autoclose': true
    });
    $(document).on('click', '.btn-stock-adjust-entry', function(){
        get_itemcode();
        $('#balance_qty_id').val("");
        $('#item_description_input').val("");
        $('#actual_qty_id').text(0);
        $('#planned_qty_id').text(0);
        $('#add-stock-entries-adjustment').modal('show');
        $('#item_desc_div').hide();
        $('#entry_type_div').hide();
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
});
</script>
<script>
    function get_itemcode(){
        $.ajax({
          url: "/get_item_code_stock_adjustment_entries",
          method: "GET",
          success: function(data) {
          $('#itemcode_line').html(data);
            
          },
          error: function(data) {
          alert(data);
          }
        });
    }
    function get_balanceqty(){
        var item_code = $('#itemcode_line').val();
        $.ajax({
          url: "/get_balanceqty_stock_adjustment_entries/"+ item_code,
          method: "GET",
          success: function(data) {
          $('#balance_qty_id').val(data.qty.balance);
          $('#orig_balance_qty').val(data.qty.balance);
          $('#actual_qty_id').text(data.qty.actual);
          $('#planned_qty_id').text(data.qty.planned);
          $('#item_description_label').text(data.qty.description);
          $('#item_description_input').val(data.qty.description);
          $('#entry_type_label').text(data.qty.entry_type);
          $('#entry_type_box').val(data.qty.entry_type);
          $('#item_desc_div').show();
          $('#entry_type_div').show();
          },
          error: function(data) {
          alert(data);
          }
        });
    }
    $('#add-stock-entries-adjustment-frm').submit(function(e){
      e.preventDefault();
      var item_code = $('#itemcode_line').val();
      
      if(item_code == "default"){
        showNotification("danger", "Pls Select Item code", "now-ui-icons travel_info");
      }else{
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
            $('#add-stock-entries-adjustment-frm').trigger("reset");
            $('#balance_qty_id').val("");
            $('#item_description_input').val("");
            $('#actual_qty_id').text('0');
            $('#planned_qty_id').text('0');
            $('#add-stock-entries-adjustment').modal('hide');
            $('#item_desc_div').hide();
            $('#entry_type_div').hide();
            stock_adjustment_list();
            inventory_history_list();

                // $('#edit-worktation-frm').trigger("reset");
                // workstation_list();

          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      }); 
      }
    });
</script>
<script>

  function markMatch(text, term) {

    var startString = '<span class="boldwrap">';
    var endString = text.replace(startString, '');

    var match = endString.toUpperCase().indexOf(term.toUpperCase());
    var $result = $('<span></span>');

    if (match < 0) {
      return $result.text(text);
    }
    var elementToReplace = endString.substr(match, term.length);
    var $match = '<span class="select2-rendered__match">' + endString.substring(match, match + term.length) + '</span>';
    text = startString + endString.replace(elementToReplace, $match);

    // console.log(text);
    $result.append(text);
    return $result;
  }

  $('.sel2').select2({
    dropdownParent: $("#add-stock-entries-adjustment"),
    dropdownAutoWidth: false,
    width: '100%',
    templateResult: function(item) {
      if (item.loading) {
        return item.text;
      }
      var term = query.term || '';
      var $result = markMatch('<span class="boldwrap">' + item.text.substring(0, item.text.indexOf("-")) + '</span>' + item.text.substring(item.text.indexOf("-")), term);
      return $result;

    },

    language: {
      searching: function(params) {
        // Intercept the query as it is happening
        query = params;
        // Change this to be appropriate for your application
        return 'Searching...';
      }
    },
    cache: true
  });
//   $('.sel2').select2({
//       dropdownParent: $("#add-stock-entries-adjustment"),
//       dropdownAutoWidth: false,
//       width: '100%',
//       cache: false
//     });
</script>
<script>
function stock_adjustment_list(page, filters, query){
    $.ajax({
          url:"/get_tbl_stock_adjustment_entry?page=" + page,
          type:"GET",
          data: {filters: filters, q: query},
          success:function(data){
            console.log(data);
            $('#adjuststock-list-tbl').html(data);
          }
        });

    }
function inventory_history_list(page, query){
    $.ajax({
          url:"/get_fabrication_inventory_history_list?page=" + page,
          type:"GET",
          data: {search_string: query},
          success:function(data){
            
            $('#inventory-list-tbl').html(data);
          }
        });

    }
</script>
<script type="text/javascript">
      function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 3000,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }
    $(document).on('click', '#tbl_stockadjustment_entry_pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         stock_adjustment_list(page);
    });
    $(document).on('click', '#tbl_fabrication_inventory_history_pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         inventory_history_list(page);
    });
    $(document).on('keyup', '#inventory_history_search', function(){
    var query = $(this).val();
    inventory_history_list(1, query);
  });
</script>


@endsection