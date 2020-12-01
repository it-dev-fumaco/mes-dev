<!-- Modal -->
<div class="modal fade" id="add-email-trans-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/save_add_email_trans" method="POST" id="add-email-trans-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title "> Add Email Alert Recipient<br>
               </h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
               </button>
            </div>
            <div class="modal-body">

               
               <div class="form-row" style="padding-top:15px;">
               <div class="form-group col-md-6">
                  <div class="form-check">
                     <label class="form-check-label" >
                        <input class="form-check-input" type="checkbox" value="" id="email_check">Non User Recipient
                        <span class="form-check-sign">
                              <span class="check"></span>
                        </span>
                     </label>
                  </div> 
                  </div>
                    
                  <div class="form-group col-md-6">
                     <button type="button" class="btn btn-primary pull-right" id="add-emailtrans-button" style="margin: 5px;">
                        <i class="now-ui-icons ui-1_simple-add"></i> Add
                     </button>
                  </div>
                  <hr>
                  <div class="form-group col-md-6">
                     <h6 class="pull-left">Recipient/s</h6>
                  </div>
                  <div class="col-md-12">
                  <table class="table" id="addemail-table" style="font-size: 10px;">
                    <col style="width: 5%;">
                    <col style="width: 40%;">
                    <col style="width: 50%;">
                    <col style="width: 5%;">
                     <thead style="display:none;" id="thead_id_email">
                        <tr>
                           <th style="width: 5%; text-align: center;font-weight: bold;">No.</th>
                           <th style="width: 40%; text-align: center;font-weight: bold;" id="th_type">Transaction Type:</th>
                           <th style="width: 50%; text-align: center;font-weight: bold;" id="th_email">Email:</th>
                           <th style="width: 5%; text-align: center;font-weight: bold;"></th>
                        </tr>
                     </thead>
                     <tbody class="table-body text-center">
                        <tr>
                           
                        </tr>
                     </tbody>
                  </table>
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
