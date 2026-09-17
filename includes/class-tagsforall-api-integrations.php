<?php
declare(strict_types=1);

namespace Tags4All\Includes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Conectores de APIs Externas (GA4, Meta Graph, YouTube v3, HubSpot v3)
 */
class ApiIntegrationsManager
{
    private array $options;

    public function __construct()
    {
        $this->options = (array) get_option('tagsforall_options', []);
        add_action('wp_head', [$this, 'injectGa4Tracking'], 10);
        add_action('wp_head', [$this, 'injectMetaPixel'], 11);
        add_action('wp_footer', [$this, 'injectHubSpotTracking'], 20);
    }

    public function injectGa4Tracking(): void
    {
        if (empty($this->options['ga4_measurement_id'])) {
            return;
        }

        $ga4_id = esc_attr($this->options['ga4_measurement_id']);
        echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $ga4_id . '"></script>' . "\n";
        echo '<script>' . "\n";
        echo 'window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","' . $ga4_id . '",{"send_page_view":true});' . "\n";
        echo '</script>' . "\n";
    }

    public function injectMetaPixel(): void
    {
        if (empty($this->options['meta_pixel_id'])) {
            return;
        }

        $pixel_id = esc_attr($this->options['meta_pixel_id']);
        echo '<script defer src="https://connect.facebook.net/en_US/fbevents.js" id="fb-pixel-script" data-pixel-id="' . $pixel_id . '"></script>' . "\n";
    }

    public function injectHubSpotTracking(): void
    {
        if (empty($this->options['hubspot_portal_id'])) {
            return;
        }

        $portal_id = esc_attr($this->options['hubspot_portal_id']);
        echo '<script async defer id="hs-script-loader" src="//js.hs-scripts.com/' . $portal_id . '.js"></script>' . "\n";
    }
}
