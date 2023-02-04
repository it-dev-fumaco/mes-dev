@extends('layouts.user_app', [
    'namePage' => 'MES',
    'activePage' => 'login',
])

@section('content')
<div class="panel-header">
        <div class="header text-center">
          <h2 class="title">Manufacturing Execution System v10 - 2022</h2>
        </div>
      </div>
      <div class="content" style="margin-top: -80px; min-height: 700px;">
         <div class="row m-0 p-0">
            <div class="col-md-4 offset-md-4">
               <form id="login-frm" action="/login_user" method="post">
                  @csrf
                  <div class="card">
                     <div class="card-header">
                        <h4 class="card-title">Your Windows Login (LDAP)</h4>
                     </div>
                     <div class="card-body">
                        <div class="form-group">
                           <label>Email</label>
                           <input type="text" class="form-control rounded" name="user_id" placeholder="Enter Email" required>
                        </div>
                        <div class="form-group">
                           <label>Password</label>
                           <input type="password" class="form-control rounded" name="password" placeholder="Enter your Windows Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">LOGIN</button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
@endsection

@section('script')
<script>
   $(document).ready(function(){
      console.log('ready');
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
   });
</script>
@endsection