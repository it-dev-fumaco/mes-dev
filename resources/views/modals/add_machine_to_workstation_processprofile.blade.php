<!-- Modal -->
<div class="modal fade" id="add-machine_to_workstation-processprofile-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm" role="document">
      <form action="/save_workstation_machine" method="POST" id="add-machine-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title "> Assign Machine<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label><b>Machine Code:</b></label>
                        <select name="machine_id" style="width: 260px;"  id="machine_code_selection" class="clearme" onchange="machine_assign_description()">
                           @foreach($machine as $row)
                           <option value="{{ $row->id }}">{{ $row->machine_code }} - {{ $row->machine_name}}
                           </option>
                           @endforeach
                        </select>
                     </div>
                     <div id="tbl_machine_details"></div>
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
