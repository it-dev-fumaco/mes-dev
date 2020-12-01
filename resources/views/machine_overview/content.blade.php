 @foreach($datas as $row)

            <div class="col-md-12"><h3 class="title text-center" style="text-align: center;color: #de6332;">{{ $row['production_line'] }}</h3>
               </div>
               
               <div class="row" style="margin-top: -25px;">
                  @foreach( $row['w_to_m'] as $rows)
                    <div  class="p-3 h-25 d-inline-block" style="width: 20%;height: 10%;" >
                                <div class="card">
                                    @php
                                    $img = $rows['image_file'] ? $rows['image_file']: '/storage/machine/machine_default.png'
                                    @endphp
                                      <img class="card-img-top image_click_class" src="{{ asset($img) }}" alt="Card image cap" style="width: 180px;height: 80px;object-fit: cover;" data-idko="{{ $rows['machine_code'] }}" data-imagefile="{{ $rows['image_file'] }} background-size: cover;">
                                      <div class="card-body machine_details_class" data-machinecode="{{ $rows['machine_code'] }}" data-workstation="{{ $rows['workstation'] }}" data-quetime="{{ $rows['avg'] }}" data-machinename="{{ $rows['machine_name'] }}" data-percetage="{{ $rows['percentage'] }}" data-completedqty="{{ $rows['completed_qty'] }}" data-acceptedqty="{{ $rows['accepted_qty'] }}">
                                       <div class="col-md-8" style="float: left;">
                                          <h1 class="card-title" style="font-size: 15pt; margin-top: -10px;"><b>{{ $rows['machine_code'] }}</b></h1>
                                       </div>
                                       <div class="col-md-4" style="float: right; margin-top: -10px;">
                                          <span style="font-size: 10pt;float: right;"><b>JT:</b>{{ $rows['timesheet'] }}</span> 
                                       </div>
         
                                        <div class="table-responsive" style="height: 100%;position: relative;">
                                          <div class="col-md-12 justify-content-center" style="margin: 0 auto;" >
                                                <div class="single-chart text-center" style="margin: 0 auto;">
                                                   <svg viewBox="0 0 36 36" class="circular-chart blue" style="margin: 0 auto;">
                                                   <path class="circle-bg"
                                                                       d="M18 2.0845
                                                                         a 15.9155 15.9155 0 0 1 0 31.831
                                                                         a 15.9155 15.9155 0 0 1 0 -31.831"
                                                                     />
                                                   <path class="circle"
                                                                       stroke-dasharray="{{ $rows['percentage'] > 101 ? '100': $rows['percentage'] }}, 100"
                                                                       d="M18 2.0845
                                                                         a 
                                                                         15.9155 15.9155 0 0 1 0 31.831
                                                                         a 15.9155 15.9155 0 0 1 0 -31.831"
                                                                     />
                                                   <text x="18" y="20.35" class="percentage" style="font-weight: bold;">{{ $rows['percentage'] > 101 ? '100': $rows['percentage'] }}%</text>
                                                   </svg>
                                              </div>
                                             </div>
                                                <div class="text-center">
                                                <span style="font-size: 10pt;"><b>Workstation:</b></span> <br>
                                                <span style="font-size: 10pt;">{{ $rows['workstation'] }}</span> 
                                                <br>
                                                <span style="font-size: 8pt;"><b>Queuing Time:</b></span><br>
                                                <span style="font-size: 7pt;">{{ $rows['avg'] }}</span> 
                                                <br>
                                              </div>
                                                
                                             
                                             <h6 class="text-center"> <b>{{ $rows['completed_qty'] }}/ {{ $rows['accepted_qty'] == 0 ? $rows['completed_qty']:$rows['accepted_qty'] }} Qty</b></h6>
                              </div>
                           </div>
                           <div style="margin-top: -20px;" class="text-center">
                                @if($rows['status'] == "On-going Maintenance")
                                  <label style="font-size: 8pt;padding-right: 3%;">
                                  <span class="dotsmall text-blink" style="background-color:#d35400;"></span> {{ $rows['status'] }}
                                  </label>
                                @else
                                  <label style="font-size: 8pt;padding-right: 3%;">
                                    <span class="dotsmall" style="background-color: {{ $rows['status'] == 'Available' ? '#28B463' : '#717D7E' }};"></span>{{ $rows['status'] }}
                                  </label>
                                @endif
                            </div>
                        </div>
                     </div>
                  @endforeach
               </div>
         @endforeach






<style type="text/css">
   .circular-chart {
  display: block;
  margin: 10px auto;
  max-width: 80%;
  max-height: 250px;
}

.circle {
  stroke: #4CC790;
  fill: none;
  stroke-width: 2.8;
  stroke-linecap: round;
  animation: progress 3s ease-out forwards;
}

@keyframes progress {
  0% {
    stroke-dasharray: 0 100;
  }
}
</style>
@section('script')
<script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<script>
   $(document).ready(function(){
      $(function(){
    // Enables popover
    $("[data-toggle=popover]").popover();
      });
   });
</script>
@endsection

