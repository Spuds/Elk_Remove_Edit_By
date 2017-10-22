<?php

/**
 * Remove "Last Edit" Addon for ElkArte
 *
 * @package RemoveLastEdit
 * @author Yoshi2889
 * @version 1.0.0
 * @license Creative Commons Attribution-ShareAlike 3.0 Unported license
 */

if (!defined('ELK'))
	die('No access...');

/**
 * Simple controller to respond to RLEM action request
 */
class Rlem_Controller extends Action_Controller
{
	/**
	 * Message Number
	 * @var int
	 */
	protected $_postid;

	/**
	 * Default action method, if a specific method wasn't
	 * directly called already. Simply forwards to main.
	 */
	public function action_index()
	{
		$this->action_rlem();
	}

	/**
	 * Just positions as a bridge between rlem_do and the topic view. Nothing more really.
	 */
	public function action_rlem()
	{
		// Valid data
		if (empty($_REQUEST['post']) || !is_numeric($_REQUEST['post']))
			return;

		$this->_postid = (int) $_REQUEST['post'];
		$this->rlem_do();
	}

	/**
	 * The main function which does all the work.
	 *
	 * @todo this really belongs in Relm.subs, but ijaa
	 */
	private function rlem_do()
	{
		global $scripturl, $user_info;

		$db = database();

		// Check if the post is valid, an ID is what it says it is, unique.
		$request = $db->query('', '
			SELECT id_msg, id_topic, id_member
			FROM {db_prefix}messages
			WHERE id_msg = {int:msgid}
			LIMIT 1',
			array(
				'msgid' => $this->_postid,
			)
		);
		if ($db->num_rows($request) == 1)
			list($id_msg, $id_topic, $id_member) = $db->fetch_row($request);
		$db->free_result($request);
		
		// Does it exist and are you allowed?
		if (isset($id_msg, $id_topic, $id_member) && (allowedTo('rlem_do_any') || (($id_member == $user_info['id']) && allowedTo('rlem_do_own'))))
		{
			// This clears the db fields used to determine if the post was modified,
			$db->query('', '
				UPDATE {db_prefix}messages
				SET
					modified_name = {string:name}
				WHERE id_msg = {int:msgid}',
				array(
					'msgid' => $this->_postid,
					'name' => ''
				)
			);

			// And we're done!
			redirectexit($scripturl . '?topic=' . $id_topic . '.msg' . $id_msg . '#msg' . $id_msg);
		}
	}
}