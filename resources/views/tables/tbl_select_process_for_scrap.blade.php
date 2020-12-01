

<div class="col-md-12">
	<ul class="steps steps-5" style="display:table; margin:0 auto;">
@forelse($q as $i => $process)
	 
	 	<li class="text-white selected-process-btn completed" data-process-id="{{ $i }}">
        	<table class="mt-4" style="width: 100%;">
        		<tr>
        			<td class="text-center"><h6 class="text-center p-0 m-2">{{ $process }}</h6></td>
        		</tr>
    		</table>
    	</li>
	
	
@empty
No Process found.
@endforelse
 </ul>


            <style>
                  .steps {
                    margin: 0;
                    padding: 0;
                    /*overflow: hidden;*/
                  }
           
                  .steps em {
                    display: block;
                    font-size: 1.1em;
                    font-weight: bold;
                  }
                  .steps li {
                    float: left;
                    margin-left: 25px;
                    margin-bottom: 10px;
                    width: 250px; /* 100 / number of steps */
                    height: 100px; /* total height */
                    list-style-type: none;
                    padding: 5px 5px 5px 30px; /* padding around text, last should include arrow width */
                    border-right: 3px solid white; /* width: gap between arrows, color: background of document */
                    position: relative;
                  }
                  /* remove extra padding on the first object since it doesn't have an arrow to the left */
                  /* .steps li:first-child {
                    padding-left: 5px;
                  } */
                  /* white arrow to the left to "erase" background (starting from the 2nd object) */
                  .steps li:nth-child(n+1)::before {
                    position: absolute;
                    top:0;
                    left:0;
                    display: block;
                    border-left: 25px solid white; /* width: arrow width, color: background of document */
                    border-top: 50px solid transparent; /* width: half height */
                    border-bottom: 50px solid transparent; /* width: half height */
                    width: 0;
                    height: 0;
                    content: " ";
                  }
                  /* colored arrow to the right */
                  .steps li::after {
                    z-index: 1; /* need to bring this above the next item */
                    position: absolute;
                    top: 0;
                    right: -25px; /* arrow width (negated) */
                    display: block;
                    border-left: 25px solid #7c8437; /* width: arrow width */
                    border-top: 50px solid transparent; /* width: half height */
                    border-bottom: 50px solid transparent; /* width: half height */
                    width:0;
                    height:0;
                    content: " ";
                  }
                  
                  /* Setup colors (both the background and the arrow) */
                  .steps li.in_progress { background-color: #D68910; }
                  .steps li.in_progress::after { border-left-color: #D68910; }

                  .steps li.completed { background-color: #28B463; }
                  .steps li.completed::after { border-left-color: #28B463; }

                  .steps li.pending { background-color: #C0392B; }
                  .steps li.pending::after { border-left-color: #C0392B; }
            </style>
</div>


<!-- operator_dashboard/M00010/Shearing/15256 -->
<!-- get_workstation_process_machine/{workstation}/{process_id} -->