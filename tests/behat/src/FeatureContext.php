<?php

namespace SilverStripe\Blog\Tests\Behat\Context;

use SilverStripe\BehatExtension\Context\SilverStripeContext;
use Behat\Mink\Element\NodeElement;
use PHPUnit\Framework\Assert;

class FeatureContext extends SilverStripeContext
{
    /**
     * @param NodeElement $ancestor
     * @param string $locator
     * @return NodeElement|null
     */
    private function getDescendantHtmlField($element, $locator)
    {
        $textarea = $element->find('css', "textarea.htmleditor[name='{$locator}']");
        if (is_null($textarea)) {
            $labels = $element->findAll('xpath', "//label[contains(text(), '{$locator}')]");
            Assert::assertCount(1, $labels, "Found more than one html field label containing the phrase '{$locator}}'");
            $label = array_shift($labels);
            $textarea = $element->find('css', '#' . $label->getAttribute('for'));
        }
        Assert::assertNotNull($textarea, "HTML field {$locator} not found");
        return $textarea;
    }
}
