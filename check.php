<?php
use Magento\Framework\App\Bootstrap;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Builder\Managers\Widgets;

require __DIR__ . '/app/bootstrap.php'; // correct path from Magento root

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

/** @var Widgets $widgetsManager */
$widgetsManager = ObjectManagerHelper::get(Widgets::class);

$registeredWidgets = $widgetsManager->getRegisteredWidgetTypes();

echo "=== Registered Goomento PageBuilder Widgets ===\n";
foreach ($registeredWidgets as $widget) {
    echo $widget . "\n";
}

