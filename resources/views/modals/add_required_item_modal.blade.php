<!-- Modal -->
<div class="modal fade modal-ste" id="add-required-item-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document" style="min-width: 50%;">
      <form action="/add_ste_items" method="POST" autocomplete="off">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">Add Item(s)</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="production_order" id="add-required-item-production-order">
                <table class="table table-bordered">
                    <col style="width: 22%;">
                    <col style="width: 22%;">
                    <col style="width: 20%;">
                    <col style="width: 28%;">
                    <col style="width: 8%;">
                    <tr>
                        <th class="text-center">Add Item As</th>
                        <th class="text-center">Item Code</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Source Warehouse</th>
                        <th></th>
                    </tr>
                    <tbody id="add-required-item-tbody">
                        
                    </tbody>
                </table>
                <div class="pull-left">
                    <button type="button" class="btn btn-info btn-sm" id="add-row-required-item-btn">
                        <i class="now-ui-icons ui-1_simple-add"></i> Add Row
                    </button>
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