<!-- Modal -->
<div class="modal fade" id="jtname-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/machine_breakdown_save" method="POST" id="machine_kanban_details">
      @csrf
      <input type="hidden" id="workstation-id">
      <input type="hidden" id="jt-id">
      <input type="hidden" id="qty-override">
      <input type="hidden" id="card-status">
      <input type="hidden" id="task-status">
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title">
                  <a href="#" class="prod-view-btn"><span id="modal-title"></span></a>
                  [<span class="workstation-name"></span>]
               </h5>
               
               <div class="pull-right">
                  <button type="button" class="btn btn-danger" id="reset-time-btn">
                     
                  <i class="now-ui-icons arrows-1_refresh-69"></i>
                  </button>
                  <!-- Example single danger button -->
<div class="btn-group">
  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Action
  </button>
  <div class="dropdown-menu">
    <a class="dropdown-item" href="#" id="change-process-btn">Change Process</a>
    <a class="dropdown-item" href="#" id="mark-done-btn">Mark as Done</a>
    <a class="dropdown-item" href="#" id="print-job-ticket-btn" target="_blank">Print Job Ticket</a>
  </div>
</div>

               </div>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="pull-left col-md-12 text-white" style="font-size: 9pt;margin-top: -50px;">
                     <span style="padding-right: 10px;"><b>Planned Start Date:</b></span>
                     <span style="padding-right: 10px;" id="prod_planstart_date"></span>
                     <span style="padding-right: 10px;"><b>Delivery Date:</b></span>
                     <label id="prod_delivery"></label>
                  </div>
                  <div class="col-md-12" style="margin-top: -10px;">
                     
                     <table style="width: 100%;" class="table-borderless">
                        <tbody>
                           <tr>
                              <td style="text-align: left; font-size: 10pt;" colspan="4">
                                 <span style="display: inline;font-weight: bold;padding-right: 5px;">Process: </span>
                                 <span style="padding-right: 10px; font-size: 12pt;" class="process"></span>
                              </td>
                           </tr>
                        <tr>
                           <td style="text-align: left; font-size: 10pt;" colspan="4">
                              <span id="sales_order" style="display: inline;font-weight: bold;padding-right: 5px;"></span>
                              <span style="padding-right: 10px;">-</span>
                              <label id="customer" style="display:inline;"></label>
                           </td>
                        </tr>
                        <tr style="height: 50px;">
                           <td style="text-align: left; font-size: 10pt;line-height: 18pt;">
                              <label id="prod_item" style="font-weight: bold; display: inline;"></label>
                              -<label id="prod_desc" style="display: inline;padding-left: 5px"></label>
                           </td>
                        </tr>
                        <tr style="margin-top: 50px; line-height: 40px; font-weight: bold;" class="text-center">
                           <td style="text-align: center; font-size: 10pt; padding-right: 20px; min-height: 20%;">
                              <span style="padding-right: 20px;"><b>Qty:</b></span>
                              <label id="prod_qty" style="padding-right: 70px;font-size: 12pt;"></label>
                              <span style="padding-right: 20px;"><b>Completed Qty:</b></span>
                              <label id="prod_cpt" style="font-size: 12pt;"></label>
                           </td>
                        </tr>
                        <tr style="">
                           <td style="text-align: left; font-size: 10pt; line-height: 13px; "></td>
                        </tr>
                        </tbody>
                     </table>
                  <div class="div_machine_details" style="margin-top: -15px;">
                  <br>
                  <h6 style="color: #de6332;">Machine Details</h6>
                  <hr>

                  <div class="text-center">
                  <h6 class="text-center"  style="font-size: 12pt; display: inline; text-align: center;" id="prod_machine_name"><b>AMADA NS2 535 SHEAR</b></h6>
                  <h6 id="prod_machine_code" style="display: inline-block;text-align: center;font-size: 12pt;"></h6>
                  </div>

                  <div class="col-md-12" class="text-center" style="text-align:center; line-height: 18pt;">
                  <div class="text-center" style="font-size: 9pt; text-align: center;">
                  <span style="padding-right: 2px;text-align: center;" id="label_start_time" class="label_start_time"><b>Start Time:</b></span>
                  <span style="padding-right: 10px;text-align: center;" id="prod_start_time"></span>
                  <span style="padding-right: 2px;text-align: center;" id="label_prod_end_time"><b>End Time:</b></span>
                  <span id="prod_end_time" style="text-align: center;"></span>

                  </div>
                  <div class="text-center" style="font-size: 9pt; text-align: center;">
                  <span style="padding-right: 2px;text-align: center;" id="label_duration"><b>Duration:</b></span>
                  <span style="padding-right: 10px;text-align: center;" id="prod_duration"></span>
                  <span style="padding-right: 2px;text-align: center;" id="label_cycle_time"><b>Cycle Time:</b></span>
                  <span id="prod_cycle_time" style="text-align: center;"></span>

                  </div>
                  <label style="padding-right: 10px;font-weight: bold;font-size: 9pt; display: inline;" id="label_operator">Operator:</label><label id="prod_operator" style="font-style: italic;font-size: 9pt;display: inline;"></label>
                  </div>
                  </div>
                  
                  <div class="div_quality_check" style="margin-top: -15px;">
                  <br>
                  <h6 style="color: #de6332;">QA Remarks</h6>
                  <hr>
                  <table style="width: 100%; border-color: #D5D8DC;font-size:10pt;" id="jt-details-tbl">
                  <col style="width: 15%;">
                  <col style="width: 15%;">
                  <col style="width: 12%;">
                  <col style="width: 12%;">
                  <col style="width: 10%;">
                  <col style="width: 12%;">
                  <col style="width: 12%;">
                  <col style="width: 12%;">
                  <thead style="font-size: 8pt;">
                      <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Qa Inspection type</b></td>
                      <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Qa Inspection Date</b></td>
                      <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Qa Staff</b></td>
                      <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Sampling qty</b></td>
                      <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Reject qty</b></td>
                      <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Rework qty</b></td>
                      <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Status</b></td>
                      <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Inspection type</b></td>
                      
                  </thead>
                  <tbody style="font-size: 8pt;"></tbody>
                </table>
                
                
                  </div>
                  </div>
               </div>
            </div>
</div>
</form>
</div>
</div>

