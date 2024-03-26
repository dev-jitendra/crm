

import Acl from 'acl';

class TeamAcl extends Acl {

    checkInTeam(model) {
        const userTeamIdList = this.getUser().getTeamIdList();

        return (userTeamIdList.indexOf(model.id) !== -1);
    }
}

export default TeamAcl;
