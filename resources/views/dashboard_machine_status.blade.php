<div class="d-flex flex-row align-items-center">
    <div class="col-4 text-center p-2">
      <span class="d-block font-weight-bold" style="font-size: 11pt;">{{ $totals['total_machines_in_use'] }}</span>
      <small class="d-block text-muted">In Use</small>
    </div>
    <div class="col-4 text-center p-2">
      <span class="d-block font-weight-bold" style="font-size: 11pt;">{{ $totals['total_available'] }}</span>
      <small class="d-block text-muted">Available</small>
    </div>
    <div class="col-4 text-center p-0">
        <div class="skills_section m-0 p-0">
            <div class="skills-area m-0 p-0">
                <div class="single-ski1ll w-100 mb-1">
                    @php
                        if ($operation_id == 1) {
                            $div = 'fabrication-circlechart';
                        } else if ($operation_id == 2) {
                            $div = 'painting-circlechart';
                        } else {
                            $div = 'assembly-circlechart';
                        }
                    @endphp
                    <div class="circlechart" data-percentage="{{ $totals['percentage'] }}" id="{{ $div }}">
                        <svg class="circle-chart" viewBox="0 0 33.83098862 33.83098862">
                            <circle class="circle-chart__background" cx="16.9" cy="16.9" r="15.9"></circle>
                            <circle class="circle-chart__circle success-stroke" stroke-dasharray="92,100" cx="16.9" cy="16.9" r="15.9"></circle>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<span class="d-block font-weight-bold bg-dark text-white p-2 text-center text-uppercase" style="font-size: 10pt;">Machine / Workstation</span>
<div class="table-responsive">
    <table class="table table-striped table-bordered m-0">
        <col style="width: 18%;">
        <col style="width: 82%;">
        <tbody style="font-size: 8pt;">
            @forelse ($list as $row)
            <tr>
                <td class="p-1 text-center">
                    @if ($row->image)
                    <img src="{{ asset($row->image) }}" alt="{{ $row->machine_name }}" class="img-thumbnail" style="width: 45px; height: 45px;">
                    @else
                    <img src="{{ asset('/storage/no_img.png') }}" alt="{{ $row->machine_name }}" class="img-thumbnail" style="width: 45px; height: 45px;">
                    @endif
                </td>
                <td class="p-2">
                    <span class="font-weight-bold">{{ $row->machine_code }}</span> {{ $row->machine_name }}
                </td>
            </tr>
            @empty 
            <tr>
                <td colspan="2" class="text-center text-uppercase text-muted">No machine(s) found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<small class="d-block text-center p-2 text-uppercase"><a href="/maintenance_machine_list">- View All -</a></small>

<style>
   .table-responsive {
        max-height:300px;
    }
</style>

<script>
    $(function(){
        $('#{{ $div }}').circlechart();
    });
</script>