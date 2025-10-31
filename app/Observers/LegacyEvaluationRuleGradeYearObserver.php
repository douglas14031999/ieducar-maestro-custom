<?php

namespace App\Observers;

use App\Models\LegacyEvaluationRuleGradeYear;
use Illuminate\Support\Facades\Cache;

class LegacyEvaluationRuleGradeYearObserver
{
    public function updated(LegacyEvaluationRuleGradeYear $legacyEvaluationRuleGradeYear)
    {
        Cache::tags(['evaliation-rule'])->flush();
    }
}
