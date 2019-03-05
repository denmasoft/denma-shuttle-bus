<?php
<form name="recurring-blockoff-form" id="recurring-blockoff-form" method="POST">

	<table class="rec">
	    <tbody>
		<tr>
		    <th>Starts on:</th>
		    <td>
			<input id="rstart" value="2016-08-18" style="width:100px;" size="10" autocomplete="off" />
		    </td>
		</tr>
		<tr>
		    <th>Repeats:</th>
		    <td>
			<select id="frequency" name="frequency" title="">
			    <option value="0" title="Daily">Daily</option>
			    <option value="1" title="Every weekday (Monday to Friday)">Every weekday (Monday to Friday)</option>
			    <option value="2" title="Every Monday, Wednesday, and Friday">Every Monday, Wednesday, and Friday</option>
			    <option value="3" title="Every Tuesday and Thursday">Every Tuesday and Thursday</option>
			    <option value="4" title="Weekly">Weekly</option>
			    <option value="5" title="Monthly">Monthly</option>
			    <option value="6" title="Yearly">Yearly</option>
			</select>
		    </td>
		</tr>

		<tr class="repeat_feature repeat_every">
		    <th>Repeat every:</th>
		    <td>
			<select id="interval" name="interval"  style="width:55px;">
	<option value="1" selected="selected">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8">8</option>
	<option value="9">9</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	<option value="13">13</option>
	<option value="14">14</option>
	<option value="15">15</option>
	<option value="16">16</option>
	<option value="17">17</option>
	<option value="18">18</option>
	<option value="19">19</option>
	<option value="20">20</option>
	<option value="21">21</option>
	<option value="22">22</option>
	<option value="23">23</option>
	<option value="24">24</option>
	<option value="25">25</option>
	<option value="26">26</option>
	<option value="27">27</option>
	<option value="28">28</option>
	<option value="29">29</option>
	<option value="30">30</option>
</select>
			<span id="repeat_every_label"></span>
		    </td>
		</tr>

		<tr class="repeat_feature weekly">
		    <th>Repeat on:</th>
		    <td id="repeat_on_checkboxes">
			<div>
			    <span class="rec-dow" style="float:left;margin: 0 5px;">
			    <input id="dow0" type="checkbox" title="Sunday" name="repeat_on_days" value="SU" style="float:left;" />
			    <label title="Sunday" for="dow0" style="float:left;">&nbsp;S</label>
			    </span>
			    <span class="rec-dow" style="float:left;margin: 0 5px;">
			    <input id="dow1" type="checkbox" title="Monday" name="repeat_on_days" value="MO" style="float:left;"  />
			    <label title="Monday" for="dow1" style="float:left;">&nbsp;M</label>
			    </span>
			    <span class="rec-dow" style="float:left;margin: 0 5px;">
			    <input id="dow2" type="checkbox" title="Tuesday" name="repeat_on_days" value="TU" style="float:left;"  />
			    <label title="Tuesday" for="dow2" style="float:left;">&nbsp;T</label>
			    </span>
			    <span class="rec-dow" style="float:left;margin: 0 5px;">
			    <input id="dow3" type="checkbox" title="Wednesday" name="repeat_on_days" value="WE" style="float:left;"  />
			    <label title="Wednesday" for="dow3" style="float:left;">&nbsp;W</label>
			    </span>
			    <span class="rec-dow" style="float:left;margin: 0 5px;">
			    <input id="dow4" type="checkbox" title="Thursday" name="repeat_on_days" value="TH" style="float:left;" checked="checked"  />
			    <label title="Thursday" for="dow4" style="float:left;">&nbsp;T</label>
			    </span>
			    <span class="rec-dow" style="float:left;margin: 0 5px;">
			    <input id="dow5" type="checkbox" title="Friday" name="repeat_on_days" value="FR" style="float:left;"  />
			    <label title="Friday" for="dow5" style="float:left;">&nbsp;F</label>
			    </span>
			    <span class="rec-dow" style="float:left;margin: 0 5px;">
			    <input id="dow6" type="checkbox" title="Saturday" name="repeat_on_days" value="SA" style="float:left;"  />
			    <label title="Saturday" for="dow6" style="float:left;">&nbsp;S</label>
			    </span>
			</div>
		    </td>
		</tr>

		<tr class="repeat_feature monthly">
		    <th>Repeat by:</th>
		    <td>
			<span class="" style="float:left;">
			<input id="domrepeat1" type="radio" title="Repeat by day of the month" value="domrepeat" checked="checked" name="repeatby" style="float:left;"/ >
			<label title="Repeat by day of the month" for="domrepeat1" style="width:140px;">&nbsp;day of the month</label>
			</span>
			<span class="" style="float:left;">
			<input id="dowrepeat2" type="radio" title="Repeat by day of the week" value="dowrepeat" name="repeatby" style="float:left;" />
			<label title="Repeat by day of the week" for="dowrepeat2" style="width:140px;">&nbsp;day of the week</label>
			</span>
		    </td>
		</tr>

		<tr>
		    <th>Start Time:</th>
		    <td>
			<select id="start_hr" name="start_hr" style="width:55px;height:25px;margin-bottom:0;">
	<option value="00" selected="selected">00</option>
	<option value="01">01</option>
	<option value="02">02</option>
	<option value="03">03</option>
	<option value="04">04</option>
	<option value="05">05</option>
	<option value="06">06</option>
	<option value="07">07</option>
	<option value="08">08</option>
	<option value="09">09</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	<option value="13">13</option>
	<option value="14">14</option>
	<option value="15">15</option>
	<option value="16">16</option>
	<option value="17">17</option>
	<option value="18">18</option>
	<option value="19">19</option>
	<option value="20">20</option>
	<option value="21">21</option>
	<option value="22">22</option>
	<option value="23">23</option>
