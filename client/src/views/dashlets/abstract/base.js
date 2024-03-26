



import View from 'view';


class BaseDashletView extends View {

    
    optionsData = null

    optionsFields = {
        title: {
            type: 'varchar',
            required: true,
        },
        autorefreshInterval: {
            type: 'enumFloat',
            options: [0, 0.5, 1, 2, 5, 10],
        },
    }

    disabledForReadOnlyActionList = ['options', 'remove']
    disabledForLockedActionList = ['remove']

    noPadding = false

    

    

    
    buttonList = []

    
    actionList = [
        {
            name: 'refresh',
            label: 'Refresh',
            iconHtml: '<span class="fas fa-sync-alt"></span>',
        },
        {
            name: 'options',
            label: 'Options',
            iconHtml: '<span class="fas fa-pencil-alt"></span>',
        },
        {
            name: 'remove',
            label: 'Remove',
            iconHtml: '<span class="fas fa-times"></span>',
        },
    ]

    
    actionRefresh() {
        this.render();
    }

    
    actionOptions() {}

    init() {
        this.name = this.options.name || this.name;
        this.id = this.options.id;

        this.defaultOptions = this.getMetadata().get(['dashlets', this.name, 'options', 'defaults']) ||
            this.defaultOptions || {};

        this.defaultOptions = {
            title: this.getLanguage().translate(this.name, 'dashlets'),
            ...this.defaultOptions
        };

        this.defaultOptions = Espo.Utils.clone(this.defaultOptions);

        this.optionsFields = this.getMetadata().get(['dashlets', this.name, 'options', 'fields']) ||
            this.optionsFields || {};

        this.optionsFields = Espo.Utils.clone(this.optionsFields);

        this.setupDefaultOptions();

        let options = Espo.Utils.cloneDeep(this.defaultOptions);

        for (let key in options) {
            if (typeof options[key] == 'function') {
                options[key] = options[key].call(this);
            }
        }

        let storedOptions;

        if (!this.options.readOnly) {
            storedOptions = this.getPreferences().getDashletOptions(this.id) || {};
        }
        else {
            let allOptions = this.getConfig().get('forcedDashletsOptions') ||
                this.getConfig().get('dashletsOptions') || {};

            storedOptions = allOptions[this.id] || {};
        }

        this.optionsData = _.extend(options, storedOptions);

        if (this.optionsData.autorefreshInterval) {
            let interval = this.optionsData.autorefreshInterval * 60000;

            let t;

            let process = () => {
                t = setTimeout(() => {
                    this.actionRefresh();

                    process();
                }, interval);
            };

            process();

            this.once('remove', () => {
                clearTimeout(t);
            });
        }

        this.actionList = Espo.Utils.clone(this.actionList);
        this.buttonList = Espo.Utils.clone(this.buttonList);

        if (this.options.readOnly) {
            this.actionList = this.actionList.filter(item => {
                if (~this.disabledForReadOnlyActionList.indexOf(item.name)) {
                    return false;
                }

                return true;
            })
        }

        if (this.options.locked) {
            this.actionList = this.actionList
                .filter(item => !this.disabledForLockedActionList.includes(item.name));
        }

        this.setupActionList();
        this.setupButtonList();
    }

    
    setupDefaultOptions() {}

    
    setupActionList() {}

    
    setupButtonList() {}

    
    hasOption(key) {
        return key in this.optionsData;
    }

    
    getOption(key) {
        return this.optionsData[key];
    }

    
    getTitle() {
        let title = this.getOption('title');

        if (!title) {
            title = null;
        }

        return title;
    }

    
    getContainerView() {
        return this.getParentView();
    }

    
    handleAction(event, element) {
        Espo.Utils.handleAction(this, event, element, {
            actionItems: [...this.buttonList, ...this.actionList],
            className: 'dashlet-action',
        });
    }
}

export default BaseDashletView;
