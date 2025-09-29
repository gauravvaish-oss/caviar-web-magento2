<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Exception\BuilderException;


class MultiLevelMenu extends AbstractWidget
{
    const NAME = 'vendor_test_menu_widget';

    public function getName() { return self::NAME; }
    public function getTitle() { return __('Test Menu Widget'); }
    public function getIcon() { return 'fa fa-bars'; }
    public function getCategories() { return ['general']; }
    /**
     * @param ControlsStack $widget
     * @return void
     * @throws BuilderException
     */
    public static function registerMenuItemInterface(
        ControlsStack $widget
    )
    {
        $widget->addControl('title', [
            'label' => __('Title'),
            'type'  => Controls::TEXT,
            'default' => __('Parent Item'),
        ]);
        $widget->addControl(
            'link',
            [
                'label' => __('Link'),
                'type' => Controls::URL,
                'label_block' => true,
                'default' => [
                    'is_external' => 'true',
                ],
                'placeholder' => __('https://your-link.com'),
            ]
        );
    }
    public static function registerMenuInterface(
        ControlsStack $widget
    )
    {
        $repeater = new Repeater;

        self::registerMenuItemInterface($repeater, '');
        $widget->addControl(
            'menu_items',
            [
                'label' => __('Menu Items'),
                'type' => Controls::REPEATER,
                'fields' => $repeater->getControls(),
                'title_field' => '{{{ title }}}',
            ]
        );
    }
    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
    $this->startControlsSection(
            'section_menu',
            [
                'label' => __('Menu'),
            ]
        );

        self::registerMenuInterface($this);

        $this->endControlsSection();

    }

 protected function contentTemplate()
    {
        ?>
        <div class="navbar-collapse justify-content-center" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <# if (settings.menu_items) { #>
                    <# _.each(settings.menu_items, function(parent) { #>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="{{ parent.link.url }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{ parent.title }}</a>
                            <# if (parent.children) { #>
                                <ul class="dropdown-menu">
                                    <# _.each(parent.children, function(child) { #>
                                        <li>
                                            <a class="dropdown-item" href="{{ child.link.url }}">{{ child.title }}</a>
                                        </li>
                                    <# }); #>
                                </ul>
                            <# } #>
                        </li>
                    <# }); #>
                <# } #>
            </ul>
        </div>
        <script>
        require(['jquery'], function($) {
            $(document).ready(function () {
                var menu = "{{{settings.menu_items}}}";
                console.log('MultiLevelMenu widget loaded', menu);
                // Initialize Bootstrap dropdowns
            });
        });
            </script>
        <?php
    }

     protected function render(): string
{
    $settings = $this->getSettings();
    $menuItems = $settings['menu_items'] ?? [];
    ob_start();
    ?>
    <div class="navbar-collapse justify-content-center" id="navbarNavDropdown">
        <ul class="navbar-nav">
            <?php if (!empty($menuItems)) : ?>
                <?php foreach ($menuItems as $parent) : 
                    $hasChildren = !empty($parent['children']);
                ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link <?= $hasChildren ? 'dropdown-toggle' : '' ?>" 
                           href="<?= htmlspecialchars($parent['link']['url'] ?? '#') ?>" 
                           <?= $hasChildren ? 'role="button" data-bs-toggle="dropdown" aria-expanded="false"' : '' ?>>
                            <?= htmlspecialchars($parent['title'] ?? 'Parent Item') ?>
                        </a>
                        <?php if ($hasChildren) : ?>
                            <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item <?= $hasGrandchildren ? 'dropdown-toggle' : '' ?>" 
                                           href="<?= htmlspecialchars($child['link']['url'] ?? '#') ?>" >
                                            <?= htmlspecialchars($child['title'] ?? 'Child Item') ?>
                                        </a>
                                    </li>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}
}
