<?php

namespace Sentrion\Rules\Core;

class B26 extends \Sentrion\Assets\Rule {
    public const NAME = 'Single event sessions';
    public const DESCRIPTION = 'User had sessions with only one event.';
    public const ATTRIBUTES = [];

    protected function defineCondition(): \Ruler\Operator\LogicalOperator {
        return $this->rb->logicalAnd(
            $this->rb['event_session_single_event']->equalTo(true),
        );
    }
}
