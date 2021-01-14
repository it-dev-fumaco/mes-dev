<!-- Modal -->
<div class="modal fade" id="jtname-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document" style="min-width: 40%;">
      <form action="/machine_breakdown_save" method="POST" id="machine_kanban_details">
         @csrf
         <input type="hidden" class="time-log-id">
         <input type="hidden" class="time-log-status">
         <input type="hidden" class="workstation-id">
         <input type="hidden" class="job-ticket-id">
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title">
                  <a href="#" class="prod-view-btn view-production-order-details"></a> [<span class="workstation-name"></span>]
               </h5>
               <div class="pull-right">
                  <button type="button" class="btn btn-danger" id="reset-time-btn">
                     <i class="now-ui-icons arrows-1_refresh-69"></i>
                  </button>
                  <div class="btn-group">
                     <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
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
                  <div class="pull-left col-md-12 text-white" style="font-size: 10pt;margin-top: -50px;">
                     <span class="pr-2 font-weight-bold">Planned Start Date:</span>
                     <span class="planned-start-date"></span>
                     <span class="pl-3 pr-2 font-weight-bold">Delivery Date:</span>
                     <span class="delivery-date"></span>
                  </div>
                  <div class="col-md-12" style="margin-top: -10px;">
                     <table style="width: 100%;">
                        <tr>
                           <td class="text-left" colspan="2">
                              <span class="mr-2 font-weight-bold">Process:</span>
                              <span style="font-size: 11pt;" class="process-name"></span>
                           </td>
                        </tr>
                        <tr>
                           <td class="text-left" colspan="2">
                              <span class="font-weight-bold reference-no"></span> -
                              <span class="customer"></span>
                           </td>
                        </tr>
                        <tr>
                           <td class="text-justify pt-3 pb-3" colspan="2">
                              <span class="font-weight-bold pr-2 item-code"></span>
                              <span class="pr-2 pl-1 item-description"></span>
                           </td>
                        </tr>
                        <tr class="text-center pb-2 pt-1 font-weight-bold" style="font-size: 12pt;">
                           <td style="width: 40%;">
                              <span>Qty:</span>
                              <span class="qty-to-manufacture">0</span>
                              <span class="stock-uom"></span>
                           </td>
                           <td style="width: 60%;">
                              <span class="font-weight-bold pr-2">Completed Qty:</span>
                              <span class="completed-qty">0</span>
                              <span class="stock-uom"></span>
                           </td>
                        </tr>
                     </table>
                     <div class="mt-4 mb-4" id="div_machine_details">
                        <h6 style="color: #de6332;">Machine Details</h6>
                        <hr class="mt-0 mb-2 p-0">
                        <div class="row m-0 p-0">
                           <div class="col-md-12 m-1 font-weight-bold text-center">
                              <h6 class="machine-name p-0 m-0" style="font-size: 12pt;">-</h6>
                              <h6 class="machine-code p-0 m-0" style="font-size: 11pt;">-</h6>
                           </div>
                           <div class="col-md-6 text-right" style="font-size: 9pt;">
                              <div class="d-block mt-1">
                                 <span class="mr-2 font-weight-bold">Start Time:</span>
                                 <span class="start-time">-</span>
                              </div>
                              <div class="d-block mt-1">
                                 <span class="mr-2 font-weight-bold">Duration:</span>
                                 <span class="duration-in-mins">0 min(s)</span>
                              </div>
                           </div>
                           <div class="col-md-6 text-left" style="font-size: 9pt;">
                              <div class="d-block mt-1">
                                 <span class="mr-2 font-weight-bold">End Time:</span>
                                 <span class="end-time">-</span>
                              </div>
                              <div class="d-block mt-1">
                                 <span class="mr-2 font-weight-bold">Cycle Time:</span>
                                 <span class="cycle-time-in-mins">0 min(s)</span>
                              </div>
                           </div>
                           <div class="col-md-12 mt-1 text-center" style="font-size: 9pt;">
                              <span class="mr-2 font-weight-bold">Operator:</span>
                              <span class="operator-name">-</span>
                           </div>
                        </div>
                     </div>
                     <div class="mt-4 mb-4" id="div_quality_check">
                        <h6 style="color: #de6332;">QA Remarks</h6>
                        <hr class="mt-0 mb-2 p-0">
                        <table style="width: 100%; border-color: #D5D8DC;font-size:10pt;" id="jt-details-tbl">
                           <col style="width: 18%;">
                           <col style="width: 17%;">
                           <col style="width: 13%;">
                           <col style="width: 13%;">
                           <col style="width: 13%;">
                           <col style="width: 13%;">
                           <col style="width: 13%;">
                           <thead style="font-size: 8pt;">
                              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Inspection type</b></td>
                              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Inspection Date</b></td>
                              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>QA Staff</b></td>
                              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Sampling Qty</b></td>
                              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Reject</b></td>
                              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Rework</b></td>
                              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>Status</b></td>
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