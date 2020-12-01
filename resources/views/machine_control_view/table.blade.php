{{-- <table style="width: 100%;" border="">
  <tbody>
    <tr>
      <td class="fr-cell-fixed " style="width: 10.0000%;"><strong><span style="font-size: 24px;">7:00 AM</span></strong></td>
      <td style="width: 10.0000%;">7:01</td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
    </tr>
    <tr>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td style="width: 10.0000%;">
        <br>
      </td>
      <td class="fr-cell-handler " style="width: 10.0000%;">
        <br>
      </td>
    </tr>
  </tbody>
</table>
<p>
  <br>
</p>

<table border="1" style="width: 100%;">
@for($i = 360; $i <= 1260; $i++)
@php
$hours = floor($i / 60);
$minutes = ($i % 60);
$min = str_pad($minutes, 2, '0', STR_PAD_LEFT);

@endphp
<tr>
	<td>{{ $hours }}:{{ $min }}</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
@endfor
</table>
 --}}

<table border="1" style="width: 100%;">
	<tr>
		<td>TIME / MACHINE</td>
		@foreach($machines as $machine)
		<td>{{ $machine->machine_name }}</td>
		@endforeach
	</tr>
<tr>
	<td>6:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>6:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>7:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>8:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>9:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>10:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>11:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>12:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>13:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>14:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>15:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>16:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>17:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>18:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>19:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:01</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:02</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:03</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:04</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:05</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:06</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:07</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:08</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:09</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:10</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:11</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:12</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:13</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:14</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:15</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:16</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:17</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:18</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:19</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:20</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:21</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:22</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:23</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:24</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:25</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:26</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:27</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:28</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:29</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:30</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:31</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:32</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:33</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:34</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:35</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:36</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:37</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:38</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:39</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:40</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:41</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:42</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:43</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:44</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:45</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:46</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:47</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:48</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:49</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:50</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:51</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:52</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:53</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:54</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:55</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:56</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:57</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:58</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>20:59</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
<tr>
	<td>21:00</td>
	@foreach($machines as $machine)
		<td></td>
		@endforeach
</tr>
</table>
