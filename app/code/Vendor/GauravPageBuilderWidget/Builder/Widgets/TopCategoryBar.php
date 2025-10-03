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
                                {{{settings.menu_items}}}
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
            console.log('Menu Data:', JSON.stringify(menu)); // Debugging line
        });
    });
    </script>
    <?php
}

    protected function render(): string
    {
         $settings = $this->getSettings();
         $categorySource = ObjectManagerHelper::get(\Goomento\PageBuilder\Model\Config\Source\CatalogCategory::class);
        $categories = $categorySource->toOptionArray();
        // dd($categories);die;

        $options = [];
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        foreach ($categories as $cat) {
            if(in_array($cat['value'], $settings['category'])){
                $options[$cat['value']] = preg_replace('/\s*\(ID:\s*\d+\)/', '', $cat['label']);
            }
        }

        //  dd($options);die;
         $menu_items = $settings['menu_items'];
         ob_start();
         ?>
         <div class="top-category-bar ">
  <div class="container">
    <div class="row align-items-center">
      <!-- Left Side: Toggle + Title -->
      <div class="col-md-3 d-flex align-items-center p-md-0">
        <div class="category_menu">
          <button onclick="toggleMyDiv()" class="top_category"><img src="images/toggle.png" alt=""> Top
            Category</button>
          <div class="nav_below_item nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <div id="toggle_section">
                <?php foreach($menu_items as $menu){ ?>
              <button class="nav-link active" id="v-pills-new_product-tab" data-bs-toggle="pill" data-bs-target="#v-pills-new_product" type="button" role="tab" aria-controls="v-pills-new_product" aria-selected="true"><img src="images/tv_icon.png" alt=""><?php echo $menu['title']; ?></button>
            <?php } ?> 
            </div>
          </div>
        </div>
</div>
        <!-- Right Side: Search Bar -->
        <div class="col-md-9">
          <form class="d-flex search-form">
            <input type="text" class="form-control search-input" placeholder="Search For Products">
            <ul class="search-suggestions" style="position: absolute; top: 100%; left: 0; right: 0; z-index: 999; background: #fff; border: 1px solid #ccc; list-style: none; margin: 0; padding: 0; display: none;"></ul>
            <select class="form-select category-select">
              <option>All Categories</option>
              <?php
              foreach($options as $key => $value){?>
              <option value="<?php echo $key ?>"><?php echo $value ?></option>
             <?php } ?>
              
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
    require(['jquery'], function($){
        $(document).ready(function(){
            var $input = $('.search-input');
            var $category = $('.category-select');
            var $suggestions = $('.search-suggestions');
            var $toggleSection = $('#toggle_section');

            $input.on('keyup', function(){
                var query = $(this).val();
                var category = $category.val();

                if(query.length < 3){
                    $suggestions.hide().empty();
                    return;
                }

                $.ajax({
                    url: "/customgoomento/ajax/search",
                    type: 'GET',
                    data: { q: query, category: category },
                    success: function(data){
                        $suggestions.empty();
                        if(data.length){
                            data.forEach(function(product){
                                $suggestions.append('<li style="padding:5px 10px; cursor:pointer;"><a href="'+product.url+'">'+product.name+'</a></li>');
                            });
                            $suggestions.show();
                        } else {
                            $suggestions.append('<li style="padding:5px 10px;">No products found</li>').show();
                        }
                    }
                });
            });

            // Optional: hide suggestions on click outside
            $(document).on('click', function(e){
                if(!$(e.target).closest('.search-wrapper').length){
                    $suggestions.hide();
                }
            });

                $('.top_category').hover(
                    function() {
                        $toggleSection.stop(true, true).slideDown(200);
                    },
                    function() {
                        $toggleSection.stop(true, true).slideUp(200);
                    }
                );

                // Optional: keep toggle visible when hovering over toggle_section itself
                $toggleSection.hover(
                    function(){ $(this).stop(true,true).show(); },
                    function(){ $(this).stop(true,true).slideUp(200); }
                );
        });
    });
</script>


         <?php
         return ob_get_clean();
    }
}