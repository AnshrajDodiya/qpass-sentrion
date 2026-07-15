<?php

namespace Sentrion\Rules\Core;

class B02 extends \Sentrion\Assets\Rule {
    public const NAME = 'User has changed a password';
    public const DESCRIPTION = 'The user has changed their password.';
    public const ATTRIBUTES = [];

    protected function defineCondition(): \Ruler\Operator\LogicalOperator {
        return $this->rb->logicalAnd(
            $this->rb['event_password_changed']->equalTo(true),
        );
    }
}
