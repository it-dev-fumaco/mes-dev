<div class="modal fade" id="logout_confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
  aria-hidden="true" data-keyboard="false" data-backdrop="static" style="position: absolute;top: -150px;bottom: 0;left: 0;">

    <!-- Add .modal-dialog-centered to .modal-dialog to vertically center the modal -->
    <div class="modal-dialog modal-dialog-centered" role="document">

      <div class="modal-content">
          <div class="modal-body" style="margin-top: -20px;">
            <h4>Would you like to create another transaction?</h4>
            <span id="timer"></span>
          </div>
          
          <div style="font-size: 15pt;">
                  
                  <button type="button" class="btn btn-danger btn-lg" onclick="logout_user()" style="float: right; margin-right: 20px;"> No</button>
                  <button type="submit" class="btn btn-primary btn-lg" data-dismiss="modal" onclick="stoptimer()" style="float: right;"> Yes</button>
            </div>
      </div>
    </div>
</div>