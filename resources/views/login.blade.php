@extends('layouts.user_app', [
    'namePage' => 'MES',
    'activePage' => 'login',
])

@section('content')
<div class="panel-header p-2">
   <div class="header">
      <form id="login-frm" action="/login_user" method="post" autocomplete="off">
         @csrf
         <div class="d-flex flex-row">
            <div class="col-7">
               <h2 class="title m-0">Manufacturing Execution System v.12</h2>
            </div>
            @if (!Auth::user())
               <div class="form-group m-0 col-2 p-2">
                  <input type="text" class="form-control rounded bg-white" name="user_id" placeholder="Enter Email" required>
               </div> 
               <div class="form-group m-0 col-2 p-2">
                  <input type="password" class="form-control rounded bg-white" name="password" placeholder="Enter your Windows Password" required>
               </div>
               <div class="col-1 p-2">
                  <button type="submit" class="btn btn-primary btn-block  m-0 btn-xs">LOGIN</button>
               </div>
            @else
               <div class="col-5 text-right p-2">
                  <span class="text-white mr-3" style="font-size: 12pt;">Welcome, {{ Auth::user()->employee_name }}</span>
                  <button type="button" class="btn btn-primary m-0 btn-xs">MAIN DASHBOARD</button>
                  <button type="button" class="btn btn-secondary m-0 btn-xs">LOGOUT</button>
               </div>
            @endif
         </div>
      </form>
   </div>
</div>
<div class="content pl-3 pr-3" style="margin-top: -185px;">
   <div class="card">
      <div class="card-body pt-0">
         <div class="d-flex flex-row pt-2 align-items-center">
            <div class="col-8 p-0 m-0">
               <form id="filters-form">
                  <div class="row p-0 m-0">
                     <div class="col-12 p-1">
                        <h5 class="card-title text-uppercase font-weight-bold m-0 p-0">Order Status Monitoring Dashboard</h5>
                     </div>
                     <div class="col-3 p-2">
                        <input type="text" class="form-control rounded" name="reference" placeholder="Search by Sales Order / MREQ No.">
                     </div>
                     <div class="col-3 p-2">
                        <input type="text" class="form-control rounded" name="customer" placeholder="Search by Customer Name">
                     </div>
                     <div class="col-3 p-2">
                        <input type="text" class="form-control rounded" name="project" placeholder="Search by Project Name">
                     </div>
                     <div class="col-2 p-2">
                        <button class="btn btn-secondary btn-sm m-0" id="reset-form">Clear</button>
                        <button class="btn btn-info btn-sm m-0" id="refresh-list"><i class="now-ui-icons loader_refresh"></i></button>
                     </div>
                  </div>
               </form>
            </div>
            <div class="col-4">
               <div class="d-flex flex-row-reverse p-0 m-0 align-items-center" style="color:#34495E;">
                  <div class="col-5 border-left">
                     <h3 id="current-time" class="title text-left m-2">--:--:-- --</h3>
                  </div>
                  <div class="col-6 text-center">
                     <span class="d-block font-weight-bold" style="font-size: 18px;">{{ date('l, F d, Y') }}</span>
                  </div>
               </div>
            </div>
         </div>
         <div id="order-list-div"></div>
      </div>
   </div>
</div>

<div id="loader-wrapper" hidden>
   <div id="loader"></div>
   <div class="loader-section section-left"></div>
   <div class="loader-section section-right"></div>
</div>

