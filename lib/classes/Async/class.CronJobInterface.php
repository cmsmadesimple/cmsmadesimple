<?php
namespace CMSMS\Async;

interface CronJobInterface
{
    const RECUR_NONE  = '_none';
    const RECUR_15M = '_15m';
    const RECUR_30M = '_30m';
    const RECUR_HOURLY  = '_hourly';
    const RECUR_120M = '_120m';
    const RECUR_180M = '_180m';
    const RECUR_DAILY   = '_daily';
    const RECUR_WEEKLY  = '_weekly';
    const RECUR_MONTHLY = '_monthly';
}