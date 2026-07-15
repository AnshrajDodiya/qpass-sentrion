<?php

namespace Sentrion\Rules\Core;

class R02 extends \Sentrion\Assets\Rule {
    public const NAME = 'Email in blacklist';
    public const DESCRIPTION = 'This email address appears in the blacklist.';
    public const ATTRIBUTES = [];

    protected function defineCondition(): \Ruler\Operator\LogicalOperator {
        return $this->rb->logicalAnd(
            $this->rb['le_fraud_detected']->equalTo(true),
        );
    }
}
