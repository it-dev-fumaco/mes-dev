@if($change_code['match'] == "false")
<div class="alert alert-warning text-center" role="alert">
  <span class="d-none"></span>
  <div class="container">
     <div class="alert-icon" style="color:black;">
      <i class="now-ui-icons travel_info" style="padding-right:5px;"></i><span style="font-size:13pt;"> <b>Notification Change Code :</b></span> 
            <span style="font-size:11pt;">Parent Item code was change to </span><span class="ml-1 font-weight-bold">{{$change_code['new_item']}}  </span>
     </div>
  </div>
  </div>
@endif
<div class="content p-0 m-0">
  <div class="row p-0 m-0">
    <div class="col-md-9 p-1">
      <table style="width: 100%; border-color: #D5D8DC;">
        <col style="width: 18%;">
        <col style="width: 24%;">
        <col style="width: 23%;">
        <col style="width: 20%;">
        <tr style="font-size: 9pt;">
          <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REFERENCE NO.</b></td>
          <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>CUSTOMER</b></td>
          <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROJECT</b></td>
          <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>DELIVERY DATE</b></td>
        </tr>
        <tr style="font-size: 10pt;">
          <td class="text-center" style="border: 1px solid #ABB2B9;"><span class="ref-no">{{ ($production->sales_order == '')? $production->material_request: $production->sales_order }}</span></td>
          <td class="text-center" style="border: 1px solid #ABB2B9;"><span class="cust">{{$production->customer}}</span></td>
          <td class="text-center" style="border: 1px solid #ABB2B9;"><span class="proj"> {{$production->project}}</span></td>
          <td class="text-center" style="border: 1px solid #ABB2B9;">
            <span class="del-date">{{($production->rescheduled_delivery_date == null)? $production->delivery_date: $production->rescheduled_delivery_date}}</span>
          </td>
        </tr>
        <tr style="font-size: 10pt;display:none;">
          <td style="border: 1px solid #ABB2B9; font-size: 9pt;" class="text-center"><b>ITEM DETAIL(S):</b></td>
          <td style="border: 1px solid #ABB2B9;" colspan="4"><span class="item-code font-weight-bold">{!! $bom->item_code !!} </span>- <span class="desc">{!! $bom->description !!}</span></td>
        </tr>
      </table>

      @php
        $end_date = ($materials['end_date'] == "") ? "" : $materials['end_date'];
        $start_date = ($materials['start_date'] == "") ? "Not Started" : $materials['start_date'];
        $plan_time = ($materials['planned_start_date'] == "") ? "" : \Carbon\Carbon::parse($materials['planned_start_date'])->format('M d, Y');
        if($materials['status'] == 'Completed'){
          $colorme= '#2ecc71';
          $stat= '';
          $hideme= '';
          $hide_ongoing_process='none';
        }elseif($materials['status'] == 'In Progress'){
          $stat= '';
          $colorme= '#f5b041 ';
          $hideme= '';
          $hide_ongoing_process='';
        }elseif($materials['status'] == ''){
          $stat= '';
          $colorme= 'white';
          $hideme= 'none';
          $hide_ongoing_process='none';
        }else{
          $stat= 'Pending';
          $colorme= '#d6dbdf';
          $hideme= '';
          $hide_ongoing_process='';
        }
        $displayme= ($materials['operation_id']  == "1") ? "none" : "none" ;
        $displayasssembly = ($materials['operation_id']  == "1") ? "none" : "" ;
        $prod = ($materials['production_order']  == "") ? "No Production Order" : $materials['production_order'] ;
        $prod_dash = ($materials['production_order']  == "") ? "" : " - " ;
        $parent_item_border= ($materials['operation_id']  == "3") ? "none" : "1px solid #abb2b9";
        $parent_radius= ($materials['operation_id']  == "3") ? "none" : ".2em" ;
      @endphp
      <div style="width: 100%; overflow: auto; min-height:740px;"  class="col-md-12 my-auto border">
        <div class="col-sm-12 my-auto" style="padding-top: 20px;margin-top: 30px;">
          <div class=" col-sm-12 my-auto" > 
            <ul class="tree ulclass text-center">
              <li class="liclass text-center">
                <div  class="row bread justify-content-center" style="border:{{$parent_item_border}};border-radius:{{$parent_radius}}; overflow:inherit;display:inline-block;margin:0 auto;position:relative;margin-bottom:35px;padding-left:5px;padding-right:5px;width:100%;">
                  <div class="row text-center bread justify-content-center" style="padding-top:5px;width:100%;">
                    <div class="col-md-12 bread">
                      <span class="text-center centerd prod-details-btn" style='text-align:center;font-size:18px;' data-jtno="{{ $materials['production_order'] }}"><b>{{ $materials['production_order'] }}{{ $prod_dash }}{{ $materials['item_code'] }}
                      @if($materials['status'] == 'Unknown Status')
                      <br>
                      <span class="badge badge-danger">{{ $materials['status'] }}</span>
                      @endif
                      @if($change_code['match'] == "false") <span style="font-size:14pt;">></span> <span style="font-size:16pt;">></span> <span style="font-size:19pt;">></span><span style="font-size:19pt;">{{$change_code['new_item']}}</span>
                      @endif</b> </span>
                  <span class="text-center"  style='text-align:center;font-size:12px;display:block;'>{!! $materials['description'] !!} </span>
                  <span class="text-center" style='text-align:center;font-size:12px;display:block;'>BOM : &nbsp; &nbsp;{!! $materials['bom_no'] !!} </span>

                  </div>
                </div>
                <div class="row bread breads row" style="margin-top:5px;display:{{$displayasssembly}}; text-align:initial !important;">
                  <div class="col-md-12 bread breads"  style="margin-bottom:5px;display:{{$displayasssembly}}; text-align:initial !important;width:100%;">
                    <ul class="breadcrumb-css bread breads row align-items-center justify-content-center" id="process-bcss" style="margin-bottom:5px;display:{{$displayasssembly}};  text-align:initial !important;">
                      @forelse($materials['process'] as $uli)
                        <li class="{{$uli['status']}} bread breads mt-3 mb-3" style=" text-align:initial !important;width:auto;"><a class="bread breads" style=" text-align:initial !important;padding-left:25px;margin-right:10px;width:auto;" href="javascript:void(0);">{{$uli['workstation']}} <span style="display:block; padding-right:20px;"> ({{$uli['process_name']}})</span><span style="display:block;"> {{$uli['completed_qty']}}/ {{$uli['required']}}</span></a></li>
                      @empty
                      <li class="bread" style="text-align:initial !important;"></li>
                      @endforelse
                    </ul>
                  </div>
                </div>
              </div>
              <ul class="ulclass">
                @foreach($boms as $idx => $item)
                <li class="liclass">
                  @php
                      $end_date = ($item['end_date'] == "") ? "" : $item['end_date'];
                      $start_date = ($item['start_date'] == "") ? "Not Started" : $item['start_date'];
                      $plan_time = ($item['planned_start_date'] == "") ? "" : \Carbon\Carbon::parse($item['planned_start_date'])->format('M d, Y');
                      if($item['status'] == 'Completed'){
                                $colorme= '#2ecc71';
                                $stat= '';
                              }elseif($item['status'] == 'In Progress'){
                                $stat= '';
                                $colorme= '#f5b041 ';
                              }elseif($item['status'] == 'Unknown Status'){
                                $stat= $item['status'];
                                $colorme= '';
                              }else{
                                $stat= 'Pending';
                                $colorme= '#d6dbdf';
                              }
                      $prod = ($item['production_order']  == "") ? "No Production Order" : $item['production_order'] ;
                      $prod_dash = ($item['production_order']  == "") ? "" : " - " ;
                  @endphp
                    <span class="hvrlink" style="margin-bottom: 30px;"><a class="aclass" href="#" style="background-color: {{ $colorme }}"><b><span style="font-size: 9pt;" data-jtno="{{ $item['production_order'] }}" class="prod-details-btn">{{ $item['production_order'] }}{{ $prod_dash }}{{ $item['item_code'] }} </span></b><br><i>{{ $item['parts_category'] }}</i><br>
                      <label style="float: right;color:black;"><b>Done:</b>&nbsp;{{ $item['produced_qty'] }}</label>
                      <label style="float: left;color:black;"><b>Qty:</b>&nbsp;{{ $item['qty_to_manufacture'] }} <span style="color: {{ ($item['available_stock'] > 0) ? 'green' : 'red' }}">(<b>{{ $item['available_stock'] }}</b>)</span></label>
                    </a>
                    </span>
                    <div class="details-pane">
                      <h5 class="title">{{ $item['item_code'] }}</h5>
                          <p class="desc" style="padding-top: 5px;">
                            <b>Description:</b> {!! $item['description'] !!}<br>  
                            <b>Planned Start Date:</b> {{ $plan_time }}<br>    
                            <b>Production Order : {{ $prod }}</b><br>          
                          </p>
                    </div>
                    <br>
                    <table style="padding-top: 10px; text-align: left; line-height: 10pt; font-size: 8pt;"  class="mx-auto w-auto info">
                      <tr>
                        <td><b>BOM: </b></td>
                        <td>{{ $item['bom_no'] }}</td>
                      </tr>
                      <tr style="display: {{ ($item['start_date'] == '') ? 'none' : '' }}">
                        <td><b>Start Date: </b></td>
                        <td>{{ $start_date }}</td>
                      </tr>
                      <tr style="display: {{ ($item['status'] == 'Completed') ? '': 'none' }}">
                        <td><b>End Date: </b></td>
                        <td>{{ $end_date }}</td>
                      </tr>
                      <tr style="display: {{ ($item['status'] == 'Completed') ? '': 'none' }}">
                        <td><b>Duration: </b></td>
                        <td>{{ $item['duration'] }}</td>
                      </tr>
                      <tr>
                        <td colspan="2" style="text-align: center; font-size: 9pt;">
                          @if( $stat == 'Pending')
                          <i><span>Pending</span><i>
                            @elseif ($stat == 'Unknown Status')
                            <span class="badge badge-danger"> {{ $stat }}</span>
                          @else
                            @forelse($item['current_load'] as $row)
                             {{ $row->workstation }} - {{ $row->process_name }} <br>
                            @empty
                           <i><span style="display: {{ ($item['status'] == 'Completed') ? 'none': '' }}">No On-going Process</span><i>
                            @endforelse
                          @endif
                        </td>
                      </tr>
                    </table>
                  @if(count($item['child_nodes']) > 0)
                    <ul class="ulclass">
                      @foreach($item['child_nodes'] as $child)
                        @php
                          $end_date = ($child['end_date'] == "") ? "" : $child['end_date'];
                          $start_date = ($child['start_date'] == "") ? "Not Started" : $child['start_date'];
                          $plan_time = ($child['planned_start_date'] == "") ? "" : Carbon\Carbon::parse($child['planned_start_date'])->format('M d, Y');
                          if($child['status'] == 'Completed'){
                                    $colorme= '#2ecc71';
                                    $stat= '';
                                  }elseif($child['status'] == 'In Progress'){
                                    $colorme= '#f5b041 ';
                                    $stat= '';
                                  }elseif($child['status'] == 'Unknown Status'){
                                $stat= $child['status'];
                                $colorme= '';
                                  }else{
                                    $colorme= '#d6dbdf';
                                    $stat= 'Pending';
                                  }
                          $prod = ($child['production_order']  == "") ? "No Production Order" : $child['production_order'] ;
                          $prod_dash = ($child['production_order']  == "") ? "" : " - " ;
                        @endphp
                        <li class="liclass">
                            <span class="hvrlink" style="margin-bottom: 30px;"><a class="aclass" href="#" style="background-color: {{ $colorme }}"><b><span style="font-size: 9pt;" data-jtno="{{ $child['production_order'] }}" class="prod-details-btn">{{ $child['production_order'] }}{{ $prod_dash }}{{ $child['item_code'] }} </span></b><br><i>{{ $child['parts_category'] }}</i><br>
                              <label style="float: right;color:black;"><b>Done:</b>&nbsp;{{ $child['produced_qty'] }}</label>
                              <label style="float: left;color:black;"><b>Qty:</b>&nbsp;{{ $child['qty_to_manufacture'] }} <span style="color: {{ ($child['available_stock'] > 0) ? 'green' : 'red' }}">(<b>{{ $child['available_stock'] }}</b>)</span></label>
                            </a>
                            </span>
                            <div class="details-pane">
                              <h5 class="title">{{ $child['item_code'] }}</h5>
                                  <p class="desc" style="padding-top: 5px;">
                                    <b>Description:</b> {!! $child['description'] !!}<br>  
                                    <b>Planned Start Date:</b> {{ $plan_time }}<br>    
                                    <b>Production Order : {{ $prod }}</b><br>          
                                  </p>
                            </div>
                            <br>
                            
                            <table style="padding-top: 10px; text-align: left; line-height: 10pt; font-size: 8pt;"  class="mx-auto w-auto info">
                              <tr>
                                <td><b>BOM: </b></td>
                                <td>{{ $child['bom_no'] }}</td>
                              </tr>
                              <tr style="display: {{ ($child['start_date'] == '') ? 'none' : '' }}">
                                <td><b>Start Date: </b></td>
                                <td>{{ $start_date }}</td>
                              </tr>
                              <tr style="display: {{ ($child['status'] == 'Completed') ? '': 'none' }}">
                                <td><b>End Date: </b></td>
                                <td>{{ $end_date }}</td>
                              </tr>
                              <tr style="display: {{ ($child['status'] == 'Completed') ? '': 'none' }}">
                                <td><b>Duration: </b></td>
                                <td>{{ $child['duration'] }}</td>
                              </tr>
                              <tr>
                                <td colspan="2" style="text-align: center; font-size: 9pt;">
                                  @if( $stat == 'Pending')
                                  <i><span>Pending</span><i>
                                    @elseif ($stat == 'Unknown Status')
                            <span class="badge badge-danger"> {{ $stat }}</span>
                                  @else
                                    @forelse($child['current_load'] as $row)
                                     {{ $row->workstation }} - {{ $row->process_name }} <br>
                                    @empty
                                   <i><span style="display: {{ ($child['status'] == 'Completed') ? 'none': '' }}">No On-going Process</span><i>
                                    @endforelse
                                  @endif
                                </td>
                              </tr>
                            </table>
                          @if(count($child['child_nodes']) > 0)
                            <ul class="ulclass">
                              @foreach($child['child_nodes'] as $child1)
                                 @php
                                  $end_date = ($child1['end_date'] == "") ? "" : $child1['end_date'];
                                  $start_date = ($child1['start_date'] == "") ? "Not Started" : $child1['start_date'];
                                  $plan_time = ($child1['planned_start_date'] == "") ? "" : Carbon\Carbon::parse($child1['planned_start_date'])->format('M d, Y');
                                  if($child1['status'] == 'Completed'){
                                            $colorme= '#2ecc71';
                                            $stat= '';
                                          }elseif($child1['status'] == 'In Progress'){
                                            $colorme= '#f5b041 ';
                                            $stat= '';
                                          }elseif($child1['status'] == 'Unknown Status'){
                                $stat= $child1['status'];
                                $colorme= '';
                                          }else{
                                            $colorme= '#d6dbdf';
                                            $stat= 'Pending';
                                          }
                                  $prod = ($child1['production_order']  == "") ? "No Production Order" : $child1['production_order'] ;
                                  $prod_dash = ($child1['production_order']  == "") ? "" : " - " ;
                                @endphp
                                <li class="liclass">
                                  <span class="hvrlink" style="margin-bottom: 30px;"><a class="aclass" href="#" style="background-color: {{ $colorme }}"><b><span style="font-size: 9pt;" data-jtno="{{ $child1['production_order'] }}" class="prod-details-btn">{{ $child1['production_order'] }}{{ $prod_dash }}{{ $child1['item_code'] }} </span></b><br><i>{{ $child1['parts_category'] }}</i><br>
                                  <label style="float: right;color:black;"><b>Done:</b>&nbsp;{{ $child1['produced_qty'] }}</label>
                                  <label style="float: left;color:black;"><b>Qty:</b>&nbsp;{{ $child1['qty_to_manufacture'] }} <span style="color: {{ ($child1['available_stock'] > 0) ? 'green' : 'red' }}">(<b>{{ $child1['available_stock'] }}</b>)</span></label>
                                </a>
                                </span>
                                <div class="details-pane">
                                  <h5 class="title">{{ $child1['item_code'] }}</h5>
                                      <p class="desc" style="padding-top: 5px;">
                                        <b>Description:</b> {!! $child1['description'] !!}<br>  
                                        <b>Planned Start Date:</b> {{ $plan_time }}<br>    
                                        <b>Production Order : {{ $prod }}</b><br>          
                                      </p>
                                </div>
                                <br>

                                  <table style="padding-top: 10px; text-align: left; line-height: 10pt; font-size: 8pt;"  class="mx-auto w-auto info">
                                    <tr>
                                      <td><b>BOM: </b></td>
                                      <td>{{ $child1['bom_no'] }}</td>
                                    </tr>
                                    <tr style="display: {{ ($child1['start_date'] == '') ? 'none' : '' }}">
                                      <td><b>Start Date: </b></td>
                                      <td>{{ $start_date }}</td>
                                    </tr>
                                    <tr style="display: {{ ($child1['status'] == 'Completed') ? '': 'none' }}">
                                      <td><b>End Date: </b></td>
                                      <td>{{ $end_date }}</td>
                                    </tr>
                                    <tr style="display: {{ ($child1['status'] == 'Completed') ? '': 'none' }}">
                                      <td><b>Duration: </b></td>
                                      <td>{{ $child1['duration'] }}</td>
                                    </tr>
                                    <tr>
                                      <td colspan="2" style="text-align: center; font-size: 9pt;">
                                        @if( $stat == 'Pending')
                                        <i><span>Pending</span><i>
                                          @elseif ($stat == 'Unknown Status')
                            <span class="badge badge-danger"> {{ $stat }}</span>
                                        @else
                                          @forelse($child1['current_load'] as $row)
                                           {{ $row->workstation }} - {{ $row->process_name }} <br>
                                          @empty
                                         <i><span style="display: {{ ($child1['status'] == 'Completed') ? 'none': '' }}">No On-going Process</span><i>
                                          @endforelse
                                        @endif
                                      </td>
                                    </tr>
                                  </table>
                                  @if(count($child1['child_nodes']) > 0)
                                  <ul class="ulclass">
                                      @foreach($child1['child_nodes'] as $child2)
                                      @php
                                        $end_date = ($child2['end_date'] == "") ? "" : $child2['end_date'];
                                        $start_date = ($child2['start_date'] == "") ? "Not Started" : $child2['start_date'];
                                        $plan_time = ($child2['planned_start_date'] == "") ? "" : Carbon\Carbon::parse($child2['planned_start_date'])->format('M d, Y');
                                        if($child2['status'] == 'Completed'){
                                                  $colorme= '#2ecc71';
                                                  $stat= '';
                                                }elseif($child2['status'] == 'In Progress'){
                                                  $colorme= '#f5b041 ';
                                                  $stat= '';
                                                }elseif($child2['status'] == 'Unknown Status'){
                                $stat= $child2['status'];
                                $colorme= '';
                                                }else{
                                                  $colorme= '#d6dbdf';
                                                  $stat= 'Pending';
                                                }
                                        $prod = ($child2['production_order']  == "") ? "No Production Order" : $child2['production_order'] ;
                                        $prod_dash = ($child2['production_order']  == "") ? "" : " - " ;
                                      @endphp
                                      <li class="liclass">
                                        <span class="hvrlink" style="margin-bottom: 30px;"><a class="aclass" href="#" style="background-color: {{ $colorme }}"><b><span style="font-size: 9pt;" data-jtno="{{ $child2['production_order'] }}" class="prod-details-btn">{{ $child2['production_order'] }}{{ $prod_dash }}{{ $child2['item_code'] }} </span></b><br><i>{{ $child2['parts_category'] }}</i><br>
                                          <label style="float: right;color:black;"><b>Done:</b>&nbsp;{{ $child2['produced_qty'] }}</label>
                                          <label style="float: left;color:black;"><b>Qty:</b>&nbsp;{{ $child2['qty_to_manufacture'] }} <span style="color: {{ ($child2['available_stock'] > 0) ? 'green' : 'red' }}">(<b>{{ $child2['available_stock'] }}</b>)</span></label>
                                        </a>
                                        </span>
                                        <div class="details-pane">
                                          <h5 class="title">{{ $child2['item_code'] }}</h5>
                                              <p class="desc" style="padding-top: 5px;">
                                                <b>Description:</b> {!! $child2['description'] !!}<br>  
                                                <b>Planned Start Date:</b> {{ $plan_time }}<br>    
                                                <b>Production Order : {{ $prod }}</b><br>          
                                              </p>
                                        </div>
                                        <br>

                                        <table style="padding-top: 10px; text-align: left; line-height: 8pt; font-size: 10pt;"  class="mx-auto w-auto info">
                                          <tr>
                                            <td><b>BOM: </b></td>
                                            <td>{{ $child2['bom_no'] }}</td>
                                          </tr>
                                          <tr style="display: {{ ($child2['start_date'] == '') ? 'none' : '' }}">
                                            <td><b>Start Date: </b></td>
                                            <td>{{ $start_date }}</td>
                                          </tr>
                                          <tr style="display: {{ ($child2['status'] == 'Completed') ? '': 'none' }}">
                                            <td><b>End Date: </b></td>
                                            <td>{{ $end_date }}</td>
                                          </tr>
                                          <tr style="display: {{ ($child2['status'] == 'Completed') ? '': 'none' }}">
                                            <td><b>Duration: </b></td>
                                            <td>{{ $child2['duration'] }}</td>
                                          </tr>
                                          <tr>
                                            <td colspan="2" style="text-align: center; font-size: 9pt;">
                                              @if( $stat == 'Pending')
                                              <i><span>Pending</span><i>
                                                @elseif ($stat == 'Unknown Status')
                            <span class="badge badge-danger"> {{ $stat }}</span>
                                              @else
                                                @forelse($child2['current_load'] as $row)
                                                 {{ $row->workstation }} - {{ $row->process_name }} <br>
                                                @empty
                                               <i><span style="display: {{ ($child2['status'] == 'Completed') ? 'none': '' }}">No On-going Process</span><i>
                                                @endforelse
                                              @endif
                                            </td>
                                          </tr>
                                        </table>
                                      </li>
                                    @endforeach
                                  </ul>
                                @endif
                              </li>
                            @endforeach
                          </ul>
                        @endif
                      </li>
                    @endforeach
                  </ul>
                @endif
              </li>
          @endforeach
          </ul>
        </li>
      </ul>
      </div>
    </div>
  </div>  

     
  </div>
  <div class="col-md-3">
    <div class="card" style="background-color: #0277BD;" >
        <div class="card-body pb-0">
					<div class="row">
						<div class="col-md-12 text-center" style="margin-top: -10px;">
							<h5 class="text-white" style="font-size: 12pt; margin-bottom: 5px;"><b>STATUS DETAILS</b></h5>
						</div>
					</div>
          @php
          if($timeline['fab_stat'] == "Completed"){
            $display_fab_end="";
            $display_fab="";
            $stat="is-completed";
            $end_time_fab = $timeline['fab_max'];
            $fab_medisplay="none";
            $fab_status_label="Completed";
          }elseif($timeline['fab_stat'] == "In Progress"){
            $display_fab_end="none";
            $display_fab="";
            $stat="is-current";
            $end_time_fab = "- On Going";
            $fab_medisplay="none";
            $fab_status_label="In Progress";
          }else{
            $display_fab_end="none";
            $display_fab="none";
            $stat="is-future";
            $end_time_fab = "-";
            $fab_status_label="Not Started";
          }

          if($timeline['assem_stat'] == "Completed"){
            $display_assem_end="";
            $display_assem="";
            $stat_assem="is-completed";
            $end_time_assem = $timeline['assem_max'];
            $assem_status_label="Completed";

          }elseif($timeline['assem_stat'] == "In Progress"){
            $display_assem_end="none";
            $display_assem="";
            $stat_assem="is-current";
            $end_time_assem = "- On Going";
            $assem_status_label="In Progress";

          }else{
            $display_assem_end="none";
            $display_assem="none";
            $stat_assem="is-future";
            $end_time_assem = "-";
            $assem_status_label="Not Started";

          }

          if($timeline['pain_stat'] == "Completed"){
            $display_pain_end="";
            $display_pain="";
            $stat_pain="is-completed";
            $end_time_pain = $timeline['pain_max'];
            $pain_status_label="Completed";
          }elseif($timeline['pain_stat'] == "In Progress"){
            $display_pain_end="none";
            $display_pain="";
            $stat_pain="is-current";
            $end_time_pain = "- On Going";
            $pain_status_label="In Progress";
          
          }else{
            $display_pain_end="none";
            $display_pain="none";
            $stat_pain="is-future";
            $end_time_pain ="-";
            $pain_status_label="Not Started";

          }
          
          if($materials['operation_id'] == "3"){
            $required = $materials['qty'];
            $bal = $materials['qty'] - $materials['produced_qty'];
            $produced = $materials['produced_qty'];
            $feed = $materials['feedback_qty'];
            $uom= $materials['uom'];
          }else{
            $required = '0';
            $bal = '0';
            $feed = '0';
            $produced = '0';
            $uom= '';

          }
          if($timeline['fab_stat'] == "Completed" && $timeline['pain_stat'] == "Completed" && $timeline['assem_stat'] == "Completed"){
              $overall_stat='';
          }else{
            $overall_stat='none';
          }

          if($timeline['fab_stat'] == "Completed" && $timeline['pain_stat'] == "Completed" && $timeline['assem_stat'] == "Completed"){
              $over_all_stat_h5="none";
              $over_all_stat="Completed";
              $over_all_stat_display="";
            }elseif($timeline['fab_stat'] == "In Progress" || $timeline['pain_stat'] == "In Progress" || $timeline['assem_stat'] == "In Progress"){
              $over_all_stat_h5="";
              $over_all_stat="In Progress";
              $over_all_stat_display="none";
            }else{
              $over_all_stat_h5="none";
              $over_all_stat="none";
              $over_all_stat_display="none";
            }


          
          @endphp
      		<div class="row" style="background-color: #ffffff; padding-top: 9px;min-height:800px;">
            <div class="col-md-12 text-center" style="margin-top: -1  0px;overflow:auto">
                
                <div class="card-body p-0 pb-1 text-center" style=" min-height: 100px; margin-top:8px;">
                <div class="row">
                  <div class="col-md-12 pt-1 m-0">
                    <table style="width: 100%;" id="pending-inv-transaction">
                      <col style="width: 33.33%;">
                      <col style="width: 33.33%;">
                      <col style="width: 33.33%;">
                      <col style="width: 33.33%;">
                      <tr>
                        <td class="align-top">
                          <span class="d-block font-weight-bold text-uppercase" style="font-size: 0.6vw;">Total Qty</span>
                          <span class="d-block font-weight-bold" style="font-size:1vw;">{{$required}}</span>
                          <span class="d-block" style="font-size:0.5vw;">{{$materials['uom']}}</span>
                        </td>
                        <td class="align-top">
                          <span class="d-block font-weight-bold text-uppercase" style="font-size: 0.6vw;">Bal. Qty</span>
                          <span class="d-block font-weight-bold" style="font-size:1vw;">{{$bal}}</span>
                          <span class="d-block" style="font-size:0.5vw;">{{$materials['uom']}}</span>
                        </td>
                        <td class="align-top">
                          <span class="d-block font-weight-bold text-uppercase" style="font-size: 0.6vw;">Del. Qty</span>
                          <span class="d-block font-weight-bold" style="font-size:1vw;">{{$feed}}</span>
                          <span class="d-block" style="font-size:0.5vw;">{{$materials['uom']}}</span>
                        </td>
                        
                      </tr>
                  </table>
                  </div>
                </div>
              </div>
                <ul class="timeline text-left" style="margin-top:35px">
                    <li class="timeline-milestone {{$stat}} timeline-start">
                      <div class="timeline-action">
                        <h2 class="title">Fabrication</h2>
                        {{--<span style="display:block; font-size:1vw;font-weight:bold;"> {{ $timeline['fab_produced']}} /  {{ $timeline['fab_required']}}</span>--}}
                        <p style="font-size:0.7vw;"><span class="badge badge-{{$timeline['fab_badge']}}">{{ $fab_status_label }}</span></p>
                        <span style="display:{{ $display_fab_end}};font-size:0.6vw;"><b>Total Duration:</b> {{ $timeline['fab_duration']}}</span>
                        <div class="content text-left" style="padding-top:10px;">
                        <p style="font-size:0.6vw;display:{{ $display_fab}};"><b>Start Time:</b> <span>{{ $timeline['fab_min']}}</span> </p>
                        <p style="display:{{ $display_fab_end}}; font-size:0.6vw;margin-top:-15px;"><b>End Time:</b> <span>{{ $end_time_fab }}</span></p>
                        </div>
                      </div>
                    </li>
                  
                    <li class="timeline-milestone {{$stat_pain}}" >
                      <div class="timeline-action" >
                        <h2 class="title">Painting</h2>
                        {{--<span style="display:block; font-size:1vw;font-weight:bold;">{{ $timeline['fab_produced']}} /  {{ $timeline['fab_required']}}</span>--}}
                        {{-- <p style="font-size:0.9vw;"><i>{{ $pain_status_label }}</i></p> --}}
                        <p style="font-size:0.7vw;"><span class="badge badge-{{$timeline['pain_badge']}}">{{ $pain_status_label }}</span></p>
                        <span style="display:{{ $display_pain_end }};font-size:0.6vw;"><b>Total Duration:</b>  {{ $timeline['pain_duration']}}</span>
                        <div class="content text-left" style="padding-top:10px;">
                        <p style="display:{{ $display_pain }}; font-size:0.6vw;"><b>Start Time:</b> <span>{{ $timeline['pain_min']}}</span> </p>
                        <p style="display:{{ $display_pain_end }}; font-size:0.6vw;margin-top:-15px;"><b>End Time:</b> <span>{{ $end_time_pain }}</span></p>
                        

                        </div>
                      </div>
                    </li>
                    <li class="timeline-milestone {{$stat_assem}} timeline-end" >
                      <div class="timeline-action" >
                        <h2 class="title">Assembly</h2>
                        <span style="display:block; font-size:1vw;font-weight:bold;"> {{$produced}} /  {{$required}}</span>
                        {{-- <p style="font-size:0.8vw;"><i>{{ $assem_status_label }}</i></p> --}}
                        <p style="font-size:0.7vw;"><span class="badge badge-{{$timeline['assem_badge']}}">{{ $assem_status_label }}</span></p>

                        <span style="display:{{ $display_assem_end}};font-size:0.6vw;"><b>Total Duration:</b>  {{ $timeline['assem_duration']}}</span>
                        <div class="content text-left" style="padding-top:10px;">
                        <p style="display:{{ $display_assem }}; font-size:0.6vw;"><b>Start Time:</b> <span>{{ $timeline['assem_min']}}</span> </p>
                        <p style="display:{{ $display_assem_end }}; font-size:0.6vw;margin-top:-15px;"><b>End Time:</b> <span>{{ $end_time_assem }}</span></p>
                        </div>
                      </div>
                    </li>
                  </ul>
                  <div class="row text-left" style="display:{{$overall_stat}}">
                  <h6 style="margin-left:20px;padding-top:-10px;color: #00637d;font-size: 1.2vw;" class="text-left;">Completed!</h6>
                  </div>
                <div class="row">
                 <div class="col-md-12 text-center" style="margin-top:20px;">
                 <h5 style="display:{{$over_all_stat_h5}};font-weight:bold;">{{$over_all_stat}}</h5>
                 <div style="display:{{$over_all_stat_display}}">
                <h5><b>Duration</b></h5>
                <h5 style="font-size:18px;font-weight:bold;"><b>{{$timeline['duration']}}</b></h5>
                </div>
                </div>
                
                
              </div>
                
              </div>
            </div>
					</div>
				</div>
      </div>
    </div>
  </div>
