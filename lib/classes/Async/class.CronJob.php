<?php

namespace CMSMS\Async;

abstract class CronJob extends Job implements CronJobInterface {
    use CronJobTrait;
}
