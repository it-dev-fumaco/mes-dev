<!-- Modal -->
<div class="modal fade" id="uom-conversion-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <form id="uom-conversion-frm" action="/submit_uom_conversion" method="post" autocomplete="off">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title">Add UoM Conversion</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <input type="hidden" name="conversion_id" value="0">
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     <table style="width: 100%;">

                     <tr>
                           <td>
                              <div class="form-group">
                                 <label for="">Select Material Type</label>
                                 <select class="form-control uom-sel" name="material_type">
                                    <option value="">Select Material Type</option>
                                    @foreach($material_types as $type)
                                    <option value="{{ strtoupper($type) }}">{{ strtoupper($type) }}</option>
                                    @endforeach
                                 </select>
                              </div>
                           </td>
                        </tr>


                        <tr>
                           <td>
                              <div class="form-group">
                                 <input type="hidden" name="id[]" value="0" class="id_1">
                                 <label for="">Select UOM</label>
                                 <select class="form-control uom-sel" name="uom[]">
                                    <option value="">Select UOM</option>
                                    @foreach($uom_list as $row)
                                    <option value="{{ $row->uom_id }}">{{ $row->uom_name }}</option>
                                    @endforeach
                                 </select>
                                 <label for="">Conversion Factor</label>
                                 <input class="form-control" type="text" name="conversion_factor[]" value="0" style="border-radius: 3px; padding: 10px 8px; font-size: 12pt;">
                              </div>
                           </td>
                           <td class="text-center align-middle">
                              <h1 class="p-0 m-0"><i class="now-ui-icons arrows-1_minimal-right"></i></h1>
                           </td>
                           <td>
                              <div class="form-group">
                                 <input type="hidden" name="id[]" value="0"  class="id_2">
                                 <label for="">Select UOM</label>
                                 <select class="form-control uom-sel" name="uom[]">
                                    <option value="">Select UOM</option>
                                    @foreach($uom_list as $row)
                                    <option value="{{ $row->uom_id }}">{{ $row->uom_name }}</option>
                                    @endforeach
                                 </select>
                                 <label for="">Conversion Factor</label>
                                 <input class="form-control" type="text" name="conversion_factor[]" value="0" style="border-radius: 3px; padding: 10px 8px; font-size: 12pt;">
                              </div>
                           </td>
                        </tr>
                     </table>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
                  </div>
                  <div class="col-md-6">
                     <button type="submit" class="btn btn-primary btn-block btn-lg">Submit</button>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </div>
</div>
