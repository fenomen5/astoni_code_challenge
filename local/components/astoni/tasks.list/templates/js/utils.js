const utils = {
    popupWindow: function (text) {
        return BX.PopupWindowManager.create("popup-message1", null, {
            titleBar: 'Ошибка!',
            content: "<div class='p-5 text-center'>" + text + "</div>",
            width: 500,
            height: 250,
            closeIcon : true,
            closeByEsc : true,
            overlay: {
                backgroundColor: 'gray', opacity: '80'
            }
        })
    }
}