<?php

namespace App\Traits\Front;

use App\Accessors\Front\FrontCache;
use App\Accessors\GroupAccessor;

trait Groups {
    protected GroupAccessor $groupAccessor;


    protected function initGroupAccessor()
    {
        if (!isset($this->groupAccessor)) {
            $this->groupAccessor = new GroupAccessor(FrontCache::getEventGroup()->group_id);
        }
    }
}
