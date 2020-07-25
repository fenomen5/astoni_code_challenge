const task_list = {

    task: {
      iconReturn: '<i class="fa fa-undo"></i>',
      iconComplete: '<i class="fa fa-check"></i>',

      updateStatus: function (task_id, status) {
          let task = $('#report-result-table [data-id="' + task_id +'"]');
          $(task).find('.UF_STATUS').text(status);
          let statusBtn = $(task).find('.update-status');

          if ($(statusBtn).data('status') == 2) {
              $(statusBtn).html(this.iconComplete);
              $(statusBtn).data('status', 1);
          } else {
              $(statusBtn).html(this.iconReturn);
              $(statusBtn).data('status', 2);
          }
      }  
    },

    init: function () {
        $(document).on('click', '.update-status', task_list.updateStatus);
        $(document).on('click', '.remove', task_list.removeTask);
    },
    updateStatus: function () {
        let task_id = $(this).parents('tr').data('id');
        let status = $(this).data('status') == 1 ? 2 : 1;
        let updateStatusRequest = BX.ajax.runComponentAction('astoni:tasks.list','updateStatus', {
            mode:'class',
            data: {
                task_id: task_id,
                status: status
            },
    });
        updateStatusRequest.then(function(response) {

            if (response.status = 'success') {
                task_list.task.updateStatus(response.data.task.ID, response.data.task.UF_STATUS['VALUE']);
            }
        }, function () {
            utils.popupWindow('Не удалось обновить статус задачи').show();
        } );
    },
    removeTask: function () {
        let task_id = $(this).parents('tr').data('id');

        let removeTaskRequest = BX.ajax.runComponentAction('astoni:tasks.list','removeTask', {
            mode:'class',
            data: {
                task_id: task_id
            },
        });
        removeTaskRequest.then(function(response) {
            if (response.status = 'success') {
                $('#report-result-table [data-id="' + task_id +'"]').remove();
            }
        }, function () {
            utils.popupWindow('Не удалось удалить задачу').show();
        } );
    }
}

$(document).ready()
{
    task_list.init();
}