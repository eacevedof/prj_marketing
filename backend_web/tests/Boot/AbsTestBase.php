<?php
namespace Tests\Boot;

use PHPUnit\Framework\TestCase;
use Tests\Boot\Traits\LogTrait;

abstract class AbsTestBase extends TestCase
{
    use LogTrait;

}