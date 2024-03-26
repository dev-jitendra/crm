


class ExportHelper {

    
    constructor(view) {
        
        this.view = view;

        
        this.config = view.getConfig();
    }

    
    checkIsIdle(totalCount) {
        if (this.view.getUser().isPortal()) {
            return false;
        }

        if (typeof totalCount === 'undefined') {
            totalCount = this.view.options.totalCount;
        }

        return totalCount === -1 || totalCount > this.config.get('exportIdleCountThreshold');
    }

    
    process(id) {
        Espo.Ui.notify(false);

        return new Promise(resolve => {
            this.view.createView('dialog', 'views/export/modals/idle', {id: id})
                .then(view => {
                    view.render();

                    resolve(view);

                    this.view.listenToOnce(view, 'success', data => {
                        resolve(data);

                        this.view.listenToOnce(view, 'close', () => {
                            view.trigger('close:success', data);
                        });
                    });
                });
        });
    }
}

export default ExportHelper;
