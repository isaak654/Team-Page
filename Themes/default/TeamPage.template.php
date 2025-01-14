<?php

/**
 * @package Team Page
 * @version 5.2
 * @author Diego Andrés <diegoandres_cortes@outlook.com>
 * @copyright Copyright (c) 2022, SMF Tricks
 * @license https://www.mozilla.org/en-US/MPL/2.0/
 */

function template_TeamPage_above()
{
	global $context, $scripturl;

	echo '
		<div class="buttonlist floatright">';
		
		foreach ($context['teampage_tabs'] as $page => $tab)
			echo '
				<a class="button', (isset($_REQUEST['sa']) && $_REQUEST['sa'] == $tab['page_action'] || $tab['id_page'] == $context['teampage']['page_id']) ? ' active' : (!isset($_REQUEST['sa']) && $tab['page_order'] == 0 ? ' active' : ''), '" href="' , $scripturl . '?action=team;sa=', $tab['page_action'], '">', $tab['page_name'], '</a>';

	echo '
		</div>
		<div class="clear"></div>
		
		<div class="cat_bar">
			<h3 class="catbg">
				', $context['teampage_title'], '
			</h3>
		</div>';
}

function template_teampage_view()
{
	global $context;

	// Groups or Mods
	if ($context['teampage']['page_type'] === 'Groups' || $context['teampage']['page_type'] === 'Mods')
		echo '
		<div class="roundframe" id="', !empty($context['teampage']['team']) ? 'tp_main_box' : 'tp_main_boards', '">
			', !empty($context['teampage']['groups']) ? display_group() : display_moderators(), '
		</div>';
	// Text
	else
		echo '
		<div class="roundframe">
			', $context['teampage']['body'], '
		</div>';
}

function template_TeamPage_below()
{
	global $context;

	echo $context['teampage']['copyright'];
}

function display_group()
{
	global $context, $modSettings, $txt;

	// Nothing?
	if (empty($context['teampage']['team']))
		return $txt['TeamPage_groups_empty'];

	// Blocks
	foreach($context['teampage']['team'] as $placement => $groups)
	{
		echo '
		<div id="tp_block_'. $placement . '">';
		// Groups
		foreach($groups as $group)
		{
			echo  '
			<div class="tp_group_container">
				<div class="title_bar">
					<h4 class="titlebg">
						', $group['group_name'], '
					</h4>
				</div>';

			// Description and emmm... Badge?
			if (!empty($modSettings['TeamPage_show_badges']) || (!empty($modSettings['TeamPage_show_description']) && !empty($group['description'])))
			{
				echo '
				<div class="information">
					', !empty($modSettings['TeamPage_show_description']) && !empty($group['description']) ? '<span class="tp_group_description">'. $group['description'].'</span>' : '', '
					', display_badge($group['icons']), '
				</div>';
			}
			echo '
				<ul>';

				// Members
				foreach($group['members'] as $user)
					// User display
					display_member($user, $placement, (isset($group['online_color']) ? $group['online_color'] : ''));

				echo '
				</ul>
			</div>';
		}
		echo '
		</div>';
	}
}

function display_moderators()
{
	global $context, $modSettings, $txt;

	// Nothing?
	if (empty($context['teampage']['moderators']))
		return $txt['TeamPage_groups_empty'];

	// Blocks
	foreach($context['teampage']['moderators'] as $boards => $board)
	{
		echo  '
		<div class="tp_board_container">
			<div class="title_bar">
				<h4 class="titlebg">
					', $board['name'], '
				</h4>
			</div>';

			// Description and emmm... Badge?
			if ((!empty($modSettings['TeamPage_show_badges']) && !empty($board['icons'])) || (!empty($modSettings['TeamPage_show_description']) && !empty($board['description'])))
			{
				echo '
				<div class="information">
					', !empty($modSettings['TeamPage_show_description']) && !empty($board['description']) ? '<span class="tp_group_description">'. $board['description'].'</span>' : '', '
					', display_badge($board['icons']), '
				</div>';
			}
			echo '
				<ul>';

			// Nothing?
			if (empty($board['members']))
				return '<li class="windowbg">' . $txt['TeamPage_groups_empty'] . '</li>';

			// Moderators
			foreach($board['members'] as $user)
			{
				// User display
				display_member($user);
			}

		echo '
			</ul>
		</div>';
	}
}

