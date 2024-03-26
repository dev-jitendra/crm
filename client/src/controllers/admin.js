

import Controller from 'controller';
import SearchManager from 'search-manager';
import SettingsEditView from 'views/settings/edit';
import AdminIndexView from 'views/admin/index';

class AdminController extends Controller {

    checkAccessGlobal() {
        if (this.getUser().isAdmin()) {
            return true;
        }

        return false;
    }

    
    actionPage(options) {
        const page = options.page;

        if (options.options) {
            options = {
                ...Espo.Utils.parseUrlOptionsParam(options.options),
                ...options,
            };

            delete options.options;
        }

        if (!page) {
            throw new Error();
        }

        const methodName = 'action' + Espo.Utils.upperCaseFirst(page);

        if (this[methodName]) {
            this[methodName](options);

            return;
        }

        const defs = this.getPageDefs(page);

        if (!defs) {
            throw new Espo.Exceptions.NotFound();
        }

        if (defs.view) {
            this.main(defs.view, options);

            return;
        }

        if (!defs.recordView) {
            throw new Espo.Exceptions.NotFound();
        }

        const model = this.getSettingsModel();

        model.fetch().then(() => {
            model.id = '1';

            const editView = new SettingsEditView({
                model: model,
                headerTemplate: 'admin/settings/headers/page',
                recordView: defs.recordView,
                page: page,
                label: defs.label,
                optionsToPass: [
                    'page',
                    'label',
                ],
            });

            this.main(editView);
        });
    }

    
    actionIndex(options) {
        let isReturn = options.isReturn;
        const key = this.name + 'Index';

        if (this.getRouter().backProcessed) {
            isReturn = true;
        }

        if (!isReturn && this.getStoredMainView(key)) {
            this.clearStoredMainView(key);
        }

        const view = new AdminIndexView();

        this.main(view, null, view => {
            view.render();

            this.listenTo(view, 'clear-cache', this.clearCache);
            this.listenTo(view, 'rebuild', this.rebuild);
        }, {
            useStored: isReturn,
            key: key,
        });
    }

    
    actionUsers() {
        this.getRouter().dispatch('User', 'list', {fromAdmin: true});
    }

    
    actionPortalUsers() {
        this.getRouter().dispatch('PortalUser', 'list', {fromAdmin: true});
    }

    
    actionApiUsers() {
        this.getRouter().dispatch('ApiUser', 'list', {fromAdmin: true});
    }

    
    actionTeams() {
        this.getRouter().dispatch('Team', 'list', {fromAdmin: true});
    }

    
    actionRoles() {
        this.getRouter().dispatch('Role', 'list', {fromAdmin: true});
    }

    
    actionPortalRoles() {
        this.getRouter().dispatch('PortalRole', 'list', {fromAdmin: true});
    }

    
    actionPortals() {
        this.getRouter().dispatch('Portal', 'list', {fromAdmin: true});
    }

    
    actionLeadCapture() {
        this.getRouter().dispatch('LeadCapture', 'list', {fromAdmin: true});
    }

    
    actionEmailFilters() {
        this.getRouter().dispatch('EmailFilter', 'list', {fromAdmin: true});
    }

    
    actionGroupEmailFolders() {
        this.getRouter().dispatch('GroupEmailFolder', 'list', {fromAdmin: true});
    }

    
    actionEmailTemplates() {
        this.getRouter().dispatch('EmailTemplate', 'list', {fromAdmin: true});
    }

    
    actionPdfTemplates() {
        this.getRouter().dispatch('Template', 'list', {fromAdmin: true});
    }

    
    actionDashboardTemplates() {
        this.getRouter().dispatch('DashboardTemplate', 'list', {fromAdmin: true});
    }

    
    actionWebhooks() {
        this.getRouter().dispatch('Webhook', 'list', {fromAdmin: true});
    }

    
    actionLayoutSets() {
        this.getRouter().dispatch('LayoutSet', 'list', {fromAdmin: true});
    }

    
    actionWorkingTimeCalendar() {
        this.getRouter().dispatch('WorkingTimeCalendar', 'list', {fromAdmin: true});
    }

    
    actionAttachments() {
        this.getRouter().dispatch('Attachment', 'list', {fromAdmin: true});
    }

    
    actionAuthenticationProviders() {
        this.getRouter().dispatch('AuthenticationProvider', 'list', {fromAdmin: true});
    }

    
    actionEmailAddresses() {
        this.getRouter().dispatch('EmailAddress', 'list', {fromAdmin: true});
    }

    
    actionPhoneNumbers() {
        this.getRouter().dispatch('PhoneNumber', 'list', {fromAdmin: true});
    }

    
    actionPersonalEmailAccounts() {
        this.getRouter().dispatch('EmailAccount', 'list', {fromAdmin: true});
    }

    
    actionGroupEmailAccounts() {
        this.getRouter().dispatch('InboundEmail', 'list', {fromAdmin: true});
    }

    
    actionActionHistory() {
        this.getRouter().dispatch('ActionHistoryRecord', 'list', {fromAdmin: true});
    }

    
    actionImport() {
        this.getRouter().dispatch('Import', 'index', {fromAdmin: true});
    }

    
    actionLayouts(options) {
        const scope = options.scope || null;
        const type = options.type || null;
        const em = options.em || false;

        this.main('views/admin/layouts/index', {scope: scope, type: type, em: em});
    }

    
    actionLabelManager(options) {
        const scope = options.scope || null;
        const language = options.language || null;

        this.main('views/admin/label-manager/index', {scope: scope, language: language});
    }

    
    actionTemplateManager(options) {
        const name = options.name || null;

        this.main('views/admin/template-manager/index', {name: name});
    }

    
    actionFieldManager(options) {
        const scope = options.scope || null;
        const field = options.field || null;

        this.main('views/admin/field-manager/index', {scope: scope, field: field});
    }

    
    