</div>
<style type="text/css">
#container {
    height: 700px;
    width: 300px;
    position: absolute;
    top: 50%;
    left:50%;
    -webkit-transform:translate(-50%,-50%);
    transform:translate(-50%,-50%);
}
.content1 {
    height: auto;
    width: 100%;
    /* position: absolute; */
    top: 50%;
    right:50%;
    left:50%;
    -webkit-transform:translate(-50%,-10%);
    transform:translate(-50%,-10%);
}
.timeline {
  list-style: none;
  margin: 25px 0 22px;
  padding: 0;
  position: relative;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}
.timeline:after {
  border: 6px solid;
  border-top-width: 13px;
  border-color: #00637d transparent transparent transparent;
  content: "";
  display: block;
  position: absolute;
  bottom: -19px;
  left: 15px;
}

.timeline-horizontal:after {
  border-top-width: 6px;
  border-left-width: 13px;
  border-color: transparent transparent transparent #00637d;
  top: 15px;
  right: 0;
  bottom: auto;
  left: auto;
}
.timeline-horizontal .timeline-milestone {
  border-top: 2px solid #00637d;
  display: inline;
  float: left;
  margin: 20px 0 0 0;
  padding: 40px 0 0 0;
}
.timeline-horizontal .timeline-milestone:before {
  top: -17px;
  left: auto;
}
.timeline-horizontal .timeline-milestone.is-completed:after {
  top: -17px;
  left: 0;
}

