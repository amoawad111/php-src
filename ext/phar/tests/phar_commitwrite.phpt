--TEST--
Phar::setStub()/stopBuffering()
--SKIPIF--
<?php if (!extension_loaded("phar")) die("skip"); ?>
--INI--
phar.require_hash=0
phar.readonly=0
--FILE--
<?php
$p = new Phar(dirname(__FILE__) . '/brandnewphar.phar', 0, 'brandnewphar.phar');
$p['file1.txt'] = 'hi';
$p->stopBuffering();
var_dump(strlen($p->getStub()));
$p->setStub("<?php
spl_autoload_register(function(\$class) {
    include 'phar://' . str_replace('_', '/', \$class);
});
Phar::mapPhar('brandnewphar.phar');
include 'phar://brandnewphar.phar/startup.php';
__HALT_COMPILER();
?>");
var_dump($p->getStub());
?>
===DONE===
--CLEAN--
<?php
unlink(dirname(__FILE__) . '/brandnewphar.phar');
__HALT_COMPILER();
?>
--EXPECTF--
int(6641)
string(%d) "<?php
spl_autoload_register(function($class) {
    include 'phar://' . str_replace('_', '/', $class);
});
Phar::mapPhar('brandnewphar.phar');
include 'phar://brandnewphar.phar/startup.php';
__HALT_COMPILER(); ?>
"
===DONE===