function display_member($user, $placement = 'left', $group_color = false)
{
	global $modSettings, $scripturl, $boardurl, $txt;

	echo '
	<li', ($placement != 'bottom' ? ' class="windowbg"' : '') , '>
		', !empty($modSettings['TeamPage_show_avatars']) ? '<img class="tp_avatar" src="'.(!empty($user['avatar']['href']) ? $user['avatar']['href'] : $boardurl. '/avatars/default.png').'" alt="" style="'.(!empty($modSettings['TeamPage_avatars_width']) ? 'width:'.$modSettings['TeamPage_avatars_width'].'px;' : '').(!empty($modSettings['TeamPage_avatars_height']) ? 'height:'.$modSettings['TeamPage_avatars_height'].'px;' : '').'" />' : '', '
		<h2 class="tp_user_name">
			<a href="', $scripturl, '?action=profile;u=', $user['id_member'], '"', !empty($group_color) ? ' style="color: '. $group_color . ';"' : '', '>', $user['real_name'], '</a>
			', !empty($modSettings['TeamPage_show_custom']) && !empty($user['usertitle']) ? ' - <strong>'. $user['usertitle']. '</strong>' : '', '
		</h2>
		<div class="tp_user_info">
			', !empty($user['boards']) ? boards_list($user['boards']) : '', '
			', !empty($modSettings['TeamPage_show_personal']) && !empty($user['personal_text']) ? '
			<span class="tp_user_personal">
				<strong>' . $txt['personal_text'] . ': </strong><i>' . $user['personal_text']. '</i>
			</span>' : '', '
			', !empty($modSettings['TeamPage_show_registered']) ? '
			<span class="tp_user_joined">
				<strong>' . $txt['TeamPage_date_registered'] . ': </strong>' . timeformat($user['date_registered']). '
			</span>' : '', '
			', !empty($modSettings['TeamPage_show_login']) ? '
			<span class="tp_user_joined">
				<strong>' . $txt['TeamPage_last_login'] . ': </strong>' . timeformat($user['last_login']). '
			</span>' : '', '
			', !empty($modSettings['TeamPage_show_posts']) ? '
			<span class="tp_user_joined">
				<strong>' . $txt['posts'] . ': </strong>' . $user['posts']. '
			</span>' : '', '
			', !empty($modSettings['TeamPage_show_website']) && !empty($user['website_url']) ? '
			<span class="tp_user_joined">
				<strong>' . $txt['TeamPage_website'] . ': </strong><a href="' . $user['website_url']. '" target="_blank" rel="noopener">' . $user['website_title']. '</a>
			</span>' : '', '
		</div>
	</li>';
}

function boards_list($boards)
{
	global $scripturl, $txt;

	echo '
		<span class="tp_user_boards">
			<strong>' . $txt['TeamPages_boards_moderating'] . ': </strong> ';

	$b_list = [];
	foreach($boards as $board)
		$b_list[] = '<a href="'.$scripturl.'?board='.$board['id_board'].'.0">'.$board['name'].'</a>';

	echo implode(', ', $b_list), '
		</span>';
}

function display_badge($icon)
{
	global $settings, $modSettings;

	// Display badge? is there a badge/icon?
	if (!empty($modSettings['TeamPage_show_badges'])) {
		$icon = empty($icon) ? array('', '') : explode('#', $icon);

		//We need a little fallback for the membergroup icons. If it doesn't exist in the current theme, fallback to default theme
		if (isset($icon[1]) && file_exists($settings['actual_theme_dir'] . '/images/membericons/' . $icon[1])) //icon is set and exists
			$group_icon_url = $settings['images_url'] . '/membericons/' . $icon[1];
		elseif (isset($profile['icons'][1])) //icon is set and doesn't exist, fallback to default
			$group_icon_url = $settings['default_images_url'] . '/membericons/' . $icon[1];
		else //not set, bye bye
			$group_icon_url = '';

		// Return the icon/badge!
		if (!empty($group_icon_url))
			echo
			 str_repeat('<img class="tp_group_badge" src="'.$group_icon_url.'" alt="" />', $icon[0]);
		else
			return;
	}
	else
		return;
}