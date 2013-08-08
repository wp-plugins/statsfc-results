<?php
/*
Plugin Name: StatsFC Results
Plugin URI: https://statsfc.com/docs/wordpress
Description: StatsFC Results
Version: 1.2.1
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

define('STATSFC_RESULTS_ID',	'StatsFC_Results');
define('STATSFC_RESULTS_NAME',	'StatsFC Results');

/**
 * Adds StatsFC widget.
 */
class StatsFC_Results extends WP_Widget {
	private static $_competitions = array(
		'premier-league'	=> 'Premier League',
		'fa-cup'			=> 'FA Cup',
		'league-cup'		=> 'League Cup',
		'community-shield'	=> 'Community Shield'
	);

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
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
	public function form($instance) {
		$defaults = array(
			'title'			=> __('Results', STATSFC_RESULTS_ID),
			'api_key'		=> __('', STATSFC_RESULTS_ID),
			'competition'	=> __(current(array_keys(self::$_competitions)), STATSFC_RESULTS_ID),
			'team'			=> __('', STATSFC_RESULTS_ID),
			'limit'			=> __(5, STATSFC_RESULTS_ID),
			'default_css'	=> __('', STATSFC_RESULTS_ID)
		);

		$instance		= wp_parse_args((array) $instance, $defaults);
		$title			= strip_tags($instance['title']);
		$api_key		= strip_tags($instance['api_key']);
		$competition	= strip_tags($instance['competition']);
		$team			= strip_tags($instance['team']);
		$limit			= strip_tags($instance['limit']);
		$default_css	= strip_tags($instance['default_css']);
		?>
		<p>
			<label>
				<?php _e('Title', STATSFC_RESULTS_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('API key', STATSFC_RESULTS_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo esc_attr($api_key); ?>">
			</label>
		</p>
		<p>
			<label>
				<?php _e('Competition', STATSFC_RESULTS_ID); ?>:
				<select name="<?php echo $this->get_field_name('competition'); ?>">
					<?php
					foreach (self::$_competitions as $id => $name) {
						echo '<option value="' . esc_attr($id) . '"' . ($id == $competition ? ' selected' : '') . '>' . esc_attr($name) . '</option>' . PHP_EOL;
					}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Team', STATSFC_RESULTS_ID); ?>:
				<?php
				try {
					$data = $this->_fetchData('https://api.statsfc.com/premier-league/teams.json?key=' . (! empty($api_key) ? $api_key : 'free'));

					if (empty($data)) {
						throw new Exception('There was an error connecting to the StatsFC API');
					}

					$json = json_decode($data);
					if (isset($json->error)) {
						throw new Exception($json->error);
					}
					?>
					<select class="widefat" name="<?php echo $this->get_field_name('team'); ?>">
						<option></option>
						<?php
						foreach ($json as $row) {
							echo '<option value="' . esc_attr($row->path) . '"' . ($row->path == $team ? ' selected' : '') . '>' . esc_attr($row->name) . '</option>' . PHP_EOL;
						}
						?>
					</select>
				<?php
				} catch (Exception $e) {
				?>
					<input class="widefat" name="<?php echo $this->get_field_name('team'); ?>" type="text" value="<?php echo esc_attr($team); ?>">
				<?php
				}
				?>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Number of results', STATSFC_RESULTS_ID); ?>:
				<input class="widefat" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo esc_attr($limit); ?>" min="0" max="99"><br>
				<small>Applies to single team only. Choose '0' for all results.</small>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Use default CSS?', STATSFC_RESULTS_ID); ?>
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
	public function update($new_instance, $old_instance) {
		$instance					= $old_instance;
		$instance['title']			= strip_tags($new_instance['title']);
		$instance['api_key']		= strip_tags($new_instance['api_key']);
		$instance['competition']	= strip_tags($new_instance['competition']);
		$instance['team']			= strip_tags($new_instance['team']);
		$instance['limit']			= strip_tags($new_instance['limit']);
		$instance['default_css']	= strip_tags($new_instance['default_css']);

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
	public function widget($args, $instance) {
		extract($args);

		$title			= apply_filters('widget_title', $instance['title']);
		$api_key		= $instance['api_key'];
		$competition	= $instance['competition'];
		$team			= $instance['team'];
		$limit			= (int) $instance['limit'];
		$default_css	= $instance['default_css'];

		if (empty($team)) {
			$limit = 20;
		}

		echo $before_widget;
		echo $before_title . $title . $after_title;

		try {
			$data = $this->_fetchData('https://api.statsfc.com/' . esc_attr($competition) . '/results.json?key=' . $api_key . (! empty($limit) ? '&limit=' . $limit : '') . (! empty($team) ? '&team=' . esc_attr($team) : ''));

			if (empty($data)) {
				throw new Exception('There was an error connecting to the StatsFC API');
			}

			$json = json_decode($data);
			if (isset($json->error)) {
				throw new Exception($json->error);
			}

			if (count($json) == 0) {
				throw new Exception('No results found');
			}

			if ($default_css) {
				wp_register_style(STATSFC_RESULTS_ID . '-css', plugins_url('all.css', __FILE__));
				wp_enqueue_style(STATSFC_RESULTS_ID . '-css');
			}
			?>
			<div class="statsfc_results">
				<table>
					<?php
					$total		= 0;
					$limit		= (! empty($team) ? $limit : 10);
					$previous	= null;

					foreach ($json as $result) {
						$total++;

						if (date('Y-m-d', strtotime($result->date)) !== $previous) {
							if ($total > 1) {
								echo '</tbody>' . PHP_EOL;
							}

							if ($limit > 0 && $total > $limit) {
								break;
							}

							$previous = date('Y-m-d', strtotime($result->date));
							?>
							<thead>
								<tr>
									<th colspan="5"><?php echo date('l, j F Y', strtotime($result->date)); ?></th>
								</tr>
							</thead>
							<tbody>
						<?php
						}
						?>
						<tr>
							<td class="statsfc_home<?php echo ($team == $result->home ? ' statsfc_highlight' : ''); ?>">
								<span class="statsfc_status"><?php echo $this->_status($result->status); ?></span>
								<?php echo esc_attr($result->homeshort); ?>
							</td>
							<td class="statsfc_homeScore"><?php echo $this->_score($result, 'home'); ?></td>
							<td class="statsfc_vs">-</td>
							<td class="statsfc_awayScore"><?php echo $this->_score($result, 'away'); ?></td>
							<td class="statsfc_away<?php echo ($team == $result->away ? ' statsfc_highlight' : ''); ?>"><?php echo esc_attr($result->awayshort); ?></td>
						</tr>
					<?php
					}
					?>
				</table>

				<p class="statsfc_footer"><small>Powered by StatsFC.com</small></p>
			</div>
		<?php
		} catch (Exception $e) {
			echo '<p class="statsfc_error">' . esc_attr($e->getMessage()) .'</p>' . PHP_EOL;
		}

		echo $after_widget;
	}

	private function _fetchData($url) {
		if (function_exists('curl_exec')) {
			$ch = curl_init();

			curl_setopt_array($ch, array(
				CURLOPT_AUTOREFERER		=> true,
				CURLOPT_FOLLOWLOCATION	=> true,
				CURLOPT_HEADER			=> false,
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_TIMEOUT			=> 5,
				CURLOPT_URL				=> $url
			));

			$data = curl_exec($ch);
			curl_close($ch);

			return $data;
		}

		return file_get_contents($url);
	}

	private function _status($status) {
		switch ($status) {
			case 'Finished':		return '<abbr title="Full-time">FT</abbr>';
			case 'Finished AET':	return '<abbr title="After extra-time"</abbr>AET</abbr>';
			case 'Finished AP':		return '<abbr title="After penalties">AP</abbr>';
			case 'Postponed':		return '<abbr title="Postponed">Postp.</abbr>';
			case 'Abandoned':		return '<abbr title="Abandoned">Aband.</abbr>';
		}

		return $status;
	}

	private function _score($data, $team) {
		$index = ($team == 'home' ? 0 : 1);

		switch ($data->status) {
			case 'Finished':		return $data->fulltime[$index];
			case 'Finished AET':	return ($data->fulltime[$index] + $data->extratime[$index]);
			case 'Finished AP':		return ($data->fulltime[$index] + $data->extratime[$index]) . '<sup>' . $data->penalties[$index] . '</sup>';
		}
	}
}

// register StatsFC widget
add_action('widgets_init', create_function('', 'register_widget("' . STATSFC_RESULTS_ID . '");'));