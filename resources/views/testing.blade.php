<table border="1" style="border-collapse: collapse">
    <tr>
       <td>Item Code</td>
       <td>Description</td>
       <td>Item Classification</td>
       <td colspan="6">Stock Level</td>
    </tr>
    @foreach ($arr as $item)
    <tr>
       <td rowspan="{{ count($item['ledger']) + 1 }}">{{ $item['item_code'] }}</td>
       <td rowspan="{{ count($item['ledger']) + 1 }}">{{ $item['description'] }}</td>
       <td rowspan="{{ count($item['ledger']) + 1 }}">{{ $item['item_classification'] }}</td>
    </tr>
    @foreach ($item['ledger'] as $s)
    <tr>
       <td>{{ $s->creation }}</td>
       <td>{{ $s->posting_date }}</td>
       <td>{{ $s->posting_time }}</td>
       <td>{{ $s->warehouse }}</td>
       <td>{{ $s->voucher_type }}</td>
       <td>{{ $s->voucher_no }}</td>
    </tr>

    @endforeach
    
        
    @endforeach
 </table>
 