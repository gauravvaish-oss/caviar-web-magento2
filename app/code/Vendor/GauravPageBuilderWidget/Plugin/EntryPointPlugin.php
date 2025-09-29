<?php
namespace Vendor\GauravPageBuilderWidget\Plugin;

use Goomento\PageBuilder\Builder\Managers\Widgets;
use Psr\Log\LoggerInterface;

class EntryPointPlugin
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function beforeRegisterWidgets($subject, Widgets $widgetsManager)
    {
        $this->logger->info('Registering GauravWidget');
         $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\MultiLevelMenu::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\TopCategoryBar::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\ShopByCategory::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\CustomBanner::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\CategoriesView::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\ProductCategories::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\ProductCountdown::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\TrendingProducts::class
        );
        
        
        return [$widgetsManager];
    }
}
