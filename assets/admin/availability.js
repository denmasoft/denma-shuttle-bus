function generateSummary()
{
    var summary = '',
        frequency = tbjQuery('#frequency').val(),
        interval = tbjQuery('#interval').val(),
        rstart = tbjQuery('#rstart').val(),
        start_hr = tbjQuery('#start_hr').val(),
        start_min = tbjQuery('#start_min').val(),
        end_hr = tbjQuery('#end_hr').val(),
        end_min = tbjQuery('#end_min').val();

    if(frequency==0){ // daily
        if(interval==1){
            summary += 'Daily';
        }
        else {
            summary += 'Every '+interval+' days';
        }
    }
    else if(frequency==1){ // Every weekday (Monday to Friday)
        summary += 'Weekly on weekdays';
    }
    else if(frequency==2){ // Every Monday, Wednesday, and Friday
        summary += 'Weekly on Monday, Wednesday, Friday';
    }
    else if(frequency==3){ // Every Tuesday and Thursday
        summary += 'Weekly on Tuesday, Thursday';
    }
    else if(frequency==4){ // weekly
        if(interval==1){
            summary += 'Weekly';
        }
        else {
            summary += 'Every '+interval+' weeks';
        }
        summary += ' on ';
        if(tbjQuery('#repeat_on_checkboxes').find('input[name="repeat_on_days"]:checked').length==7){
            summary += 'all days';
        }
        else {
            tbjQuery('#repeat_on_checkboxes').find('input[name="repeat_on_days"]:checked').each(function(){
                summary += tbjQuery(this).attr('title')+', ';
            })
            summary = summary.replace(/,\s*$/, "");
        }
    }
    else if(frequency==5){ // monthly
        if(interval==1){
            summary += 'Monthly';
        }
        else {
            summary += 'Every '+interval+' months';
        }

        var repeat_by = tbjQuery('[name="repeatby"]:checked').val();

        if(repeat_by=='domrepeat'){
            var d = rstart.substring(8, 10);
            summary += ' on day '+d;
        }
        else if(repeat_by=='dowrepeat'){
            var y = rstart.substring(0, 4),
                m = rstart.substring(5, 7),
                d = rstart.substring(8, 10),
                dateObj = new Date(y, parseInt(m)-1, d),
                locale = "en-us",
                weekday = dateObj.toLocaleString(locale, { weekday: "long" });

            var dayOfMonth = dateObj.getDay(),
                month = dateObj.getMonth(),
                year = dateObj.getFullYear(),
                checkDate = new Date(year, month, dateObj.getDate()),
                checkDateTime = checkDate.getTime(),
                currentWeek = 0;

            for (var i = 1; i < 32; i++) {
                var loopDate = new Date(year, month, i);
                if (loopDate.getDay() == dayOfMonth) {
                    currentWeek++;
                }
                if (loopDate.getTime() == checkDateTime) {
                    weekOfMonth = currentWeek;
                    //console.log(rstart+' - '+currentWeek);
                }
            }

            if(weekOfMonth==1){
                weekOfMonth = 'first';
            }
            else if(weekOfMonth==2){
                weekOfMonth = 'second';
            }
            else if(weekOfMonth==3){
                weekOfMonth = 'third';
            }
            else if(weekOfMonth==4){
                weekOfMonth = 'fourth';
            }
            else if(weekOfMonth==5){
                weekOfMonth = 'fifth';
            }
            else {
                weekOfMonth = 'last';
            }

            summary += ' on the '+weekOfMonth+' '+weekday;
        }
    }
    else if(frequency==6){ // yearly
        if(interval==1){
            summary += 'Annually';
        }
        else {
            summary += 'Every '+interval+' years';
        }

        var y = rstart.substring(0, 4),
            m = rstart.substring(5, 7),
            d = rstart.substring(8, 10),
            dateObj = new Date(y, parseInt(m)-1, d),
            locale = "en-us",
            month = dateObj.toLocaleString(locale, { month: "long" });

        summary += ' on '+month+' '+d;
    }

    var y = rstart.substring(0, 4),
        m = rstart.substring(5, 7),
        d = rstart.substring(8, 10),
        dateObj = new Date(y, parseInt(m)-1, d),
        locale = "en-us",
        month = dateObj.toLocaleString(locale, { month: "short" });

    summary += ', starts on '+month+' '+d+', '+y+' from '+start_hr+':'+start_min+' to '+end_hr+':'+end_min;

    var endson = tbjQuery('[name="endson"]:checked').val();
    var endson_count_input = tbjQuery('input[name="endson_count_input"]').val();
    var endson_until_input = tbjQuery('input[name="endson_until_input"]').val();

    if(endson=='endson_never'){

    }
    else if(endson=='endson_count'){
        if(endson_count_input){
            summary += ', '+parseInt(endson_count_input)+' times';
        }
    }
    else if(endson=='endson_until'){
        if(endson_until_input){
            var y = endson_until_input.substring(0, 4),
                m = endson_until_input.substring(5, 7),
                d = endson_until_input.substring(8, 10),
                dateObj = new Date(y, parseInt(m)-1, d),
                locale = "en-us",
                month = dateObj.toLocaleString(locale, { month: "short" });
            summary += ', until '+month+' '+d+', '+y;
        }
    }

    tbjQuery('.rec-summary').html('<strong>'+summary+'</strong>');
}

