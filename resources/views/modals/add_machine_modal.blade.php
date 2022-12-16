<!-- Modal -->
<div class="modal fade" id="add-machine-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <form action="/save_machine" method="POST" id="add-machine-frm" enctype="multipart/form-data">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons ui-1_simple-add"></i> Add Machine<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Machine Code:</label>
                        <input type="text" class="form-control form-control-lg" name="machine_code" required>
                     </div>
                     <div class="form-group">
                        <label>Machine ID:</label>
                        <input type="text" class="form-control form-control-lg" name="machine_id" required>
                     </div>
                     <div class="form-group">
                        <label for="category">Status:</label>
                        <select class="form-control" name="status" id="status" required>
                           <option value="" style="font-size: 12pt;" selected="selected">Select Status</option>
                           <option value="Available" style="font-size: 12pt;">Available</option>
                           <option value="On-going Maintenance" style="font-size: 12pt;">On-going Maintenance</option>
                           <option value="Unavailable" style="font-size: 12pt;">Unavailable</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Machine Name:</label>
                        <input type="text" class="form-control form-control-lg" name="machine_name" required>
                     </div>
                     <div class="form-group">
                        <label>Type:</label>
                        <input type="text" class="form-control form-control-lg" name="type" required>
                     </div>
                    
                     
                  </div>
                  <div class="col-md-6">
                     <div class="form-group" id="upload_edit_form">
                        <label>Upload Image:</label>                        
                        <div style="text-align: center;">
                           @php
                          $img = '/storage/machine/change_image.png'
                           @endphp
                            <div>
                              <img src="{{ asset($img) }}" width="150" height="150" class="imgPreview">
                              </div>
                              <div class="fileUpload btn btn-warning upload-btn">
                              <span>Choose File..</span>
                              <input type="file" name="machineImage" class="upload" enctype="multipart/form-data"/>
                           </div>                  
                       </div>
                    </div>
                     <div class="form-group">
                        <label>Model:</label>
                        <input type="text" class="form-control form-control-lg" name="model" required>
                     </div>
                     {{--<div class="form-group">
                        <label for="category">Machine Process:</label>
                        <select class="form-control" name="machine_process" id="machine_process" required>
                           <option value="" style="font-size: 12pt;" selected="selected">Machine Process</option>
                           
                        </select>
                     </div>
                     --}}
                     
                     
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
