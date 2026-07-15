<?php

namespace Sentrion\Rules\Core;

class P01 extends \Sentrion\Assets\Rule {
    public const NAME = 'Invalid phone format';
    public const DESCRIPTION = 'User provided incorrect phone number.';
    public const ATTRIBUTES = ['phone'];

    protected function defineCondition(): \Ruler\Operator\LogicalOperator {
        return $this->rb->logicalAnd(
            $this->rb['lp_invalid_phone']->equalTo(true),
            $this->rb['ep_phone_number']->notEqualTo([]),
        );
    }
}
