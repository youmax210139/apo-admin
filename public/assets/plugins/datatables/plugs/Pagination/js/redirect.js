$.fn.dataTableExt.oApi.fnPagingInfo = function(oSettings){
    return {
        "iStart" : oSettings._iDisplayStart,
        "iEnd" : oSettings.fnDisplayEnd(),
        "iLength" : oSettings._iDisplayLength,
        "iTotal" : oSettings.fnRecordsTotal(),
        "iFilteredTotal" : oSettings.fnRecordsDisplay(),
        "iPage" : Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
        "iTotalPages" : Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
    };
};

$.extend($.fn.dataTableExt.oPagination, {
    "redirect": {
        "fnInit": function (oSettings, nPaging, fnDraw) {
            var oLang = oSettings.oLanguage.oPaginate;
            var fnClickHandler = function (e) {
                e.preventDefault();
                if (oSettings.oApi._fnPageChange(oSettings, e.data.action)) {
                    fnDraw(oSettings);
                }
            };
            $(nPaging).append('<ul class="pagination">' + '<li class="paginate_button first disabled"><a href="#">' + oLang.sFirst + '</a></li>' + '<li class="paginate_button prev disabled"><a href="#">' + oLang.sPrevious + '</a></li>' + '<li class="paginate_button next disabled"><a href="#">' + oLang.sNext + '</a></li>' + '<li class="paginate_button last disabled"><a href="#">' + oLang.sLast + '</a></li>' + '<select id="redirect" class="form-control"></select>' + '</ul>');
            $(nPaging).find("#redirect").bind('change',function (e) {
                var ipage = parseInt($(this).children('option:selected').val());
                var oPaging = oSettings.oInstance.fnPagingInfo();
                if (isNaN(ipage) || ipage < 1) {
                    //ipage = 1;
                    return false;
                }else if(ipage > oPaging.iTotalPages) {
                    ipage = oPaging.iTotalPages;
                }
                $(this).val(ipage);
                ipage--;
                oSettings._iDisplayStart = ipage * oPaging.iLength;
                fnDraw(oSettings);
            });
            var els = $('a', nPaging);
            $(els[0]).bind('click.DT', {
                action: "first"
            }, fnClickHandler);
            $(els[1]).bind('click.DT', {
                action: "previous"
            }, fnClickHandler);
            $(els[2]).bind('click.DT', {
                action: "next"
            }, fnClickHandler);
            $(els[3]).bind('click.DT', {
                action: "last"
            }, fnClickHandler);
        }, 
        "fnUpdate": function (oSettings, fnDraw) {
            var iListLength = 6;
            var oPaging = oSettings.oInstance.fnPagingInfo();
            var an = oSettings.aanFeatures.p;
            var i, ien, j, sClass, iStart, iEnd, iHalf = Math.floor(iListLength / 2);
            if (oPaging.iTotalPages < iListLength) {
                iStart = 1;
                iEnd = oPaging.iTotalPages;
            }else if(oPaging.iPage <= iHalf) {
                iStart = 1;
                iEnd = iListLength;
            }else if(oPaging.iPage >= (oPaging.iTotalPages - iHalf)) {
                iStart = oPaging.iTotalPages - iListLength + 1;
                iEnd = oPaging.iTotalPages;
            } else {
                iStart = oPaging.iPage - iHalf + 1;
                iEnd = iStart + iListLength - 1;
            }
            var selected , redirectHtml = '<option value="">页码</option>';
            for( i=0; i<oPaging.iTotalPages; i++ ){
                selected = oPaging.iPage == i ? 'selected' : '';
                redirectHtml += '<option value="'+i+'" '+ selected +'>'+(i+1)+'</option>';
            }
            $("#redirect").empty().html(redirectHtml);
            for (i = 0, ien = an.length; i < ien; i++) {
                ($('li:gt(1)', an[i]).filter(':not(:last)')).filter(':not(:last)').remove();
                for (j = iStart; j <= iEnd; j++) {
                    sClass = (j == oPaging.iPage + 1) ? 'class="active"' : '';
                    $('<li ' + sClass + '><a href="#">' + j + '</a></li>').insertBefore($('.next', an[i])[0]).bind('click', function (e) {
                        e.preventDefault();
                        oSettings._iDisplayStart = (parseInt($('a', this).text(), 10) - 1) * oPaging.iLength;
                        fnDraw(oSettings);
                    });
                }
                if (oPaging.iPage === 0) {
                    $('li:lt(2)', an[i]).addClass('disabled');
                } else {
                    $('li:lt(2)', an[i]).removeClass('disabled');
                } if (oPaging.iPage === oPaging.iTotalPages - 1 || oPaging.iTotalPages === 0) {
                    $('.next', an[i]).addClass('disabled');
                    $('li:last', an[i]).addClass('disabled');
                } else {
                    $('.next', an[i]).removeClass('disabled');
                    $('li:last', an[i]).removeClass('disabled');
                }
            }
        }
    }
});