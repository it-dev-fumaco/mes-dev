
@if($validation_tab == "with_tab")
    <div class="row">
        <div class="col-3 text-white p1" style=" padding:0px 0px 0px 30px;vertical-align: text-top;">
            <div class="row" style="background-color: #ffffff; padding:0px 0px 0px 0px;" class="text-center">
               <div class="col-md-12 text-center" id="reject_tab_list" style="padding:0px 0px 0px 0px;">
                    <ul class="nav flex-column workstation_navbar" id="myTabsss" role="tablist" style="font-size: 10pt;width:100%;padding:0px 0px 0px 0px">
                        @foreach($tab as $index => $row)
                            @php
                                $string_to_spaces= str_replace(' ', '', $index);
                                $string_to_slash= str_replace('/', '', $string_to_spaces);
                            @endphp
                            <li class="nav-item" style="background-color: #0277BD;width:100%;height:100%;border:2px solid white;" >
                                <a style="word-wrap: break-word;" class="nav-link {{ $loop->first ? 'active' : '' }} text-white" id="{{ $string_to_slash}}"  data-toggle="tab" href="#tab_{{ $string_to_slash}}" >{{ $index }} </a>
                            </li>
                        @endforeach 
                    </ul>
                </div>
            </div>
        </div>
         <div class="col-md-9" style=" padding:0px 0px 0px 12px;">
             <div class="tab-content text-left">
                 @foreach($tab as $index => $rows)
                     @php
                         $string_to_spaces= str_replace(' ', '', $index);
                         $string_to_slash= str_replace('/', '', $string_to_spaces);
                     @endphp
                     <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab_{{ $string_to_slash }}" role="tabpanel" aria-labelledby="search_tab">
                         <div class="row">
                             <div class="col-md-12">
                                 <div class="row" style="margin: 0 8px; width:100%;">
                                    <div class="col-md-12 column1"  style="border:2px solid white;padding:0px 0px 0px 0px; position:relative; width:%; height:100%;">
                                        @php
                                                $limit = ceil(count($rows) / 2)
                                        @endphp
                                        @if ($limit > 1)
                                            <div class="row p-0 m-0">
                                                @foreach($rows->chunk($limit) as $res)
                                                    <div class="col-md-6 p-0">
                                                        <ul class="list-unstyled">
                                                            @foreach ($res as $rs)
                                                                <li class="p-1 d-block">
                                                                    <div class="inputGroup" style="vertical-align: text-top; border:2px solid white; width:100%;">
                                                                        <input id="option{{ $rs->reject_list_id }}" name="reject_list[]" type="checkbox" class="qc-chk" data-parentid="{{$string_to_slash}}" data-reject-reason="{{ $rs->reject_reason }}" value="{{ $rs->reject_list_id }}" style="padding:0px 0px 0px 0px;" />
                                                                        <label style=" word-wrap: break-word;" for="option{{ $rs->reject_list_id }}"> {{ $rs->reject_reason }}</label>
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
                                                        @foreach($rows as $row)
                                                            <li class="p-1 d-block">
                                                                <div class="inputGroup" style="vertical-align: text-top; border:2px solid black; width:100%;">
                                                                    <input id="option{{ $row->reject_list_id }}" name="reject_list[]" type="checkbox" class="qc-chk" data-parentid="{{$string_to_slash}}" data-reject-reason="{{ $row->reject_reason }}" value="{{ $row->reject_list_id }}" style="padding:0px 0px 0px 0px;" />
                                                                    <label style=" word-wrap: break-word;" for="option{{ $row->reject_list_id }}"> {{ $row->reject_reason }}</label>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                             </div>
                         </div>
                     </div>
                 @endforeach
             </div>
         </div>
    </div> 
