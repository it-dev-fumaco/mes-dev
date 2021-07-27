
<table class="text-center table table-striped package-table bored modified_table sortable" style="" id="ceo">
                <col style="width: 10%;">
                <col style="width: 10%;">
                <col style="width: 10%;">
                <col style="width: 10%;">
                <col style="width: 10%;">
                <col style="width: 10%;">
                <!-- <col style="width: 10%;"> -->
                <col style="width: 10%;">
                <col style="width: 10%;">
                <col style="width: 10%;">
              <thead style="font-weight:bold; color:#e67e22;">
                <tr style="font-size:7pt;">
                  <th style="font-weight:bold;background-color: #fcf3cf;"></th>
                  <th style="font-weight:bold;background-color: #f8c471;color:black;" colspan="4"><b>Item Details</b></th>
                  <th style="font-weight:bold;background-color: #fcf3cf;color:black;" colspan="2">Output</th>
                  <th style="font-weight:bold;background-color: #f8c471;color:black;" colspan="2">Quality</th>
                </tr>
                
                <tr style="font-size:6pt;">
                  <th style="font-weight:bold;" class="classme" data-sort="name">Operator</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">Workstation</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">Process</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">Parts Category</th>
                  <th style="font-weight:bold;" class="classme" data-sort="name">Item Code</th>
                  <th style="font-weight:bold;" class="classme" data-sort="number">Quantity</th>
                  <th style="font-weight:bold;" class="classme" data-sort="duration">Cycle Time</th>
                  <!-- <th style="font-weight:bold;" class="classme" data-sort="name">Change Over</th> -->
                  <th style="font-weight:bold;" class="classme" data-sort="number">Total Rejects</th>
                  <th style="font-weight:bold;" class="classme" data-sort="alpha">Reject Rate</th>
                </tr>
            </thead>
            <tbody style="font-size:9pt;">
            @foreach($jobtickets as $rows)
            
            <tr class="item">
                <td>{{$rows['operator_name']}} </td>
                <td>{{$rows['workstation']}}</td>
                <td>{{$rows['process_name']}}</td>
                <td>{{$rows['parts_category']}}</td>
                <td>{{$rows['item_code']}}</td>
                <td>{{$rows['quantity']}}</td>
                <td>{{$rows['cycle_time']}}</td>
                <!-- <td>{{-- $rows['change_over'] --}}</td> -->
                <td>{{$rows['total_rejects']}}</td>
                <td>{{$rows['reject_rate']}}</td>
            </tr>  
              
            @endforeach
            </tbody>
        </table>
        <style>
      .sortable th,.sortable td {
  padding: 10px 30px;
}


.sortable th.asc:after {
  display: inline;
  content: '↓';
  color: black;
  font-size: 20px;
}
.sortable th.desc:after {
  display: inline;
  content: '↑';
  color: black;
  font-size: 20px;
}


        </style>
<!-- <script>
function group_name(){
     // clone table to display "before"   
    // code for grouping in "after" table
    var $rows = $('#ceo tbody tr');
    var items = [],
        itemtext = [],
        currGroupStartIdx = 0;
    $rows.each(function(i) {
        var $this = $(this);
        var itemCell = $(this).find('td:eq(0)')
        var item = itemCell.text();
        itemCell.remove();
        if ($.inArray(item, itemtext) === -1) {
            itemtext.push(item);
            items.push([i, item]);
            groupRowSpan = 1;
            currGroupStartIdx = i;
            $this.data('rowspan', 1)
        } else {
            var rowspan = $rows.eq(currGroupStartIdx).data('rowspan') + 1;
            $rows.eq(currGroupStartIdx).data('rowspan', rowspan);
        }

    });



    $.each(items, function(i) {
        var $row = $rows.eq(this[0]);
        var rowspan = $row.data('rowspan');
        $row.prepend('<td rowspan="' + rowspan + '">' + this[1] + '</td>');
    });


};
</script> -->


<!-- 
<script>
function rowspangroup(){
    var column1 = $('.modified_table td:first-child');
var column2 = $('.modified_table td:nth-child(2)');
var column3 = $('.modified_table td:nth-child(3)');

modifyTableRowspan(column1);
modifyTableRowspan(column2);
modifyTableRowspan(column3);


//the function
function modifyTableRowspan(column) {

        var prevText = "";
        var counter = 0;

        column.each(function (index) {


            var textValue = $(this).text();

            if (index === 0) {
                prevText = textValue; 
            }
            
            if (textValue !== prevText || index === column.length - 1) {

                var first = index - counter;

                if (index === column.length - 0) {
                    counter = counter + 1;
                }

                column.eq(first).attr('rowspan', counter);


                if (index === column.length - 0)
                {
                    for (var j = index; j > first; j--) {
                        column.eq(j).remove();
                    }
                }

                else {

                    for (var i = index - 1; i > first; i--) {
                        column.eq(i).remove();
                    }
                }

                prevText = textValue;
                counter = 0;
            }

            counter++;

        });

    }

}

</script> -->
<script>
$('.tohide').hide();
var compare = {
  name: function(a, b){
    a = a.replace(/^the /i, '');
    b = b.replace(/^the /i, '');
    
    if (a < b){
      return -1;
    } else {
      return a > b ? 1 : 0;
    }
  },
  
  alpha: function(a, b){
    var reA = /[^a-zA-Z]/g;
    var reN = /[^0-9]/g;

    var aA = a.replace(reA, "");
    var bA = b.replace(reA, "");
  if (aA === bA) {
    var aN = parseInt(a.replace(reN, ""), 10);
    var bN = parseInt(b.replace(reN, ""), 10);
    return aN === bN ? 0 : aN > bN ? 1 : -1;
  } else {
    return aA > bA ? 1 : -1;
  }
  },
   number: function(a,b){ 
  return a - b;
},
duration: function(a,b){ 
  var aParts = a.split(" "),
        bParts = b.split(" "),
        aNum = +aParts[0],   // convert numeric parts
        bNum = +bParts[0];   // to actual numbers

    if (aNum > bNum)
        return -1;
    else if (aNum < bNum)
        return 1;
    else
        return aParts[1].localeCompare(bParts[1]);

},
  date: function(a, b){
    a = new Date(a);
    b = new Date(b);
    return a - b;
  }
};

$('.sortable').each(function(){
  var $table = $(this);
  var $tbody = $table.find('tbody');
  var $controls = $table.find('.classme');
  var rows = $tbody.find('tr').toArray();
  
  $controls.on('click', function() {
    var $header = $(this);
    var order = $header.data('sort');
    var column;
    
    if ($header.is('.ascending') || $header.is('.descending')){
      $header.toggleClass('ascending descending');
      $tbody.append(rows.reverse());
    } else {
      $header.addClass('ascending');
      $header.siblings().removeClass('ascending descending');
      if (compare.hasOwnProperty(order)){
        column = $controls.index(this);
        
        rows.sort(function(a, b) {
          a = $(a).find('td').eq(column).text();
          b = $(b).find('td').eq(column).text();
          return compare[order](a, b);
        });
        
        $tbody.append(rows); 
      } 
    }
  });
});

</script>