</select>
<span class="dot">:</span>
			<select id="start_min" name="start_min" style="width:55px;height:25px;margin-bottom:0;">
	<option value="00" selected="selected">00</option>
	<option value="01">01</option>
	<option value="02">02</option>
	<option value="03">03</option>
	<option value="04">04</option>
	<option value="05">05</option>
	<option value="06">06</option>
	<option value="07">07</option>
	<option value="08">08</option>
	<option value="09">09</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	<option value="13">13</option>
	<option value="14">14</option>
	<option value="15">15</option>
	<option value="16">16</option>
	<option value="17">17</option>
	<option value="18">18</option>
	<option value="19">19</option>
	<option value="20">20</option>
	<option value="21">21</option>
	<option value="22">22</option>
	<option value="23">23</option>
	<option value="24">24</option>
	<option value="25">25</option>
	<option value="26">26</option>
	<option value="27">27</option>
	<option value="28">28</option>
	<option value="29">29</option>
	<option value="30">30</option>
	<option value="31">31</option>
	<option value="32">32</option>
	<option value="33">33</option>
	<option value="34">34</option>
	<option value="35">35</option>
	<option value="36">36</option>
	<option value="37">37</option>
	<option value="38">38</option>
	<option value="39">39</option>
	<option value="40">40</option>
	<option value="41">41</option>
	<option value="42">42</option>
	<option value="43">43</option>
	<option value="44">44</option>
	<option value="45">45</option>
	<option value="46">46</option>
	<option value="47">47</option>
	<option value="48">48</option>
	<option value="49">49</option>
	<option value="50">50</option>
	<option value="51">51</option>
	<option value="52">52</option>
	<option value="53">53</option>
	<option value="54">54</option>
	<option value="55">55</option>
	<option value="56">56</option>
	<option value="57">57</option>
	<option value="58">58</option>
	<option value="59">59</option>
