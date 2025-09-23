<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Controls\Groups\TextShadowGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\BorderGroup;

class ProductCountdown extends AbstractWidget
{
    const NAME = 'vendor_product_countdown';

    public function getName()
    {
        return self::NAME;
    }

    public function getTitle()
    {
        return __('Product Countdown');
    }

    public function getIcon()
    {
        return 'fas fa-star';
    }

    public function getCategories()
    {
        return ['general'];
    }

    protected function registerControls()
    {
        /**
         * 🛠 General Settings
         */
        $this->startControlsSection('section_countdown', [
            'label' => __('General Settings'),
        ]);

        $this->addControl('title', [
            'label' => __('Title'),
            'type' => Controls::TEXT,
            'default' => __('Limited Time Offer'),
        ]);

        $this->addControl('product', [
            'label' => __('Product SKU'),
            'type' => Controls::SELECT2,
            'multiple' => false,
            'placeholder' => __('Type SKU ...'),
            'select2options' => [
                'ajax' => [
                    'url' => UrlBuilderHelper::getUrl('pagebuilder/catalog/search'),
                ],
            ],
        ]);

        $this->addControl('end_date', [
            'label' => __('End Date'),
            'type' => Controls::DATE_TIME,
            'default' => date('Y-m-d H:i:s', strtotime('+7 days')),
        ]);

        $this->endControlsSection();


        /**
         * 🎨 Title Styling
         */
        $this->startControlsSection('section_title_style', [
            'label' => __('Title Styling'),
            'tab'   => Controls::TAB_STYLE,
        ]);

        $this->addGroupControl(
            TypographyGroup::NAME,
            [
                'name'     => 'title_typography',
                'scheme' => \Goomento\PageBuilder\Builder\Schemes\Typography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} .special-box-title',
            ]
        );

        $this->addControl('title_color', [
            'label' => __('Color'),
            'type'  => Controls::COLOR,
            'selectors' => [
                '{{WRAPPER}} .special-title' => 'color: {{VALUE}};',
            ],
        ]);