.timeline-milestone {
  border-left: 2px solid #00637d;
  margin: 0 0 0 20px;
  padding: 0 0 5px 25px;
  position: relative;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}
.timeline-milestone:before {
  border: 7px solid #00637d;
  border-radius: 50%;
  content: "";
  display: block;
  position: absolute;
  left: -17px;
  width: 32px;
  height: 32px;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}
.timeline-milestone.is-completed:before {
  background-color: #00637d;
}
.timeline-milestone.is-completed:after {
  color: #FFF;
  font-weight: bold;
  content: "\2713";
  display: block;
  line-height: 32px;
  position: absolute;
  top: 0;
  left: -17px;
  text-align: center;
  width: 32px;
  height: 32px;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}
.timeline-milestone.is-current:before {
  background-color: #EEE;
}
.timeline-milestone.is-future:before {
  background-color: #8DACB8;
  border: 0;
}
.timeline-milestone.is-future .timeline-action .title {
  color: #8DACB8;
}

.timeline-action {
  background-color: #FFF;
  padding: 12px 10px 12px 20px;
  position: relative;
  top: -15px;
}
.timeline-action.is-expandable .title {
  cursor: pointer;
  position: relative;
}
.timeline-action.is-expandable .title:focus {
  outline: 0;
  text-decoration: underline;
}
.timeline-action.is-expandable .title:after {
  border: 6px solid #666;
  border-color: transparent transparent transparent #666;
  content: "";
  display: block;
  position: absolute;
  top: 6px;
  right: 0;
}
.timeline-action.is-expandable .content {
  display: none;
}
.timeline-action.is-expandable.is-expanded .title:after {
  border-color: #666 transparent transparent transparent;
  top: 10px;
  right: 5px;
}
.timeline-action.is-expandable.is-expanded .content {
  display: block;
}
.timeline-action .title, .timeline-action .content {
  word-wrap: break-word;
}
.timeline-action .title {
  color: #00637d;
  font-size: 0.8vw;
  margin: 0;
}
.timeline-action .date {
  display: block;
  font-size: 14px;
  margin-bottom: 15px;
}
.timeline-action .content {
  font-size: 14px;
}

