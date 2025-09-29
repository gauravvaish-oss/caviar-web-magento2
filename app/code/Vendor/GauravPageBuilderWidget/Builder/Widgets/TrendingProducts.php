<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class TrendingProducts extends AbstractWidget
{
    
    const NAME = 'vendor_trending_products';

    public function getName() { return self::NAME; }
    public function getTitle() { return __('Trending Products'); }
    public function getIcon() { return 'fa fa-folder'; }
    public function getCategories() { return ['general']; }

    protected function registerControls()
    {
        $categorySource = ObjectManagerHelper::get(\Goomento\PageBuilder\Model\Config\Source\CatalogCategory::class);
        $categories = $categorySource->toOptionArray();

        $options = [];
        foreach ($categories as $cat) {
            $options[$cat['value']] = $cat['label'];
        }

        $this->startControlsSection('content_section', [
            'label' => __('Content'),
            'tab'   => Controls::TAB_CONTENT,
        ]);

        $this->addControl('title', [
            'label' => __('Title'),
            'type' => Controls::TEXT,
            'default' => __('Top Categories Slider'),
        ]);

        $this->addControl("category", [
            'label' => __("Select Category"),
            'type' => Controls::SELECT2,
            'multiple' => true,
            'options' => $options,
            'description' => __("Max 3 categories can be selected."),
            'validation' => function ($value) {
                if (!is_array($value) || count($value) > 4) {
                    return __("You can select only 3 categories.");
                }
                return true;
            },
        ]);

        $this->endControlsSection();
    }


protected function contentTemplate()
{
    ?>
    <div class="trending-section">
        <!-- Section Header -->
        <div class="main-title section-header">
            <h2 class="section-title">{{{settings.title}}}</h2>

            <!-- Tab Buttons (dynamic) -->
            <div class="tab-buttons" id="category-tabs">
                <!-- Tabs will be injected here -->
            </div>
        </div>

        <!-- Products Content -->
        <div class="tab-content trending-product active">
            <!-- Desktop grid -->
            <div class="row d-none d-md-flex" id="desktop-products"></div>

            <!-- Mobile Swiper -->
            <div class="swiper product-swiper d-md-none">
                <div class="swiper-wrapper" id="mobile-products"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>

    <script>
    require(['jquery', 'swiper'], function($, Swiper) {
        $(document).ready(function () {
            var categories = "{{{settings.category}}}";
            var categoryArray = categories ? categories.split(",") : [];
            var formKey = $('input[name="form_key"]').val();

            var $tabsWrapper    = $("#category-tabs");
            var $desktopWrapper = $("#desktop-products");
            var $mobileWrapper  = $("#mobile-products");

            $tabsWrapper.html("");
            $desktopWrapper.html("");
            $mobileWrapper.html("");

            var swiperInstance;

            // 1️⃣ Create dynamic tabs
            categoryArray.forEach(function(categoryId, index) {
                categoryId = categoryId.trim();
                var tabHtml = `<button class="tab-btn ${index === 0 ? 'active' : ''}" data-category="${categoryId}">Loading...</button>`;
                $tabsWrapper.append(tabHtml);

                // Fetch category name for the tab
                $.ajax({
                    url: '/customgoomento/category/getproducts',
                    type: 'POST',
                    dataType: 'json',
                    data: { category_id: categoryId, form_key: formKey },
                    success: function(response) {
                        if (response.success) {
                            $tabsWrapper.find(`button[data-category='${categoryId}']`).text(response.category_name);

                            // Auto-load first category
                            if(index === 0){
                                renderCategory(response);
                            }
                        }
                    }
                });
            });

            // 2️⃣ Tab click event
            $tabsWrapper.on('click', '.tab-btn', function() {
                var categoryId = $(this).data('category');

                $tabsWrapper.find('.tab-btn').removeClass('active');
                $(this).addClass('active');

                // Fetch products for clicked category
                $.ajax({
                    url: '/customgoomento/category/getproducts',
                    type: 'POST',
                    dataType: 'json',
                    data: { category_id: categoryId, form_key: formKey },
                    success: function(response) {
                        if(response.success){
                            renderCategory(response);
                        }
                    }
                });
            });

            // 3️⃣ Function to render desktop & mobile products
            function renderCategory(response){
                var products = response.products;
                $desktopWrapper.html("");
                $mobileWrapper.html("");

                products.forEach(function(product){
                    // Desktop card
                    var desktopHtml = `
                        <div class="col-lg-4 col-md-6">
                            <div class="product-card">
                                <div class="product-image">
                                    <img class="product-img" src="${product.image}" alt="${product.name}">
                                    <span class="discount-badge">New</span>
                                    <div class="product-actions">
                                        <button class="action-btn" title="Quick View"><img src="./images/eye.png" alt=""></button>
                                        <button class="action-btn" title="Add to Wishlist"><img src="./images/heart.png" alt=""></button>
                                        <button class="action-btn" title="Compare"><img class="shuffle" src="./images/shuffle.png" alt=""></button>
                                        <button class="action-btn" title="Add to Cart"><img src="./images/cart.png" alt=""></button>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h5 class="product-title">${product.name}</h5>
                                    <div class="product-price">
                                        <span class="current-price">₹${product.price}</span>
                                        <span class="original-price">₹${Math.round(product.price * 1.4)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    $desktopWrapper.append(desktopHtml);

                    // Mobile Swiper card
                    var mobileHtml = `
                        <div class="swiper-slide">
                            <div class="product-card">
                                <div class="product-image">
                                    <img class="product-img" src="${product.image}" alt="${product.name}">
                                    <span class="discount-badge">New</span>
                                    <div class="product-actions">
                                        <button class="action-btn" title="Quick View"><img src="./images/eye.png" alt=""></button>
                                        <button class="action-btn" title="Add to Wishlist"><img src="./images/heart.png" alt=""></button>
                                        <button class="action-btn" title="Compare"><img class="shuffle" src="./images/shuffle.png" alt=""></button>
                                        <button class="action-btn" title="Add to Cart"><img src="./images/cart.png" alt=""></button>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h5 class="product-title">${product.name}</h5>
                                    <div class="product-price">
                                        <span class="current-price">₹${product.price}</span>
                                        <span class="original-price">₹${Math.round(product.price * 1.4)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    $mobileWrapper.append(mobileHtml);
                });

                // Initialize or update Swiper
                if(swiperInstance) {
                    swiperInstance.update();
                } else {
                    swiperInstance = new Swiper('.product-swiper', {
                        slidesPerView: 1,
                        spaceBetween: 10,
                        pagination: { el: '.swiper-pagination', clickable: true },
                        breakpoints: {
                            768: { slidesPerView: 2, spaceBetween: 15 },
                            992: { slidesPerView: 3, spaceBetween: 20 }
                        }
                    });
                }
            }
        });
    });
    </script>
    <?php
}

protected function render(): string
{
    $settings = $this->getSettings();
    $categoryArray = isset($settings['category']) && is_array($settings['category'])
        ? array_filter(array_map('trim', $settings['category']))
        : [];
    $title = $settings['title'] ?? '';


    ob_start();
    ?>
    <div class="trending-section">
        <!-- Section Header -->
        <div class="main-title section-header">
            <h2 class="section-title"><?= $title; ?></h2>

            <!-- Tab Buttons (dynamic) -->
            <div class="tab-buttons" id="category-tabs">
                <!-- Tabs will be injected here -->
            </div>
        </div>

        <!-- Products Content -->
        <div class="tab-content trending-product active">
            <!-- Desktop grid -->
            <div class="row d-none d-md-flex" id="desktop-products"></div>

            <!-- Mobile Swiper -->
            <div class="swiper product-swiper d-md-none">
                <div class="swiper-wrapper" id="mobile-products"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>

    <script>
    require(['jquery', 'swiper'], function($, Swiper) {
        $(document).ready(function () {
            var categories = "<?= implode(',',$categoryArray) ?>";
            var categoryArray = categories ? categories.split(",") : [];
            var formKey = $('input[name="form_key"]').val();

            var $tabsWrapper    = $("#category-tabs");
            var $desktopWrapper = $("#desktop-products");
            var $mobileWrapper  = $("#mobile-products");

            $tabsWrapper.html("");
            $desktopWrapper.html("");
            $mobileWrapper.html("");

            var swiperInstance;

            // 1️⃣ Create dynamic tabs
            categoryArray.forEach(function(categoryId, index) {
                categoryId = categoryId.trim();
                var tabHtml = `<button class="tab-btn ${index === 0 ? 'active' : ''}" data-category="${categoryId}">Loading...</button>`;
                $tabsWrapper.append(tabHtml);

                // Fetch category name for the tab
                $.ajax({
                    url: '/customgoomento/category/getproducts',
                    type: 'POST',
                    dataType: 'json',
                    data: { category_id: categoryId, form_key: formKey },
                    success: function(response) {
                        if (response.success) {
                            $tabsWrapper.find(`button[data-category='${categoryId}']`).text(response.category_name);

                            // Auto-load first category
                            if(index === 0){
                                renderCategory(response);
                            }
                        }
                    }
                });
            });

            // 2️⃣ Tab click event
            $tabsWrapper.on('click', '.tab-btn', function() {
                var categoryId = $(this).data('category');

                $tabsWrapper.find('.tab-btn').removeClass('active');
                $(this).addClass('active');

                // Fetch products for clicked category
                $.ajax({
                    url: '/customgoomento/category/getproducts',
                    type: 'POST',
                    dataType: 'json',
                    data: { category_id: categoryId, form_key: formKey },
                    success: function(response) {
                        if(response.success){
                            renderCategory(response);
                        }
                    }
                });
            });

            // 3️⃣ Function to render desktop & mobile products
            function renderCategory(response){
                var products = response.products;
                $desktopWrapper.html("");
                $mobileWrapper.html("");

                products.forEach(function(product){
                    // Desktop card
                    var desktopHtml = `
                        <div class="col-lg-4 col-md-6">
                            <div class="product-card">
                                <div class="product-image">
                                    <img class="product-img" src="${product.image}" alt="${product.name}">
                                    <span class="discount-badge">New</span>
                                    <div class="product-actions">
                                        <button class="action-btn" title="Quick View"><img src="./images/eye.png" alt=""></button>
                                        <button class="action-btn" title="Add to Wishlist"><img src="./images/heart.png" alt=""></button>
                                        <button class="action-btn" title="Compare"><img class="shuffle" src="./images/shuffle.png" alt=""></button>
                                        <button class="action-btn" title="Add to Cart"><img src="./images/cart.png" alt=""></button>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h5 class="product-title">${product.name}</h5>
                                    <div class="product-price">
                                        <span class="current-price">₹${product.price}</span>
                                        <span class="original-price">₹${Math.round(product.price * 1.4)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    $desktopWrapper.append(desktopHtml);

                    // Mobile Swiper card
                    var mobileHtml = `
                        <div class="swiper-slide">
                            <div class="product-card">
                                <div class="product-image">
                                    <img class="product-img" src="${product.image}" alt="${product.name}">
                                    <span class="discount-badge">New</span>
                                    <div class="product-actions">
                                        <button class="action-btn" title="Quick View"><img src="./images/eye.png" alt=""></button>
                                        <button class="action-btn" title="Add to Wishlist"><img src="./images/heart.png" alt=""></button>
                                        <button class="action-btn" title="Compare"><img class="shuffle" src="./images/shuffle.png" alt=""></button>
                                        <button class="action-btn" title="Add to Cart"><img src="./images/cart.png" alt=""></button>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <h5 class="product-title">${product.name}</h5>
                                    <div class="product-price">
                                        <span class="current-price">₹${product.price}</span>
                                        <span class="original-price">₹${Math.round(product.price * 1.4)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    $mobileWrapper.append(mobileHtml);
                });

                // Initialize or update Swiper
                if(swiperInstance) {
                    swiperInstance.update();
                } else {
                    swiperInstance = new Swiper('.product-swiper', {
                        slidesPerView: 1,
                        spaceBetween: 10,
                        pagination: { el: '.swiper-pagination', clickable: true },
                        breakpoints: {
                            768: { slidesPerView: 1, spaceBetween: 15 },
                            992: { slidesPerView: 1, spaceBetween: 20 }
                        }
                    });
                }
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}


}