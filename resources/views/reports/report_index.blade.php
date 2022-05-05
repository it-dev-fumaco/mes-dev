@extends('layouts.user_app', [
  'namePage' => 'Report',
  'activePage' => 'operation_report',
])

@section('content')
<div class="panel-header" style="margin-top: -70px;">
    <div class="header text-center">
       <div class="row">
          <div class="col-md-8 text-white">
             <table style="text-align: center; width: 100%;">
                <tr>
                   <td style="width: 30%; border-right: 5px solid white;">
                      <div class="pull-right title mr-3">
                         <span class="d-block m-0 p-0" style="font-size: 14pt;">{{ date('M-d-Y') }}</span>
                         <span class="d-block m-0 p-0" style="font-size: 10pt;">{{ date('l') }}</span>
                      </div>
                   </td>
                   <td style="width: 20%; border-right: 5px solid white;">
                      <h3 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h3>
                   </td>
                   <td style="width: 50%">
                      <h3 class="title text-left p-0 ml-3" style="margin: auto 20pt;">Report</h3>
                   </td>
                </tr>
             </table>
          </div>
       </div>
    </div>
 </div>
 <br>
 <div class="content" style="margin-top: -100px;">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-body" style="min-height:750px;">
                   <div class="row" style="margin-top: 15px;">
                      
                      <div class="col-md-6"  style="margin-top:30px;">
                        <table style= "width:100%;">
                            <thead  style="">
                                <tr style="">
                                    <th><span style="font-size:13.5pt;padding-left:30px;">Fabrication</span> </th>
                                </tr>
                            </thead>
                            <tbody style=" padding-left:30px; text-align:left;">
                                <tr>
                                    <td><a href="/link_fabrication_report/1" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Daily Fabrication Output Report </span></a></td>
                                </tr>
                                <tr>
                                    <td><a href="/link_fabrication_report/2" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Fabrication Operator Log Report  </span></a></td>
                                </tr>
                                <tr>
                                    <td><a href="/link_fabrication_report/3" class="hover-class"><span style="padding-left:30px;line-height:25px;">  Operator Load Utilization  </span></a></td>
                                </tr>
                               {{-- <tr>
                                    <td><a href="/link_production_schedule_report" class="hover-class"><span style="padding-left:30px;line-height:25px;">  Production Schedule Report  </span></a></td>
                                </tr> --}}
                            </tbody>
                        </table>
                      </div>
                      <div class="col-md-6"  style="margin-top:30px;">
                        <table style= "width:100%;">
                            <thead  style="">
                                <tr style="">
                                    <th><span style="font-size:13.5pt;padding-left:30px;">Painting</span> </th>
                                </tr>
                            </thead>
                            <tbody style=" padding-left:30px; text-align:left;">
                                <tr>
                                    <td><a href="/link_painting_report/1" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Daily Painting Output Report </span></a></td>
                                </tr>
                                <tr>
                                    <td><a href="/link_painting_report/2" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Painting Chemical Record  </span></a></td>
                                </tr>
                                <tr>
                                    <td><a href="/link_painting_report/3" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Water Discharged Monitoring </span></a></td>

                                </tr>
                                <tr>
                                    <td><a href="/link_painting_report/4" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Powder Coat Consumption Report </span></a></td>

                                </tr>
                                
                            </tbody>
                        </table>
                      </div>
                      <div class="col-md-6"  style="margin-top:70px;">
                        <table style= "width:100%;">
                            <thead  style="">
                                <tr style="">
                                    <th><span style="font-size:13.5pt;padding-left:30px;">Wiring and Assembly</span> </th>
                                </tr>
                            </thead>
                            <tbody style=" padding-left:30px; text-align:left;">
                                <tr>
                                    <td><a href="/link_assembly_report/1" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Daily Assembly Output Report </span></a></td>
                                </tr>
                                <tr>
                                    <td><a href="/link_assembly_report/2" class="hover-class"><span style="padding-left:30px;line-height:25px;">  Operator Load Utilization  </span></a></td>
                                </tr>
                                
                            </tbody>
                        </table>
                      </div>
                      <div class="col-md-6" style="margin-top:70px;">
                        <table style= "width:100%;">
                            <thead  style="">
                                <tr style="">
                                    <th><span style="font-size:13.5pt;padding-left:30px;">Quality Assurance</span> </th>
                                </tr>
                            </thead>
                            <tbody style=" padding-left:30px; text-align:left;">
                                <tr>
                                    <td><a href="/link_qa_report/1" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Inspection log Report </span></a></td>
                                </tr>
                                <tr>
                                    <td><a href="/link_qa_report/2" class="hover-class"><span style="padding-left:30px;line-height:25px;"> Rejection Report </span></a></td>
                                </tr>
                            </tbody>
                        </table>
                      </div>
                      <div class="col-md-6"  style="margin-top:70px;">
                        <table style= "width:100%;">
                            <thead  style="">
                                <tr style="">
                                    <th><span style="font-size:13.5pt;padding-left:30px;">Inventory</span> </th>
                                </tr>
                            </thead>
                            <tbody style=" padding-left:30px; text-align:left;">
                                <tr>
                                    <td><a href="/inventory" class="hover-class"><span style="padding-left:30px;line-height:25px;">Stock Report</span></a></td>
                                </tr>
                            </tbody>
                        </table>
                      </div>
                      <div class="col-md-6"  style="margin-top:70px;">
                        <table style= "width:100%;">
                            <thead  style="">
                                <tr style="">
                                    <th><span style="font-size:13.5pt;padding-left:30px;">Data Export</span> </th>
                                </tr>
                            </thead>
                            <tbody style=" padding-left:30px; text-align:left;">
                                <tr>
                                    <td><a href="/export/job_ticket" class="hover-class"><span style="padding-left:30px;line-height:25px;">Job Ticket</span></a></td>
                                </tr>
                                <tr>
                                    <td><a href="/export/rejection_logs" class="hover-class"><span style="padding-left:30px;line-height:25px;">Rejection Logs</span></a></td>
                                </tr>
                                <tr>
                                    <td><a href="/export/machine_list" class="hover-class"><span style="padding-left:30px;line-height:25px;">Machine List</span></a></td>
                                </tr>
                            </tbody>
                        </table>
                      </div>
                      @if (in_array('Production Manager', $user_groups->toArray()))
                        <div class="col-md-12"  style="margin-top:70px;">
                            <table style= "width:100%;">
                                <thead  style="">
                                    <tr style="">
                                        <th><span style="font-size:13.5pt;padding-left:30px;">System Audit Report</span> </th>
                                    </tr>
                                </thead>
                                <tbody style=" padding-left:30px; text-align:left;">
                                    <tr>
                                        <td><a href="/audit_report/mismatched_po_status" class="hover-class"><span style="padding-left:30px;line-height:25px;">Mismatched Production Order Status</span></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="/checkWorkOrderItemQty" class="hover-class"><span style="padding-left:30px;line-height:25px;">Completed Prod. Orders with Inaccurate Required, Transferred and Consumed Qty</span></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="/audit_report/feedbacked_po_with_pending_ste" class="hover-class"><span style="padding-left:30px;line-height:25px;">Feedbacked Production Orders with Pending STE</span></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="/completed_so_with_pending_production_order" class="hover-class"><span style="padding-left:30px;line-height:25px;">Completed Sales Order with Pending Production Order</span></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="/completed_mreq_with_pending_production_order" class="hover-class"><span style="padding-left:30px;line-height:25px;">Completed Material Request with Pending Production Order</span></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="/production_inaccurate_material_transferred" class="hover-class"><span style="padding-left:30px;line-height:25px;">Production Orders with Stock Withdrawals but has incorrect Material Transferred for Manufacturing</span></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="/audit_report/po_items_transferred_and_required_qty_is_matched" class="hover-class"><span style="padding-left:30px;line-height:25px;">Mismatched Production Order Item Required Qty and Transferred Qty</span></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="/audit_report/overridden_production_orders" class="hover-class"><span style="padding-left:30px;line-height:25px;">Overridden Production Orders</span></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                      @endif
                   </div>
                </div>
            </div>
        </div>
    </div>
 </div>

<div id="active-tab"></div>

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

@endsection