<style>
   #loader-wrapper {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: 99999;
	}
	#loader {
		display: block;
		position: relative;
		left: 50%;
		top: 50%;
		width: 150px;
		height: 150px;
		margin: -75px 0 0 -75px;
		border-radius: 50%;
		border: 3px solid transparent;
		border-top-color: #3498db;
		-webkit-animation: spin 2s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
		animation: spin 2s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
		z-index: 999999;
	}
	#loader:before {
		content: "";
		position: absolute;
		top: 5px;
		left: 5px;
		right: 5px;
		bottom: 5px;
		border-radius: 50%;
		border: 3px solid transparent;
		border-top-color: #e74c3c;
		-webkit-animation: spin 3s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
		animation: spin 3s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
	}
	#loader:after {
		content: "";
		position: absolute;
		top: 15px;
		left: 15px;
		right: 15px;
		bottom: 15px;
		border-radius: 50%;
		border: 3px solid transparent;
		border-top-color: #f9c922;
		-webkit-animation: spin 1.5s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
		animation: spin 1.5s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
	}
	@-webkit-keyframes spin {
		0%   { 
			-webkit-transform: rotate(0deg);  /* Chrome, Opera 15+, Safari 3.1+ */
			-ms-transform: rotate(0deg);  /* IE 9 */
			transform: rotate(0deg);  /* Firefox 16+, IE 10+, Opera */
		}
		100% {
			-webkit-transform: rotate(360deg);  /* Chrome, Opera 15+, Safari 3.1+ */
			-ms-transform: rotate(360deg);  /* IE 9 */
			transform: rotate(360deg);  /* Firefox 16+, IE 10+, Opera */
		}
	}
	@keyframes spin {
		0%   { 
			-webkit-transform: rotate(0deg);  /* Chrome, Opera 15+, Safari 3.1+ */
			-ms-transform: rotate(0deg);  /* IE 9 */
			transform: rotate(0deg);  /* Firefox 16+, IE 10+, Opera */
		}
		100% {
			-webkit-transform: rotate(360deg);  /* Chrome, Opera 15+, Safari 3.1+ */
			-ms-transform: rotate(360deg);  /* IE 9 */
			transform: rotate(360deg);  /* Firefox 16+, IE 10+, Opera */
		}
	}
	#loader-wrapper .loader-section {
		position: fixed;
		top: 0;
		width: 100%;
		height: 100%;
		background-color:  #e5e7e9 ;
		z-index: 1000;
		opacity: 50%;
		-webkit-transform: translateX(0);  /* Chrome, Opera 15+, Safari 3.1+ */
		-ms-transform: translateX(0);  /* IE 9 */
		transform: translateX(0);  /* Firefox 16+, IE 10+, Opera */
	}
</style>
@endsection

@section('script')
<script>
   $(document).ready(function(){
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });

      function showNotification(color, message, icon){
         $.notify({
            icon: icon,
            message: message
         },{
            type: color,
            timer: 5000,
            placement: {
               from: 'top',
               align: 'center'
            }
         });
      }

      $('#login-frm').submit(function(e){
         e.preventDefault();
         $.ajax({
            url: $(this).attr('action'),
            type:"POST",
            data: $(this).serialize(),
            success:function(data){
               if (data.success < 1) {
                  showNotification("danger", data.message, "now-ui-icons travel_info");
               }else{
                  showNotification("success", data.message, "now-ui-icons ui-1_check");
                  // setTimeout(function() {
                     window.location.href = data.redirect_to;
                  // }, 1500);
               }
            }
         });  
      });

      $('#filters-form input').keyup(function(e) {
         e.preventDefault()
         loadOrderList()
      })

      $('#reset-form').click(function(e) {
         e.preventDefault()
         $("#filters-form").trigger("reset");
         loadOrderList()
      })

      $('#refresh-list').click(function(e) {
         e.preventDefault()
         loadOrderList()
      })

      loadOrderList()
      function loadOrderList(page){
         $('#loader-wrapper').removeAttr('hidden');
         $.ajax({
            url: "/ongoing_orders?page=" + page,
            type:"GET",
            data: $('#filters-form').serialize(),
            success:function(data){
               $('#loader-wrapper').attr('hidden', true);
               $('#order-list-div').html(data);
            }
         });
      }

      $(document).on('click', '.ongoing-list-pagination a', function(e){
         e.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         loadOrderList(page);
      });

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
@endsection