.file-list {
  line-height: 1.4;
  list-style: none;
  padding-left: 10px;
}





</style>        
<style type="text/css">


  /** detail panel **/
  .dot {
    height: 12px;
    width: 12px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
  }
.details-pane {
display: none;
  color: #414141;
  background: #f1f1f1;
  border: 1px solid #a9a9a9;
  z-index: 1;
  width: 300px;
  padding: 6px 8px;
  text-align: left;
  -webkit-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
  -moz-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
  box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
  white-space: normal;
  position: absolute;
  top:0;
  left: 0;
  right: 0;
  margin: auto;
  margin-top: 100px;
}

.details-pane h5 {
  font-size: 1.5em;
  line-height: 1.1em;
  margin-bottom: 4px;
  line-height: 15px;
}

.details-pane h5 span {
  font-size: 0.40em;
  font-style: italic;
  color: #555;
  padding-left: 15px;
    line-height: 15px;

}

.details-pane .desc {
  font-size: 1.0em;
  margin-bottom: 6px;
    line-height: 20px;
    height: auto;

}

/** hover styles **/
span.hvrlink:hover + .details-pane {
  display: block;
}
a.hvrlink:hover + .details-pane {
  display: block;
}
.details-pane:hover {
  display: block;
}
  .info{
    margin-bottom: 38px;
  }
