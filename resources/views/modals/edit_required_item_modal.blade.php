<!-- Modal -->
<div class="modal fade modal-ste" id="change-required-item-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document" style="min-width: 40%;">
      <form action="/update_ste_detail" method="POST" autocomplete="off">
         @csrf
         <input type="hidden" id="change-required-item-production-order" name="production_order">
         <input type="hidden" name="old_item_code">
         <input type="hidden" name="ste_names">
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">Replace Item</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="form-row">
                  <div class="form-group col-md-6">
                     <label>Item Code</label>
                     <input type="text" class="form-control" name="item_code" placeholder="Item Code" maxlength="7" required>
                     <input type="hidden" name="item_name">
                  </div>
                  <div class="form-group col-md-3">
                     <label>Quantity</label>
                     <input type="text" class="form-control" name="quantity" placeholder="Quantity" required>
                  </div>
                  <div class="form-group col-md-3">
                     <label>Stock UOM</label>
                     <input type="text" class="form-control" name="stock_uom" placeholder="Stock UOM" readonly>
                  </div>
                  <div class="form-group col-md-6">
                     <label>Item Classification</label>
                     <input type="text" class="form-control" name="item_classification" placeholder="Item Classification" readonly>
                  </div>
                  <div class="form-group col-md-6">
                     <label>Source Warehouse</label>
                     <select name="source_warehouse" class="form-control" required></select>
                  </div>
                  <div class="form-group col-md-6">
                     <label>Description</label>
                     <textarea name="description" rows="10" class="form-control" style="min-height: 120px;" readonly></textarea>
                  </div>
                  <div class="col-md-6 inv-list"></div>
                  <div class="form-group col-md-12">
                     <label for="remarks">Remarks</label>
                     <textarea name="remarks" rows="10" class="form-control" style="min-height: 70px;"></textarea>
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

