

import ModalView from 'views/modal';
import MassActionHelper from 'helpers/mass-action';
import Select from 'ui/select';

class MassUpdateModalView extends ModalView {

    template = 'modals/mass-update'

    cssName = 'mass-update'
    className = 'dialog dialog-record'
    layoutName = 'massUpdate'

    ACTION_UPDATE = 'update'
    
    

    data() {
        return {
            scope: this.scope,
            fieldList: this.fieldList,
            entityType: this.entityType,
        };
    }

    events = {
        
        'click a[data-action="add-field"]': function (e) {
            const field = $(e.currentTarget).data('name');

            this.addField(field);
        },
        
        'click button[data-action="reset"]': function () {
            this.reset();
        }
    }

    setup() {
        this.buttonList = [
            {
                name: 'update',
                label: 'Update',
                style: 'danger',
                disabled: true,
            },
            {
                name: 'cancel',
                label: 'Cancel',
            }
        ];

        this.entityType = this.options.entityType || this.options.scope;
        this.scope = this.options.scope || this.entityType;

        this.ids = this.options.ids;
        this.where = this.options.where;
        this.searchParams = this.options.searchParams;
        this.byWhere = this.options.byWhere;

        this.hasActionMap = {};

        const totalCount = this.options.totalCount;

        this.helper = new MassActionHelper(this);

        this.idle = this.byWhere && this.helper.checkIsIdle(totalCount);

        this.$header = $('<span>')
            .append(
                $('<span>').text(this.translate(this.scope, 'scopeNamesPlural')),
                ' <span class="chevron-right"></span> ',
                $('<span>').text(this.translate('Mass Update'))
            )

        const forbiddenList = this.getAcl().getScopeForbiddenFieldList(this.entityType, 'edit') || [];

        this.wait(true);

        this.getModelFactory().create(this.entityType, (model) => {
            this.model = model;

            this.getHelper().layoutManager.get(this.entityType, this.layoutName, (layout) => {
                layout = layout || [];

                this.fieldList = [];

                layout.forEach((field) => {
                    if (~forbiddenList.indexOf(field)) {
                        return;
                    }

                    if (model.hasField(field)) {
                        this.fieldList.push(field);
                    }
                });

                this.wait(false);
            });
        });

        this.addedFieldList = [];
    }

    addField(name) {
        this.$el.find('[data-action="reset"]').removeClass('hidden');

        this.$el.find('ul.filter-list li[data-name="'+name+'"]').addClass('hidden');

        if (this.$el.find('ul.filter-list li:not(.hidden)').length === 0) {
            this.$el.find('button.select-field').addClass('disabled').attr('disabled', 'disabled');
        }

        this.addedFieldList.push(name);

        const label = this.getHelper().escapeString(
            this.translate(name, 'fields', this.entityType)
        );

        const $cell =
            $('<div>')
                .addClass('cell form-group')
                .attr('data-name', name)
                .append(
                    $('<label>')
                        .addClass('control-label')
                        .text(label)
                )
                .append(
                    $('<div>')
                        .addClass('field')
                        .attr('data-name', name)
                );

        const $row =
            $('<div>')
                .addClass('item grid-auto-fill-md')
                .attr('data-name', name)
                .append($cell);

        this.$el.find('.fields-container').append($row);

        const type = this.model.getFieldType(name);
        const viewName = this.model.getFieldParam(name, 'view') || this.getFieldManager().getViewName(type);

        const actionList = this.getMetadata().get(['entityDefs', this.entityType, name, 'massUpdateActionList']) ||
            this.getMetadata().get(['fields', type, 'massUpdateActionList']);

        const hasActionDropdown = actionList !== null;

        this.hasActionMap[name] = hasActionDropdown;

        this.disableButton('update');

        this.createView(name, viewName, {
            model: this.model,
            selector: '.field[data-name="' + name + '"]',
            defs: {
                name: name,
            },
            mode: 'edit',
        }, view => {
            this.enableButton('update');

            view.render();
        });

        if (hasActionDropdown) {
            const $select =
                $('<select>')
                    .addClass('item-action form-control')
                    .attr('data-name', name);

            actionList.forEach(action => {
                const label = this.translate(Espo.Utils.upperCaseFirst(action));

                $select.append(
                    $('<option>')
                        .text(label)
                        .val(action)
                );
            });

            const $cellAction =
                $('<div>')
                    .addClass('cell call-action form-group')
                    .attr('data-name', name)
                    .append(
                        $('<label>')
                            .addClass('control-label hidden-xs')
                            .html('&nbsp;')
                    )
                    .append(
                        $('<div>')
                            .addClass('field')
                            .attr('data-name', name)
                            .append($select)
                    );

            $row.append($cellAction);

            Select.init($select.get(0));
        }
    }

    
    getFieldView(field) {
        return this.getView(field);
    }

    
    actionUpdate() {
        this.disableButton('update');

        const attributes = {};
        const actions = {};

        this.addedFieldList.forEach(field => {
            const action = this.fetchAction(field);
            const itemAttributes = this.getFieldView(field).fetch();

            const itemActualAttributes = {};

            this.getFieldManager()
                .getEntityTypeFieldActualAttributeList(this.entityType, field)
                .forEach(attribute => {
                    actions[attribute] = action;

                    itemActualAttributes[attribute] = itemAttributes[attribute];
                });

            _.extend(attributes, itemActualAttributes);
        });

        this.model.set(attributes);

        let notValid = false;

        this.addedFieldList.forEach(field => {
            const view = this.getFieldView(field);

            notValid = view.validate() || notValid;
        });

        if (notValid) {
            Espo.Ui.error(this.translate('Not valid'))

            this.enableButton('update');

            return;
        }

        Espo.Ui.notify(this.translate('saving', 'messages'));

        Espo.Ajax
            .postRequest('MassAction', {
                action: 'update',
                entityType: this.entityType,
                params: {
                    ids: this.ids || null,
                    where: (!this.ids || this.ids.length === 0) ? this.options.where : null,
                    searchParams: (!this.ids || this.ids.length === 0) ? this.options.searchParams : null,
                },
                data: {
                    values: attributes,
                    actions: actions,
                },
                idle: this.idle,
            })
            .then(result => {
                result = result || {};

                if (result.id) {
                    this.helper
                        .process(result.id, 'update')
                        .then(view => {
                            this.listenToOnce(view, 'close', () => this.close());

                            this.listenToOnce(view, 'success', result => {
                                this.trigger('after:update', {
                                    count: result.count,
                                    idle: true,
                                });
                            });
                        });

                    return;
                }

                this.trigger('after:update', {
                    count: result.count,
                });
            })
            .catch(() => {
                this.enableButton('update');
            });
    }

    fetchAction(name) {
        if (!this.hasActionMap[name]) {
            return this.ACTION_UPDATE;
        }

        const $dropdown = this.$el.find('select.item-action[data-name="' + name + '"]');

        return $dropdown.val() || this.ACTION_UPDATE;
    }

    reset() {
        this.addedFieldList.forEach(field => {
            this.clearView(field);

            this.$el.find('.item[data-name="'+field+'"]').remove();
        });

        this.addedFieldList = [];
        this.hasActionMap = {};

        this.model.clear();

        this.$el.find('[data-action="reset"]').addClass('hidden');
        this.$el.find('button.select-field').removeClass('disabled').removeAttr('disabled');
        this.$el.find('ul.filter-list').find('li').removeClass('hidden');

        this.disableButton('update');
    }
}

export default MassUpdateModalView;