tbjQuery(document).ready(function(){

    tbjQuery('input[name="use_in_address"]').click(function(){
        if(tbjQuery(this).val()==1){ // YES
            tbjQuery('input[name="unit_price_override"]').closest('div.inputWrap').show();
        }
        else {
            tbjQuery('input[name="unit_price_override"]').closest('div.inputWrap').hide();
        }
    })
    tbjQuery('input[name="unit_price_override"]').click(function(){
        if(tbjQuery(this).val()==1){ // YES
            tbjQuery('div.price_override_feature').show();
            tbjQuery('input:radio[name="price_calculation_cumulative"][value="0"]').prop('checked', true);
            tbjQuery('input:radio[name="price_calculation_cumulative"][value="1"]').prop('checked', false);
            tbjQuery('div.non_cumulative_surcharge_date_wrapper').parent().show();
            tbjQuery('div.cumulative_surcharge_date_wrapper').parent().hide();
        }
        else {
            tbjQuery('div.price_override_feature').hide();
        }
    })
    tbjQuery('input[name="use_tariff"]').click(function(){
        if(tbjQuery(this).val()==1){ // use tariff YES will hide unit price, remove all previous distance sector
            tbjQuery('input[name="unit_price"]').parent().hide();
            tbjQuery('input[name="charge_per_min"]').parent().hide();
        }
        else {
            tbjQuery('input[name="unit_price"]').parent().show();
            tbjQuery('input[name="charge_per_min"]').parent().show();
        }
        tbjQuery('div.cumulative_surcharge_date_wrapper, div.non_cumulative_surcharge_date_wrapper').find("div.date_price:not(:first-child)").remove();
    })
    tbjQuery('input[name="google_calendar_enabled"]').click(function(){
        if(tbjQuery(this).val()==1){
            tbjQuery('div.google_calendar_feature').show();
        }
        else {
            tbjQuery('div.google_calendar_feature').hide();
        }
    })
    tbjQuery('input[name="enabled_without_driver"]').click(function(){
        if(tbjQuery(this).val()==1){
            tbjQuery('#assigned_driver').parent().show();
        }
        else {
            tbjQuery('#assigned_driver').parent().hide();
        }
    })
    tbjQuery('input[name="price_calculation_cumulative"]').click(function(){
        if(tbjQuery(this).val()==1){ // cumulative
            tbjQuery('div.non_cumulative_surcharge_date_wrapper').parent().hide();
            tbjQuery('div.cumulative_surcharge_date_wrapper').parent().show();
        }
        else {
            tbjQuery('div.non_cumulative_surcharge_date_wrapper').parent().show();
            tbjQuery('div.cumulative_surcharge_date_wrapper').parent().hide();
        }
    })
    tbjQuery('input[name="hourly_hire_enabled"]').click(function(){
        if(tbjQuery(this).val()==1){ // YES
            tbjQuery('div.hourly_feature').show();
        }
        else {
            tbjQuery('div.hourly_feature').hide();
        }
    })
    tbjQuery('#add_price').click(function(){
        var count = tbjQuery('div.non_cumulative_surcharge_date_wrapper div.date_price').length;
        var html = '';
        var i = count-1;

        html += '<div class="date_price row'+i+' clearfix">'+
            '<div class="inputwrap clearfix">'+
            '<label>'+
            '<span class="hasTip control-label" title="'+"Insert the first number of the distance sector. Remember that the first sector has to start with 0 not 1 so you don't miss 0 to 1 price."+'">'+
            'Distancia Desde:'+
            '</span>'+
            '</label>'+
            '<input class="inputbox distance_min" type="text" name="distance_min['+i+']" size="10" value="" />'+
            '</div>'+
            '<div class="inputwrap clearfix">'+
            '<label>'+
            '<span class="hasTip control-label" title="'+"Insert the last number of the distance sector. Use this number as a first number of the next sector you create."+'">'+
            'Distancia Hasta:'+
            '</span>'+
            '</label>'+
            '<input class="inputbox" type="text" name="distance_max['+i+']" size="10" value="" />'+
            '</div>';

// if use tariff YES, show separate price inputbox for each assigned tarrifs
// if NO, show one Price box
        if(tbjQuery('input[name="use_tariff"]:checked').val()==1 && parseInt(assigned_tariffs) > 0){
        }
        else {
            html +=	    '<div class="inputwrap clearfix">'+
                '<label>'+
                '<span class="hasTip control-label" title="You can have Price per unit distance (mile/kilometre) or Flat price for created distance sector.">'+
                'Price structure:'+
                '</span>'+
                '</label>'+
                '<div class="controls">'+
                '<label id="price_structure['+i+']unit_price-lbl" class="radio" for="price_structure['+i+']unit_price">'+
                '<input name="price_structure['+i+']" id="price_structure['+i+']unit_price" class="inputbox jform_price_structure" type="radio" checked="checked" value="unit_price"  />'+
                'Per unit distance'+
                '</label>'+
                '<label id="price_structure['+i+']flat_rate-lbl" class="radio" for="price_structure['+i+']flat_rate">'+
                '<input name="price_structure['+i+']" id="price_structure['+i+']flat_rate" class="inputbox jform_price_structure" type="radio" value="flat_rate" />'+
                'Flat rate'+
                '</label>'+
                '</div>'+
                '</div>';

            html +=	    '<div class="inputwrap unit_price_wrap clearfix">'+
                '<label>'+
                '<span class="hasTip control-label" title="'+"Price per Unit distance for this sector."+'">'+
                'Precio:'+
                '</span>'+
                '</label>'+
                '<input class="inputbox" type="text" name="prices['+i+']" size="10" value="" />'+
                '</div>';

            html +=	    '<div class="inputwrap flat_price_wrap clearfix" style="display:none;">'+
                '<label>'+
                '<span class="hasTip control-label" title="Flat rate for any journey falling in this distance sector.<br>Example: 0 to 15 miles $30, any journey that is between 0 and 15 miles long will cost $30.">'+
                'Flat rate:'+
                '</span>'+
                '</label>'+
                '<input class="inputbox" type="text" name="flat_price['+i+']" size="10" value="" />'+
                '</div>';
        }

        html +=	    '<div class="inputwrap clearfix">'+
            '<label>'+
            '<span class="hasTip control-label" title="'+"Flat rate discount on the Outbound journey if it falls within this distance sector. Negative numbers allowed to Add instead of Subtract discount."+'">'+
            'Outbound Discount:'+
            '</span>'+
            '</label>'+
            '<input class="inputbox" type="text" name="outbound_discount['+i+']" size="10" value="" />'+
            '</div>'+
            '<div class="inputwrap clearfix">'+
            '<label>'+
            '<span class="hasTip control-label" title="'+"Flat rate discount on Return journey if it falls within this distance sector. Negative numbers allowed to Add instead of Subtract discount."+'">'+
            'Return Discount:'+
            '</span>'+
            '</label>'+
            '<input class="inputbox" type="text" name="return_discount['+i+']" size="10" value="" />'+
            '</div>'+
            '<div class="button2-left">'+
            '<div class="blank">'+
            '<a href="javascript:void(0);" class="btn btn-small remove_price">Remove</a>'+
            '</div>'+
            '</div>'+
            '</div>';

        tbjQuery('div.non_cumulative_surcharge_date_wrapper').append(html);
        tbjQuery('html, body').animate({ scrollTop: tbjQuery('div.non_cumulative_surcharge_date_wrapper div.date_price:last-child').offset().top-80 }, 'fast');
        tbjQuery('div.non_cumulative_surcharge_date_wrapper div.date_price:last-child .distance_min').focus();

        $$('.hasTip').each(function(el) {
            var title = el.get('title');
            if (title) {
                var parts = title.split('::', 2);
                el.store('tip:title', parts[0]);
                el.store('tip:text', parts[1]);
            }
        });
        var JTooltips = new Tips($$('.hasTip'), {"maxTitleChars": 50,"fixed": false});
    })
    tbjQuery('#cumulative_add_price').click(function(){
        var count = tbjQuery('div.cumulative_surcharge_date_wrapper div.date_price').length;
        var html = '';
        var i = count-1;

        html += '<div class="date_price row'+i+' clearfix">'+
            '<div class="inputwrap clearfix">'+
            '<label>'+
            '<span class="hasTip control-label" title="'+"Insert the first number of the distance sector. Remember that the first sector has to start with 0 not 1 so you don't miss 0 to 1 price."+'">'+
            'Distancia Desde:'+
            '</span>'+
            '</label>'+
            '<input class="inputbox cumulative_distance_min" type="text" name="cumulative_distance_min['+i+']" size="10" value="" />'+
            '</div>'+
            '<div class="inputwrap clearfix">'+
            '<label>'+
            '<span class="hasTip control-label" title="'+"Insert the last number of the distance sector. Use this number as a first number of the next sector you create."+'">'+
            'Distancia Hasta:'+
            '</span>'+
            '</label>'+
            '<input class="inputbox" type="text" name="cumulative_distance_max['+i+']" size="10" value="" />'+
            '</div>';

// if use tariff YES, show separate price inputbox for each assigned tarrifs
// if NO, show one Price box
        if(tbjQuery('input[name="use_tariff"]:checked').val()==1 && parseInt(assigned_tariffs) > 0){
        }
        else {
            html +=	    '<div class="inputwrap clearfix">'+
                '<label>'+
                '<span class="hasTip control-label" title="'+"Price per Unit distance for this sector."+'">'+
                'Precio:'+
                '</span>'+
                '</label>'+
                '<input class="inputbox" type="text" name="cumulative_prices['+i+']" size="10" value="" />'+
                '</div>';
        }

        html +=	    '<div class="inputwrap clearfix">'+
            '<label>'+
            '<span class="hasTip control-label" title="'+"Flat rate discount on the Outbound journey if it falls within this distance sector. Negative numbers allowed to Add instead of Subtract discount."+'">'+
            'Outbound Discount:'+
            '</span>'+
            '</label>'+
            '<input class="inputbox" type="text" name="cumulative_outbound_discount['+i+']" size="10" value="" />'+
            '</div>'+
            '<div class="inputwrap clearfix">'+
            '<label>'+
            '<span class="hasTip control-label" title="'+"Flat rate discount on Return journey if it falls within this distance sector. Negative numbers allowed to Add instead of Subtract discount."+'">'+
            'Return Discount:'+
            '</span>'+
            '</label>'+
            '<input class="inputbox" type="text" name="cumulative_return_discount['+i+']" size="10" value="" />'+
            '</div>'+
            '<div class="button2-left">'+
            '<div class="blank">'+
            '<a href="javascript:void(0);" class="btn btn-small remove_price">Remove</a>'+
            '</div>'+
            '</div>'+
            '</div>';

        tbjQuery('div.cumulative_surcharge_date_wrapper').append(html);
        tbjQuery('html, body').animate({ scrollTop: tbjQuery('div.cumulative_surcharge_date_wrapper div.date_price:last-child').offset().top-80 }, 'fast');
        tbjQuery('div.cumulative_surcharge_date_wrapper div.date_price:last-child .cumulative_distance_min').focus();

        $$('.hasTip').each(function(el) {
            var title = el.get('title');
            if (title) {
                var parts = title.split('::', 2);
                el.store('tip:title', parts[0]);
                el.store('tip:text', parts[1]);
            }
        });
        var JTooltips = new Tips($$('.hasTip'), {"maxTitleChars": 50,"fixed": false});
    })
    tbjQuery(document).on("click", '.remove_price', function (e) {
        var r = confirm("Are you sure?");
        if (r == true) {
            tbjQuery(this).closest('div.date_price').remove();
        }
    })
    tbjQuery(document).on("click", '.jform_price_structure', function (e){
        var selected = tbjQuery(this).val();
        if(selected=='flat_rate'){
            tbjQuery(this).closest('div.date_price').find('div.unit_price_wrap').hide();
            tbjQuery(this).closest('div.date_price').find('div.flat_price_wrap').show();
        }
        else {
            tbjQuery(this).closest('div.date_price').find('div.unit_price_wrap').show();
            tbjQuery(this).closest('div.date_price').find('div.flat_price_wrap').hide();
        }
    })

    // Recurring Blockoff functions
    generateSummary();
    var recurring_blockoff_dialog, form;

    recurring_blockoff_dialog = tbjQuery( "#recurring-blockoff-dialog-form" ).dialog({
        autoOpen: false,
        minWidth: 350,
        width:'auto',
        modal: true,
        buttons: {
            "Done": addRecurringBlockoff,
            Cancel: function(){
                tbjQuery('div.recur_blockoff_blocks').removeClass('active');
                recurring_blockoff_dialog.dialog( "close" );
            }
        },
        close: function(){
            tbjQuery('div.recur_blockoff_blocks').removeClass('active');
            form[0].reset();
            tbjQuery('.rec-summary').html('');
        }
    });

    form = recurring_blockoff_dialog.find( "form" ).on( "submit", function( event ) {
        event.preventDefault();
        addRecurringBlockoff();
    });
    tbjQuery( "#add_recurring_blockoff" ).button().on( "click", function() {
        // initially daily will be selected
        tbjQuery('.repeat_feature.weekly, .repeat_feature.monthly').hide();
        tbjQuery('.repeat_feature.repeat_every').show();
        tbjQuery('.rec-summary').html('');

        tbjQuery('#repeat_every_label').html('days');

        recurring_blockoff_dialog.dialog( "open" );
    });
    tbjQuery('#frequency').on( "change", function() {

        var frequency = tbjQuery(this).val();
        if(frequency==1||frequency==2||frequency==3){
            tbjQuery('.repeat_feature').hide();
        }
        else {
            tbjQuery('.repeat_feature').show();
            if(frequency==0){ // daily
                tbjQuery('.repeat_feature.weekly, .repeat_feature.monthly').hide();
                tbjQuery('#repeat_every_label').html('days');
            }
            else if(frequency==4){ // weekly
                tbjQuery('.repeat_feature.monthly').hide();
                tbjQuery('#repeat_every_label').html('weeks');
            }
            else if(frequency==5){ // monthly
                tbjQuery('.repeat_feature.weekly').hide();
                tbjQuery('#repeat_every_label').html('months');
            }
            else if(frequency==6){ // yearly
                tbjQuery('.repeat_feature.weekly, .repeat_feature.monthly').hide();
                tbjQuery('#repeat_every_label').html('years');
            }
        }
    })
    tbjQuery( "#endson_until_input" ).datepicker({
        dateFormat: 'yy-mm-dd',
        showOn: "button"
    })
    tbjQuery( "#rstart" ).datepicker({
        dateFormat: 'yy-mm-dd',
        showOn: "button"
    })
    tbjQuery(document).on("click", '.recur_blockoff_block_edit', function (e) {
        tbjQuery('div.recur_blockoff_blocks').removeClass('active');
        var parentBlock = tbjQuery(this).closest('div.recur_blockoff_blocks');
        parentBlock.addClass('active');

        var blockoff_frequency = parentBlock.find('.blockoff_frequency').val();
        tbjQuery('#action').val('update');
        tbjQuery('#frequency').val(parentBlock.find('.blockoff_frequency').val());
        tbjQuery('#interval').val(parentBlock.find('.blockoff_interval').val());
        tbjQuery('#rstart').val(parentBlock.find('.blockoff_rstart').val());
        tbjQuery('#start_hr').val(parentBlock.find('.blockoff_start_hr').val());
        tbjQuery('#start_min').val(parentBlock.find('.blockoff_start_min').val());
        tbjQuery('#end_hr').val(parentBlock.find('.blockoff_end_hr').val());
        tbjQuery('#end_min').val(parentBlock.find('.blockoff_end_min').val());

        if(blockoff_frequency==1||blockoff_frequency==2||blockoff_frequency==3){
            tbjQuery('.repeat_feature').hide();
        }
        else {
            tbjQuery('.repeat_feature').show();
            if(blockoff_frequency==0){ // daily
                tbjQuery('.repeat_feature.weekly, .repeat_feature.monthly').hide();
                tbjQuery('#repeat_every_label').html('days');
            }
            else if(blockoff_frequency==4){ // weekly
                tbjQuery('.repeat_feature.monthly').hide();
                tbjQuery('#repeat_every_label').html('weeks');

                var blockoff_repeaton_arr = [];
                parentBlock.find('.blockoff_repeaton').each(function(){
                    blockoff_repeaton_arr.push(tbjQuery(this).val());
                })

                tbjQuery('#repeat_on_checkboxes').find('input[name="repeat_on_days"]').each(function(){
                    tbjQuery(this).attr('checked', false);
                    if(tbjQuery.inArray( tbjQuery(this).val(), blockoff_repeaton_arr )!=-1){
                        tbjQuery(this).attr('checked', true);
                    }
                })
            }
            else if(blockoff_frequency==5){ // monthly
                tbjQuery('.repeat_feature.weekly').hide();
                tbjQuery('#repeat_every_label').html('months');

                var blockoff_repeatby = parentBlock.find('.blockoff_repeatby').val();
                tbjQuery('input[name="repeatby"][value="'+blockoff_repeatby+'"]').attr('checked', true);
            }
            else if(blockoff_frequency==6){ // yearly
                tbjQuery('.repeat_feature.weekly, .repeat_feature.monthly').hide();
                tbjQuery('#repeat_every_label').html('years');
            }
        }

        var blockoff_endson = parentBlock.find('.blockoff_endson').val();

        if(blockoff_endson=='endson_never'){
            tbjQuery('#endson_count_input').attr('disabled', true);
            tbjQuery('#endson_until_input').attr('disabled', true);
        }
        else if(blockoff_endson=='endson_count'){
            tbjQuery('#endson_count_input').attr('disabled', false);
            tbjQuery('#endson_until_input').attr('disabled', true);
        }
        else if(blockoff_endson=='endson_until'){
            tbjQuery('#endson_count_input').attr('disabled', true);
            tbjQuery('#endson_until_input').attr('disabled', false);
        }

        tbjQuery('input[name="endson"][value="'+blockoff_endson+'"]').attr('checked', true);
        tbjQuery('input[name="endson_count_input"]').val(parentBlock.find('.blockoff_endson_count').val());
        tbjQuery('input[name="endson_until_input"]').val(parentBlock.find('.blockoff_endson_until').val());
        tbjQuery('.rec-summary').html(parentBlock.find('.blockoff_summary').val());

        recurring_blockoff_dialog.dialog( "open" );
    });
    tbjQuery(document).on("click", '.recur_blockoff_block_delete', function (e) {
        var r = confirm("Are you sure?");
        if (r == true) {
            tbjQuery('[name="recur_blockoff_changed"]').val(1);
            tbjQuery(this).closest('div.recur_blockoff_blocks').remove();
        } else {
            return false;
        }
    });
    tbjQuery(document).on("change", 'input,select', function (e) {
        if(tbjQuery(this).attr('id')=='rstart')
        {
            var frequency = tbjQuery('#frequency').val();
            if(frequency==4){ // weekly
                var rstart = tbjQuery('#rstart').val();
                var y = rstart.substring(0, 4),
                    m = rstart.substring(5, 7),
                    d = rstart.substring(8, 10),
                    dateObj = new Date(y, parseInt(m)-1, d),
                    locale = "en-us",
                    weekday = dateObj.toLocaleString(locale, { weekday: "short" });

                tbjQuery('#repeat_on_checkboxes').find('input[name="repeat_on_days"]').each(function(){
                    tbjQuery(this).attr('checked', false)
                })

                if(weekday=='Sat'){
                    tbjQuery('#repeat_on_checkboxes').find('input[name="repeat_on_days"][value="SA"]').attr('checked', true);
                }
                else if(weekday=='Sun'){
                    tbjQuery('#repeat_on_checkboxes').find('input[name="repeat_on_days"][value="SU"]').attr('checked', true);
                }
                else if(weekday=='Mon'){
                    tbjQuery('#repeat_on_checkboxes').find('input[name="repeat_on_days"][value="MO"]').attr('checked', true);
                }
                else if(weekday=='Tue'){
                    tbjQuery('#repeat_on_checkboxes').find('input[name="repeat_on_days"][value="TU"]').attr('checked', true);
                }
                else if(weekday=='Wed'){
                    tbjQuery('#repeat_on_checkboxes').find('input[name="repeat_on_days"][value="WE"]').attr('checked', true);
                }
                else if(weekday=='Thu'){
                    tbjQuery('#repeat_on_checkboxes').find('input[name="repeat_on_days"][value="TH"]').attr('checked', true);
                }
                else if(weekday=='Fri'){
                    tbjQuery('#repeat_on_checkboxes').find('input[name="repeat_on_days"][value="FR"]').attr('checked', true);
                }
            }
        }

        generateSummary();
    })
    tbjQuery(document).on("click", '#endson_never', function (e) {
        tbjQuery('#endson_count_input').attr('disabled', true);
        tbjQuery('#endson_until_input').attr('disabled', true);
    })
    tbjQuery(document).on("click", '#endson_count', function (e) {
        tbjQuery('#endson_count_input').attr('disabled', false);
        tbjQuery('#endson_until_input').attr('disabled', true);
    })
    tbjQuery(document).on("click", '#endson_until', function (e) {
        tbjQuery('#endson_count_input').attr('disabled', true);
        tbjQuery('#endson_until_input').attr('disabled', false);
    })
    function addRecurringBlockoff() {
        tbjQuery('[name="recur_blockoff_changed"]').val(1);

        if(tbjQuery('#action').val()=='update'){
            var current_block_count = tbjQuery('.recur_blockoff_blocks.active').index();
        }
        else {
            var current_block_count = tbjQuery('#recur_blockoff_wrapper').children().length;
        }

        var frequency = tbjQuery('#frequency').val(),
            interval = tbjQuery('#interval').val(),
            rstart = tbjQuery('#rstart').val(),
            start_hr = tbjQuery('#start_hr').val(),
            start_min = tbjQuery('#start_min').val(),
            end_hr = tbjQuery('#end_hr').val(),
            end_min = tbjQuery('#end_min').val(),
            repeat_on_days = [], repeatby = '',
            endson = tbjQuery('[name="endson"]:checked').val(),
            endson_count_input=0,
            endson_until_input=0, divHTML = '' ;

        if(endson=='endson_count'){
            endson_count_input = tbjQuery('input[name="endson_count_input"]').val();
        }
        else if(endson=='endson_until'){
            endson_until_input = tbjQuery('input[name="endson_until_input"]').val();
        }

        if(frequency==4){ // weekly
            tbjQuery('#repeat_on_checkboxes').find('input[name="repeat_on_days"]:checked').each(function(){
                repeat_on_days.push(tbjQuery(this).val());
            })
        }
        else if(frequency==5){ // monthly
            repeatby = tbjQuery('[name="repeatby"]:checked').val();
        }

        if(tbjQuery('#action').val()=='add'){
            divHTML += '<div class="recur_blockoff_blocks">';
        }

        divHTML += '<span>'+tbjQuery('.rec-summary').html()+'</span>&nbsp;'
            +'<a href="javascript:void(0);" class="recur_blockoff_block_edit">Editar</a>&nbsp;'
            +'<a href="javascript:void(0);" class="recur_blockoff_block_delete">Borrar</a>&nbsp;'
            +'<input type="hidden" name="recur_blockoff['+current_block_count+'][frequency]" class="blockoff_frequency" value="'+frequency+'" />'
            +'<input type="hidden" name="recur_blockoff['+current_block_count+'][interval]" class="blockoff_interval" value="'+interval+'" />'
            +'<input type="hidden" name="recur_blockoff['+current_block_count+'][rstart]" class="blockoff_rstart" value="'+rstart+'" />'
            +'<input type="hidden" name="recur_blockoff['+current_block_count+'][start_hr]" class="blockoff_start_hr" value="'+start_hr+'" />'
            +'<input type="hidden" name="recur_blockoff['+current_block_count+'][start_min]" class="blockoff_start_min" value="'+start_min+'" />'
            +'<input type="hidden" name="recur_blockoff['+current_block_count+'][end_hr]" class="blockoff_end_hr" value="'+end_hr+'" />'
            +'<input type="hidden" name="recur_blockoff['+current_block_count+'][end_min]" class="blockoff_end_min" value="'+end_min+'" />';

        for (var i = 0; i < repeat_on_days.length; i++) {
            divHTML +='<input type="hidden" name="recur_blockoff['+current_block_count+'][repeaton][]" class="blockoff_repeaton" value="'+repeat_on_days[i]+'" />';
        }
        if(repeatby!=""){
            divHTML +='<input type="hidden" name="recur_blockoff['+current_block_count+'][repeatby]" class="blockoff_repeatby" value="'+repeatby+'" />';
        }

        divHTML +='<input type="hidden" name="recur_blockoff['+current_block_count+'][endson]" class="blockoff_endson" value="'+endson+'" />'
            +'<input type="hidden" name="recur_blockoff['+current_block_count+'][endson_count]" class="blockoff_endson_count" value="'+endson_count_input+'" />'
            +'<input type="hidden" name="recur_blockoff['+current_block_count+'][endson_until]" class="blockoff_endson_until" value="'+endson_until_input+'" />'
            +'<input type="hidden" name="recur_blockoff['+current_block_count+'][summary]" class="blockoff_summary" value="'+tbjQuery('.rec-summary').html()+'" />';

        if(tbjQuery('#action').val()=='add'){
            divHTML += '</div>';
        }

        if(tbjQuery('#action').val()=='update'){
            tbjQuery('.recur_blockoff_blocks.active').html(divHTML);
        }
        else {
            tbjQuery('#recur_blockoff_wrapper').append(divHTML);
        }

        // reset data for New add
        tbjQuery('div.recur_blockoff_blocks').removeClass('active');
        tbjQuery('#action').val('add');

        recurring_blockoff_dialog.dialog( "close" );
    }
})