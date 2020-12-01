<!-- Modal -->
<div class="modal fade" id="jt-search-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <form id="jt-search-frm" autocomplete="off">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">Production Order Search</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
                     <div class="form-group">
                        <label for="jt-no-search">Enter Production Order No.</label>
                        <div class="input-group">
                           <div class="input-group-prepend">
                              <div class="input-group-text">PROM-</div>
                           </div>
                           <input type="text" class="form-control" id="jt-no-search" style="font-size: 15pt;" required>
                        </div>
                     </div>
                     <div id="jt-no-numpad" class="text-center numpad-div">
                        <div class="row1">
                           <span class="numpad prod-search-numpad" data-val="1" data-inputid="jt-no-search">1</span>
                           <span class="numpad prod-search-numpad" data-val="2" data-inputid="jt-no-search">2</span>
                           <span class="numpad prod-search-numpad" data-val="3" data-inputid="jt-no-search">3</span>
                        </div>
                        <div class="row1">
                           <span class="numpad prod-search-numpad" data-val="4" data-inputid="jt-no-search">4</span>
                           <span class="numpad prod-search-numpad" data-val="5" data-inputid="jt-no-search">5</span>
                           <span class="numpad prod-search-numpad" data-val="6" data-inputid="jt-no-search">6</span>
                        </div>
                        <div class="row1">
                           <span class="numpad prod-search-numpad" data-val="7" data-inputid="jt-no-search">7</span>
                           <span class="numpad prod-search-numpad" data-val="8" data-inputid="jt-no-search">8</span>
                           <span class="numpad prod-search-numpad" data-val="9" data-inputid="jt-no-search">9</span>
                        </div>
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('jt-no-search').value=document.getElementById('jt-no-search').value.slice(0, -1);"><</span>
                           <span class="numpad prod-search-numpad" data-val="0" data-inputid="jt-no-search">0</span>
                           <span class="numpad" onclick="document.getElementById('jt-no-search').value='';">Clear</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-8 offset-md-2">
                     <button type="submit" class="btn btn-block btn-primary btn-lg">SEARCH</button>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </div>
</div>