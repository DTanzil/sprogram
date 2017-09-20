/**
 * Contains functionality for the app flags feature on the committee screen
 * Assigns onclick handlers for every td where the flag can be toggled, sends an
 * ajax request to the Applications model to update the status of the flag
 */
$(document).ready(function() {
    var baseUrl = window.sprogram.baseUrl;

    if($('thead th:contains("Flag")').length > 0) {

        $('tbody td.flag').click(function(event) {
            var td = $(this);
            var id = $(this).attr('id');

            //if td element has children, then a flag has been applied previously
            //toggle the flag value and ensure its an integer
            var currentFlag = $(this).children().length > 0 ? 1 : 0;
            var newFlag = currentFlag == 0 ? 1 : 0;

            var data = {
                flag:newFlag,
                id:id
            }

            $.ajax({
                method:'POST',
                url: baseUrl + 'Applications/updateFlag',
                data:data,
                dataType:'json',
                success:updateFlag,
                error:function(xhr) {
                    console.error(xhr);
                }
            });

            //append or remove the flag div depending on current status of the flag
            function updateFlag(data) {
                console.log(data);
                if(data == '1') {
                    td.append('<div class="flagged"></div>');
                } else {
                    td.empty();
                }
            }
        });

    }

});