    actionEntityManager(options) {
        const scope = options.scope || null;

        if (scope && options.edit) {
            this.main('views/admin/entity-manager/edit', {scope: scope});

            return;
        }

        if (options.create) {
            this.main('views/admin/entity-manager/edit');

            return;
        }

        if (scope && options.formula) {
            this.main('views/admin/entity-manager/formula', {scope: scope, type: options.type});

            return;
        }

        if (scope) {
            this.main('views/admin/entity-manager/scope', {scope: scope});

            return;
        }

        this.main('views/admin/entity-manager/index');
    }

    
    actionLinkManager(options) {
        const scope = options.scope || null;

        this.main('views/admin/link-manager/index', {scope: scope});
    }

    
    actionSystemRequirements() {
        this.main('views/admin/system-requirements/index');
    }

    
    getSettingsModel() {
        const model = this.getConfig().clone();
        model.defs = this.getConfig().defs;

        this.listenTo(model, 'after:save', () => {
            this.getConfig().load();

            this._broadcastChannel.postMessage('update:config');
        });

        
        return model;
    }

    
    actionAuthTokens() {
        this.collectionFactory.create('AuthToken', collection => {
            const searchManager = new SearchManager(
                collection,
                'list',
                this.getStorage(),
                this.getDateTime()
            );

            searchManager.loadStored();
            collection.where = searchManager.getWhere();
            collection.maxSize = this.getConfig().get('recordsPerPage') || collection.maxSize;

            this.main('views/admin/auth-token/list', {
                scope: 'AuthToken',
                collection: collection,
                searchManager: searchManager
            });
        });
    }

    
    actionAuthLog() {
        this.collectionFactory.create('AuthLogRecord', collection => {
            const searchManager = new SearchManager(
                collection,
                'list',
                this.getStorage(),
                this.getDateTime()
            );

            searchManager.loadStored();

            collection.where = searchManager.getWhere();
            collection.maxSize = this.getConfig().get('recordsPerPage') || collection.maxSize;

            this.main('views/admin/auth-log-record/list', {
                scope: 'AuthLogRecord',
                collection: collection,
                searchManager: searchManager
            });
        });
    }

    
    actionJobs() {
        this.collectionFactory.create('Job', collection => {
            const searchManager = new SearchManager(
                collection,
                'list',
                this.getStorage(),
                this.getDateTime()
            );

            searchManager.loadStored();

            collection.where = searchManager.getWhere();
            collection.maxSize = this.getConfig().get('recordsPerPage') || collection.maxSize;

            this.main('views/admin/job/list', {
                scope: 'Job',
                collection: collection,
                searchManager: searchManager,
            });
        });
    }

    
    actionIntegrations(options) {
        const integration = options.name || null;

        this.main('views/admin/integrations/index', {integration: integration});
    }

    
    actionExtensions() {
        this.main('views/admin/extensions/index');
    }

    rebuild() {
        if (this.rebuildRunning) {
            return;
        }

        this.rebuildRunning = true;

        const master = this.get('master');

        Espo.Ui.notify(master.translate('pleaseWait', 'messages'));

        Espo.Ajax
            .postRequest('Admin/rebuild')
            .then(() => {
                const msg = master.translate('Rebuild has been done', 'labels', 'Admin');

                Espo.Ui.success(msg);

                this.rebuildRunning = false;
            })
            .catch(() => {
                this.rebuildRunning = false;
            });
    }

    clearCache() {
        if (this.clearCacheRunning) {
            return;
        }

        this.clearCacheRunning = true;

        const master = this.get('master');

        Espo.Ui.notify(master.translate('pleaseWait', 'messages'));

        Espo.Ajax.postRequest('Admin/clearCache')
            .then(() => {
                const msg = master.translate('Cache has been cleared', 'labels', 'Admin');

                Espo.Ui.success(msg);

                this.clearCacheRunning = false;
            })
            .catch(() => {
                this.clearCacheRunning = false;
            });
    }

    
    getPageDefs(page) {
        const panelsDefs = this.getMetadata().get(['app', 'adminPanel']) || {};

        let resultDefs = null;

        for (const panelKey in panelsDefs) {
            const itemList = panelsDefs[panelKey].itemList || [];

            for (const defs of itemList) {
                if (defs.url === '#Admin/' + page) {
                    resultDefs = defs;

                    break;
                }
            }

            if (resultDefs) {
                break;
            }
        }

        return resultDefs;
    }
}

export default AdminController;
