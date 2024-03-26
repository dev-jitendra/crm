

define('views/authentication-provider/fields/method', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setupOptions: function () {
            
            let defs = this.getMetadata().get(['authenticationMethods']) || {};

            let options = Object.keys(defs)
                .filter(item => {
                    
                    let data = defs[item].provider || {};

                    return data.isAvailable;
                });

            options.unshift('');

            this.params.options = options;
        },
    });
});
