


class MassActionHelper {

    
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

        if (typeof totalCount === 'undefined' && this.view.collection) {
            totalCount = this.view.collection.total;
        }

        return totalCount === -1 || totalCount > this.config.get('massActionIdleCountThreshold');
    }

    
    process(id, action) {
        Espo.Ui.notify(false);

        return new Promise(resolve => {
            this.view
                .createView('dialog', 'views/modals/mass-action', {
                    id: id,
                    action: action,
                    scope: this.view.scope || this.view.entityType,
                })
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

export default MassActionHelper;
