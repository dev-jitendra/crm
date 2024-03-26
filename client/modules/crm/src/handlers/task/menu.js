

import ActionHandler from 'action-handler';

class TaskMenuHandler extends ActionHandler {

    complete() {
        const model = this.view.model;

        model
            .save({status: 'Completed'}, {patch: true})
            .then(() => {
                Espo.Ui.success(this.view.getLanguage().translateOption('Completed', 'status', 'Task'));
            });
    }

    
    isCompleteAvailable() {
        const status = this.view.model.get('status');

        const view = this.view;

        if (view.getRecordView().isEditMode()) {
            return false;
        }

        
        const notActualStatuses = this.view.getMetadata().get('entityDefs.Task.fields.status.notActualOptions') || [];

        return !notActualStatuses.includes(status);
    }
}

export default TaskMenuHandler;
