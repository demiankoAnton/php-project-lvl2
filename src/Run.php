<?php

namespace Differ\Run;

use Docopt;

use function Differ\Differ\genDiff;

function run()
{
    $doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: pretty]

DOC;

    $args = Docopt::handle($doc);

    $firstFilePath = realpath($args['<firstFile>']);
    $secondFilePath = realpath($args['<secondFile>']);

    if ($firstFilePath && $secondFilePath) {
        $firstFileData = json_decode(file_get_contents($firstFilePath), true);
        $secondFileData = json_decode(file_get_contents($secondFilePath), true);
    } else {
        echo 'missing file!';
        return;
    }

    genDiff($firstFileData, $secondFileData);
}