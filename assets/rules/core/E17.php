<?php

namespace Sentrion\Rules\Core;

class E17 extends \Sentrion\Assets\Rule {
    public const NAME = 'Free email and spam';
    public const DESCRIPTION = 'Email appears in spam lists and registered by free provider. Increased risk of spamming.';
    public const ATTRIBUTES = ['email', 'domain'];

    protected function defineCondition(): \Ruler\Operator\LogicalOperator {
        return $this->rb->logicalAnd(
            $this->rb['ld_domain_free_email_provider']->equalTo(true),
            $this->rb['le_email_in_blockemails']->equalTo(true),
        );
    }
}
