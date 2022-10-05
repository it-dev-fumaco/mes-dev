<div class="card shadow-none border">
    <div class="card-header pt-2 pl-3 pr-3 pb-2 bg-danger">
      <h6 class="text-white font-weight-bold text-center m-0 rounded-top" style="font-size: 10.5pt;">Rejection <span class="badge badge-primary pull-right">{{ count($list) }}</span></h6>
    </div>
    <div class="card-body p-0" style="min-height: 100px;">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table {{ count($list) > 0 ? 'table-striped' : '' }} m-0">
                <col style="width: 30%;">
                <col style="width: 20%;">
                <col style="width: 50%;">
                <tbody style="font-size: 8pt;">
                    @forelse ($list as $row)
                    <tr>
                        <td class="p-1 text-center font-weight-bold">{{ $row->workstation }}</td>
                        <td class="p-1 text-center font-weight-bold">{{ number_format($row->rejected_qty) }}</td>
                        <td class="p-1 text-center">
                            <span class="d-block font-weight-bold">{{ $row->reject_reason }}</span>
                            <small class="d-block">{{ $row->created_by }}</small>
                        </td>
                    </tr>
                    @empty 
                    <tr>
                        <td colspan="3" class="text-center text-uppercase text-muted">No rejection(s) reported</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>