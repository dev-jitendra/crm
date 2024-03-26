

define('views/admin/formula-sandbox/record/edit', ['views/record/edit'], function (Dep) {

    return Dep.extend({

        scriptAreaHeight: 400,

        bottomView: null,

        sideView: null,

        buttonList: [
            {
                name: 'run',
                label: 'Run',
                style: 'danger',
                title: 'Ctrl+Enter',
            },
        ],

        dropdownItemList: [],

        isWide: true,

        accessControlDisabled: true,

        saveAndContinueEditingAction: false,

        saveAndNewAction: false,

        shortcutKeyCtrlEnterAction: 'run',

        setup: function () {
            this.scope = 'Formula';

            let additionalFunctionDataList = [
                {
                    "name": "output\\print",
                    "insertText": "output\\print(VALUE)"
                },
                {
                    "name": "output\\printLine",
                    "insertText": "output\\printLine(VALUE)"
                }
            ];

            this.detailLayout = [
                {
                    rows: [
                        [
                            false,
                            {
                                name: 'targetType',
                                labelText: this.translate('targetType', 'fields', 'Formula'),
                            },
                            {
                                name: 'target',
                                labelText: this.translate('target', 'fields', 'Formula'),
                            },
                        ]
                    ]
                },
                {
                    rows: [
                        [
                            {
                                name: 'script',
                                noLabel: true,
                                options: {
                                    targetEntityType: this.model.get('targetType'),
                                    height: this.scriptAreaHeight,
                                    additionalFunctionDataList: additionalFunctionDataList,
                                },
                            },
                        ]
                    ]
                },
                {
                    name: 'output',
                    rows: [
                        [
                            {
                                name: 'errorMessage',
                                labelText: this.translate('error', 'fields', 'Formula'),
                            },
                        ],
                        [
                            {
                                name: 'output',
                                labelText: this.translate('output', 'fields', 'Formula'),
                            },
                        ]
                    ]
                },
            ];

            Dep.prototype.setup.call(this);

            if (!this.model.get('targetType')) {
                this.hideField('target');
            }
            else {
                this.showField('target');
            }

            this.controlTargetTypeField();
            this.listenTo(this.model, 'change:targetId', () => this.controlTargetTypeField());

            this.controlOutputField();
            this.listenTo(this.model, 'change', () => this.controlOutputField());
        },

        controlTargetTypeField: function () {
            if (this.model.get('targetId')) {
                this.setFieldReadOnly('targetType');

                return;
            }

            this.setFieldNotReadOnly('targetType');
        },

        controlOutputField: function () {
            if (this.model.get('errorMessage')) {
                this.showField('errorMessage');
            }
            else {
                this.hideField('errorMessage');
            }
        },

        actionRun: function () {
            this.model.trigger('run');
        },
    });
});
