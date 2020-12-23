<!-- Modal -->
<div class="modal fade" id="change-raw-mat-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="min-width: 35%;">
      <form id="change-raw-mat-frm" action="#" method="post" autocomplete="off">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title">Item Inquiry</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <input type="hidden" name="production_order_item_id">
            <input type="hidden" id="req-url-val">
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-3">
                     <a href="http://athenaerp.fumaco.local/storage/icon/no_img.png" data-toggle="lightbox" id="a-img">
                        <img src="http://athenaerp.fumaco.local/storage/icon/no_img.png" class="img-thumbnail" id="b-img" width="150">
                      </a>
                  </div>
                  <div class="col-md-9">
                     <div class="form-group">
                        <input type="text" class="form-control w-50" id="input-raw-mat" name="item_code_replacecment">
                        <div id="autocomplete-box"></div>
                        <input type="hidden" id="item-classification-raw-mat">
                     </div>
                     <div class="form-group">
                        <span class="d-block font-weight-bold">Item Code: </span>
                        <span class="d-block" id="raw-mat-item-code">-</span>
                        <span class="d-block font-weight-bold">Description: </span>
                        <span class="d-block" id="raw-mat-description">-</span>

                        <span class="d-block font-weight-bold">Warehouse: </span>
                        <span class="d-block" id="raw-mat-warehouse">-</span>

                        <span class="d-block font-weight-bold">Actual Qty: </span>
                        <span class="d-block" id="raw-mat-actual-qty">-</span>
                     </div>
                     <div class="inv-list"></div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-4 offset-md-2">
                     <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Cancel</button>
                  </div>
                  <div class="col-md-4">
                     <button type="submit" class="btn btn-primary btn-block">Change Raw Material</button>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </div>
</div>