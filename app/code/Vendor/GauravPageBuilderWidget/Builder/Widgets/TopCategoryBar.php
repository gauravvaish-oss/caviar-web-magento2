<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Builder\Base\ControlsStack;

class TopCategoryBar extends AbstractWidget
{
    const NAME = 'vendor_top_category_bar_search';

    public function getName() { return self::NAME; }
    public function getTitle() { return __('Categories Bar And Search'); }
    public function getIcon() { return 'fa fa-folder'; }
    public function getCategories() { return ['general']; }

    /**
     * Register a single parent menu item
     */
    public static function registerMenuItemInterface(ControlsStack $widget)
    {
        $widget->addControl('title', [
            'label' => __('Title'),
            'type'  => Controls::TEXT,
            'default' => __('Menu Item'),
        ]);

        $widget->addControl('icon', [
            'label' => __('Icon'),
            'type' => Controls::ICONS,
            'label_block' => true,
            'default' => [
                'value' => 'fab fa-wordpress',
                'library' => 'fa-brands',
            ],
        ]);

        $widget->addControl('link', [
            'label' => __('Link'),
            'type' => Controls::URL,
            'label_block' => true,
            'default' => [
                'url' => '#',
                'is_external' => true,
            ],
            'placeholder' => __('https://your-link.com'),
        ]);
    }

    /**
     * Register a single submenu item
     */
    public static function registerSubMenuItemInterface(ControlsStack $widget)
    {
        $widget->addControl('title', [
            'label' => __('Sub Menu Title'),
            'type'  => Controls::TEXT,
            'default' => __('Sub Menu Item'),
        ]);

        $widget->addControl('icon', [
            'label' => __('Icon'),
            'type' => Controls::ICONS,
            'label_block' => true,
            'default' => [
                'value' => 'fab fa-wordpress',
                'library' => 'fa-brands',
            ],
        ]);

        $widget->addControl('link', [
            'label' => __('Link'),
            'type' => Controls::URL,
            'label_block' => true,
            'default' => [
                'url' => '#',
                'is_external' => true,
            ],
            'placeholder' => __('https://your-link.com'),
        ]);
    }

    /**
     * Register repeater for submenu items
     */
    public static function registerSubMenuInterface(ControlsStack $widget)
    {
        $subRepeater = new Repeater();
        self::registerSubMenuItemInterface($subRepeater);

        $widget->addControl('sub_menu_items', [
            'label' => __('Sub Menu Items'),
            'type' => Controls::REPEATER,
            'fields' => $subRepeater->getControls(),
            'title_field' => '{{{ title }}}',
        ]);
    }

    /**
     * Register full parent menu with submenus
     */
    public static function registerMenuInterface(ControlsStack $widget)
{
    $parentRepeater = new Repeater();

    // Add parent menu fields
    self::registerMenuItemInterface($parentRepeater);

    // Add submenus nested inside the parent
    $subRepeater = new Repeater();
    self::registerSubMenuItemInterface($subRepeater);

    $parentRepeater->addControl('sub_menu_items', [
        'label' => __('Sub Menu Items'),
        'type' => Controls::REPEATER,
        'fields' => $subRepeater->getControls(),
        'title_field' => '{{{ title }}}',
    ]);

    $widget->addControl('menu_items', [
        'label' => __('Menu Items'),
        'type' => Controls::REPEATER,
        'fields' => $parentRepeater->getControls(),
        'title_field' => '{{{ title }}}',
    ]);
}

    protected function registerControls()
    {
        $categorySource = ObjectManagerHelper::get(\Goomento\PageBuilder\Model\Config\Source\CatalogCategory::class);
        $categories = $categorySource->toOptionArray();

        $options = [];
        foreach ($categories as $cat) {
            $options[$cat['value']] = $cat['label'];
        }

        // Top Categories Section
        $this->startControlsSection('content_section', [
            'label' => __('Top Categories Menu Section'),
            'tab'   => Controls::TAB_CONTENT,
        ]);
        $this->addControl('title_category', [
            'label' => __('Title Category'),
            'type'  => Controls::TEXT,
            'default' => __('Top Categories'),
        ]);

        self::registerMenuInterface($this);
        $this->endControlsSection();

        // Dropdown Category Selection
        $this->startControlsSection('dropdown_section', [
            'label' => __('Dropdown Options'),
            'tab'   => Controls::TAB_CONTENT,
        ]);
        $this->addControl("category", [
            'label' => __("Select Category"),
            'type' => Controls::SELECT2,
            'multiple' => true,
            'options' => $options,
        ]);
        $this->endControlsSection();
    }

    protected function contentTemplate()
{
    ?>
    <div class="top-category-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 d-flex align-items-center p-md-0">
                    <div class="category_menu">
                        <button onclick="toggleMyDiv()" class="top_category">
                            <img src="images/toggle.png" alt=""> {{{settings.title_category}}}
                        </button>
                        <div class="nav_below_item nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <div id="toggle_section" style="display: block;">
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <form class="d-flex search-form">
                        <input type="text" class="form-control search-input" placeholder="Search For Products">
                        <select class="form-select category-select">
                            <option value="">All Categories</option>
                           
                        </select>
                        <button class="btn search-btn" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    require(['jquery'], function($) {
        $(document).ready(function() {
            var menu = '{{{settings.menu_items}}}';
            console.log('Menu Data:', menu); // Debugging line
        });
    });
    </script>
    <?php
}

}