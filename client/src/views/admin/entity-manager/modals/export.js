

import ModalView from 'views/modal';
import Model from 'model';
import EditForModalRecordView from 'views/record/edit-for-modal';
import VarcharFieldView from 'views/fields/varchar';

class EntityManagerExportModalView extends ModalView {

    
    templateContent = `
        <div class="record-container no-side-margin">{{{record}}}</div>
    `

    setup() {
        this.headerText = this.translate('Export');

        this.buttonList = [
            {
                name: 'export',
                label: 'Export',
                style: 'danger',
                onClick: () => this.export(),
            },
            {
                name: 'cancel',
                label: 'Cancel',
            },
        ];

        let manifest = this.getConfig().get('customExportManifest') || {};

        this.model = new Model({
            name: manifest.name ?? null,
            module: manifest.module ?? null,
            version: manifest.version ?? '0.0.1',
            author: manifest.author ?? null,
            description: manifest.description ?? null,
        });

        this.recordView = new EditForModalRecordView({
            model: this.model,
            detailLayout: [
                {
                    rows: [
                        [
                            {
                                view: new VarcharFieldView({
                                    name: 'name',
                                    labelText: this.translate('name', 'fields'),
                                    params: {
                                        pattern: '$latinLettersDigitsWhitespace',
                                        required: true,
                                    },

                                }),
                            },
                            {
                                view: new VarcharFieldView({
                                    name: 'module',
                                    labelText: this.translate('module', 'fields', 'EntityManager'),
                                    params: {
                                        pattern: '[A-Z][a-z][A-Za-z]+',
                                        required: true,
                                    },
                                }),
                            },
                        ],
                        [
                            {
                                view: new VarcharFieldView({
                                    name: 'version',
                                    labelText: this.translate('version', 'fields', 'EntityManager'),
                                    params: {
                                        pattern: '[0-9]+\\.[0-9]+\\.[0-9]+',
                                        required: true,
                                    },
                                }),
                            },
                            false
                        ],
                        [
                            {
                                view: new VarcharFieldView({
                                    name: 'author',
                                    labelText: this.translate('author', 'fields', 'EntityManager'),
                                    params: {
                                        required: true,
                                    },
                                }),

                            },
                            {
                                view: new VarcharFieldView({
                                    name: 'description',
                                    labelText: this.translate('description', 'fields'),
                                    params: {},

                                }),
                            },
                        ],
                    ]
                }
            ]
        });

        this.assignView('record', this.recordView);
    }

    export() {
        const data = this.recordView.fetch();

        if (this.recordView.validate()) {
            return;
        }

        this.disableButton('export');

        Espo.Ui.notify(' ... ');

        Espo.Ajax
            .postRequest('EntityManager/action/exportCustom', data)
            .then(response => {
                this.close();

                this.getConfig().set('customExportManifest', data);

                Espo.Ui.success(this.translate('Done'));

                window.location = this.getBasePath() + '?entryPoint=download&id=' + response.id;
            })
            .catch(() => this.enableButton('create'));
    }
}

export default EntityManagerExportModalView;
