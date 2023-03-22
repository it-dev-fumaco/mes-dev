@extends('layouts.user_app', [
  'namePage' => 'Delivery Schedule List',
  'activePage' => 'weekly_rejection_report',
  'pageHeader' => 'Delivery Schedule List',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-2" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
    <div class="col-12 m-0 bg-white border">
        @php
            $start = \Carbon\Carbon::now()->subDays(7);
            $end = \Carbon\Carbon::now();
            if(request()->has('date')){
                $start = \Carbon\Carbon::parse(explode(' - ', request('date'))[0]);
                $end = isset(explode(' - ', request('date'))[1]) ? \Carbon\Carbon::parse(explode(' - ', request('date'))[1]) : null;
            }
        @endphp
        <form action="/weekly_rejection_report" autocomplete="off">
            <div class="row p-0 m-0">
                <div class="p-1 mt-1 mb-1 col-5">
                    <div class="d-flex flex-row align-items-center">
                        <div class="col-5 pt-1 pl-2 pr-2 pb-1">
                            <input type="text" class='form-control rounded' id="daterange" name='date' />
                        </div>
                        <div class="col-2 p-1">
                            <button class="btn btn-primary btn-xs p-2 m-0 w-100" type="submit">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <table class="table table-bordered table-striped table-hover">
            <thead class="text-white bg-secondary font-weight-bold" style="font-size: 9px;">
                <th class="text-center p-2" style="width: 8%;">Ref. Order</th>
                <th class="text-center p-2" style="width: 12%;">Customer</th>
                <th class="text-center p-2" style="width: 32%;">Item Code</th>
                <th class="text-center p-2" style="width: 6%;">Qty</th>
                <th class="text-center p-2" style="width: 6%;">Delivered Qty</th>
                <th class="text-center p-2" style="width: 6%;">Produced Qty</th>
                <th class="text-center p-2" style="width: 8%;">Delivery Date</th>
                <th class="text-center p-2" style="width: 10%;">Rescheduled Del. Date</th>
                <th class="text-center p-2" style="width: 12%;">Owner</th>
            </thead>
            <tbody style="font-size: 12px;">
                @forelse($q as $r)
                @php
                    $feedbacked_qty = isset($production_qty[$r->name][$r->item_code]) ? $production_qty[$r->name][$r->item_code] : 0;
                    $owner = ucwords(str_replace('.', ' ', explode('@', $r->owner)[0]));
                @endphp
                <tr>
                    <td class="p-2 text-center font-weight-bold">{{ $r->name }}</td>
                    <td class="p-2 text-center">{{ $r->customer }}</td>
                    <td class="p-2 text-justify">
                        <b>{{ $r->item_code }}</b> {!! strip_tags($r->description) !!}
                    </td>
                    <td class="p-2 text-center">
                        <span class="d-block font-weight-bold">{{ number_format($r->qty) }}</span>
                        <small>{{ $r->uom }}</small>
                    </td>
                    <td class="p-2 text-center">
                        <span class="d-block font-weight-bold">{{ number_format($r->delivered_qty) }}</span>
                        <small>{{ $r->uom }}</small>
                    </td>
                    <td class="p-2 text-center">
                        <span class="d-block font-weight-bold">{{ number_format($feedbacked_qty) }}</span>
                        <small>{{ $r->uom }}</small>
                    </td>
                    <td class="p-2 text-center">{{ \Carbon\Carbon::parse($r->delivery_date)->format('M. d, Y') }}</td>
                    <td class="p-2 text-center">{{ $r->delivery_date != $r->rescheduled_delivery_date ? \Carbon\Carbon::parse($r->rescheduled_delivery_date)->format('M. d, Y') : '-' }}</td>
                    <td class="p-2 text-center">{{ $owner }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-uppercase text-muted">No result(s) found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="float-right mt-4">
            {!! $q->appends(request()->query())->links('pagination::bootstrap-4') !!}
        </div>
    </div>
</div>



@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />
<script>
$(document).ready(function(){
    var start_date = "{{ request('date') ? date('m/d/Y', strtotime(explode(' - ', request('date'))[0])) : null }}";
    var end_date = "{{ isset(explode(' - ', request('date'))[1]) ? date('m/d/Y', strtotime(explode(' - ', request('date'))[1])) : null }}";

    $('#daterange').daterangepicker({
        opens: 'left',
        placeholder: 'Select Date Range',
        startDate: start_date ? start_date : moment().subtract(7, 'days'),
        endDate: end_date ? end_date : moment(),
    });
});
</script>
@endsection