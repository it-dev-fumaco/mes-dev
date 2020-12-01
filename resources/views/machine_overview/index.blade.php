@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'machine_overview',
])

@section('content')
@include('modals.add_image_modal')
@include('modals.machine_details_modal')
<div class="panel-header">
   <div class="header text-center" style="margin-top: -50px;">
      <h2 class="title">Machine Overview</h2><h4 class="title"></h4>

   </div>
</div>
<div class="content" style="margin-top: -180px;">
<div class="card" style="width: 100%;background-color: #e7e8ed;">
  <div class="col-md-3 offset-md-9 text-center" style="padding-top: 10px;">
    <div class="form-group">
      <select class="form-control" id="production_line" name="production_line" onchange="getContent()" style="background-color: white;font-size: 12pt;">
            <option value="All">All</option>
            @foreach($workstation as $row)
              <option value="{{ $row->workstation_id }}" style="font-size: 12pt;">{{ $row->workstation_name }}</option>
            @endforeach
      </select>
  </div>
  </div>

  <div class="card-body" id="dashboard-rows">
  </div>
  
</div>
</div>
                     
<style type="text/css">
.col-centered {
 display:inline-block;
    float:none;
    /* reset the text-align */
    text-align:left;
    /* inline-block space fix */
    margin-right:-4px;
      vertical-align: text-top;
}
#table-customize{
   table-layout: fixed;
}

/* Float four columns side by side */
.column {
  float: left;
  width: 25%;
  padding: 0 10px;
}

/* Remove extra left and right margins, due to padding */
.row {margin: 0 -5px;}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}

/* Responsive columns */
@media screen and (max-width: 600px) {
  .column {
    width: 100%;
    display: block;
    margin-bottom: 20px;
  }
}
.flex-wrapper {
  display: flex;
  flex-flow: row nowrap;
}

.single-chart {
  width: 60%;
  justify-content: space-around ;
}

.circular-chart {
  display: block;
  margin: 10px auto;
  max-width: 80%;
  max-height: 250px;
}

.circle-bg {
  fill: none;
  stroke: #eee;
  stroke-width: 3.8;
}

.circle {
  fill: none;
  stroke-width: 2.8;
  stroke-linecap: round;
  animation: ease-out forwards;
}

/*@keyframes progress {
  0% {
    stroke-dasharray: 0 100;
  }
}*/

.circular-chart.orange .circle {
  stroke: #ff9f00;
}

.circular-chart.green .circle {
  stroke: #4CC790;
}

.circular-chart.blue .circle {
  stroke: #3c9ee5;
}

