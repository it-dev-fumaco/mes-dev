<div class="card mt-2 mb-2">
    <div class="card-body pb-0 pt-0">
        <div class="row bg-white" style="min-height: 200px;">
            <div class="col-md-12 p-0">
                <h6 class="text-white font-weight-bold text-center m-0 p-2" style="font-size: 10.5pt; border: 1px solid; background-color: #0772DD;">Machine / Workstation</h6>
                <table class="table table-striped table-bordered text-center m-0 custom-table-fixed-1">
                    <thead class="text-white bg-secondary" style="font-size: 6.5pt;">
                        <tr>
                            <th class="text-center p-2" style="width: 140px; min-width: 140px;"><b>Workstation</b></th>
                            <th class="text-center p-2" style="width: 90px; min-width: 90px;"><b>On Queue</b></th>
                            <th class="text-center p-2" style="width: 90px; min-width: 90px;"><b>On Going</b></th>
                            <th class="text-center p-2" style="width: 85px; min-width: 83px;"><b>Status</b></th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 9pt; max-height: 400px;">
                        @foreach ($result as $row)
                        <tr>
                            <td class="text-justify p-1" style="width: 140px; min-width: 140px;">
                                <span class="font-weight-bold d-block">{{ $row['machine_code'] }}</span>
                                <span class="font-italic d-block text-dark" style="font-size: 7pt;">{{ $row['machine_name'] }}</span>
                            </td>
                            <td class="text-center" style="width: 90px; min-width: 90px;">{{ $row['on_queue'] }}</td>
                            <td class="text-center" style="width: 90px; min-width: 90px;">{{ $row['on_going'] }}</td>
                            <td class="text-center" style="width: 85px; min-width: 85px;">
                                <span class="badge {{ ($row['status'] == 'Active') ? 'badge-success' : 'badge-warning' }}" style="font-size: 9pt;">{{ $row['status'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>