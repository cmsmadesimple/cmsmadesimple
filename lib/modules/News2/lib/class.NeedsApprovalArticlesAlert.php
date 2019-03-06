<?php
namespace News2;
use News2;
use CMSMS\AdminAlerts\TranslatableAlert;

class NeedsApprovalArticlesAlert extends TranslatableAlert
{
    public function __construct(News2 $mod, $count)
    {
        parent::__construct([ $mod::MANAGE_PERM, $mod::APPROVE_PERM ]);
        $this->name = __CLASS__;
        $this->priority = self::PRIORITY_LOW;
        $this->titlekey = 'alert_title_pending_entries';
        $this->module = __NAMESPACE__;
        $this->msgkey = 'notify_n_pending_items';
        $this->msgargs = $count;
    }
} // class
