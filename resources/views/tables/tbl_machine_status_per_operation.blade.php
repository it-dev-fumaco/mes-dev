<div class="card mt-2 mb-2">
    <div class="card-body pb-0 pt-0">
        <div class="row bg-white" style="min-height: 200px;">
            <div class="col-md-12 p-0">
                <table class="table table-striped table-bordered text-center m-0">
                    <col style="width: 42%;">
                    <col style="width: 20%;">
                    <col style="width: 20%;">
                    <col style="width: 18%;">
                    <thead style="background-color: #0772DD;">
                        <th class="text-center p-2" colspan="6">
                            <h6 class="text-white font-weight-bold text-center m-0" style="font-size: 10.5pt;">Machine / Workstation</h6>
                        </th>
                    </thead>
                    <thead class="text-white bg-secondary" style="font-size: 6.5pt;">
                        <th class="text-center p-2"><b>Workstation</b></th>
                        <th class="text-center p-2"><b>On Queue</b></th>
                        <th class="text-center p-2"><b>On Going</b></th>
                        <th class="text-center p-2"><b>Status</b></th>
                    </thead>
                    <tbody style="font-size: 9pt;">
                        @foreach ($result as $row)
                        <tr>
                            <td class="text-justify p-1">
                                <span class="font-weight-bold d-block">{{ $row['machine_code'] }}</span>
                                <span class="font-italic d-block text-dark" style="font-size: 7pt;">{{ $row['machine_name'] }}</span>
                            </td>
                            <td class="text-center">{{ $row['on_queue'] }}</td>
                            <td class="text-center">{{ $row['on_going'] }}</td>
                            <td class="text-center">
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