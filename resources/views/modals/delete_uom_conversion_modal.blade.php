<!-- Modal -->
<div class="modal fade" id="delete-uom-conversion-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/delete_uom_conversion" method="POST">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color:  #e74c3c ;">
               <h5 class="modal-title">Delete UoM Conversion</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     <input type="hidden" name="uom_conversion_id">
                        <div class="row m-0">
                           <div class="col-sm-12">
                              Delete UoM Conversion <span class="uom-description font-weight-bold"> - </span> ?
                           </div>               
                        </div>
                  </div>

               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               &nbsp;
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
