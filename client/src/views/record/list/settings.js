

import View from 'view';

class RecordListSettingsView extends View {

    
    templateContent = `
        {{#if toDisplay}}
        <div class="btn-group">
            <a
                role="button"
                class="btn btn-text dropdown-toggle"
                data-toggle="dropdown"
            ><span class="fas fa-cog fa-sm"></span></a>
            <ul class="dropdown-menu pull-right">
            {{#each dataList}}
                <li>
                    <a
                        role="button"
                        tabindex="0"
                        data-action="toggleColumn"
                        data-name="{{name}}"
                    ><span class="check-icon fas fa-check pull-right{{#if hidden}} hidden{{/if}}"></span><div>{{label}}</div></a>
                </li>
            {{/each}}
            {{#if isNotDefault}}
                <li class="divider"></li>
                <li>
                    <a
                        role="button"
                        tabindex="0"
                        data-action="resetToDefault"
                    >{{translate 'Reset'}}</a>
                </li>
            {{/if}}
            </ul>
        </div>
        {{/if}}
    `

    data() {
        const dataList = this.getDataList();
        const isNotDefault = dataList.find(item => item.hiddenDefault !== item.hidden) !== undefined;

        return {
            dataList: dataList,
            toDisplay: dataList.length > 0,
            isNotDefault: isNotDefault,
        };
    }

    
    constructor(options) {
        super();

        this.layoutProvider = options.layoutProvider;
        this.helper = options.helper;
        this.entityType = options.entityType;
        this.onChange = options.onChange;
    }

    setup() {
        this.addActionHandler('toggleColumn', (e, target) => this.toggleColumn(target.dataset.name));
        this.addActionHandler('resetToDefault', () => this.resetToDefault());
    }

    
    getDataList() {
        const list = this.layoutProvider() || [];
        const map = this.helper.getHiddenColumnMap() || {};

        return list.filter(item => item.name && !item.link && !item.noLabel && !item.customLabel)
            .map(item => {
                const label = item.label || item.name;
                const hidden = (item.name in map) ? map[item.name] : !!item.hidden;

                return {
                    name: item.name,
                    label: this.translate(label, 'fields', this.entityType),
                    hidden: hidden,
                    hiddenDefault: !!item.hidden,
                };
            })
    }

    
    toggleColumn(name) {
        const map = this.helper.getHiddenColumnMap() || {};

        const item = this.getDataList().find(item => item.name === name);

        const defaultValue = item ? item.hiddenDefault : false;

        map[name] = !((name in map) ? map[name] : defaultValue);

        this.helper.storeHiddenColumnMap(map);

        this.onChange();
    }

    
    resetToDefault() {
        this.helper.clearHiddenColumnMap();

        this.onChange();
    }
}

export default RecordListSettingsView;
