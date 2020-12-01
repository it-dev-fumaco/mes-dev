<!-- Modal -->
<div class="modal fade" id="add-machine-process-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/save_machine_process" method="POST" id="add-machine-process-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons ui-1_simple-add"></i> Add Process<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     
              
                     <input type="hidden" name="machine_code" value="{{ $machine_list->machine_code }}">
                     <div class="form-group">
                        <label>Workstation:</label>
                        @if($machine_workstations != null)
                        <select class="form-control" name="workstation_process" id="workstation_process" onchange="get_workstation_process()">
                           @forelse ($machine_workstations as $row)
                              <option value="{{ $row->workstation_id }}">{{ $row->workstation }}</option>
                           @empty
                              <p>no data fouund</p>
                           @endforelse
                        </select>
                        @else
                        <select class="form-control" name="process" id="process">
                           <option value="" selected>Pls assign first machine to workstation</option>
                        </select>
                        @endif
                     </div>

                      <div class="form-group">
                        <label>Machine Process:</label>
                        <select class="form-control" name="process" id="process">
                        <option value=""></option>
                        
                     </select>
                     </div>
                  </div>

               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                @if($process != null)
               <button type="submit" class="btn btn-primary">Submit</button>
               @endif
            </div>
         </div>
      </form>
   </div>
</div>
