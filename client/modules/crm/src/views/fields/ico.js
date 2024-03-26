

define('crm:views/fields/ico', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        
        templateContent: `{{! ~}}
            <span
                class="{{iconClass}} text-muted action icon"
                style="cursor: pointer"
                title="{{viewLabel}}"
                data-action="quickView"
                data-id="{{id}}"
                {{#if notRelationship}}data-scope="{{scope}}"{{/if}}
            ></span>
        {{~!}}`,

        data: function () {
            return {
                notRelationship: this.params.notRelationship,
                viewLabel: this.translate('View'),
                id: this.model.id,
                scope: this.model.entityType,
                iconClass: this.getMetadata().get(['clientDefs', this.model.entityType, 'iconClass']) ||
                    'far fa-calendar-times',
            };
        },
    });
});
