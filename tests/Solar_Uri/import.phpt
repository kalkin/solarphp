--TEST--
Solar_Uri::import()
--FILE---
<?php
// include ../_prepend.inc
if (is_readable(dirname(dirname(__FILE__)) . '/_prepend.inc')) {
    require dirname(dirname(__FILE__)) . '/_prepend.inc';
}

// include ./_prepend.inc
if (is_readable(dirname(__FILE__) . '/_prepend.inc')) {
    require dirname(__FILE__) . '/_prepend.inc';
}

// ---------------------------------------------------------------------

// the URI object itself
$uri = Solar::factory('Solar_Uri');

// set up the expected values
$scheme = 'http';
$host = 'www.example.net';
$port = 8080;
$path = '/some/path/index.php';
$info = array(
    'more', 'path', 'info'
);
$query = array(
    'a"key' => 'a&value',
    'b?key' => 'this that other',
    'c\'key' => 'tag+tag+tag',
);

$spec = "$scheme://$host:$port$path/" . implode('/', $info);

$tmp = array();
foreach ($query as $k => $v) {
    $tmp[] .= urlencode($k) . '=' . urlencode($v);
}
$spec .= '?' . implode('&', $tmp);


// import the URI spec and test that it imported properly
$uri->import($spec);
$assert->same($uri->scheme, $scheme);
$assert->same($uri->host, $host);
$assert->same($uri->port, $port);
$assert->same($uri->path, $path);
$assert->same($uri->info, $info);
$assert->same($uri->query, $query);

// npw export, re-import, and check again
// to make sure there are no translation errors.
$spec = $uri->export();
$uri->import($spec);
$assert->same($uri->scheme, $scheme);
$assert->same($uri->host, $host);
$assert->same($uri->port, $port);
$assert->same($uri->path, $path);
$assert->same($uri->info, $info);
$assert->same($uri->query, $query);

// ---------------------------------------------------------------------

// include ./_append.inc
if (is_readable(dirname(__FILE__) . '/_append.inc')) {
    require dirname(__FILE__) . '/_append.inc';
}
// include ../_append.inc
if (is_readable(dirname(dirname(__FILE__)) . '/_append.inc')) {
    require dirname(dirname(__FILE__)) . '/_append.inc';
}
?>
--EXPECT--