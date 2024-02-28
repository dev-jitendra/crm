<?php
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

namespace Espo\Core\ORM\Repository\Option;

use Espo\ORM\Repository\Option\SaveOption as BaseSaveOption;

/**
 * Save options.
 */
class SaveOption
{
    /**
     * Silent. Boolean.
     */
    public const SILENT = 'silent';
    /**
     * Import. Boolean.
     */
    public const IMPORT = 'import';
    /**
     * Called from a Record service.
     * @since 8.0.1
     */
    public const API = 'api';
    /**
     * Skip all additional processing. Boolean.
     */
    public const SKIP_ALL = BaseSaveOption::SKIP_ALL;
    /**
     * Keep new. Boolean.
     */
    public const KEEP_NEW = BaseSaveOption::KEEP_NEW;
    /**
     * Keep dirty. Boolean.
     */
    public const KEEP_DIRTY = BaseSaveOption::KEEP_DIRTY;
    /**
     * Skip hooks. Boolean.
     */
    public const SKIP_HOOKS = 'skipHooks';
    /**
     * Skip setting created-by. Boolean.
     */
    public const SKIP_CREATED_BY = 'skipCreatedBy';
    /**
     * Skip setting modified-by. Boolean.
     */
    public const SKIP_MODIFIED_BY = 'skipModifiedBy';
    /**
     * Override created-by. String.
     */
    public const CREATED_BY_ID = 'createdById';
    /**
     * Override modified-by. String.
     */
    public const MODIFIED_BY_ID = 'modifiedById';
}
