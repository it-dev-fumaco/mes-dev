<!-- Modal -->
<div class="modal fade modal-ste" id="change-required-qty-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <form action="/update_production_order_item_required_qty" method="POST" autocomplete="off">
         @csrf
         <input type="hidden" name="production_order_item_id">
         <input type="hidden" name="required_qty">
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">Update Required Qty</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body m-0 p-2">
               <div class="form-row text-center">
                  <div class="form-group col-md-6 offset-md-3">
                     <label>Required Qty</label>
                     <input type="text" class="form-control" name="qty" required style="font-size: 12pt; text-align: center;">
                  </div>
               </div>
            </div>
            <div class="modal-footer p-2">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>

