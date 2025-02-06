<?php

namespace App\Tests\E2E\App;

use Tests\E2E\AbstractE2ETest;

class HomepageTest extends AbstractE2ETest
{
    /**
     * @group E2E_app
     */
    public function testHomepageLoadsSuccessfully(): void
    {
        $this->action->visit(self::HOMEPAGE_PATH);

        // TODO: test something else.
        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);
    }
}
