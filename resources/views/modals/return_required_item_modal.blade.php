<!-- Modal -->
<div class="modal fade" id="return-required-item-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <form action="/create_material_transfer_for_return" method="POST" autocomplete="off">
            @csrf
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #0277BD;">
                    <h5 class="modal-title">Return Item to Warehouse</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="row m-0">
                                <div class="col-sm-12">
                                    <input type="hidden" name="ste_names">
                                    <input type="hidden" name="production_order">
                                    <input type="hidden" name="item_code">
                                    <input type="hidden" name="id">
                                    <input type="hidden" name="qty">
                                    <input type="hidden" name="source_warehouse">
                                    <input type="hidden" name="target_warehouse">
                                    <div class="form-group text-center">
                                        <label>Enter quantity</label>
                                        <input type="text" class="form-control" name="qty_to_return" style="border-radius: 2px; text-align: center; font-size: 15pt;" required>
                                    </div>
                                </div>               
                            </div>
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
