

import RecordController from 'controllers/record';

class ImportController extends RecordController {

    defaultAction = 'index'

    storedData

    checkAccessGlobal() {
        if (this.getAcl().checkScope('Import')) {
            return true;
        }

        return false;
    }

    checkAccess(action) {
        if (this.getAcl().checkScope('Import')) {
            return true;
        }

        return false;
    }

    
    
    actionIndex(o) {
        o = o || {};

        let step = null;

        if (o.step) {
            step = parseInt(step);
        }

        let formData = null;
        let fileContents = null;

        if (o.formData) {
            this.storedData = undefined;
        }

        if (this.storedData) {
            formData = this.storedData.formData;
            fileContents = this.storedData.fileContents;
        }

        if (!formData) {
            step = null;
        }

        formData = formData || o.formData;

        this.main('views/import/index', {
            step: step,
            formData: formData,
            fileContents: fileContents,
            fromAdmin: o.fromAdmin,
        },  view => {
            this.listenTo(view, 'change', () => {
                this.storedData = {
                    formData: view.formData,
                    fileContents: view.fileContents,
                };
            });

            this.listenTo(view, 'done', () => {
                this.storedData = undefined;
            });

            view.render();
        });
    }
}

export default ImportController;