@endif
@if($validation_tab == "no_tab")
    <div class="row">
        <div class="col-12 text-white " style="padding-right: 0px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="row" style="margin: 0 8px;">
                        <div class="col-md-12 column1" id="column1">
                            @php
                                $limit = ceil(count($tab) / 2)
                            @endphp
                            @if ($limit > 1)
                                <div class="row p-0 m-0">
                                    @forelse($tab->chunk($limit) as $res)
                                        <div class="col-md-6 p-0">
                                            <ul class="list-unstyled">
                                                @forelse ($res as $rows)
                                                    <li class="p-1 d-block">
                                                        <div class="inputGroup" style="vertical-align: text-top; border:2px solid white; width:100%;">
                                                            <input id="option{{ $rows->reject_list_id }}" name="reject_list[]" type="checkbox" class="qc-chk" data-reject-reason="{{ $rows->reject_reason }}" value="{{ $rows->reject_list_id }}" />
                                                            <label for="option{{ $rows->reject_list_id }}"> {{ $rows->reject_reason }}</label>
                                                        </div>
                                                    </li>
                                                @empty
                                                <li class="p-1 d-block text-black" style="color:black;"><h6>No Operator Reject Found</h6> </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    @empty
                                        <h6>No Operator Reject Found</h6>
                                    @endforelse
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-12 pr-3 pl-3">
                                        <ul class="list-unstyled">
                                            @forelse($tab as $row)
                                                <li class="p-1 d-block">
                                                    <div class="inputGroup" style="vertical-align: text-top; border:2px solid white; width:100%;">
                                                        <input id="option{{ $row->reject_list_id }}" name="reject_list[]" type="checkbox" class="qc-chk"  data-reject-reason="{{ $row->reject_reason }}" value="{{ $row->reject_list_id }}" style="padding:0px 0px 0px 0px;" />
                                                        <label style=" word-wrap: break-word;" for="option{{ $row->reject_list_id }}"> {{ $row->reject_reason }}</label>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="p-1 d-block text-black" style="color:black;"><h6>No Operator Reject Found</h6> </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            @endif                                                                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
@endif

<style type="text/css">

.nav-item .active {
  background-color:  #11a3cf;
  width:100%;
  height:100%;
  /* color: black; */
  font-weight: bold;
}
.nav-link .active {
  background-color:  #11a3cf;
  width:100%;
  height:100%;
  /* color: black; */
  font-weight: bold;
}

.inputGroup {
    vertical-align: text-top;
background-color: #fff;
display: block;
/* margin: 10px 0; */
position: relative;
}
.inputGroup label {
padding: 10px 20px 6px 60px;
width: 100%;
height:100%;
display: block;
text-align: left;
color: #3C454C;
cursor: pointer;
position: relative;
z-index: 2;
margin:0;
transition: color 200ms ease-in;
overflow: hidden;
}
.inputGroup label:before {
width: 100%;
height: 100%;
border-radius: 50%;
content: '';
background-color: #5562eb;
position: absolute;
/* left: 50%;
top: 50%; */
-webkit-transform: translate(-50%, -50%) scale3d(1, 1, 1);
transform: translate(-50%, -50%) scale3d(1, 1, 1);
transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
opacity: 0;
z-index: -1;
}
.inputGroup label:after {
width: 32px;
height: 32px;
content: '';
border: 2px solid #D1D7DC;
background-color: #fff;
background-image: url("data:image/svg+xml,%3Csvg width='32' height='32' viewBox='0 0 32 32' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5.414 11L4 12.414l5.414 5.414L20.828 6.414 19.414 5l-10 10z' fill='%23fff' fill-rule='nonzero'/%3E%3C/svg%3E ");
background-repeat: no-repeat;
background-position: 2px 3px;
border-radius: 50%;
z-index: 2;
position: absolute;
left: 20px;
top: 50%;
-webkit-transform: translateY(-50%);
transform: translateY(-50%);
cursor: pointer;
transition: all 200ms ease-in;
}
.inputGroup input:checked ~ label {
color: #fff;
}
.inputGroup input:checked ~ label:before {
-webkit-transform: translate(-50%, -50%) scale3d(56, 56, 1);
transform: translate(-50%, -50%) scale3d(56, 56, 1);
opacity: 1;
}
.inputGroup input:checked ~ label:after {
background-color: #54E0C7;
border-color: #54E0C7;
}
.inputGroup input {
width: 32px;
height: 32px;
order: 1;
z-index: 2;
position: absolute;
/* right: 30px; */
/* top: 50%; */
/* padding:0px 0px 0px 5px; */
-webkit-transform: translateY(-50%);
transform: translateY(-50%);
cursor: pointer;
visibility: hidden;
}

.form {
padding: 0 16px;
max-width: 550px;
margin: 10px 0 auto auto;
font-size: 15px;
font-weight: 600;
line-height: 36px;
}
</style>