</style>
<style type="text/css">
  
.breads{
  text-align:initial !important;
}
/* It's supposed to look like a tree diagram */
.tree .ulclass:not(.bread), .tree .liclass:not(.bread){
    list-style: none;
    margin: 0;
    padding: 0;
    position: relative;
}

.tree:not(.bread) {
    margin: 0;
    /* text-align: center; */
}
.tree:not(.bread), .tree .ulclass:not(.bread) {
    display: table;
}
.tree .ulclass:not(.bread) {
  width: 100%;
  text-align:center;
}

    .tree .liclass:not(.bread) {
        display: table-cell;
        padding: 2em 0;
        vertical-align: top;
    }
        /* _________ */
        .tree .liclass:not(.bread):before {
            outline: solid 1px #666;
            content: "";
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
        }
        .tree .liclass:not(.bread):first-child:before {left: 50%;}
        .tree .liclass:not(.bread):last-child:before {right: 50%;}

        .tree .aclass:not(.bread){
            border: solid .1em #666;
            border-radius: .2em;
            display: inline-block;
            margin: 0 .2em .5em;
            padding: .2em .5em;
            position: relative;
            color: black;
            min-width: 200px;;
        }
            /* | */
            .tree a:not(.bread):before{
                outline: solid 1px black;
                color: black;
                content: "";
                height: 1.8em;
                left: 50%;
                position: absolute;
              }
            .tree .ulclass:not(.bread):before{
                outline: solid 1px black;
                color: black;
                content: "";
                height: 1em;
                left: 50%;
                padding-top: 2.5em;
                position: absolute;
              }
            .tree .ulclass:not(.bread):before {
                top: -2.5em;
            }
            .tree a:not(.bread):before{
                top: -2em;
            }

