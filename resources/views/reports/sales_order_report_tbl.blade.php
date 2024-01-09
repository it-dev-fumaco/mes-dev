<table class="table table-bordered">
    <tr>
        <th class="text-center">Name</th>
        <th class="text-center">Created By</th>
        <th class="text-center">Created At</th>
        <th class="text-center">Customer</th>
        <th class="text-center">Status</th>
    </tr>
    @forelse ($sales_orders as $so)
        <tr>
            <td class="text-center">{{ $so->name }}</th>
            <td class="text-center">{{ $so->owner }}</th>
            <td class="text-center">{{ Carbon\Carbon::parse($so->creation)->format('M. d, Y - h:i A') }}</th>
            <td class="text-center">{{ $so->customer }}</th>
            <td class="text-center">{{ $so->status }}</th>
        </tr>
    @empty
        <tr>
            <td colspan=5 class="text-center">No result(s) found</th>
        </tr>
    @endforelse
</table>

<div class="row">
    <div class="col-6">
        <h5><b>Total: {{ $sales_orders->total() }}</b></h5>
    </div>
    <div class="col-6">
        <div class="text-center mt-2 table-paginate pull-right">{{ $sales_orders->links() }}</div>
    </div>
</div>
