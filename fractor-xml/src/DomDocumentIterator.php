<?php

namespace a9f\FractorXml;

use a9f\FractorXml\Contract\DomNodeVisitor;

final class DomDocumentIterator
{
    /** @var int */
    public const REMOVE_NODE = 3;

    /**
     * @param list<DomNodeVisitor> $visitors
     */
    public function __construct(private readonly iterable $visitors)
    {
    }

    public function traverseDocument(\DOMDocument $document): void
    {
        foreach ($this->visitors as $visitor) {
            $visitor->beforeTraversal($document);
        }

        if ($document->firstChild instanceof \DOMNode) {
            $this->traverseNode($document->firstChild);
        }

        foreach ($this->visitors as $visitor) {
            $visitor->afterTraversal($document);
        }
    }

    private function traverseNode(\DOMNode $node): void
    {
        $nodeRemoved = false;
        foreach ($this->visitors as $visitor) {
            $result = $visitor->enterNode($node);

            if ($node->parentNode === null) {
                // TODO convert into a custom ShouldNotHappenException
                throw new \RuntimeException('Node has no parent node');
            }

            if ($result === DomDocumentIterator::REMOVE_NODE) {
                $node->parentNode->removeChild($node);
                $nodeRemoved = true;
            } elseif ($result->isSameNode($node) === false) {
                $node->parentNode->replaceChild($result, $node);
                $nodeRemoved = true; // "replaced", actually, but that's close enough for us
            }
        }

        if ($nodeRemoved === false) {
            foreach ($node->childNodes->getIterator() as $childNode) {
                $this->traverseNode($childNode);
            }
        }

        foreach ($this->visitors as $visitor) {
            $visitor->leaveNode($node);
        }
    }
}