/* The root node doesn't connect upwards */
.tree > li {margin-top: 0;}
    .tree > li:not(.bread):before,
    .tree > li:not(.bread):after,
    .tree > li > a:not(.bread):before,
    .tree > li > a:not(.bread):before {
      outline: none;
    }
.breadcrumb-css {
font-size: 8pt;
font-weight: bold;
padding: 0;
background: transparent;
list-style: none;
overflow: hidden;
margin-top: 3px;
margin-bottom: 3px;
width: 100%;
border-radius: 4px;
}

.breadcrumb-css>li {
display: table-cell;
vertical-align: top;
width: 0.8%;
}

.breadcrumb-css>li+li:before {
padding: 0;
}

.breadcrumb-css li a {
color: white;
text-decoration: none;
padding: 10px 0 10px 5px;
position: relative;
display: inline-block;
width: calc( 100% - 10px );
background-color: hsla(0, 0%, 83%, 1);
text-align: center;
text-transform: capitalize;
}

.breadcrumb-css li.completed a {
background: brown;
background: hsla(153, 57%, 51%, 1);
}

.breadcrumb-css li.completed a:after {
border-left: 30px solid hsla(153, 57%, 51%, 1);
}

.breadcrumb-css li.active a {
background: #ffc107;
}

.breadcrumb-css li.active a:after {
border-left: 30px solid #ffc107;
}

