<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class CustomBanner extends AbstractWidget
{
    const NAME = 'vendor_custom_banner';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTitle(): string
    {
        return __('Custom Banner');
    }

    public function getIcon(): string
    {
        return 'fa fa-image';
    }

    public function getCategories(): array
    {
        return ['general'];
    }

    protected function registerControls()
    {
        /**
         * Content Tab
         */
        $this->startControlsSection('content_section', [
            'label' => __('Content'),
            'tab'   => Controls::TAB_CONTENT,
        ]);

        $this->addControl('background_image', [
            'label' => __('Background Image'),
            'type'  => Controls::MEDIA,
        ]);

        $this->addControl('subtitle', [
            'label' => __('Subtitle'),
            'type'  => Controls::TEXT,
            'default' => __('HURRY UP!'),
        ]);

        $this->addControl('title', [
            'label' => __('Title'),
            'type'  => Controls::TEXTAREA,
            'default' => __('All Electrical Service'),
        ]);

        $this->addControl('description', [
            'label' => __('Description'),
            'type'  => Controls::TEXTAREA,
            'default' => __('Product That Can Be Worn Day And It Is So We Keep It For Any Time, In The At Any Time Set.'),
        ]);

        $this->addControl('button_text', [
            'label' => __('Button Text'),
            'type'  => Controls::TEXT,
            'default' => __('SHOP NOW'),
        ]);

        $this->addControl('button_link', [
            'label' => __('Button Link'),
            'type'  => Controls::URL,
        ]);

        $this->endControlsSection();

        /**
         * Style Tab - Banner
         */
        $this->startControlsSection('banner_style', [
            'label' => __('Banner'),
            'tab'   => Controls::TAB_STYLE,
        ]);

        $this->addControl('banner_padding', [
            'label' => __('Padding'),
            'type'  => Controls::DIMENSIONS,
            'selectors' => [
                '{{WRAPPER}} .banner-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
        $this->addControl(
            'banner_image_opacity',
            [
                'label' => __('Opacity'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . 'banner-section' . ' img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->addControl(
            'banner_background_hover_transition',
            [
                'label' => __('Transition Duration'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 0.3,
                ],
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . 'banner-section' . ' img' => 'transition-duration: {{SIZE}}s',
                ],
            ]
        );

        $this->endControlsSection();

        /**
         * Style Tab - Title
         */
        $this->startControlsSection('title_style', [
            'label' => __('Title'),
            'tab'   => Controls::TAB_STYLE,
        ]);

        $this->addControl('title_color', [
            'label' => __('Color'),
            'type'  => Controls::COLOR,
            'selectors' => [
                '{{WRAPPER}} .banner-title' => 'color: {{VALUE}};',
            ],
        ]);

        $this->addControl('title_typography', [
            'label' => __('Typography'),
            'type'  => Controls::TYPOGRAPHY,
            'selector' => '{{WRAPPER}} .banner-title',
        ]);

        $this->endControlsSection();

        /**
         * Style Tab - Subtitle
         */
        $this->startControlsSection('subtitle_style', [
            'label' => __('Subtitle'),
            'tab'   => Controls::TAB_STYLE,
        ]);

        $this->addControl('subtitle_color', [
            'label' => __('Color'),
            'type'  => Controls::COLOR,
            'selectors' => [
                '{{WRAPPER}} .banner-subtitle' => 'color: {{VALUE}};',
            ],
        ]);

        $this->addControl('subtitle_typography', [
            'label' => __('Typography'),
            'type'  => Controls::TYPOGRAPHY,
            'selector' => '{{WRAPPER}} .banner-subtitle',
        ]);

        $this->endControlsSection();

        /**
         * Style Tab - Description
         */
        $this->startControlsSection('desc_style', [
            'label' => __('Description'),
            'tab'   => Controls::TAB_STYLE,
        ]);

        $this->addControl('desc_color', [
            'label' => __('Color'),
            'type'  => Controls::COLOR,
            'selectors' => [
                '{{WRAPPER}} .banner-desc' => 'color: {{VALUE}};',
            ],
        ]);

        $this->addControl('desc_typography', [
            'label' => __('Typography'),
            'type'  => Controls::TYPOGRAPHY,
            'selector' => '{{WRAPPER}} .banner-desc',
        ]);

        $this->endControlsSection();

        /**
         * Style Tab - Button
         */
        $this->startControlsSection('button_style', [
            'label' => __('Button'),
            'tab'   => Controls::TAB_STYLE,
        ]);

        $this->addControl('button_color', [
            'label' => __('Text Color'),
            'type'  => Controls::COLOR,
            'selectors' => [
                '{{WRAPPER}} .banner-btn' => 'color: {{VALUE}};',
            ],
        ]);

        $this->addControl('button_bg_color', [
            'label' => __('Background Color'),
            'type'  => Controls::COLOR,
            'selectors' => [
                '{{WRAPPER}} .banner-btn' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->addControl('button_typography', [
            'label' => __('Typography'),
            'type'  => Controls::TYPOGRAPHY,
            'selector' => '{{WRAPPER}} .banner-btn',
        ]);

        $this->endControlsSection();
    }

    protected function render(): string
    {
        $settings = $this->getSettingsForDisplay();
        $bg = $settings['background_image']['url'] ?? '';

        ob_start(); ?>
        <section class="banner-section position-relative" style="background-image:url('<?= $bg ?>'); background-size:cover; background-position:center;">
            <div class="banner-content text-center text-white">
                <?php if (!empty($settings['subtitle'])): ?>
                    <p class="banner-subtitle"><?= $settings['subtitle'] ?></p>
                <?php endif; ?>

                <?php if (!empty($settings['title'])): ?>
                    <h2 class="banner-title"><?= nl2br($settings['title']) ?></h2>
                <?php endif; ?>

                <?php if (!empty($settings['description'])): ?>
                    <p class="banner-desc"><?= nl2br($settings['description']) ?></p>
                <?php endif; ?>

                <?php if (!empty($settings['button_text'])): ?>
                    <a href="<?= $settings['button_link']['url'] ?? '#' ?>" class="btn banner-btn">
                        <?= $settings['button_text'] ?>
                    </a>
                <?php endif; ?>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    protected function contentTemplate(): string
    {
        return <<<HTML
        <section class="banner-section position-relative" style="background-size:cover; background-position:center;">
            <div class="banner-content text-center text-white">
                <p class="banner-subtitle">{{{ settings.subtitle }}}</p>
                <h2 class="banner-title">{{{ settings.title }}}</h2>
                <p class="banner-desc">{{{ settings.description }}}</p>
                <a href="#" class="btn banner-btn">{{{ settings.button_text }}}</a>
            </div>
        </section>
        HTML;
    }
}
