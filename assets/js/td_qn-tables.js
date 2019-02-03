(function($,obj){

    let datepickerOptions = {
        dateFormat: "dd.mm.yy",
        prevText: '<i class="fa fa-chevron-left"></i>',
        nextText: '<i class="fa fa-chevron-right"></i>',
        changeYear: true
    };
    obj.table_obj = null;

    obj.init_table = function(id,url,columnsDef,filters,options)
    {

        //var _self = this;
        filters = filters || 0;
        let opt_table = $.extend({
                "stateSave":false,
                "retrieve":true,
                "jQueryUI":false,
                "processing":false,
                "serverSide":true,
                "ajax":{
                    "url":url,"type":"POST",
                    "data":{
                        "action": 'get_quick_news_data'
                    },
                },

                "ordering": true,
                "order": [2, "desc"],
                "pagingType": "simple_numbers",
                "language": {
                    "emptyTable": "На сегодня нет новостей",
                    "info": "_START_ - _END_ из _TOTAL_ записей",
                    "infoEmpty": "0 из 0 записей",
                    "infoFiltered": '',
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Показать _MENU_ записей",
                    "zeroRecords": "Не найдено записей",
                    "paginate": {
                        "first": "Первая",
                        "last": "Последня",
                        "next": "След",
                        "previous": "Пред"
                    }
                },
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 100], [ 10, 25, 50, 100]],
                "lengthChange":true,
                "columnDefs": columnsDef,
                fixedHeader: true,
               /* "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    nRow.setAttribute('id', aData[1]);
                },*/
               /* "initComplete": function (settings, json) {
                    let api = this.api();
                    if (filters !== 0) {
                        setFilters(api, filters, true);
                        //        initDefaultFilters(api,filters,state);
                    }
                }*/

            },options);

        this.table_obj  = $(id).dataTable(opt_table);
        return this.table_obj.api();


    };
    function setFilters(tableApi,filters,ajax)
    {
        ajax = ajax || false;
        showCustomFilters($(tableApi.table().node()).attr('id'), $(tableApi.table().node()).attr('id')+"-filters");
        for(var i=0; i<filters.length;i++)
        {
            if (ajax)
            {
                switch (filters[i].filterType) {
                    case 'dateRange':
                        dateRangeFilterAX(tableApi, filters[i].container, filters[i].column);
                        break;
                    case 'select_sp':
                        selectFilter(tableApi, filters[i].defaultSelect, filters[i].defaultSelectedValue, filters[i].container, filters[i].column, filters[i].filterName, filters[i].cssClass, filters[i].style,filters[i].externalTable);
                        break;
                }
            } else
            {
                switch (filters[i].filterType) {
                    case 'dateRange':
                        dateRangeFilter(tableApi, filters[i].container, filters[i].column);
                        break;
                }
            }
        }
    }
     obj.initFilter = function(options)
     {
        let opt_filter = $.extend({
            container:null,
            column:0,

        },options);
        if(opt_filter.container) {
            let $select =$(opt_filter.container),
                api = this.table_obj.api();



            $select.on('change', function () {
                let val = $(this).val();
                api.column(opt_filter.column).search(val ? val : '', false, false).draw();
            });
        }
     };
    obj.initFilterDateRange = function(options)
    {
        let opt_filter = $.extend({
            start_date:null,
            end_date:null,
            column:0,
        },options);

            let $start_date =$(opt_filter.start_date),
                $end_date =$(opt_filter.end_date),
                $select = $(opt_filter.start_date+","+opt_filter.end_date),
                api = this.table_obj.api();
            let $node = $("#td_qn-reset-date-range");
                $node.on('click',function(){
                    $end_date.val("");
                    $start_date.val("");
                    $(this).hide();
                    $select.trigger("change");
                });
                $select.datepicker(datepickerOptions);
                if(($start_date.val()!=="" || $end_date.val()!=="") && !$node.is(":visible"))
                        $node.show();

            $select.on('change', function () {
                if(($start_date.val()!=="" || $end_date.val()!=="") && !$node.is(":visible"))
                    $node.show();

                let val = $start_date.val()+"]:["+$end_date.val();
                api.column(opt_filter.column).search(val ? val : '', false, false).draw();
            });

    };
    function dateRangeFilterAX(tableApi, containerId, columnNum)
    {
        let rangeContainer = $('<div class="float space"><input type="datetime" id="txtStartDate" style="width:115px" class="form-control datepicker" value="" dateformat="mm.dd.yy" placeholder="Date From"/> &nbsp;<input type="dateTime" dateformat="mm.dd.yy" id="txtEndDate" style="width:115px" class="form-control datepicker" value="" placeholder="Date To"></div>').prependTo("#" + containerId);
        $("#txtStartDate, #txtEndDate").datepicker(datepickerOptions);
        $("#txtStartDate, #txtEndDate").on("change", function () {
            var val = $("#txtStartDate").val() + "]:[" + $("#txtEndDate").val();
            tableApi.columns(columnNum).search(val ? val : '', false, false).draw();
        });
    }
    function selectFilter(tableApi, defSelect, defSelectedValue, containerId,columnNum,filterName,cssClass,style,externalTable)
    {
        style = style || '';
        cssClass = cssClass || '';
        externalTable = externalTable || null;
        var url = tableApi.ajax.url() + "?f=1&filterName=" + filterName;
        if(tableApi !=null && typeof tableApi !== 'undefined')
        {
            var def = '';
            if (defSelect)
            {
                def = '<option value="">' + defSelect + '</option>';
            }
            if (defSelectedValue)
            {
                tableApi.column(columnNum).search(defSelectedValue);
            }
            var select = $('<select id="select-filters-'+containerId+'" '+style+' '+cssClass+'>'+def+'</select>').prependTo("#"+containerId).on("change", function(){
                var val = $(this).val();
                tableApi.column(columnNum).search(val ? val : '', false, false).draw();
                if(externalTable!=null)
                {
                    externalTable.Api.column(externalTable.columnNum).search(val ? val : '', false, false).draw();
                }
            });
            $.getJSON(url, function (data) {
                $.each(data, function (key, val) {
                    if (val.Id === tableApi.column(columnNum).search()) {
                        select.append('<option value="' + val.Id + '" selected="selected">' + val.Value + '</option>');
                        $("#" + containerId).fadeIn();
                    }
                    else {
                        select.append('<option value="' + val.Id + '" >' + val.Value + '</option>');
                    }
                });
                if (!defSelect)
                {
                    select.trigger("change");
                }
            });
        }
    }
    function dateRangeFilter(tableApi, containerId,columnNum)
    {
        var rangeContainer = $('<div class="float space"><input type="datetime" id="txtStartDate" style="width:100px" class="form-control datepicker" value="" dateformat="mm.dd.yy" placeholder="Date From"/> &nbsp;<input type="dateTime" dateformat="mm.dd.yy" id="txtEndDate" style="width:100px" class="form-control" value="" placeholder="Date To"></div>').prependTo("#" + containerId);
        $("#txtStartDate, #txtEndDate").datepicker(datepickerOptions);
        $("#txtStartDate, #txtEndDate").on("change", function () {
            //console.log($("#txtStartDate").val());
            filterbyDateRange(tableApi, columnNum, $("#txtStartDate").val(), $("#txtEndDate").val());
            tableApi.draw();
            $.fn.dataTable.ext.search.pop();
        });
    }
    let filterbyDateRange = function(tableApi, columnNum,startDate,endDate)
    {
        $.fn.dataTable.ext.search.push(function (settings, data, index) {
                var rowDate = normalizeDate(data[columnNum]);
                var start = normalizeDate(startDate);
                var end = normalizeDate(endDate);
                //console.log(start + " " + end + " "+ rowDate + " "+columnNum+" "+index);
                // If our date from the row is between the start and end
                if (start <= rowDate && rowDate <= end) {
                    return true;
                } else if (rowDate >= start && end === '' && start !== '') {
                    return true;
                } else if (rowDate <= end && start === '' && end !== '') {
                    return true;
                }
                else if(start ==='' && end ==='')
                {
                    console.log(start + " " + end);
                    return true;
                }
                else {
                    return false;
                }
            }
        );
    }
    var normalizeDate = function (dateString) {
        if (dateString === '')
        {
            return '';
        }
        var date = new Date(dateString)
        var normalized = (("0" + (date.getMonth() + 1)).slice(-2)) + "." + ("0" + date.getDate()).slice(-2) + "." + date.getFullYear();
        return normalized;
    }
})(jQuery,window.td_qn_tables = window.td_qn_tables||{});

