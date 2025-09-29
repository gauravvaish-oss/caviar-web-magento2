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
       $this->startControlsSection('content_section', [
            'label' => __('Content'),
            'tab'   => Controls::TAB_CONTENT,
        ]);

        $this->addControl('title', [
            'label' => __('Title'),
            'type' => Controls::TEXT,
            'default' => __('Title Text'),
        ]);
        $this->addControl('subtitle', [
            'label' => __('Sub Title'),
            'type' => Controls::TEXT,
            'default' => __('Subtitle Text'),
        ]);
        $this->addControl('description', [
            'label' => __('Description'),
            'type' => Controls::TEXT,
            'default' => __('Description Text'),
        ]);
        $this->addControl('button_text', [
            'label' => __('Button Text'),
            'type' => Controls::TEXT,
            'default' => __('Click Here'),
        ]);
        $this->addControl('button_link', [
            'label' => __('Button Link'),
            'type' => Controls::TEXT,
            'default' => __('#'),
        ]);
        $this->addControl("banner_img", [
            'label' => __("Banner Image"),
            'type'  => Controls::MEDIA,
        ]);

        $this->endControlsSection();
    }

    protected function contentTemplate()
    {
        ?>
        <div class="col-md-12">
            <div class="row">
              <section class="banner-section position-relative">
                <img src="{{{settings.banner_img.url}}}" class="img-fluid w-100 banner-img" alt="Banner">

                <div class="banner-content text-center text-white">
                  <p class="banner-subtitle">{{{settings.subtitle}}}</p>
                  <h2 class="banner-title">{{{settings.title}}}</h2>
                  <p class="banner-desc">{{{settings.description}}}</p>
                  <a href="{{{settings.button_link}}}" class="btn btn-warning btn-lg banner-btn">{{{settings.button_text}}}</a>
                </div>
              </section>

            </div>
          </div>
        <?php
    }

    protected function render(): string
    {
        $settings = $this->getSettings();
        ob_start();
        ?>
        <div class="col-md-12">
            <div class="row">
              <section class="banner-section position-relative">
                <img src="<?php echo $settings['banner_img']['url']; ?>" class="img-fluid w-100 banner-img" alt="Banner">

                <div class="banner-content text-center text-white">
                  <p class="banner-subtitle"><?php echo $settings['subtitle']; ?></p>
                  <h2 class="banner-title"><?php echo $settings['title']; ?></h2>
                  <p class="banner-desc"><?php echo $settings['description']; ?></p>
                  <a href="<?php echo $settings['button_link']; ?>" class="btn btn-warning btn-lg banner-btn"><?php echo $settings['button_text'] ?></a>
                </div>
              </section>

            </div>
          </div>
        <?php
        return ob_get_clean();
    }
}
