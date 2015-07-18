<?php
/*
Plugin Name: StatsFC Results
Plugin URI: https://statsfc.com/widgets/results
Description: StatsFC Results
Version: 1.6
Author: Will Woodward
Author URI: http://willjw.co.uk
License: GPL2
*/

/*  Copyright 2013  Will Woodward  (email : will@willjw.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('STATSFC_RESULTS_ID',      'StatsFC_Results');
define('STATSFC_RESULTS_NAME',    'StatsFC Results');
define('STATSFC_RESULTS_VERSION', '1.6');

/**
 * Adds StatsFC widget.
 */
class StatsFC_Results extends WP_Widget
{
    public $isShortcode = false;

    protected static $count = 0;

    private static $defaults = array(
        'title'       => '',
        'key'         => '',
        'competition' => '',
        'team'        => '',
        'highlight'   => '',
        'from'        => '',
        'to'          => '',
        'limit'       => 0,
        'goals'       => false,
        'show_badges' => true,
        'show_dates'  => true,
        'order'       => 'desc',
        'timezone'    => 'Europe/London',
        'default_css' => true
    );

    private static $whitelist = array(
        'competition',
        'team',
        'highlight',
        'from',
        'to',
        'limit',
        'goals',
        'showBadges',
        'showDates',
        'order',
        'timezone'
    );

    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        parent::__construct(STATSFC_RESULTS_ID, STATSFC_RESULTS_NAME, array('description' => 'StatsFC Results'));
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     *
     * @todo Option to show match incidents.
     */
    public function form($instance)
    {
        $instance    = wp_parse_args((array) $instance, self::$defaults);
        $title       = strip_tags($instance['title']);
        $key         = strip_tags($instance['key']);
        $competition = strip_tags($instance['competition']);
        $team        = strip_tags($instance['team']);
        $highlight   = strip_tags($instance['highlight']);
        $from        = strip_tags($instance['from']);
        $to          = strip_tags($instance['to']);
        $limit       = strip_tags($instance['limit']);
        $goals       = strip_tags($instance['goals']);
        $show_badges = strip_tags($instance['show_badges']);
        $show_dates  = strip_tags($instance['show_dates']);
        $order       = strip_tags($instance['order']);
        $timezone    = strip_tags($instance['timezone']);
        $default_css = strip_tags($instance['default_css']);
        ?>
        <p>
            <label>
                <?php _e('Title', STATSFC_RESULTS_ID); ?>
                <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
            </label>
        </p>
        <p>
            <label>
                <?php _e('Key', STATSFC_RESULTS_ID); ?>
                <input class="widefat" name="<?php echo $this->get_field_name('key'); ?>" type="text" value="<?php echo esc_attr($key); ?>">
            </label>
        </p>
        <p>
            <label>
                <?php _e('Competition', STATSFC_RESULTS_ID); ?>
                <input class="widefat" name="<?php echo $this->get_field_name('competition'); ?>" type="text" value="<?php echo esc_attr($competition); ?>">
            </label>
        </p>
        <p>
            <label>
                <?php _e('Team', STATSFC_RESULTS_ID); ?>
                <input class="widefat" name="<?php echo $this->get_field_name('team'); ?>" type="text" value="<?php echo esc_attr($team); ?>" placeholder="e.g., Liverpool, Manchester City">
            </label>
        </p>
        <p>
            <label>
                <?php _e('Highlight team', STATSFC_RESULTS_ID); ?>
                <input class="widefat" name="<?php echo $this->get_field_name('highlight'); ?>" type="text" value="<?php echo esc_attr($highlight); ?>" placeholder="e.g., Liverpool, Manchester City">
            </label>
        </p>
        <p>
            <label>
                <?php _e('From', STATSFC_RESULTS_ID); ?>
                <input class="widefat" name="<?php echo $this->get_field_name('from'); ?>" type="text" value="<?php echo esc_attr($from); ?>" placeholder="e.g., <?php echo date('Y-m-d'); ?>, today">
            </label>
        </p>
        <p>
            <label>
                <?php _e('To', STATSFC_RESULTS_ID); ?>
                <input class="widefat" name="<?php echo $this->get_field_name('to'); ?>" type="text" value="<?php echo esc_attr($to); ?>" placeholder="e.g., <?php echo date('Y-m-d', '+2 weeks'); ?>, +2 weeks, next Monday">
            </label>
        </p>
        <p>
            <label>
                <?php _e('Limit', STATSFC_RESULTS_ID); ?>
                <input class="widefat" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo esc_attr($limit); ?>" min="0" max="99"><br>
                <small>Applies to single team only. Choose '0' for all results.</small>
            </label>
        </p>
        <p>
            <label>
                <?php _e('Show goal scorers?', STATSFC_RESULTS_ID); ?>
                <input type="checkbox" name="<?php echo $this->get_field_name('goals'); ?>"<?php echo ($goals == 'on' ? ' checked' : ''); ?>>
            </label>
        </p>
        <p>
            <label>
                <?php _e('Show badges?', STATSFC_RESULTS_ID); ?>
                <input type="checkbox" name="<?php echo $this->get_field_name('show_badges'); ?>"<?php echo ($show_badges == 'on' ? ' checked' : ''); ?>>
            </label>
        </p>
        <p>
            <label>
                <?php _e('Show dates?', STATSFC_RESULTS_ID); ?>
                <input type="checkbox" name="<?php echo $this->get_field_name('show_dates'); ?>"<?php echo ($show_dates == 'on' ? ' checked' : ''); ?>>
            </label>
        </p>
        <p>
            <label>
                <?php _e('Order', STATSFC_RESULTS_ID); ?>
                <select class="widefat" name="<?php echo $this->get_field_name('order'); ?>">
                    <?php
                    $orders = array(
                        'asc'  => 'Ascending',
                        'desc' => 'Descending'
                    );

                    foreach ($orders as $key => $value) {
                        echo '<option value="' . esc_attr($key) . '"' . ($key == $order ? ' selected' : '') . '>' . esc_attr($value) . '</option>' . PHP_EOL;
                    }
                    ?>
                </select>
            </label>
        </p>
        <p>
            <label>
                <?php _e('Timezone', STATSFC_RESULTS_ID); ?>
                <select class="widefat" name="<?php echo $this->get_field_name('timezone'); ?>">
                    <?php
                    $zones = timezone_identifiers_list();

                    foreach ($zones as $zone) {
                        echo '<option value="' . esc_attr($zone) . '"' . ($zone == $timezone ? ' selected' : '') . '>' . esc_attr($zone) . '</option>' . PHP_EOL;
                    }
                    ?>
                </select>
            </label>
        </p>
        <p>
            <label>
                <?php _e('Use default styles?', STATSFC_RESULTS_ID); ?>
                <input type="checkbox" name="<?php echo $this->get_field_name('default_css'); ?>"<?php echo ($default_css == 'on' ? ' checked' : ''); ?>>
            </label>
        </p>
    <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance                = $old_instance;
        $instance['title']       = strip_tags($new_instance['title']);
        $instance['key']         = strip_tags($new_instance['key']);
        $instance['competition'] = strip_tags($new_instance['competition']);
        $instance['team']        = strip_tags($new_instance['team']);
        $instance['highlight']   = strip_tags($instance['highlight']);
        $instance['from']        = strip_tags($new_instance['from']);
        $instance['to']          = strip_tags($new_instance['to']);
        $instance['limit']       = strip_tags($new_instance['limit']);
        $instance['goals']       = strip_tags($instance['goals']);
        $instance['show_badges'] = strip_tags($instance['show_badges']);
        $instance['show_dates']  = strip_tags($instance['show_dates']);
        $instance['order']       = strip_tags($new_instance['order']);
        $instance['timezone']    = strip_tags($new_instance['timezone']);
        $instance['default_css'] = strip_tags($new_instance['default_css']);

        return $instance;
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        extract($args);

        $title       = apply_filters('widget_title', $instance['title']);
        $unique_id   = ++static::$count;
        $key         = $instance['key'];
        $referer     = (array_key_exists('HTTP_REFERER', $_SERVER) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : '');
        $default_css = filter_var($instance['default_css'], FILTER_VALIDATE_BOOLEAN);

        $options = array(
            'competition' => $instance['competition'],
            'team'        => $instance['team'],
            'highlight'   => $instance['highlight'],
            'from'        => $instance['from'],
            'to'          => $instance['to'],
            'limit'       => (int) $instance['limit'],
            'goals'       => (filter_var($instance['goals'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false'),
            'showBadges'  => (filter_var($instance['show_badges'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false'),
            'showDates'   => (filter_var($instance['show_dates'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false'),
            'order'       => $instance['order'],
            'timezone'    => $instance['timezone']
        );

        $html  = $before_widget;
        $html .= $before_title . $title . $after_title;
        $html .= '<div id="statsfc-results-' . $unique_id . '"></div>' . PHP_EOL;
        $html .= $after_widget;

        // Enqueue CSS
        if ($default_css) {
            wp_register_style(STATSFC_RESULTS_ID . '-css', plugins_url('all.css', __FILE__), null, STATSFC_RESULTS_VERSION);
            wp_enqueue_style(STATSFC_RESULTS_ID . '-css');
        }

        // Enqueue base JS
        wp_register_script(STATSFC_RESULTS_ID . '-js', plugins_url('results.js', __FILE__), array('jquery'), STATSFC_RESULTS_VERSION, true);
        wp_enqueue_script(STATSFC_RESULTS_ID . '-js');

        // Enqueue widget JS
        $object = 'statsfc_results_' . $unique_id;

        $script  = '<script>' . PHP_EOL;
        $script .= 'var ' . $object . ' = new StatsFC_Results(' . json_encode($key) . ');' . PHP_EOL;
        $script .= $object . '.referer = ' . json_encode($referer) . ';' . PHP_EOL;

        foreach (static::$whitelist as $parameter) {
            if (! array_key_exists($parameter, $options)) {
                continue;
            }

            $script .= $object . '.' . $parameter . ' = ' . json_encode($options[$parameter]) . ';' . PHP_EOL;
        }

        $script .= $object . '.display("statsfc-results-' . $unique_id . '");' . PHP_EOL;
        $script .= '</script>';

        add_action('wp_print_footer_scripts', function() use ($script)
        {
            echo $script;
        });

        if ($this->isShortcode) {
            return $html;
        } else {
            echo $html;
        }
    }

    public static function shortcode($atts)
    {
        $args = shortcode_atts(self::$defaults, $atts);

        $widget              = new self;
        $widget->isShortcode = true;

        return $widget->widget(array(), $args);
    }
}

// Register StatsFC widget
add_action('widgets_init', function()
{
    register_widget(STATSFC_RESULTS_ID);
});

add_shortcode('statsfc-results', STATSFC_RESULTS_ID . '::shortcode');