var dayNames = ['Sun', 'Mon', 'Tues', 'Wednes', 'Thurs', 'Fri', 'Satur'];
var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
Date.prototype.format = function (format) {
    var wordSplitter = /\W+/, _date = this;
    this.Date = function (format) {
        var words = format.split(wordSplitter);

        words.forEach(function (w) {
            if (typeof (wordReplacer[w]) === "function") {
                format = format.replace(w, wordReplacer[w]());
            }
            else {
                wordReplacer['e'](w);
            }
        });
        return format.replace(/\s+(?=\b(?:st|nd|rd|th)\b)/g, "");
    };
    var wordReplacer = {
        //The day of the month, from 1 through 31. (eg. 5/1/2014 1:45:30 PM, Output: 1)
        d: function () {
            return _date.getDate();
        },
        //The day of the month, from 01 through 31. (eg. 5/1/2014 1:45:30 PM, Output: 01)
        dd: function () {
            return _pad(_date.getDate(), 2);
        },
        //The abbreviated name of the day of the week. (eg. 5/15/2014 1:45:30 PM, Output: Mon)
        ddd: function () {
            return dayNames[_date.getDay()].slice(0, 3);
        },
        //The full name of the day of the week. (eg. 5/15/2014 1:45:30 PM, Output: Monday)
        dddd: function () {
            return dayNames[_date.getDay()] + 'day';
        },
        //The tenths of a second in a date and time value. (eg. 5/15/2014 13:45:30.617, Output: 6)
        f: function () {
            return parseInt(_date.getMilliseconds() / 100);
        },
        //The hundredths of a second in a date and time value.  (eg. 5/15/2014 13:45:30.617, Output: 61)
        ff: function () {
            return parseInt(_date.getMilliseconds() / 10);
        },
        //The milliseconds in a date and time value. (eg. 5/15/2014 13:45:30.617, Output: 617)
        fff: function () {
            return _date.getMilliseconds();
        },
        //If non-zero, The tenths of a second in a date and time value. (eg. 5/15/2014 13:45:30.617, Output: 6)
        F: function () {
            return (_date.getMilliseconds() / 100 > 0) ? parseInt(_date.getMilliseconds() / 100) : '';
        },
        //If non-zero, The hundredths of a second in a date and time value.  (eg. 5/15/2014 13:45:30.617, Output: 61)
        FF: function () {
            return (_date.getMilliseconds() / 10 > 0) ? parseInt(_date.getMilliseconds() / 10) : '';
        },
        //If non-zero, The milliseconds in a date and time value. (eg. 5/15/2014 13:45:30.617, Output: 617)
        FFF: function () {
            return (_date.getMilliseconds() > 0) ? _date.getMilliseconds() : '';
        },
        //The hour, using a 12-hour clock from 1 to 12. (eg. 5/15/2014 1:45:30 AM, Output: 1)
        h: function () {
            return _date.getHours() % 12 || 12;
        },
        //The hour, using a 12-hour clock from 01 to 12. (eg. 5/15/2014 1:45:30 AM, Output: 01)
        hh: function () {
            return _pad(_date.getHours() % 12 || 12, 2);
        },
        //The hour, using a 24-hour clock from 0 to 23. (eg. 5/15/2014 1:45:30 AM, Output: 1)
        H: function () {
            return _date.getHours();
        },
        //The hour, using a 24-hour clock from 00 to 23. (eg. 5/15/2014 1:45:30 AM, Output: 01)
        HH: function () {
            return _pad(_date.getHours(), 2);
        },
        //The minute, from 0 through 59. (eg. 5/15/2014 1:09:30 AM, Output: 9
        m: function () {
            return _date.getMinutes();
        },
        //The minute, from 00 through 59. (eg. 5/15/2014 1:09:30 AM, Output: 09
        mm: function () {
            return _pad(_date.getMinutes(), 2);
        },
        //The month, from 1 through 12. (eg. 5/15/2014 1:45:30 PM, Output: 6
        M: function () {
            return _date.getMonth() + 1;
        },
        //The month, from 01 through 12. (eg. 5/15/2014 1:45:30 PM, Output: 06
        MM: function () {
            return _pad(_date.getMonth() + 1, 2);
        },
        //The abbreviated name of the month. (eg. 5/15/2014 1:45:30 PM, Output: Jun
        MMM: function () {
            return monthNames[_date.getMonth()].slice(0, 3);
        },
        //The full name of the month. (eg. 5/15/2014 1:45:30 PM, Output: June)
        MMMM: function () {
            return monthNames[_date.getMonth()];
        },
        //The second, from 0 through 59. (eg. 5/15/2014 1:45:09 PM, Output: 9)
        s: function () {
            return _date.getSeconds();
        },
        //The second, from 00 through 59. (eg. 5/15/2014 1:45:09 PM, Output: 09)
        ss: function () {
            return _pad(_date.getSeconds(), 2);
        },
        //The first character of the AM/PM designator. (eg. 5/15/2014 1:45:30 PM, Output: P)
        t: function () {
            return _date.getHours() >= 12 ? 'P' : 'A';
        },
        //The AM/PM designator. (eg. 5/15/2014 1:45:30 PM, Output: PM)
        tt: function () {
            return _date.getHours() >= 12 ? 'PM' : 'AM';
        },
        //The year, from 0 to 99. (eg. 5/15/2014 1:45:30 PM, Output: 9)
        y: function () {
            return Number(_date.getFullYear().toString().substr(2, 2));
        },
        //The year, from 00 to 99. (eg. 5/15/2014 1:45:30 PM, Output: 09)
        yy: function () {
            return _pad(_date.getFullYear().toString().substr(2, 2), 2);
        },
        //The year, with a minimum of three digits. (eg. 5/15/2014 1:45:30 PM, Output: 2009)
        yyy: function () {
            var _y = Number(_date.getFullYear().toString().substr(1, 2));
            return _y > 100 ? _y : _date.getFullYear();
        },
        //The year as a four-digit number. (eg. 5/15/2014 1:45:30 PM, Output: 2009)
        yyyy: function () {
            return _date.getFullYear();
        },
        //The year as a five-digit number. (eg. 5/15/2014 1:45:30 PM, Output: 02009)
        yyyyy: function () {
            return _pad(_date.getFullYear(), 5);
        },
        //Hours offset from UTC, with no leading zeros. (eg. 5/15/2014 1:45:30 PM -07:00, Output: -7)
        z: function () {
            return parseInt(_date.getTimezoneOffset() / 60); //hourse
        },
        //Hours offset from UTC, with a leading zero for a single-digit value. (eg. 5/15/2014 1:45:30 PM -07:00, Output: -07)
        zz: function () {
            var _h = parseInt(_date.getTimezoneOffset() / 60); //hourse
            if (_h < 0) _h = '-' + _pad(Math.abs(_h), 2);
            return _h;
        },
        //Hours and minutes offset from UTC. (eg. 5/15/2014 1:45:30 PM -07:00, Output: -07:00)
        zzz: function () {
            var _h = parseInt(_date.getTimezoneOffset() / 60); //hourse
            var _m = _date.getTimezoneOffset() - (60 * _h);
            var _hm = _pad(_h, 2) + ':' + _pad(Math.abs(_m), 2);
            if (_h < 0) _hm = '-' + _pad(Math.abs(_h), 2) + ':' + _pad(Math.abs(_m), 2);
            return _hm;
        },
        //Date ordinal display from day of the date. (eg. 5/15/2014 1:45:30 PM, Output: 15th)
        st: function () {
            var _day = wordReplacer.d();
            return _day < 4 | _day > 20 && ['st', 'nd', 'rd'][_day % 10 - 1] || 'th';
        },
        e: function (method) {
            throw 'ERROR: Not supported method [' + method + ']';
        }
    };
    _pad = function (n, c) {
        if ((n = n + '').length < c) {
            return new Array((++c) - n.length).join('0') + n;
        }
        return n;
    };
    return this.Date(format);
};