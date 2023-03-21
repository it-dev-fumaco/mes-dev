@extends('layouts.user_app', [
  'namePage' => 'Production Order with Issued Stocks Discrepancy Report',
  'activePage' => 'stocks_transferred_but_none_in_wip',
  'pageHeader' => 'Production Order with Issued Stocks Discrepancy Report',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header">
</div>

<div class="row p-0" style="margin-top: -205px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
        <div class="col-12 mx-auto bg-white">
            <div class="row">
                <div class="col-12 mx-auto">
                    <div class="container-fluid">
                        <form action="/audit_report/stocks_transferred_but_none_in_wip" method="get">
                            <div class="row">
                                <div class="col-2 p-2">
                                    <input type="text" name="search" class="form-control rounded" placeholder="Search" value="{{ request('search') ? request('search') : null }}"/>
                                </div>
                                <div class="col-2 p-2">
                                    <select name="warehouse" class="form-control rounded">
                                        <option value="" {{ !request('warehouse') ? 'selected' : null }}>Select a Warehouse</option>
                                        @foreach ($filter_warehouses as $warehouse)
                                            <option value="{{ $warehouse }}" {{ request('warehouse') == $warehouse ? 'selected' : null }}>{{ $warehouse }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-1">
                                    <button type="submit" class="btn btn-sm btn-primary">Search</button>
                                </div>
                                <div class="offset-6 col-1">
                                    <h6 class="pt-2">Total: <span class="badge badge-primary" style="font-size: 11pt;">{{ $bin_arr->total() }}</span></h6>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table class="table table-bordered">
                        <thead style="font-size: 9px;">
                            <tr>
                                <th class="text-center text-uppercase font-weight-bold p-2" style="width: 100px;">Item Code</th>
                                <th class="text-center text-uppercase font-weight-bold p-2" style="width: 200px;">Warehouse</th>
                                <th class="text-center text-uppercase font-weight-bold p-2" style="width: 100px;">Actual Qty</th>
                                <th class="text-center text-uppercase font-weight-bold p-2" style="width: 100px;">Transferred Qty</th>
                                <th class="text-center text-uppercase font-weight-bold p-2" style="width: 600px;">Production Orders</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bin_arr as $po)
                            <tr>
                                <td class="text-center font-weight-bolder" style="font-size: 13px;">{{ $po['item_code'] }}</td>
                                <td class="text-center" style="font-size: 13px;">{{ $po['warehouse'] }}</td>
                                <td class="text-center" style="font-size: 13px;">
                                    <span class="d-block font-weight-bold">{{ ($po['actual_qty'] * 1) }}</span>
                                    <small class="d-block">{{ $po['uom'] }}</small>
                                </td>
                                <td class="text-center" style="font-size: 13px;">
                                    <span class="d-block font-weight-bold">{{ ($po['transferred_qty'] * 1) }}</span>
                                    <small class="d-block">{{ $po['uom'] }}</small>
                                </td>
                                <td class="text-center p-1">
                                    <table class="table table-bordered m-0">
                                        <thead class="text-uppercase" style="font-size: 8px;">
                                            <th class="font-weight-bold p-1" style="width: 100px;">Prod. Order</th>
                                            <th class="font-weight-bold p-1" style="width: 100px;">Required Qty</th>
                                            <th class="font-weight-bold p-1" style="width: 100px;">Transferred</th>
                                            <th class="font-weight-bold p-1" style="width: 100px;">Consumed</th>
                                            <th class="font-weight-bold p-1" style="width: 100px;">Date Created</th>
                                            <th class="font-weight-bold p-1" style="width: 100px;">Created By</th>
                                        </thead>
                                        <tbody style="font-size: 12px;">
                                        @foreach ($po['production_orders'] as $item)
                                            <tr>
                                                <td class="p-1">{{ $item->name }}</td>
                                                <td class="p-1">{{ $item->required_qty * 1 }}</td>
                                                <td class="p-1">{{ $item->transferred_qty * 1 }}</td>
                                                <td class="p-1">{{ $item->consumed_qty * 1 }}</td>
                                                <td class="p-1">{{ \Carbon\Carbon::parse($item->creation)->format('M. d, Y') }}</td>
                                                <td class="p-1">{{ explode('@', $item->owner)[0] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan=6 class="text-center">No item(s)</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="float-right mt-4">{!! $bin_arr->appends(request()->query())->links('pagination::bootstrap-4') !!}</div>
                </div>
            </div>
    </div>
</div>
@endsection
@section('script')
@endsection