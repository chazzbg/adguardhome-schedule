<?php

namespace App\Service;

use App\Entity\Rule;
use App\Enums\RuleAction;

class ExpressionCompiler
{

    public function compile(Rule $rule, RuleAction $action): string
    {
        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        ];

        $time = match ($action) {
            RuleAction::ACTION_BLOCK => $rule->getBlockAt(),
            RuleAction::ACTION_UNBLOCK => $rule->getUnblockAt(),
        };

        $minute = (int)$time->format('i');
        $hour = (int)$time->format('H');
        $dom = '*';
        $month = '*';
        $dow = [];
        foreach ($days as $k => $day) {
            if ($rule->{'is' . $day}()) {
                $dow[] = $k + 1;
            }
        }
        $dow = implode(',', $dow);

        $values = [$minute, $hour, $dom, $month, $dow];

        return implode(' ', $values);
    }
}