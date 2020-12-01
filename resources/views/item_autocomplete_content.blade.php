<ul id="item-list" class="ul_list">
    @forelse($q as $item)
    @php
      if ($item->item_image_path) { 
        $img = "/img/" . $item->item_image_path;
      }else{
        $img = "/icon/no_img.png";
      }
      $img = 'http://athenaerp.fumaco.local/storage/' . $img;
    @endphp
    <li class="truncate selected-item" data-item-code="{{ $item->name }}" data-description="{!! $item->description !!}" data-img="{{ $img }}">
        <img src="{{ $img }}" style="float:left;width: 40px; height: 40px;margin-right: 10px;">
        <b>{{ $item->name }}</b>
        <br>{!! $item->description !!}
    </li>
    @empty
    <li class="no-hover" style="padding: 10px;">
        <center><b>No results found.</b></center>	
    </li>
    @endforelse 

        </ul>

        <style type="text/css">
          .truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border-bottom: 1px solid #ccc;
            padding: 10px;
            text-decoration: none;
            cursor: pointer;
          }
          .truncate:hover{
            background-color: #DCDCDC;
            color: #373D3F;
          }
          
          .no-hover:hover{
            background-color: #fff;
          }
          .ul_list{
            padding: 0;
            margin: 5px;
            font-size: 9pt;
            list-style-type: none;
            text-align: left;
          }
          
          </style>