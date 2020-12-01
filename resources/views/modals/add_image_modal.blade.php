<!-- Modal -->
<div class="modal fade bd-example-modal-lg add_image_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #0277BD;">
        <h5 class="modal-title machine_name_title">Change Image</h5>
        <label class="machine_name_title"></label>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="/update_machine_img" autocomplete="off" enctype="multipart/form-data">
      @csrf
      <div class="modal-body">
         <div class="col-md-12" id="data_table_entry">

           <input type="hidden" name="test5" class="test5">
            <input type="hidden" name="user_image" class="user_image">
            <label>Machine Code: <span class="machine_name_title"></span></label>
              <div class="form-group">
                  <div style="text-align: center;">
                     @php
                    $img = '/storage/machine/change_image.png';
                     @endphp
                      <div>
                        <img src="{{ asset($img) }}" width="110" height="110" class="imgPreview">
                        </div>
                        <div class="fileUpload btn btn-primary upload-btn">
                        <span>Choose File..</span>
                        <input type="file" name="empImage" class="upload" />
                     </div>
                      
                                           
                 </div>
              </div>
         </div>
      </div>
      <div class="modal-footer" style="margin-top: -30px;">
                <button type="submit" class="btn btn-info"> Update</button>
               <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
      </form>
    </div>
  </div>
</div>