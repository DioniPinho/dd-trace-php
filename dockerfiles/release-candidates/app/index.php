<?php

header('Content-Type:text/plain');

error_log('This line should go into the error log');

class Foo
{
    public function generateKey($page)
    {
        // Tests distributed tracing
        $ch = curl_init('https://www.sammyk.me/' . $page);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-mt-rand: ' . mt_rand(),
        ]);
        $content = curl_exec($ch);
        echo 'Downloaded "' . $page . '" content (' . strlen($content) . ' bytes)' . PHP_EOL;

        if ('speaking' === $page) {
            //$this->dbConnect();
            //new PDO('mysql:dbname=testdb;host=127.0.0.1', 'user', $page);
            //throw new Exception('Oops! ' . $page);
            $this->doException($page);
        }
        return uniqid($page, true);
    }

    private function dbConnect($page)
    {
        return new PDO('mysql:dbname=testdb;host=127.0.0.1', 'user', $page);
    }

    private function doException($page)
    {
        throw new Exception('Oops! ' . $page);
    }
}

if (function_exists('DDTrace\\trace_method')) {
    DDTrace\trace_method('Foo', 'generateKey', function ($s, array $args) {
        $s->name = 'Foo.generateKey';
        $s->resource = $args[0];
        $s->service = 'php';
    });
} else {
    echo PHP_EOL . 'WARNING! ddtrace does not appear to be installed...' . PHP_EOL . PHP_EOL;
}

$pages = [
    'page.contact' => 'contact',
    'page.about' => 'about',
    'page.speaking' => 'speaking',
];

$f = new Foo();
foreach ($pages as $key => $page) {
    //$content = $f->generateKey($page);
    try {
        $content = $f->generateKey($page);
    } catch (Exception $e) {
        $content = $e->getMessage();
    }
    echo $key . ' => ' . $content . ' (' . strlen($content) . ' bytes)' . PHP_EOL;
}
