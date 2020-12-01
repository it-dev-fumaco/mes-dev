<table class="table custom-table-striped text-center">
<colgroup>
@foreach($data as $roww)
   <col style="width:{{$col}}%;">
    @endforeach
  </colgroup> 
   <thead class="text-primary" style="font-size: 7pt;">
   @foreach($data as $row)
   <th class="text-center sticky-header"><b>{{$row->process_name}}</b></th>
    @endforeach
   </thead>
   <tbody style="font-size: 9pt;">
        <tr>
         @foreach($data as $rows)
            @php
                                   if($rows->status == "Completed"){
                                     $status="#2ecc71";
                                   }else if($rows->status == "In Progress"){
                                     $status="#f4d03f";
                                   }else{
                                     $status="#b2babb";
                                   }
                                   if($row->prod_status == 'Completed'){
                                     $display = '';
                                   }else{
                                     $display = '2';
                                   }
                                   @endphp
            <td class="text-center" style="background-color: {{$status}};color:white;" rowspan="{{$display}}"><span style="font-size: 9pt;">{{ $rows->status }}</span><span style="font-size: 9pt;display:{{ ($rows->completed_qty <= 0)? 'none': 'block' }};">( {{ $rows->completed_qty }} )</span></td>
            @endforeach
           
           </td>
         </tr>
        @php
                                   if($status == 'Completed'){
                                     $display = '';
                                     $colpsn = '2';
                                     $colorme = '#2ecc71';
                                   }else{
                                     $display='none';
                                     $colorme = '';
                                     $colpsn = '1';
                                   }
                                   @endphp
   
         <tr class="heightcustom" style="background-color: {{ $colorme }};color:white; ">
           
           <td class="text-center" colspan="{{ $count }}" style="height: 20px;border: none;">{{ ($status == 'Completed')? $duration:'' }} </td>
           <td style="display:{{$display}} ;">
           </td>
         </tr> 
   
   </tbody>
</table>

<style type="text/css">
  .custom-table-striped > tbody > tr:nth-child(4n+1) > td, .custom-table-striped > tbody > tr:nth-child(4n+1) > th {
   background-color: whitesmoke;
}
  td.heightcustom > div {
    width: 100%;
    height: 100%;
    overflow:hidden;
}
td.heightcustom {
    height: 20px;
}
  .breadcrumb-c {
    font-size: 8pt;
    font-weight: bold;
    padding: 0px;
    background: transparent;
    list-style: none;
    overflow: hidden;
    margin-top: 10px;
    margin-bottom: 10px;
    width: 100%;
    border-radius: 4px;
}

th.sticky-header {
  position: sticky;
  top: 0;
  z-index: 10;
  background-color: white;
}
.breadcrumb-c>li {
    display: table-cell;
    vertical-align: top;
    width: 1%;
}

.breadcrumb-c>li+li:before {
    padding: 0;
}

.breadcrumb-c li a {
    color: white;
    text-decoration: none;
    padding: 1px 0 1px 5px;
    position: relative;
    display: inline-block;
    width: calc( 100% - 10px );
    background-color: hsla(0, 0%, 83%, 1);
    text-align: center;
    text-transform: capitalize;
}

.breadcrumb-c li.completed a {
    background: brown;
    background: hsla(153, 57%, 51%, 1);
}

.breadcrumb-c li.completed a:after {
    border-left: 30px solid hsla(153, 57%, 51%, 1);
}

.breadcrumb-c li.active a {
    background: #ffc107;
}

.breadcrumb-c li.active a:after {
    border-left: 30px solid #ffc107;
}

.breadcrumb-c li:first-child a {
    padding-left: 1px;
}

.breadcrumb-c li:last-of-type a {
    width: calc( 100% - 38px );
}

.breadcrumb-c li a:before {
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

.breadcrumb-c li a:after {
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


  .truncate {
    white-space: nowrap;
    /*overflow: hidden;*/
    text-overflow: ellipsis;
  }
</style>



<script type="text/javascript">
      function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 500,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }
</script>
<script type="text/javascript">
    $(document).on('click', '.prod-details-btn', function(e){
    e.preventDefault();
    var jtno = $(this).data('jtno');
    $('#jt-workstations-modal .modal-title').text(jtno);
    if(jtno){
      getJtDetails($(this).data('jtno'));
    }else{
      showNotification("danger", 'No Job Ticket found.', "now-ui-icons travel_info");
    }
  });
  

</script>