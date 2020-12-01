@foreach(collect($list)->chunk(2) as $result)
      <div class="row">
        @foreach($result as $row)
        <div class="col-md-6">
          <div class="card">
            <div class="card-header {{ $row['status'] }}" style="height: 80px;">
              <h5 class="title text-white text-center" style="font-size: 25pt;">{{ $row['workstation_name'] }}</h5>
            </div>
            <div class="card-body p-0" style="min-height: 330px; background-color: #263238;">
              <div class="row text-center text-white pt-2 pb-0">
                <span style="position: absolute; font-size: 90pt; left: 46%; top: 16%;">/</span>
                <div class="col-md-6 text-right pr-5">
                  <span style="display: block; font-size: 50pt; font-weight: bold;">{{ number_format($row['actual']) }}</span>
                  <span class="font-weight-bold" style="letter-spacing: 1px; font-size: 15pt;">ACTUAL</span>
                </div>
                <div class="col-md-6 text-left pl-5">
                  <span style="display: block; font-size: 50pt; font-weight: bold;">{{ number_format($row['target']) }}</span>
                  <span class="font-weight-bold" style="letter-spacing: 1px; font-size: 15pt;">TARGET</span>
                </div>
                <div class="col-md-12 mtsd-4" style="margin-top: 30px;">
                  <table class="table mb-0 p-0" style="width: 100%;">
                    <tr style="background-color: #3498db;">
                      <td style="width: 33%;"><span class="font-weight-bold" style="letter-spacing: 1px; font-size: 15pt;">MAC UTZ</span></td>
                      <td style="width: 34%;"><span class="font-weight-bold" style="letter-spacing: 1px; font-size: 15pt;">QUALITY</span></td>
                      <td style="width: 33%;"><span class="font-weight-bold" style="letter-spacing: 1px; font-size: 14pt;">QA EFFICIENCY</span></td>
                    </tr>
                    <tr>
                      <td style="border-right: 2px solid;"><span style="display: block; font-size: 40pt; font-weight: bold;">{{ number_format((int)$row['machine_utilization'], 0, '.', '') }}%</span></td>
                      <td style="border-right: 2px solid;"><span style="display: block; font-size: 40pt; font-weight: bold;">{{ number_format((int)$row['quality'], 0, '.', '') }}%</span></td>
                      <td><span style="display: block; font-size: 40pt; font-weight: bold;">{{ number_format((int)$row['qa_efficiency'], 0, '.', '') }}%</span></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
     
      @endforeach