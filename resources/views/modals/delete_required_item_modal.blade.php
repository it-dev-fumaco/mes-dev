<div class="modal fade" id="delete-required-item-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <form action="#" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #e74c3c;">
                    <h5 class="modal-title">Cancel Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row p-0">
                    <div class="col-md-12 p-1">
                        <input type="hidden" name="ste_names">
                        <input type="hidden" name="production_order">
                        <input type="hidden" name="item_code">
                        <input type="hidden" name="source_warehouse">
                        <input type="hidden" name="production_order_item_id">
                        <p class="text-center m-0">Cancel withdrawal request(s) for <span class="font-weight-bold"></span></p>
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
