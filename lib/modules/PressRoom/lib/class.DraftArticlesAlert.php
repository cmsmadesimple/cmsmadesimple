<?php
namespace PressRoom;
use PressRoom;
use CMSMS\AdminAlerts\TranslatableAlert;

class DraftArticlesAlert extends TranslatableAlert
{
    public function __construct(PressRoom $mod, $count)
    {
        parent::__construct([ $mod::MANAGE_PERM ]);
        $this->name = __CLASS__;
        $this->priority = self::PRIORITY_LOW;
        $this->titlekey = 'alert_title_draft_entries';
        $this->module = __NAMESPACE__;
        $this->msgkey = 'notify_n_draft_items';
        $this->msgargs = $count;
    }
} // class
