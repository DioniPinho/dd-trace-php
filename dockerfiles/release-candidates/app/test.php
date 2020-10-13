<?php

header('Content-Type:text/plain');

error_log('This line should go into the error log');

class Foo
{
    public function generateKey($page)
    {
        if ('speaking' === $page) {
            try {
                $this->dbConnect($page);
            } catch (Exception $e) {
                $page = $e->getMessage();
            }
        }
        return uniqid($page, true);
    }

    private function dbConnect($page)
    {
        return new PDO('mysql:dbname=testdb;host=127.0.0.1', 'user', $page);
    }
}

\DDTrace\trace_method('Foo', 'generateKey', function ($s, array $args) {
    $s->name = 'Foo.generateKey';
    $s->resource = $args[0];
    $s->service = 'php';
});

$pages = [
    'page.contact' => 'contact',
    'page.about' => 'about',
    'page.speaking' => 'speaking',
];

$f = new Foo();
foreach ($pages as $key => $page) {
    $content = $f->generateKey($page);
    echo $key . ' => ' . $content . ' (' . strlen($content) . ' bytes)' . PHP_EOL;
}

var_dump(getenv('TEST_ENV'));
var_dump(getenv('DD_ENV'));
