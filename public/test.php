<?php

// Показываем все ошибки
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Проверяем что .env существует
$envPath = __DIR__ . '/../.env';
echo '<h3>1. Файл .env</h3>';
if (file_exists($envPath)) {
    echo 'OK - .env существует<br>';
} else {
    echo '<b style="color:red">ОШИБКА - .env НЕ НАЙДЕН!</b><br>';
    echo 'Путь: ' . $envPath . '<br>';
}

// Проверяем vendor/autoload.php
echo '<h3>2. Vendor</h3>';
$vendorPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorPath)) {
    echo 'OK - vendor/autoload.php существует<br>';
} else {
    echo '<b style="color:red">ОШИБКА - vendor/autoload.php НЕ НАЙДЕН!</b><br>';
}

// Проверяем версию PHP
echo '<h3>3. PHP</h3>';
echo 'Версия: ' . phpversion() . '<br>';

// Проверяем нужные расширения
echo '<h3>4. Расширения PHP</h3>';
$required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json'];
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        echo "$ext - OK<br>";
    } else {
        echo "<b style='color:red'>$ext - НЕ УСТАНОВЛЕН!</b><br>";
    }
}

// Пробуем загрузить Laravel
echo '<h3>5. Загрузка Laravel</h3>';
try {
    require $vendorPath;
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo 'OK - Laravel загружен<br>';
} catch (\Throwable $e) {
    echo '<b style="color:red">ОШИБКА: ' . $e->getMessage() . '</b><br>';
    echo 'Файл: ' . $e->getFile() . ':' . $e->getLine() . '<br>';
}
