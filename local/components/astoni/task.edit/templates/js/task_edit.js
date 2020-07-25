const task_edit = {

    init: function () {
        $('#UF_TASK_DATETIME').datetimepicker({
            format: 'DD.MM.YYYY HH:mm:ss',
        });
    }
}

$(document).ready(function () {
    task_edit.init();
});