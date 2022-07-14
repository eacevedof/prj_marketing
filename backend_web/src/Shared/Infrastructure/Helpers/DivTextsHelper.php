<?php
namespace App\Shared\Infrastructure\Helpers;

final class DivTextsHelper extends AppHelper implements IHelper
{
    public function echo(array|string $texts): void
    {
        if (is_string($texts)) {
            echo "<p>$texts</p>";
            return;
        }

        if (is_array($texts))
            $this->_print_array($texts);
    }
    
    private function _print_array(array $texts): void
    {
        foreach ($texts as $part) {
            if (is_string($part)) {
                echo "<p>$part</p>";
                continue;
            }

            if (is_array($part)) {
                $h2 = $part["h2"] ?? "";
                if ($h2) echo "<h2>$h2</h2>";
                $h3 = $part["h3"] ?? "";
                if ($h3) echo "<h3>$h3</h3>";
                $p = $part["p"] ?? "";
                if ($p) echo "<p>$p</p>";
            }
        }
    }
}
