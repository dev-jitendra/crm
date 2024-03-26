

import ActionHandler from 'action-handler';

class ImportHandler extends ActionHandler {

    
    actionErrorExport() {
        Espo.Ajax
            .postRequest(`Import/${this.view.model.id}/exportErrors`)
            .then(data => {
                if (!data.attachmentId) {
                    const message = this.view.translate('noErrors', 'messages', 'Import');

                    Espo.Ui.warning(message);

                    return;
                }

                window.location = this.view.getBasePath() + '?entryPoint=download&id=' + data.attachmentId;
            });
    }
}

export default ImportHandler;
