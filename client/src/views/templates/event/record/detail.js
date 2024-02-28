/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM – Open Source CRM application.
 * Copyright (C) 2014-2024 Yurii Kuznietsov, Taras Machyshyn, Oleksii Avramenko
 * Website: https://www.espocrm.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

define('views/templates/event/record/detail', ['views/record/detail'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.getAcl().checkModel(this.model, 'edit')) {
                if (['Held', 'Not Held'].indexOf(this.model.get('status')) === -1) {
                    this.dropdownItemList.push({
                        'html': this.translate('Set Held', 'labels', this.scope),
                        'name': 'setHeld',
                    });

                    this.dropdownItemList.push({
                        'html': this.translate('Set Not Held', 'labels', this.scope),
                        'name': 'setNotHeld',
                    });
                }
            }
        },

        actionSetHeld: function () {
            this.model
                .save({status: 'Held'}, {patch: true})
                .then(() => {
                    Espo.Ui.success(this.translate('Saved', 'labels', 'Meeting'));

                    this.removeButton('setHeld');
                    this.removeButton('setNotHeld');
                });
        },

        actionSetNotHeld: function () {
            this.model
                .save({status: 'Not Held'}, {patch: true})
                .then(() => {
                    Espo.Ui.success(this.translate('Saved', 'labels', 'Meeting'));

                    this.removeButton('setHeld');
                    this.removeButton('setNotHeld');
                });
        },
    });
});