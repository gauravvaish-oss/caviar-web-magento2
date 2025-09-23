<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

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
            'label' => __("Select Categories"),
            'type' => Controls::SELECT2,
            'multiple' => true,
            'options' => $options,
        ]);

        $this->endControlsSection();
    }

protected function contentTemplate()
{
    ?>
    <div class="main-title">
        <div class="d-flex align-items-center justify-content-between">
            <h2 data-bind="text: settings.title"></h2>
            <div class="d-flex justify-content-end">
                <div class="top_product-prev swiper-button-prev"></div>
                <div class="top_product-next swiper-button-next"></div>
            </div>
        </div>

        <div class="swiper top_product_slider">
            <div class="swiper-wrapper" data-bind="foreach: settings.category">
                <div class="swiper-slide category-card text-center">
                    <img src="https://via.placeholder.com/150" alt="Category Preview" />
                    <h4>Category ID: <span data-bind="text: $data"></span></h4>
                    <span class="product-count">(Preview)</span>
                </div>
            </div>
        </div>
    </div>
    <?php
}

}