.breadcrumb-css li:first-child a {
padding-left: 1px;
}

.breadcrumb-css li:last-of-type a {
width: calc( 100% - 38px );
}

.breadcrumb-css li a:before {
content: " ";
display: block;
width: 0;
height: 0;
border-top: 50px solid transparent;
border-bottom: 50px solid transparent;
border-left: 30px solid white;
position: absolute;
top: 50%;
margin-top: -50px;
margin-left: 1px;
left: 100%;
z-index: 1;
}

.breadcrumb-css li a:after {
content: " ";
display: block;
width: 0;
height: 0;
border-top: 50px solid transparent;
border-bottom: 50px solid transparent;
border-left: 30px solid hsla(0, 0%, 83%, 1);
position: absolute;
top: 50%;
margin-top: -50px;
left: 100%;
z-index: 2;
}
   
</style>
<script type="text/javascript">
      function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 3000,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }
</script>

<script>
  $(document).ready(function(){
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
    
    $(document).on('click', '#add-shift-button', function(){
      $('#add-shift-modal').modal('show');
    });
    
    $('.breads').css('text-align','initial');
    setInterval(updateClock, 1000);
    function updateClock(){
      var currentTime = new Date();
      var currentHours = currentTime.getHours();
      var currentMinutes = currentTime.getMinutes();
      var currentSeconds = currentTime.getSeconds();
      // Pad the minutes and seconds with leading zeros, if required
      currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
      currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
      // Choose either "AM" or "PM" as appropriate
      var timeOfDay = (currentHours < 12) ? "AM" : "PM";
      // Convert the hours component to 12-hour format if needed
      currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;
      // Convert an hours component of "0" to "12"
      currentHours = (currentHours === 0) ? 12 : currentHours;
      currentHours = (currentHours < 10 ? "0" : "") + currentHours;
      // Compose the string for display
      var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;

      $("#current-time").html(currentTimeString);
    }
  });
</script>