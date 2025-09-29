<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class ShopByCategory extends AbstractWidget
{
    
    const NAME = 'vendor_sho[p_by_category';

    public function getName() { return self::NAME; }
    public function getTitle() { return __('Shop BY Category'); }
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
            'options' => $options
        ]);

        $this->endControlsSection();
    }


protected function contentTemplate()
{
    ?>
    <section class="shop-by_category">
        <div class="row">
            <div class="main-title">
                <div class="d-flex align-items-center justify-content-between">
                    <h2>{{{settings.title}}}</h2>
                    <div class="d-flex justify-content-end">
                        <div class="shop-prev swiper-button-prev"></div>
                        <div class="shop-next swiper-button-next"></div>
                    </div>
                </div>

                <div class="swiper shop_by_category">
                    <div class="swiper-wrapper" id="subcategory-wrapper">
                        <!-- Dynamic slides will be injected here -->
                    </div>
                    <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                </div>
            </div>
        </div>
    </section>

    <script>
    require(['jquery', 'swiper'], function($, Swiper) {
        $(document).ready(function () {
            var categories = "{{{settings.category}}}";
            var categoryArray = categories ? categories.split(",") : [];
            var formKey = $('input[name="form_key"]').val();
            var swiperInstance;

            var $swiperWrapper = $("#subcategory-wrapper");
            $swiperWrapper.html(""); // clear initial content

            // Fetch each category and build slide
            categoryArray.forEach(function(categoryId) {
                categoryId = categoryId.trim();

                $.ajax({
                    url: '/customgoomento/category/shopbycategory', // ✅ updated controller URL
                    type: 'POST',
                    dataType: 'json',
                    data: { category_id: categoryId, form_key: formKey },
                    success: function(response) {
                        if (response.success) {
                            var subcategories = response.subcategories || [];
                            var subListHtml = "";

                            if (subcategories.length > 0) {
                                subcategories.forEach(function(sub) {
                                    subListHtml += `
                                        <li>
                                            <h6><a href="${sub.url}">${sub.name}</a></h6>
                                        </li>`;
                                });
                            } else {
                                subListHtml = `<li><h6>No subcategories found</h6></li>`;
                            }

                            var slideHtml = `
                                <div class="swiper-slide">
                                    <div class="category_section_bg">
                                        <img src="${response.category_image ?? ''}" alt="${response.category_name}" class="img-fluid">
                                        <ul>${subListHtml}</ul>
                                        <div class="text-center mb-lg-4 mb-3">
                                            <a href="${response.category_url}">Shop Now</a>
                                        </div>
                                    </div>
                                </div>
                            `;

                            $swiperWrapper.append(slideHtml);

                            // 🔄 Initialize or update Swiper
                            if (swiperInstance) {
                                swiperInstance.update();
                            } else {
                                swiperInstance = new Swiper('.shop_by_category', {
                                    slidesPerView: 1,
                                    spaceBetween: 20,
                                    navigation: {
                                        nextEl: '.shop-next',
                                        prevEl: '.shop-prev'
                                    },
                                    breakpoints: {
                                        768: { slidesPerView: 2 },
                                        992: { slidesPerView: 3 }
                                    }
                                });
                            }
                        }
                    }
                });
            });
        });
    });
    </script>
    <?php
}

}