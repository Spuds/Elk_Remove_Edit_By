<?php

/* Remove "Last Edit" Addon for ElkArte
 *
 * @package RemoveLastEdit
 * @author Yoshi2889
 * @version 0.4.0
 * @license Creative Commons Attribution-ShareAlike 3.0 Unported license
 */

if (!defined('ELK'))
	die('No access...');

/**
 * The permissions, allowing people to remove either their own or others' edit sign.
 *
 * - Permissions hook, integrate_load_permissions, called from ManagePermissions.php
 * - used to add new permissions
 *
 * @param mixed[] $permissionGroups
 * @param mixed[] $permissionList
 */
function rlem_permissions(&$permissionGroups, &$permissionList)
{
	loadLanguage('Rlem');

	// Permission groups.
	$permissionGroups['membergroup']['rlem'] = array('rlem');

	// A list.
	$permissions = array(
		'rlem_do_own',
		'rlem_do_any',
	);

	// Add them.
	foreach ($permissions as $perm)
		$permissionList['membergroup'][$perm] = array(false, 'rlem', 'rlem');
}

/**
 * Add the remove link to the edit by text for those can remove it
 *
 * - Display Hook, integrate_prepare_display_context, called from Display.controller
 * - Used to interact with the message array before its sent to the template
 *
 * @param mixed[] $output
 * @param mixed[] $message
 */
function rlem_display(&$output, &$message)
{
	global $context, $scripturl;

	// Make sure we need to do anything
	if ($context['user']['is_guest'] || empty($output['modified']['name']))
		return;

	// Then check if you can do anything
	$can_rlem = allowedTo('rlem_do_any') || (($message['id_member'] == $context['user']['id']) && allowedTo('rlem_do_own'));
	if (!$can_rlem)
		return;

	$output['modified']['last_edit_text'] = $output['modified']['last_edit_text'] . '<sup><a href="' . $scripturl . '?action=rlem;post=' . $message['id_msg'] . '"><i class="fa fa-minus-circle"></i></a></sup>';
}