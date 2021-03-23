
@php
    $limit = ceil(count($prod_details) / 2)
@endphp
    @if ($limit > 1)
        <div class="row p-0 m-0">
            @foreach ($prod_details->chunk($limit) as $res)
                <div class="col-md-6 p-0">
                    <ul class="list-unstyled">
                        @foreach ($res as $r0)
                            @php
                                if($forpage == "Prod_Sched"){
                                    if($r0->status == 'In Progress'){
                                        $stat_badge="warning";
                                    }else{
                                        $stat_badge="secondary";
                                    }
                                    if($operation == "3"){
                                        if($planned_date > (($r0->rescheduled_delivery_date == null)? $r0->deli:$r0->rescheduled_delivery_date)){
                                            $stat_badge="danger";
                                        }
                                    }
                                }else{
                                    if($r0->status == 'In Progress'){
                                        $stat_badge ="warning";
                                    }else{
                                        $stat_badge="secondary";
                                    }
                                }             
                            @endphp
                            <li class="">
                                <div class="custom-control custom-checkbox mr-sm-2" style="font-size:12pt;">
                                    <input id="option{{ $r0->production_order }}" class="custom-control-input prodname" name="prodname[]"  data-dateslct="{{ $r0->production_order }}" type="checkbox" value="{{ $r0->production_order }}">
                                    <label for="option{{ $r0->production_order }}" class="custom-control-label" style="line-height: 1.8;">
                                        <span class="badge badge-{{$stat_badge}}" style="text-align: center;font-size:13px;color:white; ">
                                            <b>{{ $r0->production_order }} ({{ ($r0->sales_order == null)? $r0->material_request:$r0->sales_order }})</b>
                                        </span>
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-md-12 pr-3 pl-3">
                <ul class="list-unstyled">
                    @forelse ($prod_details as $r1)
                        @php
                            if($forpage == "Prod_Sched"){
                                if($r1->status == 'In Progress'){
                                    $stat_badge="warning";
                                }else{
                                    $stat_badge="secondary";
                                }
                                if($operation == "3"){
                                    if($planned_date > (($r1->rescheduled_delivery_date == null)? $r1->deli:$r1->rescheduled_delivery_date)){
                                        $stat_badge="danger";
                                    }
                                }
                            }else{
                                if($r1->status == 'In Progress'){
                                    $stat_badge ="warning";
                                }else{
                                    $stat_badge="secondary";
                                }
                            }
                        @endphp
                        <li class="">
                            <div class="custom-control custom-checkbox mr-sm-2" style="font-size:12pt;">
                                <input id="option{{ $r1->production_order }}" class="custom-control-input" name="prodname[]" type="checkbox" data-dateslct="{{ $r1->production_order }}" value="{{ $r1->production_order }}">
                                <label for="option{{ $r1->production_order }}" class="custom-control-label" style="line-height: 1.8;">
                                    <span class="badge badge-{{$stat_badge}}" style="text-align: center;font-size:13px;color:white; ">
                                        <b>{{ $r1->production_order }} ({{ ($r1->sales_order == null)? $r1->material_request:$r1->sales_order }})</b>
                                    </span>
                                </label>
                            </div>
                        </li>
                    @empty
                        <li stye>---No Production Order Found---</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endif