        $this->addResponsiveControl('title_margin', [
            'label' => __('Margin'),
            'type' => Controls::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .special-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->addResponsiveControl('title_padding', [
            'label' => __('Padding'),
            'type' => Controls::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .special-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->addResponsiveControl(
            'title_alignment',
            [
                'label' => __('Alignment'),
                'type' => Controls::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'fas fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'fas fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'fas fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified'),
                        'icon' => 'fas fa-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .special-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\BorderGroup::NAME,
            [
                'name' => 'title_border',
                'selector' => '{{WRAPPER}} .special-title',
                'separator' => 'before',
            ]
        );
         $this->addResponsiveControl(
            'title_border_radius',
            [
                'label' => __('Image Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .spacial-title' =>
                        'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

//  🖼️ Product Image Styling
         $this->startControlsSection('section_product_image_style', [
            'label' => __('Product Image Styling'),
            'tab'   => Controls::TAB_STYLE,
        ]);

        $this->addResponsiveControl(
            'product_image_alignment',
            [
                'label' => __('Alignment'),
                'type' => Controls::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'fas fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'fas fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'fas fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->addResponsiveControl(
            'product_image_margin',
            [
                'label' => __('Image Margin'),
                'type' => Controls::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .spacial_trend .img-fluid' =>
                        'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'product_image_padding',
            [
                'label' => __('Image Padding'),
                'type' => Controls::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .spacial_trend .img-fluid' =>
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'product_image_radius',
            [
                'label' => __('Image Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .spacial_trend .img-fluid' =>
                        'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->endControlsSection();

        /**
         * 🎨 Countdown Section Styling
         */
        $this->startControlsSection('section_countdown_style', [
            'label' => __('Countdown Section Styling'),
            'tab'   => Controls::TAB_STYLE,
        ]);

        $this->addControl('time_countdown_display', [
            'label'   => __('Display'),
            'type'    => Controls::SELECT,
            'options' => [
                'block'        => __('Block'),
                'inline-block' => __('Inline Block'),
                'flex'         => __('Flex'),
                'inline-flex'  => __('Inline Flex'),
                'grid'         => __('Grid'),
                'inline-grid'  => __('Inline Grid'),
                'none'         => __('None'),
            ],
            'default' => 'flex',
            'selectors' => [
                '{{WRAPPER}} .time_countdown_section' => 'display: {{VALUE}};',
            ],
        ]);

        $this->addControl('time_countdown_justify', [
            'label'   => __('Justify Content'),
            'type'    => Controls::SELECT,
            'options' => [
                'flex-start'    => __('Start'),
                'center'        => __('Center'),
                'flex-end'      => __('End'),
                'space-between' => __('Space Between'),
                'space-around'  => __('Space Around'),
                'space-evenly'  => __('Space Evenly'),
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .time_countdown_section' => 'justify-content: {{VALUE}};',
            ],
            'condition' => [
                'time_countdown_display' => ['flex','inline-flex','grid','inline-grid'],
            ],
        ]);

        $this->addControl('time_countdown_align', [
            'label'   => __('Align Items'),
            'type'    => Controls::SELECT,
            'options' => [
                'stretch'     => __('Stretch'),
                'flex-start'  => __('Start'),
                'center'      => __('Center'),
                'flex-end'    => __('End'),
                'baseline'    => __('Baseline'),
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .time_countdown_section' => 'align-items: {{VALUE}};',
            ],
            'condition' => [
                'time_countdown_display' => ['flex','inline-flex','grid','inline-grid'],
            ],
        ]);

        $this->addResponsiveControl('time_countdown_gap', [
            'label' => __('Gap'),
            'type' => Controls::SLIDER,
            'selectors' => [
                '{{WRAPPER}} .time_countdown_section' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

            $this->endControlsSection();


        /**
         * 🎨 Countdown Numbers Styling
         */
        $this->startControlsSection('section_countdown_number_style', [
            'label' => __('Countdown Numbers Styling'),
            'tab'   => Controls::TAB_STYLE,
        ]);

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup::NAME,
            [
                'name'     => 'time_countdown_typography',
                'selector' => '{{WRAPPER}} .time_countdown',
            ]
        );

        $this->addControl('time_countdown_color', [
            'label' => __('Color'),
            'type'  => Controls::COLOR,
            'selectors' => [
                '{{WRAPPER}} .time_countdown' => 'color: {{VALUE}};',
            ],
        ]);

        $this->addControl('time_countdown_bg', [
            'label' => __('Background'),
            'type'  => Controls::COLOR,
            'selectors' => [
                '{{WRAPPER}} .time_countdown' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->addResponsiveControl('time_countdown_margin', [
            'label' => __('Margin'),
            'type'  => Controls::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .time_countdown' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->addResponsiveControl('time_countdown_padding', [
            'label' => __('Padding'),
            'type'  => Controls::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .time_countdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->addResponsiveControl('time_countdown_radius', [
            'label' => __('Border Radius'),
            'type'  => Controls::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .time_countdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->endControlsSection();


        /**
         * 🎨 Current Price Styling
         */
        $this->startControlsSection('section_current_price_style', [
            'label' => __('Current Price Styling'),
            'tab'   => Controls::TAB_STYLE,
        ]);

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup::NAME,
            [
                'name'     => 'current_price_typography',
                'selector' => '{{WRAPPER}} .current-price',
            ]
        );

        $this->addControl('current_price_color', [
            'label' => __('Color'),
            'type'  => Controls::COLOR,
            'selectors' => [
                '{{WRAPPER}} .current-price' => 'color: {{VALUE}};',
            ],
        ]);

        $this->addResponsiveControl('current_price_margin', [
            'label' => __('Margin'),
            'type'  => Controls::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .current-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->addResponsiveControl('current_price_padding', [
            'label' => __('Padding'),
            'type'  => Controls::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .current-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->addResponsiveControl(
            'current_price_alignment',
            [
                'label' => __('Alignment'),
                'type' => Controls::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'fas fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'fas fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'fas fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified'),
                        'icon' => 'fas fa-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .current-price' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsSection();


        /**
         * 🎨 Original Price Styling
         */
       
        $this->startControlsSection('section_original_price_style', [
            'label' => __('Original Price Styling'),
            'tab'   => Controls::TAB_STYLE,
        ]);

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup::NAME,
            [
                'name'     => 'original_price_typography',
                'selector' => '{{WRAPPER}} .original-price',
            ]
        );

        $this->addControl('original_price_color', [
            'label' => __('Color'),
            'type'  => Controls::COLOR,
            'selectors' => [
                '{{WRAPPER}} .original-price' => 'color: {{VALUE}};',
            ],
            'default' => '#7E7E7E',
        ]);

        $this->addResponsiveControl('original_price_margin', [
            'label' => __('Margin'),
            'type'  => Controls::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .original-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->addResponsiveControl('original_price_padding', [
            'label' => __('Padding'),
            'type'  => Controls::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .original-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->addResponsiveControl(
            'original_price_alignment',
            [
                'label' => __('Alignment'),
                'type' => Controls::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'fas fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'fas fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'fas fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified'),
                        'icon' => 'fas fa-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .original-price' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsSection();



       

    }

    protected function contentTemplate()
    {
        ?>
        <div class="spacial_trend ">
          <div class="special-title">
            <h5 class="special-box-title">{{{ settings.title}}}</h5>
          </div>
          <img src="images/buds.png" alt="img" class="img-fluid">
          <div class="time_countdown_section" data-end-date="{{{ settings.end_date }}}">
            <div class="time_countdown">
              <div>314</div>
              <span>Days</span>
            </div>
            <div class="time_countdown">
              <div>09</div>
              <span>hour</span>
            </div>
            <div class="time_countdown">
              <div>25</div>
              <span>min</span>
            </div>
            <div class="time_countdown">
              <div>05</div>
              <span>sec</span>
            </div>
          </div>

          <div class="product-info">
                      <div class="product-rating">
                        <div class="stars">
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <i class="far fa-star"></i> 
                        </div>
                      </div>
                      <h5 class="product-title">Bluetooth headphone</h5>
                      <div class="product-price">
                        <span class="current-price">₹1226.00</span>
                        <span class="original-price">₹1250.99</span>
                      </div>
                    </div>
        </div>
    <script>
require(['jquery'], function($) {
    $(document).on('change', '[data-setting="product"]', function() {
        var sku = $(this).val();
        if (!sku) return;
        console.log("Selected SKU:", sku);
        $.ajax({
            url: BASE_URL + 'pagebuilder/product/info',
            type: 'GET',
            data: {sku: sku},
            success: function(data) {
                // Example JSON: {name, price, original_price, image}
                var container = $('.spacial_trend');
                container.find('.product-title').text(data.name);
                container.find('.current-price').text(data.price);
                container.find('.original-price').text(data.original_price);
                container.find('img.img-fluid').attr('src', data.image);
            }
        });
    });

    function initCountdown($section) {
        var endDateStr = $section.data('end-date');
        if (!endDateStr) return;

        // Convert to timestamp
        var endDate = new Date(endDateStr.replace(' ', 'T')).getTime();
        if (isNaN(endDate)) return;

        function updateCountdown() {
            var now = new Date().getTime();
            var distance = endDate - now;

            if (distance <= 0) {
                $section.find('.days').text("00");
                $section.find('.hours').text("00");
                $section.find('.minutes').text("00");
                $section.find('.seconds').text("00");
                clearInterval(interval);
                return;
            }

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            $section.find('.days').text(String(days).padStart(2, '0'));
            $section.find('.hours').text(String(hours).padStart(2, '0'));
            $section.find('.minutes').text(String(minutes).padStart(2, '0'));
            $section.find('.seconds').text(String(seconds).padStart(2, '0'));
        }

        updateCountdown();
        var interval = setInterval(updateCountdown, 1000);
    }

    $(document).ready(function () {
        $('.time_countdown_section').each(function () {
            initCountdown($(this));
        });
    });
});
</script>
        <?php
    }
    protected function render(): string
{
    $settings = $this->getSettings();
    $endDate = $settings['end_date'] ?? '';
    $title = $settings['title'] ?? '';

    // Load product using ObjectManager
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
    $productRepository = $objectManager->get(\Magento\Catalog\Model\ProductRepository::class);
    $product = $productRepository->get($settings['product']); // load by SKU from widget settings

    // Get product image URL
    $imageHelper = $objectManager->get(\Magento\Catalog\Helper\Image::class);
    $productImage = $imageHelper->init($product, 'product_base_image')->getUrl();

    // Get product price
    $finalPrice = number_format((int)$product->getFinalPrice(), 2);
    $originalPrice = number_format((int)$product->getPrice(), 2);
    $productUrl = $product->getProductUrl();

    return '
    <div class="spacial_trend">
        <div class="special-title">
            <h5 class="special-box-title">' . $title . '</h5>
        </div>
<a href="' . $productUrl . '">
            <img src="' . $productImage . '" alt="' . $product->getName() . '" class="img-fluid">
        </a>
        <div class="time_countdown_section" data-end-date="' . $endDate . '" style="display:flex;">
            <div class="time_countdown"><div class="days">00</div><span>Days</span></div>
            <div class="time_countdown"><div class="hours">00</div><span>Hour</span></div>
            <div class="time_countdown"><div class="minutes">00</div><span>Min</span></div>
            <div class="time_countdown"><div class="seconds">00</div><span>Sec</span></div>
        </div>

        <div class="product-info">
            <div class="product-rating">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="far fa-star"></i> 
                </div>
            </div>
 <h5 class="product-title">
                <a href="' . $productUrl . '">' . $product->getName() . '</a>
            </h5>            <div class="product-price">
                <span class="current-price">₹' . $finalPrice . '</span>
                <span class="original-price">₹' . $originalPrice . '</span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".time_countdown_section").forEach(function(section) {
                var endDateStr = section.getAttribute("data-end-date");
                if (!endDateStr) return;

                var endDate = new Date(endDateStr.replace(" ", "T")).getTime();

                function updateCountdown() {
                    var now = new Date().getTime();
                    var distance = endDate - now;

                    if (distance <= 0) {
                        section.querySelector(".days").textContent = "00";
                        section.querySelector(".hours").textContent = "00";
                        section.querySelector(".minutes").textContent = "00";
                        section.querySelector(".seconds").textContent = "00";
                        clearInterval(interval);
                        return;
                    }

                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    section.querySelector(".days").textContent = String(days).padStart(2,"0");
                    section.querySelector(".hours").textContent = String(hours).padStart(2,"0");
                    section.querySelector(".minutes").textContent = String(minutes).padStart(2,"0");
                    section.querySelector(".seconds").textContent = String(seconds).padStart(2,"0");
                }

                updateCountdown();
                var interval = setInterval(updateCountdown, 1000);
            });
        });
    </script>
    ';
}
}
