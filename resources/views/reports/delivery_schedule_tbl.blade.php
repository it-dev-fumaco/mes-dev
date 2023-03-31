<div class="container-fluid">
@if ($arr)
    <div class="row bg-white pl-2" style="position: sticky; top: 0; z-index: 200;">
        <div class="col-2 mt-0 text-left p-2">
            <b>Delivery Date</b>
        </div>
        <div class="col-10 mt-0 p-0">
            <table class="table m-0" style="font-size: 9pt; border: none; table-layout: fixed;">
                <colgroup>
                    <col style="width: 9% !important;">     <!-- Reference -->
                    <col style="width: 12% !important;">    <!-- Production Order -->
                    <col style="width: 16% !important;">    <!-- Customer -->
                    <col style="width: 10% !important;">     <!-- Qty -->
                    <col style="width: 28% !important;">    <!-- Item -->
                    <col style="width: 15% !important;">    <!-- Previous Schedule -->
                    <col style="width: 10% !important;">     <!-- Action -->
                </colgroup>
                <tr>
                    <th class="p-1 text-center">Reference</th>
                    <th class="p-1 text-center">Production Order</th>
                    <th class="p-1 text-center">Customer</th>
                    <th class="p-1 text-center">Qty</th>
                    <th class="p-1 text-center">Item</th>
                    <th class="p-1 text-center">Previous Schedule</th>
                    <th class="p-1 text-center">Action</th>
                </tr>
            </table>
        </div>
    </div>   
@endif
@forelse ($arr as $delivery_date => $items_arr)
    <div class="row pl-2">
        <div class="col-2 mt-0 text-left p-2">
            <span style="font-size: 12pt; font-weight: 700">{{ Carbon\Carbon::parse($delivery_date)->format('M. d, Y') }}</span> <br>
            <span>{{ Carbon\Carbon::parse($delivery_date)->format('l') }}</span> <br>
            @if (Carbon\Carbon::parse($delivery_date)->endOfDay() < Carbon\Carbon::now()->startOfDay())
                <span style="color: #E42223">LATE DELIVERY</span>
            @elseif(Carbon\Carbon::parse($delivery_date)->startOfDay() == Carbon\Carbon::now()->startOfDay())
                <span style="color: #1F8C04">TODAY</span>
            @endif
        </div>
        <div class="col-10 mt-0 p-0">
            <table class="table table-striped" style="font-size: 8.5pt; table-layout: fixed;">
                <colgroup>
                    <col style="width: 9% !important;">     <!-- Reference -->
                    <col style="width: 12% !important;">    <!-- Production Order -->
                    <col style="width: 16% !important;">    <!-- Customer -->
                    <col style="width: 10% !important;">     <!-- Qty -->
                    <col style="width: 28% !important;">    <!-- Item -->
                    <col style="width: 15% !important;">    <!-- Previous Schedule -->
                    <col style="width: 10% !important;">     <!-- Action -->
                </colgroup>
                <tbody>
                    @foreach ($items_arr as $item)
                        <tr>
                            <td class="text-center pt-0 pb-0">
                                <b>{{ $item['reference'] }}</b>
                                @if ($item['rescheduled'])
                                <br>
                                <span class="badge badge-warning">Rescheduled</span>
                                @endif
                            </td>
                            <td class="text-center pt-0 pb-0">
                                @php
                                    switch ($item['status']) {
                                        case 'For Feedback':
                                        case 'Partially Feedbacked':
                                        case 'For Partial Feedback':
                                            $status_badge = '#22D3CC';
                                            break;
                                        case 'On Queue':
                                            $status_badge = '#17A2B8';
                                            break;
                                        case 'Feedbacked':
                                        case 'Completed':
                                            $status_badge = '#28A745';
                                            break;
                                        case 'Cancelled':
                                        case 'Closed':
                                        case 'Unknown Status':
                                            $status_badge = '#808495';
                                            break;
                                        case 'Material Issued':
                                            $status_badge = '#28A745';
                                            break;
                                        case 'Material For Issue':
                                        case 'Not Started':
                                            $status_badge = '#DC3545';
                                            break;
                                        case 'In Progress':
                                            $status_badge = '#FFC107';
                                            break;
                                        default:
                                            $status_badge = '#000';
                                            break;
                                    }
                                @endphp
                                <b>{{ $item['production_order'] }}</b><br>
                                <span class="badge" style="background-color: {{ $status_badge }}; color: {{ $item['status'] == 'In Progress' ? '#000' : '#fff' }}">{{ $item['status'] }}</span>
                            </td>
                            <td class="text-center pt-0 pb-0">
                                <b>{{ $item['project'] ? $item['project'] : '-' }}</b><br>
                                {{ $item['customer'] }}
                            </td>
                            <td class="text-center pt-0 pb-0">
                                <span style="font-size: 12pt; font-weight: 700">{{ number_format($item['qty_to_manufacture']) }}</span><br/>
                                {{ $item['uom'] }}
                            </td>
                            <td class="text-justify pt-0 pb-0 description-container" style="position: relative">
                                <b>{{ $item['item_code'] }}</b> - {{ \Illuminate\Support\Str::limit(strip_tags($item['description']), 150, $end='...') }}
                                <div class="container item-details-container bg-white">
                                    <div class="row p-0 border border-secondary">
                                        <div class="col-2 pt-1 pb-1" style="display: flex; justify-content: center; align-items: center;">
                                            <img src="{{ $item['image'] }}" class="w-100">
                                        </div>
                                        <div class="col-10 pt-1 pb-1">
                                            <b>{{ $item['item_code'] }}</b> - {{ strip_tags($item['description']) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center pt-0 pb-0">
                                @if ($item['rescheduled'])
                                    @foreach ($item['previous_delivery_dates'] as $previous_dates)
                                        {{ Carbon\Carbon::parse($previous_dates['previous_delivery_date'])->format('M. d, Y') }}
                                    @endforeach
                                @endif
                            </td>
                            <td class="pt-0 pb-0 text-center">
                                <div class="btn-group m-0">
                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 8pt;">Action </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item create-ste-btn" href="#" data-production-order="{{ $item['production_order'] }}" data-item-code="{{ $item['item_code'] }}">View Materials</a>
                                        <a class="dropdown-item resched-deli-btn" href="#" data-production-order="{{ $item['production_order'] }}">Reschedule Delivery Date</a>
                                        @if (!in_array($item['status'], ['Cancelled', 'Feedbacked']))
                                            @if ($item['status'] == 'Closed')
                                                <a class="dropdown-item re-open-production-btn" href="#"data-production-order="{{ $item['production_order'] }}">Re-open Production</a>
                                            @else
                                                <a class="dropdown-item close-production-btn" href="#"data-production-order="{{ $item['production_order'] }}">Close Production</a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="container text-center">
        <h4>No result(s) found.</h4>
    </div>
@endforelse
</div>
<style>
    .item-details-container{
        position: absolute;
        left: 20px;
        top: 15px;
        min-height: 50px;
        z-index: 9999;
        display: none;
        transition: .4s;
    }

    .description-container:hover > .item-details-container{
        display: block;
    }
</style>
<script>
    $('#date').val('{{ $date }}');
</script>