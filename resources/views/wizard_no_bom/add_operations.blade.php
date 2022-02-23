<table class="table table-hov1er table-bordered" border="1">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 60%;">
    <col style="width: 20%;">
    <thead>
        <thead class="text-white bg-secondary" style="font-size: 7pt;">
            <th class="text-center">Prod. Order</th>
            <th class="text-center" colspan="2"><b>Item Description</b></th>
            <th class="text-center"><b>Workstation / Process</b></th>
        </tr>
    </thead>
    <tbody style="font-size: 9pt;">
        @foreach ($production_orders as $production_order)
        <tr>
            <td class="text-center">{{ $production_order->name }}</td>
            <td class="text-center">
                @php
                $img = ($production_order->item_image_path) ? "/img/" . $production_order->item_image_path : "/icon/no_img.png";
                @endphp
                <a href="http://athenaerp.fumaco.local/storage/{{ $img }}" data-toggle="lightbox">
                    <img src="http://athenaerp.fumaco.local/storage/{{ $img }}" class="img-thumbnail" width="100">
                </a>
            </td>
            <td class="text-justify pl-3">
                <span class="item-code font-weight-bold"><b>{{ $production_order->production_item }}</b></span> - <span class="item-classification">{{ $production_order->item_classification }}</span><br>
                <span class="item-description">{!! $production_order->description  !!}</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-primary view-production-process pprocess" data-production-order="{{ $production_order->name }}" id="nobom{{ $production_order->name }}">
                    <i class="now-ui-icons ui-1_simple-add"></i> Update Process
                </button>
                <button type="button" rel="tooltip" class="btn btn-danger delete-row">
                    <i class="now-ui-icons ui-1_simple-remove"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>