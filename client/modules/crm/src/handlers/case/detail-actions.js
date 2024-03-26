

import ActionHandler from 'action-handler';

class CaseDetailActionHandler extends ActionHandler {

    close() {
        const model = this.view.model;

        model.save({status: 'Closed'}, {patch: true})
            .then(() => {
                Espo.Ui.success(this.view.translate('Closed', 'labels', 'Case'));
            });
    }

    reject() {
        const model = this.view.model;

        model.save({status: 'Rejected'}, {patch: true})
            .then(() => {
                Espo.Ui.success(this.view.translate('Rejected', 'labels', 'Case'));
            });
    }

    
    isCloseAvailable() {
        return this.isStatusAvailable('Closed');
    }

    
    isRejectAvailable() {
        return this.isStatusAvailable('Rejected');
    }

    isStatusAvailable(status) {
        const model = this.view.model;
        const acl = this.view.getAcl();
        const metadata = this.view.getMetadata();

        
        const notActualStatuses = metadata.get('entityDefs.Case.fields.status.notActualOptions') || [];

        if (notActualStatuses.includes(model.get('status'))) {
            return false;
        }

        if (!acl.check(model, 'edit')) {
            return false;
        }

        if (!acl.checkField(model.entityType, 'status', 'edit')) {
            return false;
        }

        const statusList = metadata.get(['entityDefs', 'Case', 'fields', 'status', 'options']) || [];

        if (!statusList.includes(status)) {
            return false;
        }

        return true;
    }
}

export default CaseDetailActionHandler;
