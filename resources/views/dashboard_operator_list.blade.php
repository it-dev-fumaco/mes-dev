<span class="d-block font-weight-bold bg-dark text-white p-2 text-center text-uppercase" style="font-size: 10pt;">Operator(s)</span>
<div class="table-responsive">
    <table class="table table-striped table-bordered m-0">
        <col style="width: 18%;">
        <col style="width: 82%;">
        <tbody style="font-size: 8pt;">
            @forelse ($list as $row)
            <tr>
                <td class="p-1 text-center">
                    @if ($row['image'])
                    <img src="{{ ($row['image']) }}" alt="{{ $row['name'] }}" class="img-thumbnail" style="width: 45px; height: 45px;">
                    @else
                    <img src="{{ asset('/storage/no_img.png') }}" alt="{{ $row['name'] }}" class="img-thumbnail" style="width: 45px; height: 45px;">
                    @endif
                </td>
                <td class="p-2 align-middle">
                    <span class="d-block font-weight-bold">{{ $row['name'] }}</span>
                    <span class="badge {{ $row['status'] == 'busy' ? 'badge-warning' : 'badge-secondary' }}">{{ $row['status'] == 'busy' ? 'Busy' : 'Idle' }}</span>
                </td>
            </tr>
            @empty 
            <tr>
                <td colspan="2" class="text-center text-uppercase text-muted">No operator(s) found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
   .table-responsive {
        max-height:300px;
    }
</style>