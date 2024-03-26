

define('views/scheduled-job/fields/job', ['views/fields/enum'], function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.isEditMode() || this.isDetailMode()) {
                this.wait(true);

                Espo.Ajax
                    .getRequest('Admin/jobs')
                    .then(data => {
                        this.params.options = data.filter(item => {
                            return !this.getMetadata().get(['entityDefs', 'ScheduledJob', 'jobs', item, 'isSystem']);
                        });

                        this.params.options.unshift('');

                        this.wait(false);
                    });
            }

            if (this.model.isNew()) {
                this.on('change', () => {
                    var job = this.model.get('job');

                    if (job) {
                        var label = this.getLanguage().translateOption(job, 'job', 'ScheduledJob');
                        var scheduling = this.getMetadata().get('entityDefs.ScheduledJob.jobSchedulingMap.' + job) ||
                            '*/10 * * * *';

                        this.model.set('name', label);
                        this.model.set('scheduling', scheduling);

                        return;
                    }

                    this.model.set('name', '');
                    this.model.set('scheduling', '');
                });
            }
        },
    });
});
