

define('views/admin/entity-manager/modals/select-formula', ['views/modal'], function (Dep) {

    
    return Dep.extend({

        
        templateContent: `
            <div class="panel no-side-margin">
                <table class="table table-bordered">
                    {{#each typeList}}
                    <tr>
                        <td style="width: 40%">
                            <a
                                class="btn btn-default btn-lg btn-full-wide"
                                href="#Admin/entityManager/formula&scope={{../scope}}&type={{this}}"
                            >
                            {{translate this category='fields' scope='EntityManager'}}
                            </a>
                        </td>
                        <td style="width: 60%">
                            <div class="complex-text">{{complexText (translate this category='messages' scope='EntityManager')}}
                        </td>
                    </tr>
                    {{/each}}
                </table>
            </div>
        `,

        backdrop: true,

        data: function () {
            return {
                typeList: this.typeList,
                scope: this.scope,
            };
        },

        setup: function () {
            this.scope = this.options.scope;

            this.typeList = [
                'beforeSaveCustomScript',
                'beforeSaveApiScript',
            ];

            this.headerText = this.translate('Formula', 'labels', 'EntityManager')
        },
    });
});
