<!-- Modal -->
<div class="modal fade" id="enter-operator-id-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title text-center">Modal Title</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6">
                     <table style="width: 100%;">
                        <tr>
                           <td class="text-center">
                              <span style="font-size: 15pt; font-weight: bold;" class="production-order">PROM-00001</span>
                           </td>
                        </tr>
                        <tr>
                           <td><br><span>Customer: </span><span class="customer-name" style="font-size: 12pt; font-weight: bold;">-</span></td>
                        </tr>
                        <tr>
                           <td><span>Reference No.: </span><span class="reference-no" style="font-size: 12pt; font-weight: bold;">-</span></td>
                        </tr>
                        <tr>
                           <td><span class="item-code" style="font-size: 13pt; font-weight: bold;">-</span></td>
                        </tr>
                        <tr>
                           <td><span class="item-description">-</span></td>
                        </tr>
                     </table>
                     <br>
                     <table style="width: 100%; margin-top: 8px;" border="1">
                        <col style="width: 34%;">
                        <col style="width: 33%;">
                        <col style="width: 33%;">
                        <tr>
                           <th class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;">Quantity</th>
                           <th class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;">Pending</th>
                           <th class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;">Completed</th>
                        </tr>
                        <tr style="font-size: 15pt;">
                           <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><span class="required-qty">70</span></td>
                           <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><span class="pending-qty">40</span></td>
                           <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><span class="completed-qty">30</span></td>
                        </tr>
                     </table>
                  </div>
                  <div class="col-md-6">
                     <h6 class="text-center">Enter Biometric ID</h6>
                     <form action="#" method="POST" autocomplete="off">
                        @csrf
                        <div class="row">
                           <div class="col-md-10 offset-md-1">
                              <div class="form-group">
                                 <input type="text" class="form-control" name="operator_id" id="user-id" style="font-size: 15pt; text-align: center;" required>
                              </div>
                           </div>
                        </div>
                        <div class="text-center numpad-div">
                           <div class="row1">
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '1';">1</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '2';">2</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '3';">3</span>
                           </div>
                           <div class="row1">
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '4';">4</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '5';">5</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '6';">6</span>
                           </div>
                           <div class="row1">
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '7';">7</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '8';">8</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '9';">9</span>
                           </div>
                           <div class="row1">
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value.slice(0, -1);"><</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '0';">0</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value='';">Clear</span>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-10 offset-md-1">
                              <button type="submit" class="btn btn-block btn-primary btn-lg">LOGIN</button>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
   </div>
</div>