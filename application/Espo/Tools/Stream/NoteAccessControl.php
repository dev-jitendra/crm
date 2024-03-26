<?php


namespace Espo\Tools\Stream;

use Espo\Entities\Note;
use Espo\Entities\User;

use Espo\Core\Utils\Acl\UserAclManagerProvider;

class NoteAccessControl
{
    private UserAclManagerProvider $userAclManagerProvider;

    public function __construct(UserAclManagerProvider $userAclManagerProvider)
    {
        $this->userAclManagerProvider = $userAclManagerProvider;
    }

    public function apply(Note $note, User $user): void
    {
        if ($note->getType() === Note::TYPE_UPDATE && $note->getParentType()) {
            $data = $note->getData();

            $fields = $data->fields ?? [];

            $data->attributes = $data->attributes ?? (object) [];
            $data->attributes->was = $data->attributes->was ?? (object) [];
            $data->attributes->became = $data->attributes->became ?? (object) [];

            $forbiddenFieldList = $this->userAclManagerProvider
                ->get($user)
                ->getScopeForbiddenFieldList($user, $note->getParentType());

            $forbiddenAttributeList = $this->userAclManagerProvider
                ->get($user)
                ->getScopeForbiddenAttributeList($user, $note->getParentType());

            $data->fields = array_values(array_diff($fields, $forbiddenFieldList));

            foreach ($forbiddenAttributeList as $attribute) {
                unset($data->attributes->was->$attribute);
                unset($data->attributes->became->$attribute);
            }

            $note->set('data', $data);
        }

        if ($note->getType() === Note::TYPE_STATUS && $note->getParentType()) {
            $forbiddenFieldList = $this->userAclManagerProvider
                ->get($user)
                ->getScopeForbiddenFieldList($user, $note->getParentType());

            $data = $note->getData();

            $field = $data->field ?? null;

            if (in_array($field, $forbiddenFieldList)) {
                $data->value = null;
                $data->style = null;
            }

            $note->set('data', $data);
        }

        if ($note->getType() === Note::TYPE_CREATE && $note->getParentType()) {
            $forbiddenFieldList = $this->userAclManagerProvider
                ->get($user)
                ->getScopeForbiddenFieldList($user, $note->getParentType());

            $data = $note->getData();

            $field = $data->statusField ?? null;

            if (in_array($field, $forbiddenFieldList)) {
                $data->statusValue = null;
                $data->statusStyle = null;
            }

            $note->set('data', $data);
        }
    }
}
