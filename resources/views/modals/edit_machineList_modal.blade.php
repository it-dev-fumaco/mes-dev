<!-- Modal -->
<div class="modal fade" id="edit-machine-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <form action="/edit_machine" method="POST" id="edit-machine-frm" enctype="multipart/form-data">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons design-2_ruler-pencil"></i> Edit Machines<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6">
                    <input type="hidden" name="machineID" id="edit_machineid">
                    <input type="hidden" name="origimage" id="edit_orig_image">
                    <input type="hidden" name="origmachine_code" id="edit_origmachine_code">

                     <div class="form-group">
                        <label>Machine Code:</label>
                        <input type="text" class="form-control form-control-lg" name="machine_code" id="edit_machinecode">
                     </div>
                     <div class="form-group">
                        <label>Machine ID:</label>
                        <input type="text" class="form-control form-control-lg" name="machine_id" id="editt_machineid">
                     </div>
                     <div class="form-group">
                        <label for="category">Status:</label>
                     
                        <select class="form-control" name="status" id="edit_machine_status" required>
                           <option value="" style="font-size: 12pt;">Select Status</option>
                           <option style="font-size: 12pt;" value="Available">Available</option>
                           <option style="font-size: 12pt;" value="On-going Maintenance">On-going Maintenance</option>
                           <option value="Unavailable" style="font-size: 12pt;">Unavailable</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Machine Name:</label>
                        <input type="text" class="form-control form-control-lg" name="machine_name" id="edit_machine_name">
                     </div>
                     <div class="form-group">
                        <label>Type:</label>
                        <input type="text" class="form-control form-control-lg" name="type" id="edit_machine_type">
                     </div>
                    
                     
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Upload Image:</label>                        
                        <div style="text-align: center;" id="machine_test">
                            <div>
                            @php
                          $img = '/storage/machine/change_image.png'
                           @endphp
                              <img src="{{ asset($img) }}" width="150" height="150" class="imgPreview" id="machine_image">
                              </div>
                              <div class="fileUpload btn btn-warning upload-btn">
                              <span>Choose File..</span>
                              <input type="file" name="machineImage" class="upload" id="machine_image_forupload" enctype="multipart/form-data"/>
                           </div>                  
                       </div>
                    </div>
                     <div class="form-group">
                        <label>Model:</label>
                        <input type="text" class="form-control form-control-lg" name="model" id="edit_machine_model">
                     </div>
                    
                     
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