</select>
		    </td>
		</tr>
		<tr>
		    <th>End Time:</th>
		    <td>
			<select id="end_hr" name="end_hr" style="width:55px;height:25px;margin-bottom:0;">
	<option value="00" selected="selected">00</option>
	<option value="01">01</option>
	<option value="02">02</option>
	<option value="03">03</option>
	<option value="04">04</option>
	<option value="05">05</option>
	<option value="06">06</option>
	<option value="07">07</option>
	<option value="08">08</option>
	<option value="09">09</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	<option value="13">13</option>
	<option value="14">14</option>
	<option value="15">15</option>
	<option value="16">16</option>
	<option value="17">17</option>
	<option value="18">18</option>
	<option value="19">19</option>
	<option value="20">20</option>
	<option value="21">21</option>
	<option value="22">22</option>
	<option value="23">23</option>
</select>
<span class="dot">:</span>
			<select id="end_min" name="end_min" style="width:55px;height:25px;margin-bottom:0;">
	<option value="00" selected="selected">00</option>
	<option value="01">01</option>
	<option value="02">02</option>
	<option value="03">03</option>
	<option value="04">04</option>
	<option value="05">05</option>
	<option value="06">06</option>
	<option value="07">07</option>
	<option value="08">08</option>
	<option value="09">09</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	<option value="13">13</option>
	<option value="14">14</option>
	<option value="15">15</option>
	<option value="16">16</option>
	<option value="17">17</option>
	<option value="18">18</option>
	<option value="19">19</option>
	<option value="20">20</option>
	<option value="21">21</option>
	<option value="22">22</option>
	<option value="23">23</option>
	<option value="24">24</option>
	<option value="25">25</option>
	<option value="26">26</option>
	<option value="27">27</option>
	<option value="28">28</option>
	<option value="29">29</option>
	<option value="30">30</option>
	<option value="31">31</option>
	<option value="32">32</option>
	<option value="33">33</option>
	<option value="34">34</option>
	<option value="35">35</option>
	<option value="36">36</option>
	<option value="37">37</option>
	<option value="38">38</option>
	<option value="39">39</option>
	<option value="40">40</option>
	<option value="41">41</option>
	<option value="42">42</option>
	<option value="43">43</option>
	<option value="44">44</option>
	<option value="45">45</option>
	<option value="46">46</option>
	<option value="47">47</option>
	<option value="48">48</option>
	<option value="49">49</option>
	<option value="50">50</option>
	<option value="51">51</option>
	<option value="52">52</option>
	<option value="53">53</option>
	<option value="54">54</option>
	<option value="55">55</option>
	<option value="56">56</option>
	<option value="57">57</option>
	<option value="58">58</option>
	<option value="59">59</option>
</select>
		    </td>
		</tr>
		<tr>
		    <th class="rec-ends-th">Ends:</th>
		    <td>
			<span class="rec-ends-opt">
			    <input id="endson_never" type="radio" title="Ends never" name="endson" checked="checked" value="endson_never" style="float:left;" />
			    <label title="Ends never" for="endson_never" style="width:80px;">&nbsp;Never</label>
			</span>
			<span class="rec-ends-opt">
			    <input id="endson_count" type="radio" title="Ends after a number of occurrences" name="endson" value="endson_count" style="float:left;" />
			    <label title="Ends after a number of occurrences" for="endson_count" style="width:200px;">
			    &nbsp;After
<input id="endson_count_input" name="endson_count_input" disabled="disabled" style="width:50px;" title="Occurrences" value="" size="3" />
			    occurrences
			    </label>
			</span>
			<span class="rec-ends-opt">
			    <input id="endson_until" type="radio" title="Ends on a specified date" name="endson" value="endson_until" style="float:left;" />
			    <label title="Ends on a specified date" for="endson_until" style="width:200px;">
			    &nbsp;On
<input id="endson_until_input" name="endson_until_input" disabled="disabled" style="width:100px;" title="Specified date" value="2016-08-18" size="10" autocomplete="off" />
			    </label>
			</span>
		    </td>
		</tr>
		<tr>
		    <th>Summary:</th>
		    <td class="rec-summary"></td>
		</tr>
	    </tbody>
	</table>

	<!-- Allow form submission with keyboard without duplicating the dialog button -->
	<input type="hidden" name="action" id="action" value="add" />
	<input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
  </form>