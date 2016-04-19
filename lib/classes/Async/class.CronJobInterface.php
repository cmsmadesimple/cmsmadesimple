<?php
namespace CMSMS\Async;

interface CronJobInterface
{
    const RECUR_NONE  = '_none';
    const RECUR_HOURLY  = '_hourly';
    const RECUR_DAILY   = '_daily';
    const RECUR_WEEKLY  = '_weekly';
    const RECUR_MONTHLY = '_monthly';
}