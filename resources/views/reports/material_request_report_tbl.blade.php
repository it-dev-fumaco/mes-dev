<table class="table table-bordered">
    <tr>
        <th class="text-center">Name</th>
        <th class="text-center">Customer</th>
        <th class="text-center">Status</th>
        <th class="text-center">Created By</th>
        <th class="text-center">Created At</th>
    </tr>
    @forelse ($material_request as $mr)
    @php
        $created_by = explode('@', $mr->owner)[0];
        $created_by = str_replace('.', ' ', $created_by);
        $created_by = ucwords($created_by);
    @endphp
        <tr>
            <td class="text-center">{{ $mr->name }}</th>
            <td class="text-center">{{ $mr->customer }}</th>
            <td class="text-center">{{ $mr->status }}</th>
            <td class="text-center">{{ $created_by }}</th>
            <td class="text-center">{{ Carbon\Carbon::parse($mr->creation)->format('M. d, Y - h:i A') }}</th>
        </tr>
    @empty
        <tr>
            <td colspan=5 class="text-center">No result(s) found</th>
        </tr>
    @endforelse
</table>

<div class="row">
    <div class="col-6">
        <h5><b>Total: {{ $material_request->total() }}</b></h5>
    </div>
    <div class="col-6">
        <div class="text-center mt-2 table-paginate pull-right">{{ $material_request->links() }}</div>
    </div>
</div>
