<?php

namespace Differ\Formatters\Pretty;

function formatToPretty($ast)
{
    $renderedDiff = formatElementToPretty($ast);

    return "{\n{$renderedDiff}\n}";
}

function formatElementToPretty($ast, $nestingLevel = 0)
{
    $nesting = str_repeat('    ', $nestingLevel);

    $renderedDiff = array_reduce($ast, function ($acc, $astElement) use ($nesting, $nestingLevel) {
        switch ($astElement['status']) {
            case 'nested':
                $element = formatElementToPretty($astElement['children'], $nestingLevel + 1);
                $acc[] = "{$nesting}    {$astElement['name']}: {\n{$element}\n    {$nesting}}";
                break;
            case 'added':
                $value = getProperValue($astElement['value'], $nestingLevel);
                $acc[] = " {$nesting} + {$astElement['name']}: {$value}";
                break;
            case 'deleted':
                $value = getProperValue($astElement['value'], $nestingLevel);
                $acc[] = "{$nesting}  - {$astElement['name']}: {$value}";
                break;
            case 'changed':
                $prevValue = getProperValue($astElement['prevValue'], $nestingLevel);
                $curValue = getProperValue($astElement['curValue'], $nestingLevel);

                $acc[] = "{$nesting}  + {$astElement['name']}: {$curValue}";
                $acc[] = "{$nesting}  - {$astElement['name']}: {$prevValue}";
                break;
            case 'unchanged':
                $value = getProperValue($astElement['value'], $nestingLevel);
                $acc[] = "{$nesting}    {$astElement['name']}: {$value}";
                break;
        }

        return $acc;
    }, []);

    $result = implode("\n", $renderedDiff);
    return "{$result}";
}

function getProperValue($value, $nestingLevel = 0)
{
    if (is_array($value)) {
        return formatNestedValue($value, $nestingLevel);
    }

    if (is_bool($value)) {
        return $value ? "true" : "false";
    }

    return $value;
}

function formatNestedValue($value, $nestingLevel = 0)
{
    $keys = array_keys($value);
    $nesting = str_repeat('    ', $nestingLevel + 1);

    $renderedArray = array_reduce($keys, function ($acc, $key) use ($keys, $value, $nesting) {
        $acc[] = "{$nesting}    {$key}: {$value[$key]}";
        return $acc;
    }, []);

    $result = implode("\n", $renderedArray);

    return "{\n{$result}\n{$nesting}}";
}
