/**
 * Inspired by : http://jsfiddle.net/LiquidSky/djav37tg/
 */

$(document).ready(function() {
    $('table.tablesorter').tablesorter(
        {sortList: [[2,0]]}
    );

    var table = $('table.paginated');
    var currentPage = 0;
    var numPerPage = 100;
    var numRows = table.find('tbody tr').length;
    var numPages = Math.ceil(numRows / numPerPage);

    function repaginate(table) {
        table.find('tbody tr').hide()
            .slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
    }

    function changePage(event) {
        currentPage = parseInt($(this).text()) - 1;
        repaginate(table);
        $(this).addClass('active').siblings().removeClass('active');
    }

    /**
     * Custom, case-insensitive version of the :contains() selector
     */
    jQuery.expr[':'].icontains = function(a, i, m) {
      return jQuery(a).text().toUpperCase()
          .indexOf(m[3].toUpperCase()) >= 0;
    };

    $('table.paginated').each(function() {

        repaginate(table);

        var pager = $('<div class="pager"></div>');
        for(var page = 0; page < numPages; page++) {
            $('<span class="page-number"></span>')
                .text(page + 1)
                .click(changePage)
                .appendTo(pager)
                .addClass('clickable');
        }
        pager.addClass('col-lg-6');
        pager.appendTo($('#table-ctrl')).find('span.page-number:first').addClass('active');


    });

    $('#search').keyup(function() {
        var input = $(this).val();
        if(input.length > 0) {
            $('tbody tr').hide();
            $("tbody tr:icontains(" + input + ")").slice(0, 10).show();
        } else {
            repaginate($('table.paginated'));
        }
    })
});