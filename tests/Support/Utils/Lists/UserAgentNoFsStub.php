<?php

declare(strict_types=1);

namespace Tests\Support\Utils\Lists;

use Sentrion\Utils\Assets\Lists\UserAgent;

/**
 * Stub: disables filesystem access.
 */
final class UserAgentNoFsStub extends UserAgent {
    protected static function getExtension(): ?array {
        return null;
    }
}
