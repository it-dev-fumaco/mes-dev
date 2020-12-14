


<div class="row">
    <div class="col-md-12">
        <h5 class="font-weight-bold mb-0">{{ $bundle_details->item_code }}</h5>
        <p class="text-justify">{{ $bundle_details->description }}</p>
    </div>
    <div class="col-md-12">
        <table class="table table-striped table-bordered" style=" font-size: 9pt;">
            <thead class="text-primary">
                <th class="text-center"><b>No.</b></th>
                <th class="text-center" colspan="2"><b>Item Description</b></th>
                <th class="text-center"><b>Quantity</b></th>
            </thead>
            <tbody>
                @foreach($components_arr as $row)
                <tr>
                    <td class="text-center">{{ $row['idx'] }}</td>
                    <td class="text-center">
                        <a href="{{ $row['image'] }}" data-toggle="lightbox">
                            <img src="{{ $row['image'] }}" class="img-thumbnail" width="100">
                        </a>
                    </td>
                    <td class="text-justify"><span class="font-weight-bold">{{ $row['item_code'] }}</span> - {{ $row['description'] }}</td>
                    <td class="text-center">
                        <span class="d-block font-weight-bold">{{ $row['qty'] * 1 }}</span>
                        <span>{{ $row['uom'] }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>