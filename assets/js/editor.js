(function () {
    tinymce.PluginManager.add('ep_order_form', function (editor) {

        editor.addButton('ep_order_form', {
            text: 'Форма оплаты',
            icon: false,

            onclick: function () {

                var postId = editor.settings.post_id || 0;

                if (!postId) {
                    alert('Не удалось определить ID товара');
                    return;
                }

                editor.insertContent('[ep_order_form product_id="' + postId + '"]');
            }
        });

    });
})();
