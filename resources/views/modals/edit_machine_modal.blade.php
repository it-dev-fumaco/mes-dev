<!-- Modal -->
<div class="modal fade" id="edit-machine-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <form action="/edit_machine" method="POST" id="edit-machine-frm" enctype="multipart/form-data">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons design-2_ruler-pencil"></i> Edit Machines<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6">
                    <input type="hidden" name="machineID" value="{{ $machine_list->machine_id }}">
                    <input type="hidden" name="origimage" value="{{ $machine_list->image }}">

                     <div class="form-group">
                        <label>Machine Code:</label>
                        <input type="text" class="form-control form-control-lg" name="machine_code" value="{{ $machine_list->machine_code }}">
                     </div>
                     <div class="form-group">
                        <label>Machine ID:</label>
                        <input type="text" class="form-control form-control-lg" name="machine_id" value="{{ $machine_list->reference_key }}">
                     </div>
                     <div class="form-group">
                        <label for="category">Status:</label>
                        @php 
                        $status1="Available";
                        $status2="Unavailable";
                        $status3="On-going Maintenance";

                        
                        @endphp
                        <select class="form-control" name="status" id="status" required>
                           <option value="" style="font-size: 12pt;" selected="selected">Select Status</option>
                           <option style="font-size: 12pt;" value="Available" {{  ($status1 == $machine_list->status) ? "selected" : "" }}>Available</option>
                           <option style="font-size: 12pt;" value="On-going Maintenance" {{  ($status3 == $machine_list->status) ? "selected" : "" }}>On-going Maintenance</option>
                           <option value="Unavailable" style="font-size: 12pt;" {{  ($status2 == $machine_list->status) ? "selected" : "" }}>Unavailable</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Machine Name:</label>
                        <input type="text" class="form-control form-control-lg" name="machine_name" value="{{ $machine_list->machine_name }}">
                     </div>
                     <div class="form-group">
                        <label>Type:</label>
                        <input type="text" class="form-control form-control-lg" name="type" value="{{ $machine_list->type }}">
                     </div>
                    
                     
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Upload Image:</label>                        
                        <div style="text-align: center;" id="machine_test">
                           @php
                          $img = ($machine_list->image == null) ? '/storage/machine/change_image.png' : $machine_list->image;
                           @endphp
                            <div>
                              <img src="{{ asset($img) }}" width="150" height="150" class="imgPreview">
                              </div>
                              <div class="fileUpload btn btn-warning upload-btn">
                              <span>Choose File..</span>
                              <input type="file" name="machineImage" class="upload" />
                           </div>                  
                       </div>
                    </div>
                     <div class="form-group">
                        <label>Model:</label>
                        <input type="text" class="form-control form-control-lg" name="model" value="{{ $machine_list->model }}">
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
