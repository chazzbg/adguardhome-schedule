<?php

namespace App\Service;

use App\Entity\Rule;

class ExpressionCompiler
{

    public function compile(Rule $rule)
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
        $minute = (int)$rule->getTime()->format('i');
        $hour = (int)$rule->getTime()->format('H');
        $dom = '*';
        $month = '*';
        $dow = [];
        foreach ($days as $k => $day) {
            if($rule->{'is'.$day}()){
                $dow[] = $k;
            }
        }
        $dow = implode(',', $dow);

        $values = [$minute, $hour, $dom, $month, $dow];

        return implode(' ', $values);

    }
}