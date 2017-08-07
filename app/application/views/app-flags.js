$(document).ready(function() {
    if($('thead th:contains("Flag")').length > 0) {

        $('tbody td.flag').click(function(event) {
            var td = $(this);
            var currentFlag = $(this).children().length > 0 ? 1 : 0;
            var id = $(this).attr('id');

            //if(currentFlag === ' ') {currentFlag = '0';}
            var newFlag = currentFlag == 0 ? 1 : 0;

            var data = {
                flag:newFlag,
                id:id
            }

            $.ajax({
                method:'POST',
                url: <?= '"' . base_url() . 'index.php/Applications/updateFlag' . '"'?>,
                data:data,
                dataType:'json',
                success:updateFlag,
                error:function(xhr) {
                    console.error(xhr);
                }
            });

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