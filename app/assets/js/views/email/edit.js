window.addEventListener('load', function() {
    var editor;

    // ContentTools.StylePalette.add([
    //     new ContentTools.Style('Author', 'author', ['p'])
    // ]);
    
    editor = ContentTools.EditorApp.get();
    editor.init('*[data-editable]', 'main-content');

    editor.addEventListener('saved', function (ev) {
        new ContentTools.FlashUI('ok');
    });

    $('input[name="submit"]').click(function(ev) {
        var div = $('div[data-name="main-content"]').html();
        $('input[name="EmailBody"]').attr('value', div);
    });

    $('div.ct-ignition__button.ct-ignition__button--edit').click(function(ev) {
        console.log('edit');
        $('input[name="submit"]').disabled = true;
    });

    $('div.ct-ignition__button.ct-ignition__button--confirm').click(function(ev) {
        console.log('submit');
        $('input[name="submit"]').disabled = false;
    });

    $('div.ct-ignition__button.ct-ignition__button--cancel').click(function(ev) {
        console.log('cancel');
        $('input[name="submit"]').disabled = false;
    });
});