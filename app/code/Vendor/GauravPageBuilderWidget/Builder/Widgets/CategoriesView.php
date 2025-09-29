<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class CategoriesView extends AbstractWidget
{
    const NAME = 'vendor_category_view';

    public function getName() { return self::NAME; }
    public function getTitle() { return __('Categories View'); }
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
            'description' => __("Please select at least 3 categories."),
            'validation' => function ($value) {
                if (!is_array($value) || count($value) < 3) {
                    return __("You must select at least 3 categories.");
                }
                return true;
            },
        ]);
        $this->addControl('array_png', [
            'label' => __('Arrow Picture'),
            'type'  => Controls::MEDIA,
        ]);

        $this->endControlsSection();
    }

protected function contentTemplate()
{
    ?>
    <div class="main-title">
        <div class="d-flex align-items-center justify-content-center justify-content-md-between">
            <h2>{{{settings.title}}}</h2>
            <div class="d-none d-md-flex justify-content-end">
                <div class="top_product-prev swiper-button-prev" tabindex="0" role="button"></div>
                <div class="top_product-next swiper-button-next" tabindex="0" role="button"></div>
            </div>
        </div>

        <div class="swiper top_product_slider">
            <div class="swiper-wrapper" id="category-slider-view"></div>
            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
        </div>
    </div>

    <script>
    require(['jquery', 'swiper'], function($, Swiper) {
        $(document).ready(function () {
            var categories = "{{{settings.category}}}";
            var categoryArray = categories ? categories.split(",") : [];
            var formKey = $('input[name="form_key"]').val();
            var $sliderWrapper = $("#category-slider-view");
            $sliderWrapper.html("");
            var ajaxRequests = [];
            categoryArray.forEach(function(categoryId, index) {
                categoryId = categoryId.trim();
                var request = $.ajax({
                    url: '/customgoomento/category/categoriesview',
                    type: 'POST',
                    dataType: 'json',
                    data: { category_id: categoryId, form_key: formKey },
                    success: function(response) {
                        if (response.success) {
                            var html = `
                                <div class="swiper-slide" role="group" aria-label="${index + 1} / ${categoryArray.length}">
                                    <div class="top_product_section_bg">
                                        <img src="${response.category_image}" alt="${response.category_name}" class="img-fluid">
                                        <h5>${response.category_name}</h5>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>${response.product_count} ( Items )</span>
                                            <a href="${response.category_url}">
                                                <img src="{{{settings.array_png.url}}}" alt="Go to ${response.category_name}" class="img-fluid">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $sliderWrapper.append(html);
                        } else {
                            console.error("Failed to load category:", categoryId);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error for category " + categoryId + ":", xhr.responseText);
                    }
                });

                ajaxRequests.push(request);
            });
            $.when.apply($, ajaxRequests).done(function() {
                new Swiper('.top_product_slider', {
                    slidesPerView: 3,
                    spaceBetween: 10,
                    navigation: {
                        nextEl: '.top_product-next',
                        prevEl: '.top_product-prev',
                    },
                    breakpoints: {
                        768: { slidesPerView: 2 },
                        480: { slidesPerView: 1 }
                    }
                });
            });

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
    $arrowUrl = $settings['array_png']['url'] ?? ''; // fallback if not set

    ob_start();
    ?>
<div class="main-title">
    <div class="d-flex align-items-center justify-content-center justify-content-md-between">
        <h2><?= htmlspecialchars($title) ?></h2>
        <div class="d-none d-md-flex justify-content-end">
            <div class="top_product-prev swiper-button-prev" tabindex="0" role="button"></div>
            <div class="top_product-next swiper-button-next" tabindex="0" role="button"></div>
        </div>
    </div>

    <div class="swiper top_product_slider">
        <div class="swiper-wrapper" id="category-slider-view"></div>
        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
    </div>
</div>

<script>
require(['jquery', 'swiper'], function($, Swiper) {
    $(document).ready(function () {
        var categoryArray = <?= json_encode($categoryArray) ?>;
        if (!categoryArray.length) return;

        var formKey = $('input[name="form_key"]').val();
        var $sliderWrapper = $("#category-slider-view");
        $sliderWrapper.empty();

        var ajaxRequests = categoryArray.map(function(categoryId, index) {
            return $.ajax({
                url: '/customgoomento/category/categoriesview',
                type: 'POST',
                dataType: 'json',
                data: { category_id: categoryId, form_key: formKey },
                success: function(response) {
                    if (response.success) {
                        var categoryImage = response.category_image || '<?= $arrowUrl ?>';
                        var categoryName = response.category_name || 'Unnamed';
                        var productCount = response.product_count || 0;
                        var categoryUrl = response.category_url || '#';

                        var html = `
                            <div class="swiper-slide" role="group" aria-label="${index + 1} / ${categoryArray.length}">
                                <div class="top_product_section_bg">
                                    <img src="${categoryImage}" alt="${categoryName}" class="img-fluid">
                                    <h5>${categoryName}</h5>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>${productCount} ( Items )</span>
                                        <a href="${categoryUrl}">
                                            <img src="<?= $arrowUrl ?>" alt="Go to ${categoryName}" class="img-fluid">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                        $sliderWrapper.append(html);
                    } else {
                        console.warn("Failed to load category:", categoryId);
                    }
                },
                error: function(xhr) {
                    console.error("AJAX error for category " + categoryId + ":", xhr.responseText);
                }
            });
        });

        // Initialize Swiper after all AJAX calls complete
        $.when.apply($, ajaxRequests).done(function() {
            if ($sliderWrapper.children().length) {
                new Swiper('.top_product_slider', {
                    slidesPerView: 3,
                    spaceBetween: 10,
                    loop: true,
                    navigation: {
                        nextEl: '.top_product-next',
                        prevEl: '.top_product-prev',
                    },
                    breakpoints: {
                        768: { slidesPerView: 1 },
                        480: { slidesPerView: 1 }
                    }
                });
            }
        });
    });
});
</script>
    <?php
    return ob_get_clean();
}

}
