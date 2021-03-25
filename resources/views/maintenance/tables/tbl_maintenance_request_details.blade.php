<div class="col-md-12">
    <div class="col-md-12">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <img src="{{$main->image}}" style="width: 100px; height: 100px;">
                        </div>
                        <div class="col-md-5">
                            <table>
                                <tr>
                                    <td><span style="font-size:25px;"><b>{{$main->machine_breakdown_id}}</b></span></td>
                                </tr>
                                <tr>
                                    <td style="font-size:15px;"><b>{{$main->machine_id}}</b> [ {{$main->machine_name}}]</td> 
                                </tr>
                                <tr>
                                    <td col="2"><i>{{$main->type}} - {{($main->type =='Breakdown')? $main->breakdown_reason:$main->corrective_reason}} </i> </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-5">
                            <table>
                                <tr>
                                    <td><b>Reported By:</b> </td>
                                    <td><span style="padding-left: 5px;">{{$main->reported_by}}</span></td>
                                </tr>
                                <tr>
                                    <td><b>Date Reported:</b></td>
                                    <td><span style="padding-left: 5px;">{{\Carbon\Carbon::parse($main->date_reported)->format('M d, Y')}}</span><input type="hidden" id="date_reported" name="date_reported" value="{{$main->date_reported}}"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group" style="margin-top: -10px;">
                                         <b> Status:</b>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control" name="maintenance_status" id="maintenance_status" style="display: inline-block; width: 100%;" required>
                                          <option value="Pending" {{ ($main->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                          <option value="Completed" {{ ($main->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                                          <option value="In Progress" {{ ($main->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" value="{{$main->machine_breakdown_id}}" name='breakdown_id'>
    <div class="col-md-12" style="margin-top:15px;">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="date_resolve" style=" display: inline-block; margin-right: 1%;"><b>Date Resolved:</b></label>
                    <input type="text" class="form-control date" value="{{ \Carbon\Carbon::parse($main->date_resolved)->format('M d, Y h:m A') }}" name="date_resolve" id="date_resolve_picker" style="display: inline-block; width: 65%; font-weight: bolder;" required />
                </div>
                <div class="form-group" style="margin-top: -20px;">
                    <label for="findings" class=" col-form-label"><b>Finding/s:</b></label>
					<textarea class="form-control" name="findings" rows="3" style="border:1px solid  #cccccc;" id="findings">{{$main->findings}}</textarea>
                </div>
                <div class="form-group">
                    <label for="t_duration" style=" display: inline-block; margin-right: 1%;"><b>Duration:</b></label>
                    <input type="text" class="form-control "  name="t_duration" id="t_duration" style="display: inline-block; width: 65%; font-weight: bolder;">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group" style="">
                    <label for="maintennace_type" style=" display: inline-block; margin-right: 1%;"><b>Maintenance Type :</b></label>
                    <select class="form-control" name="maintennace_type" id="maintennace_type" style="display: inline-block; width: 59%;" required>
                        <option value="Breakdown" {{ ($main->type) == 'Breakdown' ? 'selected' : '' }}>Breakdown </option>
                        <option value="Corrective" {{ ($main->type) == 'Corrective' ? 'selected' : '' }}>Corrective</option>
                    </select>
                </div> 
                <div class="form-group" style="margin-top: -20px;">
                    <label for="work_done" class=" col-form-label"><b>Work Done:</b></label>
					<textarea class="form-control" id="work_done" name="work_done" rows="3" style="border:1px solid  #cccccc;">{{$main->work_done}}</textarea>
                </div>
                <div class="form-group">
                    <label for="maintennace_type" style=" display: inline-block; margin-right: 1%;"><b>Assigned Maintenance Staff :</b></label>
                    <table style="border:1px solid black;">
                        <tbody style="border:1px solid black;">
                            @forelse($assigned_main_staff as $r)
                            <tr style="line-height:8px;"><p style="line-height:8px;">{{$r->employee_name}}</p></tr>
                            
                            @empty 
                                <tr style="line-height:8px;"><br>--NO ASSIGN MAINTENANCE STAFF--</tr>
                            @endforelse
                        </tbody>
                        
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />
    
    <script>
        $('#date_resolve_picker').daterangepicker({
        "autoUpdateInput": true,
        "singleDatePicker": true,
        "showDropdowns": true,
        "timePicker": true,
        "locale": {format: 'MMMM D, YYYY hh:mm A'},
      }, function(start, end, label) {
        console.log('New date range selected: ' + start.format('MMMM D, YYYY hh:mm A') + ' to ' + end.format('MMMM D, YYYY hh:mm A') + ' (predefined range: ' + label + ')');
      });
      
      $('#date_resolve_picker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MMMM D, YYYY hh:mm A'));
        var date_resolve = $(this).val();
        var date_reported = $('#date_reported').val();
        console.log(date_resolve);
        console.log(date_reported);
        console.log(timeDiffCalc(new Date(date_reported), new Date(date_resolve)));
        console.log(diffinhrs(new Date(date_reported), new Date(date_resolve)));
        $('#t_duration').val(timeDiffCalc(new Date(date_reported), new Date(date_resolve)));
      });

      $('#date_resolve_picker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
      });

    //   $("#date_resolve_picker").val('');
    </script>
    