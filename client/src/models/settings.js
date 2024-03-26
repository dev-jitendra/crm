



import Model from 'model';


class Settings extends Model {

    name = 'Settings'
    entityType = 'Settings'
    urlRoot = 'Settings'

    
    load() {
        return new Promise(resolve => {
            this.fetch()
                .then(() => resolve());
        });
    }

    
    getByPath(path) {
        if (!path.length) {
            return null;
        }

        let p;

        for (let i = 0; i < path.length; i++) {
            const item = path[i];

            if (i === 0) {
                p = this.get(item);
            }
            else {
                if (item in p) {
                    p = p[item];
                }
                else {
                    return null;
                }
            }

            if (i === path.length - 1) {
                return p;
            }

            if (p === null || typeof p !== 'object') {
                return null;
            }
        }
    }
}

export default Settings;
