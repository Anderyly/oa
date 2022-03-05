(function ($) {
    var itemIndex = 0;
    $.fn.suggest = function (options) {
        var params = {
            boxId: "suggestBox",
            boxWidth: 250,
            boxHeight: 200,
            controller: "",
            action: "",
            headerText: "",
            displayFields: "",
            valueField: "",
            keyField: "",
            keyTextBoxName: "",
            callBack: ""
        };
        var ops = $.extend(params, options);
        var headerTextArr = new Array();
        var displayFieldsArr = new Array();
        headerTextArr = ops.headerText.split(';');
        displayFieldsArr = ops.displayFields.split(';');
        var headerStr = "";
        var headerLen = headerTextArr.length;

        if (headerLen == 1 || headerLen == 0) {
            var textBox = $(this);

            ops.boxWidth = textBox.css("width");

        }

        var box = '';
        if (ops.headerText.length == 0) {

            box = '<div id="' + ops.boxId + '" style="display:none;width:' + ops.boxWidth + ';height:' + ops.boxHeight + '"><ul class="suggestBoxItems"></ul></div>';

        }
        else {
            for (var i = 0; i < headerLen; i++) {
                if (i == headerLen - 1) {
                    headerStr += '<span class="headerTextShort">' + headerTextArr[i] + '</span>'
                }
                else {
                    headerStr += '<span class="headerTextLong">' + headerTextArr[i] + '</span>'
                }
            }
            box = '<div id="' + ops.boxId + '" style="display:none;width:' + ops.boxWidth + ';height:' + ops.boxHeight + '"><div class = "headerText">' + headerStr + '</div><ul class="suggestBoxItems"></ul></div>';

        }
        $(this).after(box);

        var itemCount = 0;
        $(this).bind('keyup', function (e) {
            var value = $.trim($(this).val());
            if (value.length >= 1) {
                var position = $(this).position();

                $('#' + ops.boxId).css({ 'display': 'block', 'background': 'white', 'color': 'black', 'position': 'absolute', 'border': "1px solid #D5D5D5", 'left': position.left, 'top': position.top + 22 });
                var pVal = $(this).val() + "";
                if (pVal.search('&') >= 0) {
                    pVal = pVal.replace('&', '%26');
                }
                if (e.keyCode != 38 && e.keyCode != 40 && e.keyCode != 13 && e.keyCode != 9) {
                    var sugTextBox = $(this);
                    var dataUrl = "/" + ops.controller + "/" + ops.action + "?mid=589ef397-eba5-4d4f-867d-538f3dbe2d4f";
                    if (pVal != "") {
                        $.ajax({
                            type: "post",
                            async: true,
                            url: dataUrl,
                            data: "param=" + pVal,
                            dataType: "json",
                            cache: false,
                            timeout: 5000,
                            beforeSend: loading(ops.boxId),
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                alert(textStatus);
                                $('#' + ops.boxId).slideUp("slow");
                                $('#' + ops.boxId + ' ul').html('');
                            },
                            success: function (data) {
                                initBox(ops.boxId, sugTextBox, data, displayFieldsArr, ops.valueField, ops.keyField, ops.keyTextBoxName);
                            }

                        });
                    }

                    itemIndex = 0;
                }
                var itemCount = $('#' + ops.boxId + ' ul li').length;
                switch (e.keyCode) {
                    case 38:
                        if (itemIndex == 0) {
                            itemIndex = itemCount + 1;
                        }
                        if (itemIndex > 1) {
                            $('#' + ops.boxId + ' ul li:nth-child(' + itemIndex + ')').css({ 'background': 'white', 'color': 'black' });
                            itemIndex--;
                        }

                        $('#' + ops.boxId + ' ul li:nth-child(' + itemIndex + ')').css({ 'background': '#7AADEB', 'color': 'white' });
                        $(this).val($('#' + ops.boxId + ' ul li:nth-child(' + itemIndex + ')').find('font').text());
                        if (ops.keyTextBoxName != "") {
                            $('#' + ops.keyTextBoxName).val($('#' + ops.boxId + ' ul li:nth-child(' + itemIndex + ')').find('div').text());
                        }
                        break;
                    case 40:
                        if (itemIndex < itemCount) {
                            $('#' + ops.boxId + ' ul li:nth-child(' + itemIndex + ')').css({ 'background': 'white', 'color': 'black' });
                            itemIndex++;
                        }

                        $('#' + ops.boxId + ' ul li:nth-child(' + itemIndex + ')').css({ 'background': '#7AADEB', 'color': 'white' });
                        $(this).val($('#' + ops.boxId + ' ul li:nth-child(' + itemIndex + ')').find('font').text());
                        if (ops.keyTextBoxName != "") {
                            $('#' + ops.keyTextBoxName).val($('#' + ops.boxId + ' ul li:nth-child(' + itemIndex + ')').find('div').text());
                        }
                        break;
                    case 13:
                        if (itemIndex > 0 && itemIndex <= itemCount) {
                            $(this).val($('#' + ops.boxId + ' ul li:nth-child(' + itemIndex + ')').find('font').text());
                            if (ops.keyTextBoxName != "") {
                                $('#' + ops.keyTextBoxName).val($('#' + ops.boxId + ' ul li:nth-child(' + itemIndex + ')').find('div').text());
                            }
                            $('#' + ops.boxId).slideUp("fast");
                            $('#' + ops.boxId + ' ul').html('');
                            eval(ops.callBack);
                        }
                        break;
                    default:
                        break;
                }
            }
            else {

                $('#' + ops.boxId).slideUp("fast");
                $('#' + ops.boxId + ' ul').html('');
            }
        });
        $(this).blur(function () {
            var tempLi = $('#' + ops.boxId + ' ul li:nth-child(1)');

            if (itemIndex == 0 && tempLi != undefined) {

                $(this).val(tempLi.find('font').text());
                if (ops.keyTextBoxName != "") {
                    $('#' + ops.keyTextBoxName).val(tempLi.find('div').text());
                }
                itemIndex = 1;
            }
            if ($('#' + ops.boxId + ' ul').html() != '') {
                eval(ops.callBack);
            }


            $('#' + ops.boxId).slideUp("fast");
            $('#' + ops.boxId + ' ul').html('');
        });


    };

    function loading(boxId) {
        $('#' + boxId + ' ul').html('<img alt="loading" src="/Scripts/SuggestBox/loading.gif"/>');

    }
    function initBox(boxId, obj, data, displayFieldsArr, valueField, keyField, keyTextBoxName) {

        var str = "";
        if (data == undefined || data.Data == undefined || data.Data.length == 0) {
            $('#' + boxId + ' ul').html('<div class="noRecordsTip">无匹配记录<div>');
        }
        else {

            for (var i = 0; i < data.Data.length; i++) {
                var fieldStr = "";
                for (var j = 0; j < displayFieldsArr.length; j++) {
                    if (displayFieldsArr[j] == valueField) {
                        if (j == 0 || j != displayFieldsArr.length - 1) {
                            fieldStr += "<font class='singleField'>" + data.Data[i][displayFieldsArr[j]] + "</font>";
                        }
                        else {
                            fieldStr += "<font>" + data.Data[i][displayFieldsArr[j]] + "</font>";

                        }
                    }
                    else {
                        var tempValue = data.Data[i][displayFieldsArr[j]];

                        if (tempValue.length > 16) {
                            tempValue = tempValue.substr(0, 16) + "...";
                        }
                        fieldStr += "<span class='commonFields'>" + tempValue + "</span>";
                    }
                }
                if (keyField != "") {
                    fieldStr += "<div style = 'display:none;'>" + data.Data[i][keyField] + "</div>";
                }

                str += "<li>" + fieldStr + "</li>";
            }
            $('#' + boxId + ' ul').html(str);
        }

        if (data != undefined && data.Data != undefined && data.Data.length == 1) {
            var tempLi = $('#' + boxId + ' ul li');
            obj.val(tempLi.find('font').text());
            if (keyTextBoxName != "") {
                $('#' + keyTextBoxName).val(tempLi.find('div').text());
            }
            itemIndex = 1;
        }
        $('#' + boxId + ' ul li').each(function () {
            $(this).bind('click', function () {
                obj.val($(this).find('font').text());
                if (keyTextBoxName != "") {
                    $('#' + keyTextBoxName).val($(this).find('div').text());
                }
                eval(ops.callBack);
                $('#' + boxId).slideUp("fast");

            });
        });

        $('#' + boxId + ' ul li').each(function () {
            $(this).hover(
                function () {
                    $('#' + boxId + ' ul li:nth-child(' + itemIndex + ')').css({ 'background': 'white', 'color': 'black' });
                    itemIndex = $('#' + boxId + ' ul li').index($(this)[0]) + 1;
                    $(this).css({ 'background': '#7AADEB', 'color': 'white' });
                    obj.val($(this).find('font').text());
                    if (keyTextBoxName != "") {
                        $('#' + keyTextBoxName).val($(this).find('div').text());
                    }

                },
                function () {
                    $(this).css({ 'background': 'white', 'color': 'black' });
                }
            );
        });
    };
})(jQuery);