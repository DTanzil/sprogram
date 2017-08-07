/**
 * Table pagination inspired by : http://jsfiddle.net/LiquidSky/djav37tg/
 * Author: jshill
 *
 * This script runs on any table with the class 'paginated'.
 * It splits a huge table into many pages for easier viewing.
 * This script also handles search functionality for the table
 */


$(document).ready(function() {
    // Sort the table by which th has the 'sort-by-first' class
    var sortCols = $('thead th');
    var sortMethod;
    $.each(sortCols, function(i, el) {
        if($(sortCols[i]).hasClass('sort-by-first')) {
            sortMethod = [i, 0];
        }
    });

    // Configure tablesorter plugin
    $('table.tablesorter').tablesorter(
        {
            sortList: [sortMethod],
            widgets: ['zebra']
        }
    );

    // Setup pagination on the table
    var table = $('table.paginated');
    var currentPage = 0;
    var numPerPage = 100;
    var numRows = table.find('tbody tr').length;
    var numPages = Math.ceil(numRows / numPerPage);
    $('table.paginated').each(function() {
        // Paginate for the first time
        repaginate(table);

        // Add pages to the controls div
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

    // On keyup, search the table for rows containing the input text
    $('#search').keyup(function() {
        var input = $(this).val();
        // IF there's text in the box, do a search
        // ELSE clear the results and repaginate the table
        if(input.length > 0) {
            $('tbody tr').hide();
            $("tbody tr:istartswith(" + input + ")").slice(0, 10).show();
        } else {
            repaginate($('table.paginated'));
        }
    });

    // Page is finished loading, toggle the table visibility and show it
    loading();


    /**
     * Custom, case-insensitive version of the :contains() selector
     */
    jQuery.expr[':'].icontains = function(a, i, m) {
      return jQuery(a).text().toUpperCase()
          .indexOf(m[3].toUpperCase()) >= 0;
    }

    // Case-insensitive version of the :startswith selector
    jQuery.expr[':'].istartswith = function(a, i, m) {

        var children = $(a).children();

        var text = children.map(function() { return $.trim($(this).text()).toUpperCase(); });
        var ret = false;
        text.map(function(i, d) { 
            if(d.startsWith(m[3].toUpperCase())) { 
                ret = true; 
            }
        });
        return ret;
    }

    // Hides all the table rows and only shows rows that belong to the current page
    function repaginate(table) {
        table.find('tbody tr').hide()
            .slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
    }

    // Change the current page to this one, and repaginate the table accordingly
    function changePage(event) {
        currentPage = parseInt($(this).text()) - 1;
        repaginate(table);
        $(this).addClass('active').siblings().removeClass('active');
    }

    // Toggles the visibility of the table and loading gif
    function loading() {
        var table = $('table.paginated');
        if(table.css('visibility') == 'hidden') {
            console.log('showing');
            table.css('visibility', 'visible');
            $('img.loading').remove();
        } else {
            console.log('hiding');
            table.css('visibility', 'hidden');
            $('<img class="loading" src="assets/images/ajax-loader.gif"/>').insertBefore(table);
        }
    }
});
