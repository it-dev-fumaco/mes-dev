<div class="row text-center">
   <div class="col-md-12" style="text-align: center;">
        <h4 class="title">Machine</h4>
   </div>
            <div class="table-responsive" style="height: 200px;position: relative;">
               <table class="table">
                  <thead class="text-primary" style="font-size: 11pt !important; background-color: #204166;">
                     <th class="text-center"><b>Machine Code</b></th>
                     <th class="text-center"><b>Machine Name</b></th>
                     <th class="text-center"><b>Machine Status</b></th>
                  </thead>
                  <tbody style="font-size: 11pt;">
                     @foreach($machine_available as $row)
                     <tr>
                        <td class="text-center">{{ $row->machine_code }}</td>
                        <td class="text-center">{{ $row->machine_name }}</td>
                        <td class="text-center"><b>      
                           @if($row->status == "On-going Maintenance")
                              <label class="card-title" style="margin-right: 50px;">
                              <span class="dot text-blink" style="background-color:#d35400;"></span> {{ $row->status }}
                              </label>
                              @else
                              <label class="card-title" style="margin-right: 50px;">
                              <span class="dot" style="background-color: {{ $row->status == 'Available' ? '#28B463' : '#717D7E' }};"></span> {{ $row->status }}
                              </label>
                              @endif
                        </b></td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
         </div>