.percentage {
  fill: #666;
  font-family: sans-serif;
  font-size: 0.5em;
  text-anchor: middle;
}
.user-image {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.imgPreview {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.upload-btn{
   padding: 6px 12px;
}

.fileUpload {
   position: relative;
   overflow: hidden;
   font-size: 9pt;
}

.fileUpload input.upload {
   position: absolute;
   top: 0;
   right: 0;
   margin: 0;
   padding: 0;
   cursor: pointer;
   opacity: 0;
   filter: alpha(opacity=0);
}
   .dot {
  height: 15px;
  width: 15px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
}
   .dotsmall {
  height: 8px;
  width: 8px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
}
   .text-blink {color: orange;
  animation: blinker 1s linear infinite;
}
#tbl_machine_list{
  overflow-y: hidden;
}

@keyframes blinker {  
  50% { opacity: 0; }
}

</style>
@endsection

@section('script')
<script src="{{ asset('js/charts/Chart.min.js') }}"></script>
<script src="{{ asset('js/charts/utils.js') }}"></script>
<script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<script>
   $(document).ready(function(){
      getContent();
      // setInterval('getContent();', 1000);
      $('input').on('change', function(){
        $('.progress-circle').attr('data-percentage', $(this).val());
      })
      $(function(){
    // Enables popover
    $("[data-toggle=popover]").popover();
      });
   });
</script>
<script type="text/javascript">
         function getContent(){
        // console.log("ready");
        var prod_line = $('#production_line').val();
         $.ajax({
            url:"/machine_task_list/",
            data: {prod_line: prod_line},
            type:"GET",
            success:function(data){
               $('#dashboard-rows').html(data);
            }
         });  
      }
</script>
<script type="text/javascript">
     function modal_click(){
      var machine_code = $(this).data('id');
      alert(machine_code);
      // alert('ready');
    // var workstation = $('#workstation_name').val(); 
    // var workstation_status = $workstation;
    // var combine = workstation_status+ " " + "[ " + "Production Line: " + workstation + ' ]';
    // $.ajax({
    //   url: "/operator/header_table_data/" + workstation +"/"+ workstation_status,
    //   method: "GET",
    //   success: function(response) {
    //     $('#workstation_name_title').text(combine);
    //     $('#data_table_entry').html(response);
        $('.add_image_modal').modal('show');
    //   },
    //   error: function(response) {
    //     alert(response);
    //   }
    // });
  }
</script>
<script type="text/javascript">
   $(document).on("click", ".machine_details_class", function () {
     $('.machine_details_modal').modal('show');
     var machine_code = $(this).data('machinecode');
     var workstation = $(this).data('workstation');
     var quetime = $(this).data('quetime');
     var machinename = $(this).data('machinename');
     var percetage = $(this).data('percetage');
     var completedqty = $(this).data('completedqty');
     var acceptedqty = $(this).data('acceptedqty');
      data = {
         machine_code : machine_code,
         workstation : workstation,
         quetime: quetime,
         percetage: percetage,
         completedqty: completedqty,
         acceptedqty: acceptedqty

        }

      $.ajax({
      url: "/machine_overview/details_overview",
      data: data,
      method: "GET",
      success: function(response) {
         $('#machine_details_table').html(response);
         breakdown(machine_code);
         corrective(machine_code);
        // $('#workstation_name_title').text(combine);
        // $('#data_table_entry').html(response);
        // $('.add_image_modal').modal('show');
      },
      error: function(response) {
        alert(response);
      }
    });

});
</script>
<script type="text/javascript">
 function breakdown($machine_code){
       var machine_code = $machine_code;
       var month = $('#breakdown_chart_chart .month').val();
       var year = $('#breakdown_chart_chart .year').val();
       console.log('ready');
       data = {
        month: month,
        year : year
       }
      $.ajax({
         url: "/machine_overview/machine_details_chart/breakdown/"+ machine_code,
         method: "GET",
         data: data,
         success: function(data) {

            var reason = [];
            var occurence = [];
            var duration = [];
           

            for(var i in data) {
               reason.push(data[i].reason);
               occurence.push(data[i].occurence);
               duration.push(data[i].duration);
            }

            var chartdata = {
               labels: reason,
               datasets : [{
                  backgroundColor: '#00838F',
                  data: duration,
                  label: "Duration(Hrs)"
               },
               {
                  backgroundColor: '#558B2F',
                  data: occurence,
                  label: "Occurence"
               }
               ]
               
            };

            var ctx = $("#breakdown_chart");

            if (window.breakdownchartctx != undefined) {
               window.breakdownchartctx.destroy();
            }

            window.breakdownchartctx = new Chart(ctx, {
               type: 'bar',
               data: chartdata,
               options: {
                  responsive: true,
                  legend: {
                     position: 'top',
                     labels:{
                        boxWidth: 11
                     }
                  },
                  scales: {
                     xAxes: [{ stacked: true }],
                     yAxes: [{ stacked: true }]
                  },
                  tooltips: {
                     mode: 'label',
                     callbacks: {
                        label: function(t, d) {
                           var dstLabel = d.datasets[t.datasetIndex].label;
                           var yLabel = t.yLabel;
                           return dstLabel + ': ' + yLabel;
                        }
                     }
                  }
               }
            });
         },
         error: function(data) {
            alert('Error fetching data!');
         }
      });
   }
</script>
<script type="text/javascript">
 function corrective($machine_code){
       var machine_code = $machine_code;
       var month = $('#corrective_chart_chart .month').val();
       var year = $('#corrective_chart_chart .year').val();
       data = {
        month: month,
        year : year
       }
      $.ajax({
         url: "/machine_overview/machine_details_chart/corrective/"+ machine_code,
         method: "GET",
         data: data,
         success: function(data) {

            var reason = [];
            var occurence = [];
            var duration = [];
           

            for(var i in data) {
               reason.push(data[i].reason);
               occurence.push(data[i].occurence);
               duration.push(data[i].duration);
            }

            var chartdata = {
               labels: reason,
               datasets : [{
                  backgroundColor: '#00838F',
                  data: duration,
                  label: "Duration(Hrs)"
               },
               {
                  backgroundColor: '#558B2F',
                  data: occurence,
                  label: "Occurence"
               }
               ]
               
            };

            var ctx = $("#corrective_chart");

            if (window.correctivechartctx != undefined) {
               window.correctivechartctx.destroy();
            }

            window.correctivechartctx = new Chart(ctx, {
               type: 'bar',
               data: chartdata,
               options: {
                  responsive: true,
                  legend: {
                     position: 'top',
                     labels:{
                        boxWidth: 11
                     }
                  },
                  scales: {
                     xAxes: [{ stacked: true }],
                     yAxes: [{ stacked: true }]
                  },
                  tooltips: {
                     mode: 'label',
                     callbacks: {
                        label: function(t, d) {
                           var dstLabel = d.datasets[t.datasetIndex].label;
                           var yLabel = t.yLabel;
                           return dstLabel + ': ' + yLabel;
                        }
                     }
                  }
               }
            });
         },
         error: function(data) {
            alert('Error fetching data!');
         }
      });
   }
</script>
<script type="text/javascript">
   $(document).on("click", ".image_click_class", function () {
     var myBookId = $(this).data('idko');
     var orig_image = $(this).data('imagefile');
     // alert(myBookId);
     // $(".modal-body #bookId").val( myBookId );
      $('#data_table_entry .test5').val(myBookId);
      $('#data_table_entry .user_image').val(orig_image);
      $('#data_table_entry .machine_name_title').text(myBookId);
     $('.add_image_modal').modal('show');

     // As pointed out in comments, 
     // it is unnecessary to have to manually call the modal.
     // $('#addBookDialog').modal('show');
});
</script>
<script type="text/javascript">
       $("#data_table_entry .upload").change(function () {
         if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#data_table_entry .imgPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
         }
      });
</script>
<script type="text/javascript">
   $("#breakdown_chart_chart .filter1").change(function () {
         var machine_code = $(this).data('machinecode');
         console.log(machine_code);
         alert(machine_code);
      });
</script>
<script type="text/javascript">
  function breakdown_filter($hi){
     breakdown($hi);
  }
</script>
<script type="text/javascript">
  function corrective_filter($hi){
     corrective($hi);
  }
</script>
@endsection