



import PanelsContainerRecordView from 'views/record/panels-container';


class DetailBottomRecordView extends PanelsContainerRecordView {

    
    template = 'record/bottom'

    
    mode = 'detail'
    streamPanel = true
    relationshipPanels = true
    readOnly = false
    portalLayoutDisabled = false
    name = 'bottom'

    
    setupPanels() {
        const scope = this.scope;

        this.panelList = Espo.Utils.clone(
            this.getMetadata()
                .get(['clientDefs', scope, 'bottomPanels', this.type]) || this.panelList || []);

        this.panelList.forEach(item => {
            if ('index' in item) {
                return;
            }

            if ('order' in item) {
                item.index = item.order;
            }
        });

        if (this.streamPanel && this.getMetadata().get(['scopes', scope, 'stream'])) {
            this.setupStreamPanel();
        }
    }

    
    setupStreamPanel() {
        let streamAllowed = this.getAcl().checkModel(this.model, 'stream', true);

        if (streamAllowed === null) {
            this.listenToOnce(this.model, 'sync', () => {
                streamAllowed = this.getAcl().checkModel(this.model, 'stream', true);

                if (streamAllowed) {
                    this.onPanelsReady(() => {
                        this.showPanel('stream', 'acl');
                    });
                }
            });
        }

        if (streamAllowed !== false) {
            this.panelList.push({
                name: 'stream',
                label: 'Stream',
                view: this.getMetadata().get(['clientDefs', this.scope, 'streamPanelView']) || 'views/stream/panel',
                sticked: true,
                hidden: !streamAllowed,
                index: 2,
            });

            if (!streamAllowed) {
                this.recordHelper.setPanelStateParam('stream', 'hiddenAclLocked', true);
            }
        }
    }

    init() {
        this.recordHelper = this.options.recordHelper;
        this.scope = this.entityType = this.model.name;

        this.readOnlyLocked = this.options.readOnlyLocked || this.readOnly;
        this.readOnly = this.options.readOnly || this.readOnly;
        this.inlineEditDisabled = this.options.inlineEditDisabled || this.inlineEditDisabled;

        this.portalLayoutDisabled = this.options.portalLayoutDisabled || this.portalLayoutDisabled;

        this.recordViewObject = this.options.recordViewObject;
    }

    setup() {
        this.type = this.mode;

        if ('type' in this.options) {
            this.type = this.options.type;
        }

        this.panelList = [];

        this.setupPanels();

        this.wait(true);

        Promise.all([
            new Promise(resolve => {
                this.getHelper().layoutManager.get(
                    this.scope,
                    'bottomPanels' + Espo.Utils.upperCaseFirst(this.type),
                    (layoutData) => {
                        this.layoutData = layoutData;

                        resolve();
                    }
                );
            })
        ]).then(() => {
            const panelNameList = [];

            this.panelList = this.panelList.filter(p => {
                panelNameList.push(p.name);

                if (p.aclScope) {
                    if (!this.getAcl().checkScope(p.aclScope)) {
                        return;
                    }
                }

                if (p.accessDataList) {
                    if (!Espo.Utils.checkAccessDataList(p.accessDataList, this.getAcl(), this.getUser())) {
                        return false;
                    }
                }

                return true;
            });

            if (this.relationshipPanels) {
                const linkDefs = (this.model.defs || {}).links || {};

                if (this.layoutData) {
                    for (const name in this.layoutData) {
                        if (!linkDefs[name]) {
                            continue;
                        }

                        const p = this.layoutData[name];

                        if (!~panelNameList.indexOf(name) && !p.disabled) {
                            this.addRelationshipPanel(name, p);
                        }
                    }
                }
            }

            this.panelList = this.panelList.map((p) => {
                const item = Espo.Utils.clone(p);

                if (this.recordHelper.getPanelStateParam(p.name, 'hidden') !== null) {
                    item.hidden = this.recordHelper.getPanelStateParam(p.name, 'hidden');
                }
                else {
                    this.recordHelper.setPanelStateParam(p.name, 'hidden', item.hidden || false);
                }

                return item;
            });

            this.panelList.forEach((item) => {
                item.actionsViewKey = item.name + 'Actions';
            });

            this.alterPanels();
            this.setupPanelsFinal();
            this.setupPanelViews();

            this.wait(false);
        });
    }

    
    setReadOnly() {
        this.readOnly = true;
    }

    
    addRelationshipPanel(name, item) {
        const scope = this.scope;
        const scopesDefs = this.getMetadata().get('scopes') || {};

        let p;

        if (typeof item === 'string' || item instanceof String) {
            p = {name: item};
        }
        else {
            p = Espo.Utils.clone(item || {});
        }

        p.name = p.name || name;

        if (!p.name) {
            return;
        }

        if (typeof p.order === 'undefined') p.order = 5;

        name = p.name;

        const links = (this.model.defs || {}).links || {};

        if (!(name in links)) {
            return;
        }

        const foreignScope = links[name].entity;

        if ((scopesDefs[foreignScope] || {}).disabled) {
            return;
        }

        if (!this.getAcl().check(foreignScope, 'read')) {
            return;
        }

        let defs = this.getMetadata().get(['clientDefs', scope, 'relationshipPanels', name]) || {};

        defs = Espo.Utils.clone(defs);

        for (const i in defs) {
            if (i in p) {
                continue;
            }

            p[i] = defs[i];
        }

        if (!p.view) {
            p.view = 'views/record/panels/relationship';
        }

        if (this.recordHelper.getPanelStateParam(p.name, 'hidden') !== null) {
            p.hidden = this.recordHelper.getPanelStateParam(p.name, 'hidden');
        }
        else {
            this.recordHelper.setPanelStateParam(p.name, 'hidden', p.hidden || false);
        }

        this.panelList.push(p);
    }
}

export default DetailBottomRecordView;
