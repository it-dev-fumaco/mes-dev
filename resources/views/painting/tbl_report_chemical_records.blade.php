
<table class="text-center table table-bordered table-responsive" style="width: 100%;" id="ceo">
                <col style="width: 9%;">
                <col style="width: 11%;">
                <col style="width: 10%;">
                <col style="width: 7%;">
                <col style="width: 8%;">
                <col style="width: 10%;">
                <col style="width: 8%;">
                <col style="width: 9%;">
                <col style="width: 8%;">
                <col style="width: 8%;">
                <col style="width: 12%;">
              <thead style="font-weight:bold; color:#e67e22;">
                <tr style="font-size:7pt;">
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 8px;" rowspan="3"><b>Date</b></th>
                  <th style="font-weight:bold;background-color: #f8c471;color:black;border: 1px solid black;line-height: 5px;padding: 7px 5px 6px 12px;" rowspan="2"><b>DEGREASING</b></th>
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 15px;" colspan="2" rowspan="2"><b>add 3.0 kg of FC-4349D to increase free akali by 1 point</b></th>
                  <th style="font-weight:bold;background-color: #f8c471;color:black;border: 1px solid black;line-height: 8px;" colspan="6"><b>PHOSPHATING</b></th>
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 8px;" rowspan="3"><b>Operator</b></th>
                </tr>
                <tr style="font-size:7pt;">
                  <th style="font-weight:bold;background-color: #f8c471;color:black;border: 1px solid black;line-height: 5px;"><b>PB3100R</b></th>
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 15px;" colspan="2" rowspan="1"><b>add 2.6 kg of PB-3100R to increase Total Acid by 1 point</b></th>
                  <th style="font-weight:bold;background-color: #f8c471;color:black;border: 1px solid black;line-height: 5px;" ><b>AC-131</b> </th>
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 15px;" colspan="2" rowspan="1"><b>add 0.23 kg of AC-131 to increase Accelerator by 1 point</b></th>
                </tr>
                <tr style="font-size:7pt;">
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 12px;font-weight: bold;"><b>Free AKALI(6.5-7.5)</b></th>
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 12px;"><b>Status</b></th>
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 12px;font-size: 10px;"><b>Increase/Decrease</b></th>
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 12px;font-weight: bold;"><b> Replenishing(16-20)</b></th>
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 12px;"><b>Status</b></th>
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 12px;font-size: 10px;"><b>Increase/Decrease</b></th>
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 12px;font-weight: bold;" ><b>Accelerator(6-9) </b></th>
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 12px;"><b>Status</b></th>
                  <th style="background-color: #fcf3cf;color:black;border: 1px solid black;line-height: 12px;font-size: 10px;"><b>Increase/Decrease</b></th>
                </tr>
                
              
            </thead>
            <tbody style="font-size:9pt;">
              @forelse($data as $rows)
                 <tr>
                    <td class="text-center align-middle">
                       {{$rows['chem_date']}}
                    </td>
                    <td class="text-center"><b>{{ $rows['degreasing_freealkali'] }}</b></td>
                    <td class="text-center">{{ $rows['degre_add_status'] }}</td>
                    <td class="align-top">{{ $rows['degrasing_point'] }}</td>
                    <td class="text-center"><b>{{ $rows['phospating_acid'] }}</b></td>
                    <td class="text-center">{{ $rows['phospating_increase_type'] }}</td>
                    <td class="align-top">{{ $rows['phospating_acid_point'] }}</td>
                    <td class="text-center"><b>{{ $rows['phospating_accelerator'] }}</b></td>
                    <td class="text-center">{{ $rows['accelerator_increase_type'] }}</td>
                    <td class="align-top">{{ $rows['accelerator_increase_point'] }}</td>
                    <td class="align-top">{{ $rows['operator_name'] }}</td>
                 </tr>
              @empty
                 <tr>
                    <td colspan="12" class="text-center" style="font-size: 11pt;">No Record Found</td>
                 </tr>
              @endforelse

            </tbody>
        </table>
        <style>
      #modified_table  td {
      border: 1px solid black;
      }
      #modified_table tr {
      border: 1px solid black;
      }
      #modified_table th{
      border: 1px solid black;
      }

        </style>



