<?php

namespace Scribe\Jabiru\Extension\Core;

use Scribe\Jabiru\Component\Element\ElementLiteral;
use Scribe\Jabiru\Event\EmitterAwareInterface;
use Scribe\Jabiru\Event\EmitterAwareTrait;
use Scribe\Jabiru\Extension\ExtensionInterface;
use Scribe\Jabiru\Renderer\RendererAwareInterface;
use Scribe\Jabiru\Renderer\RendererAwareTrait;
use Scribe\Jabiru\Markdown;

/**
 * Original source code from Markdown.pl
 *
 * > Copyright (c) 2004 John Gruber
 * > <http://daringfireball.net/projects/markdown/>
 */
class InlineStyleExtension implements ExtensionInterface, RendererAwareInterface, EmitterAwareInterface
{

    use RendererAwareTrait;
    use EmitterAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function register(Markdown $markdown)
    {
        $markdown->on('inline', array($this, 'processBold'), 70);
        $markdown->on('inline', array($this, 'processItalic'), 71);
    }

    /**
     * @param ElementLiteral $text
     */
    public function processBold(ElementLiteral $text)
    {
        if (!$text->contains('**') && !$text->contains('__')) {
            return;
        }

        /** @noinspection PhpUnusedParameterInspection */
        $text->replace('{ (\w?) (\*\*|__) (?=\S) (.+?[*_]*) (?<=\S) \2 (\w?) }sx', function (ElementLiteral $w, ElementLiteral $prevChar, ElementLiteral $a, ElementLiteral $target, ElementLiteral $nextChar) {
            if (!$prevChar->isEmpty() && !$nextChar->isEmpty() && $target->contains(' ')) {
                $this->getEmitter()->emit('escape.special_chars', [$w->replaceString(['*', '_'], ['\\*', '\\_'])]);

                return $w;
            }

            return $prevChar . $this->getRenderer()->renderBoldText($target) . $nextChar;
        });
    }

    /**
     * @param ElementLiteral $text
     */
    public function processItalic(ElementLiteral $text)
    {
        if (!$text->contains('*') && !$text->contains('_')) {
            return;
        }

        /** @noinspection PhpUnusedParameterInspection */
        $text->replace('{ (\w?) (\*|_) (?=\S) (.+?) (?<=\S) \2 (\w?) }sx', function (ElementLiteral $w, ElementLiteral $prevChar, ElementLiteral $a, ElementLiteral $target, ElementLiteral $nextChar) {
            if (!$prevChar->isEmpty() && !$nextChar->isEmpty() && $target->contains(' ')) {
                $this->getEmitter()->emit('escape.special_chars', [$w->replaceString(['*', '_'], ['\\*', '\\_'])]);

                return $w;
            }

            return $prevChar . $this->getRenderer()->renderItalicText($target) . $nextChar;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'inlineStyle';
    }

}
