
<table class="table table-bordered table-striped" style="font-size: 9pt;">
    <colgroup>
        <col style="width: 10%">
        <col style="width: 60%">
        <col style="width: 15%">
        <col style="width: 15%">
    </colgroup>
    <tr>
        <th class="text-center p-1" colspan="2">Item</th>
        <th class="text-center p-1">Actual Qty</th>
        <th class="text-center p-1">Floating Stocks</th>
    </tr>
    @forelse ($report as $item)
        @php
            if ($item->item_image_path) { 
                $img = "/img/" . $item->item_image_path;
            }else{
                $img = "/icon/no_img.png";
            }
            $img = 'http://athenaerp.fumaco.local/storage/' . $img;
        @endphp
        <tr>
            <td>
                <img class="img-thumbnail" src="{{ $img }}" alt="" class="w-100 mx-auto">
            </td>
            <td>
                <b>{{ $item->item_code }}</b> - {{ strip_tags($item->description) }}
            </td>
            <td class="text-center">
                <b>{{ number_format($item->actual_qty, 4) }}</b> <br>
                    <small>{{ $item->stock_uom }}</small>
            </td>
            <td class="text-center">
                <b>{{ number_format($item->floating_stocks, 4) }}</b> <br>
                    <small>{{ $item->stock_uom }}</small>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan=20 class="text-center">No result(s) found</th>
        </tr>
    @endforelse
</table>