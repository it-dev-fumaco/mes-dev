<div class="card mt-2 mb-2">
    <div class="card-body pb-0 pt-0">
        <div class="row bg-white" style="min-height: 200px;">
            <div class="col-md-12 p-0">
                <h6 class="text-white font-weight-bold text-center m-0 p-2" style="font-size: 10.5pt; border: 1px solid; background-color: #0772DD;">Machine Availability</h6>
                <table class="table table-bordered text-center m-0">
                    <thead class="text-white bg-secondary" style="font-size: 6.5pt;">
                        <tr>
                            <th class="text-center p-2" colspan="2"><b>Maintenance Type</b></th>
                            <th class="text-center p-2"><b>Total</b></th>

                        </tr>
                    </thead>
                    <tbody style="font-size: 9pt;">
                        <tr>
                            <td rowspan="2" class="p-1">Unplanned</td>
                            <td class="p-1">Breakdown</td>
                            <td class="p-1">{{ $maintenance_count['breakdown'] }}</td>
                        </tr>
                        <tr>
                            <td class="p-1">Corrective</td>
                            <td class="p-1">{{ $maintenance_count['corrective'] }}</td>
                        </tr>
                        <tr>
                            <td class="p-1">Planned</td>
                            <td class="p-1">Preventive</td>
                            <td class="p-1">{{ $maintenance_count['preventive'] }}</td>
                        </tr>
                    </tbody>
                </table>
                <h6 class="text-white font-weight-bold text-center m-0 p-2" style="font-size: 10.5pt; border: 1px solid; background-color: #0772DD;">Maintenance Schedule</h6>
                <table class="table table-bordered text-center m-0 custom-table-fixed-1">
                    <thead class="text-white bg-secondary" style="font-size: 6.5pt;">
                        <tr>
                            <th class="text-center p-2" style="width: 200px; min-width: 200px;"><b>Machine</b></th>
                            <th class="text-center p-2" style="width: 100px; min-width: 100px;"><b>Type</b></th>
                            <th class="text-center p-2" style="width: 100px; min-width: 100px;"><b>Schedule</b></th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 9pt; max-height: 248px;">
                        @foreach ($unplanned as $row)
                        <tr>
                            <td class="text-justify p-1" style="width: 190px; min-width: 190px;">
                                <span class="font-weight-bold d-block">{{ $row->machine_code }} </span>
                                <span class="font-italic d-block text-dark" style="font-size: 7pt;">{{ $row->machine_name }}</span>
                                <span class="font-italic text-dark" style="font-size: 7pt;">Date Reported: {{ $row->date_reported }}</span>
                            </td>
                            <td class="text-center" style="width: 100px; min-width: 100px;">{{ $row->type }}</td>
                            <td class="text-center" style="width: 100px; min-width: 100px;">--</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>