



import ModalView from 'views/modal';
import EditForModalRecordView from 'views/record/edit-for-modal';
import Model from 'model';
import EnumFieldView from 'views/fields/enum';
import VarcharFieldView from 'views/fields/varchar';

class LayoutCreateModalView extends ModalView {

    
    templateContent = `
        <div class="complex-text-container">{{complexText info}}</div>
        <div class="record no-side-margin">{{{record}}}</div>
    `

    className = 'dialog dialog-record'

    

    
    constructor(options) {
        super();

        this.scope = options.scope;
    }

    data() {
        return {
            info: this.translate('createInfo', 'messages', 'LayoutManager'),
        }
    }

    setup() {
        this.headerText = this.translate('Create');

        this.buttonList = [
            {
                name: 'create',
                style: 'danger',
                label: 'Create',
                onClick: () => this.actionCreate(),
            },
            {
                name: 'cancel',
                label: 'Cancel',
            },
        ];

        this.model = new Model({
            type: 'list',
            name: 'listForMyEntityType',
            label: 'List (for MyEntityType)',
        });

        this.recordView = new EditForModalRecordView({
            model: this.model,
            detailLayout: [
                {
                    columns: [
                        [
                            {
                                view: new EnumFieldView({
                                    name: 'type',
                                    params: {
                                        readOnly: true,
                                        translation: 'Admin.layouts',
                                        options: ['list'],
                                    },
                                    labelText: this.translate('type', 'fields', 'Admin'),
                                }),
                            },
                            {
                                view: new VarcharFieldView({
                                    name: 'name',
                                    params: {
                                        required: true,
                                        noSpellCheck: true,
                                        pattern: '$latinLetters',
                                    },
                                    labelText: this.translate('name', 'fields'),
                                }),
                            },
                            {
                                view: new VarcharFieldView({
                                    name: 'label',
                                    params: {
                                        required: true,
                                        pattern: '$noBadCharacters',
                                    },
                                    labelText: this.translate('label', 'fields', 'Admin'),
                                }),
                            },
                        ],
                        []
                    ]
                }
            ]
        });

        this.assignView('record', this.recordView, '.record');
    }

    actionCreate() {
        this.recordView.fetch();

        if (this.recordView.validate()) {
            return;
        }

        this.disableButton('create');

        Espo.Ui.notify(' ... ');

        Espo.Ajax
            .postRequest('Layout/action/create', {
                scope: this.scope,
                type: this.model.get('type'),
                name: this.model.get('name'),
                label: this.model.get('label'),
            })
            .then(() => {
                this.reRender();

                Espo.Ui.success('Created', {suppress: true});

                this.trigger('done');

                this.close();
            })
            .catch(() => {
                this.enableButton('create');
            });
    }
}

export default LayoutCreateModalView;
