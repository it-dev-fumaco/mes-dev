<div class="d-flex flex-row align-items-center p-2">
  <img src="{{ $item_details['image'] }}" alt="{{ $item_details['item_code'] }}" class="p-1 col-1" style="width: 80px; height: 80px;">
  <div class="p-1 col-8">
      <span class="font-weight-bold d-block">{{ $item_details['item_code'] }}</span> {!! strip_tags($item_details['description']) !!}
  </div>
  <div class="col-1 text-center">
    <span class="d-block text-uppercase text-secondary">Ordered</span>
    <span class="d-block font-weight-bold" style="font-size: 18px;">{{ number_format($item_details['qty']) }}</span>
    <small class="d-block text-muted">{{ $item_details['stock_uom'] }}</small>
  </div>
  <div class="col-1 text-center">
    <span class="d-block text-uppercase text-secondary">Feedbacked</span>
    <span class="d-block font-weight-bold" style="font-size: 18px;">{{ number_format($item_details['feedback_qty']) }}</span>
    <small class="d-block text-muted">{{ $item_details['stock_uom'] }}</small>
  </div>
  <div class="col-1 text-center">
    <span class="d-block text-uppercase text-secondary">Delivered</span>
    <span class="d-block font-weight-bold" style="font-size: 18px;">{{ number_format($item_details['delivered_qty']) }}</span>
    <small class="d-block text-muted">{{ $item_details['stock_uom'] }}</small>
  </div>
</div>

@php
    $operations = [
      [
        'name' => 'Fabrication',
        'slug' => 'fabrication'
      ],
      [
        'name' => 'Painting',
        'slug' => 'painting'
      ],
      [
        'name' => 'Wiring & Assembly',
        'slug' => 'assembly'
      ],
    ];
@endphp

<div class="d-flex justify-content-center col-10 mx-auto mt-5">
  <ul class="breadcrumb-custom">
    @foreach ($operations as $operation)
    @if (isset($operation_status[$operation['slug']]))
    @php
      $op_status = $operation_status[$operation['slug']]['status'];
      $start_time = isset($operation_status[$operation['slug']]['start']) ? $operation_status[$operation['slug']]['start'] : '--';
      $end_time = isset($operation_status[$operation['slug']]['end']) && $op_status != 'active' ? $operation_status[$operation['slug']]['end'] : '--';
      $op_duration = isset($operation_status[$operation['slug']]['duration']) && $op_status != 'active' ? $operation_status[$operation['slug']]['duration'] : '--';
    @endphp
    <li class="{{ $operation_status[$operation['slug']]['status'] }}">
      <a href="#">
        <span class="d-block font-weight-bold text-uppercase" style="font-size: 14px;">{{ $operation['name'] }}</span>
        <div class="d-block text-justify ml-5 mr-5 mt-2 mb-2 w-75 mx-auto">
          <span class="d-block">Start Time: {{ $start_time }}</span>
          <span class="d-block">End Time: {{ $end_time }}</span>
          <span class="d-block">Total Duration: {{ $op_duration }}</span>
        </div>
      </a>
    </li>
    @endif
    @endforeach
  </ul>
</div>

<div class="overflow-auto" style="min-height: 400px;">
  <center>
    <ul class="tree ulclass text-center">
      <li class="liclass text-center">
        @include('includes.tracking_view_tree_box', ['item' => $item_details])
        @if (count($parts) > 0) 
        <ul class="ulclass mt-3">
          @foreach ($parts as $part)
          <li class="liclass">
            @include('includes.tracking_view_tree_box', ['item' => $part])
            @if (count($part['child_nodes']) > 0) 
            <ul class="ulclass mt-3">
              @foreach ($part['child_nodes'] as $child_level_1)
              <li class="liclass">
                @include('includes.tracking_view_tree_box', ['item' => $child_level_1])
                @if (count($child_level_1['child_nodes']) > 0) 
                <ul class="ulclass mt-3">
                @foreach ($child_level_1['child_nodes'] as $child_level_2)
                <li class="liclass">
                  @include('includes.tracking_view_tree_box', ['item' => $child_level_2])
                  @if (count($child_level_2['child_nodes']) > 0) 
                  <ul class="ulclass mt-3">
                    @foreach ($child_level_2['child_nodes'] as $child_level_3)
                    <li class="liclass">
                      @include('includes.tracking_view_tree_box', ['item' => $child_level_3])
                      @if (count($child_level_3['child_nodes']) > 0) 
                      <ul class="ulclass mt-3">
                        @foreach ($child_level_3['child_nodes'] as $child_level_4)
                        <li class="liclass">
                          @include('includes.tracking_view_tree_box', ['item' => $child_level_4])
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
            @endif
          </li>
          @endforeach
        </ul>
        @endif
      </li>
    </ul>
  </center>
  
</div>
  
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
    background:#EAECEE;
    border: 1px solid #B2BABB;
    z-index: 1;
    width: 400px;
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


  .breadcrumb-custom {
    font-size: 8pt;
    padding: 0px;
    background: transparent;
    list-style: none;
    overflow: hidden;
    margin-top: 10px;
    margin-bottom: 10px;
    width: 100%;
    border-radius: 4px;
}
.breadcrumb-custom>li {
    display: table-cell;
    vertical-align: top;
    width: 1%;
}

.breadcrumb-custom>li+li:before {
    padding: 0;
}

.breadcrumb-custom li a {
    color: white;
    text-decoration: none;
    padding: 10px 0 10px 5px;
    height: 100px;
    position: relative;
    display: inline-block;
    width: calc( 100% - 10px );
    background-color: hsla(0, 0%, 83%, 1);
    text-align: center;
    text-transform: capitalize;
}

.breadcrumb-custom li.completed a {
    background: brown;
    background: hsla(153, 57%, 51%, 1);
}

.breadcrumb-custom li.completed a:after {
    border-left: 30px solid hsla(153, 57%, 51%, 1);
}

.breadcrumb-custom li.for_feedback a {
    background: brown;
    background: rgb(84, 153, 199);
}

.breadcrumb-custom li.for_feedback a:after {
    border-left: 30px solid rgb(84, 153, 199);
}

.breadcrumb-custom li.active a {
    background: #ffc107;
}

.breadcrumb-custom li.active a:after {
    border-left: 30px solid #ffc107;
}

.breadcrumb-custom li:first-child a {
    padding-left: 1px;
}

.breadcrumb-custom li:last-of-type a {
    width: calc( 100% - 38px );
}

.breadcrumb-custom li a:before {
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
    margin-left: 10px;
    left: 100%;
    z-index: 1;
}

.breadcrumb-custom li a:after {
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
  });
</script>