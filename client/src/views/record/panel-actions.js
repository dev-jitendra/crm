

import View from 'view';

class PanelActionsView extends View {

    template = 'record/panel-actions'

    data() {
        return {
            defs: this.options.defs,
            buttonList: this.getButtonList(),
            actionList: this.getActionList(),
            entityType: this.options.entityType,
            scope: this.options.scope,
        };
    }

    setup() {
        this.buttonList = this.options.defs.buttonList || [];
        this.actionList = this.options.defs.actionList || [];
        this.defs = this.options.defs;
    }

    getButtonList() {
        const list = [];

        this.buttonList.forEach(item => {
            if (item.hidden) {
                return;
            }

            list.push(item);
        });

        return list;
    }

    getActionList() {
        return this.actionList
            .filter(item => !item.hidden)
            .map(item => {
                item = Espo.Utils.clone(item);

                if (item.action) {
                    item.data = Espo.Utils.clone(item.data || {});
                    item.data.panel = this.options.defs.name;
                }

                return item;
            });
    }
}

export default PanelActionsView;
